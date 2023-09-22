<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

/**
 * Controller for Contexts
 *
 * @package X3CMS
 */
class Contexts_controller extends X3ui_controller
{
	/**
	 * Constructor
	 * check if user is logged
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::logged();
	}

	/**
	 * Show contexts
	 *
	 * @return  void
	 */
	public function _default()
	{
		$this->index(2, X4Route_core::$lang);
	}

	/**
	 * Show contexts
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @return  void
	 */
	public function index(int $id_area, string $lang)
	{
		// load dictionary
		$this->dict->get_wordarray(array('contexts', 'articles'));

		$area = new Area_model();
		list($id_area, $areas) = $area->get_my_areas($id_area);

		// get page
		$page = $this->get_page('contexts');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = $this->actions($id_area, $lang);

		// content
		$mod = new Context_model();
		$view->content = new X4View_core('contexts/context_list');
		$view->content->items = $mod->get_contexts($id_area, $lang);

		// area switcher
		$view->content->id_area = $id_area;
		$view->content->areas = $areas;

		// language switcher
		$view->content->lang = $lang;
        if (MULTILANGUAGE)
        {
            $lang = new Language_model();
            $view->content->langs = $lang->get_languages();
        }

		$view->render(true);
	}

	/**
	 * Contexts actions
	 *
	 * @return  void
	 */
	private function actions(int $id_area, string $lang)
	{
		return '<a class="link" @click="popup(\''.BASE_URL.'contexts/edit/'.$id_area.'/'.$lang.'\')" title="'._NEW_CONTEXT.'">
            <i class="fa-solid fa-lg fa-circle-plus"></i>
        </a>';
	}

	/**
	 * Change status
	 *
	 * @param   string  $what field to change
     * @param   integer $id_area
	 * @param   integer $id ID of the item to change
	 * @param   integer $value value to set (0 = off, 1 = on)
	 * @return  void
	 */
	public function set(string $what, int $id_area, int $id, int $value = 0)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($id_area, 'contexts', $id, $what);
		if (is_null($msg))
		{
			// do action
			$mod = new Context_model();
			$obj = $mod->get_by_id($id);

			// default contexts cannot change status
			$result = ($obj->code > 100)
				? $mod->update($id, array($what => $value))
				: false;

			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);

			// set update
			if ($result[1])
            {
                $msg->update = array(
                    'element' => 'page',
					'url' => $_SERVER['HTTP_REFERER']
                );
            }
		}
		$this->response($msg);
	}

	/**
	 * New / Edit context form
	 *
     * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   integer	$id Context ID
	 * @return  void
	 */
	public function edit(int $id_area, string $lang, int $id = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'contexts'));

		// get object
		$mod = new Context_model();
		$item = ($id)
			? $mod->get_by_id($id)
			: new Context_obj($id_area, $lang);

        $form_fields = new X4Form_core('context/context_edit');
		$form_fields->item = $item;

        $mod = new Area_model();
        $form_fields->areas = $mod->get_areas();

        $mod = new Language_model();
        $form_fields->languages = $mod->get_languages();

		// get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->editing($id, $_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
        $view = new X4View_core('modal');
        $view->title = ($id)
			? _EDIT_CONTEXT
			: _ADD_CONTEXT;

		// content
		$view->content = new X4View_core('editor');
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

        $view->render(true);
	}

	/**
	 * Register Edit / New Context form data
	 *
	 * @access	private
	 * @param   integer $id item ID (if 0 then is a new item)
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing(int $id, array $_post)
	{
		$msg = null;
		// check permission
		$msg = ($id)
			? AdmUtils_helper::chk_priv_level($_post['id_area'], 'contexts', $id, 'edit')
			: AdmUtils_helper::chk_priv_level($_post['id_area'], '_context_creation', 0, 'create');

		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'id_area' => $_post['id_area'],
				'lang' => $_post['lang'],
				'name' => strtolower($_post['name']),
				'xkey' => X4Utils_helper::slugify($_post['name'])
			);

			$mod = new Context_model();

			// check if context already exists
			$check = $mod->exists($post, $id);
			if ($check)
            {
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_CONTEXT_ALREADY_EXISTS', 'msg'));
            }
			else
			{
				// update or insert
				if ($id)
				{
					$result = $mod->update($id, $post);
					// check if dictionary name for the context already exists
					if ($result[1])
					{
						$mod->check_dictionary($post);
					}
				}
				else
				{
					// get the code of the new context
					$code = $mod->get_max_code($post['id_area'], $post['lang']);

					// this implies that one site can't have more than 33 languages
					// you have 3 default contexts (draft, page, multipages) for each language and for each area
					$post['code'] = ($code > 100) ? ($code+1) : 101;

					$result = $mod->insert($post);
					if ($result[1])
					{
                        // permissions
                        $perm = new Permission_model();
                        $array[] = array(
                            'action' => 'insert',
                            'id_what' => $result[0],
                            'id_user' => $_SESSION['xuid'],
                            'level' => 4
                        );
                        $perm->pexec('contexts', $array, $post['id_area']);

						// add item into dictionary
						$mod->check_dictionary($post, 1);
					}
				}

				// set message
				$msg = AdmUtils_helper::set_msg($result);

				// set what update
				if ($result[1])
				{
                	$msg->update = array(
						'element' => 'topic',
						'url' => BASE_URL.'contexts/index/'.$post['id_area'].'/'.$post['lang']
					);
				}
			}
		}
		$this->response($msg);
	}

	/**
	 * Delete context form (use Ajax)
	 *
	 * @param   integer $id Context ID
	 * @return  void
	 */
	public function delete(int $id)
	{
		// get object
		$mod = new Context_model();
		$item = $mod->get_by_id($id, 'contexts', 'id, id_area, lang, name, code');

		// only added context can be deleted
		if ($item->code > 100)
		{
			// load dictionaries
			$this->dict->get_wordarray(array('form', 'contexts'));

			// build the form
			$fields = array();
			$fields[] = array(
				'label' => null,
				'type' => 'hidden',
				'value' => $id,
				'name' => 'id'
			);

			// if submitted
			if (X4Route_core::$post)
			{
				$this->deleting($item);
				die;
			}
            $view = new X4View_core('modal');
            $view->title = _DELETE_CONTEXT;
			// contents
			$view->content = new X4View_core('delete');

			$view->content->item = $item->name;

			// form builder
			$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
                '@click="submitForm(\'delete\')"');
			$view->render(true);
		}
	}

	/**
	 * Delete context
	 *
	 * @access	private
	 * @param   stdClass    $item Context item
	 * @return  void
	 */
	private function deleting(stdClass $item)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($item->id_area, 'contexts', $item->id, 'delete');
		if (is_null($msg))
		{
			// do action
			$mod = new Context_model();
			$result = $mod->delete($item->id);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// clear useless permissions
			if ($result[1])
            {
				$perm = new Permission_model();
				$perm->deleting_by_what('contexts', $item->id);

				// set what update
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'contexts/index/'.$item->id_area.'/'.$item->lang
				);
			}
		}
		$this->response($msg);
	}
}
