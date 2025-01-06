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
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::logged();
	}

	/**
	 * Show contexts
	 */
	public function _default() : void
	{
		$this->index(2, X4Route_core::$lang);
	}

	/**
	 * Show contexts
	 */
	public function index(int $id_area, string $lang) : void
	{
		$this->dict->get_wordarray(array('contexts', 'articles'));

		$area = new Area_model();
		list($id_area, $areas) = $area->get_my_areas($this->site->data->id, $id_area);

		$page = $this->get_page('contexts');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = AdminUtils_helper::link(
                'memo',
                'contexts:'.$page->lang,
                $this->memo('contexts:'.$page->lang, $_SESSION['xuid']),
                _MEMO
            ).$this->actions($id_area, $lang);

        // switchers
        $view->id_area = $id_area;
        $view->lang = $lang;
        $view->areas = $areas;
        $view->url = 'contexts/index/XAREAX/XLANGX';

		$mod = new Context_model();
		$view->content = new X4View_core('contexts/context_list');
        $view->content->page = $page;
		$view->content->items = $mod->get_contexts($id_area, $lang);

		$view->render(true);
	}

	/**
	 * Contexts actions
	 */
	private function actions(int $id_area, string $lang) : string
	{
		return '<a class="link" @click="popup(\''.BASE_URL.'contexts/edit/'.$id_area.'/'.$lang.'\')" title="'._NEW_CONTEXT.'">
            <i class="fa-solid fa-lg fa-circle-plus"></i>
        </a>';
	}

	/**
	 * Change status
	 */
	public function set(string $what, int $id_area, int $id, int $value = 0) : void
	{
		$msg = AdminUtils_helper::chk_priv_level($id_area, 'contexts', $id, $what);
		if (is_null($msg))
		{
			$mod = new Context_model();
			$obj = $mod->get_by_id($id);

			// default contexts cannot change status
			$result = ($obj->code > 100)
				? $mod->update($id, array($what => $value))
				: false;

			$this->dict->get_words();
			$msg = AdminUtils_helper::set_msg($result);

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
	 */
	public function edit(int $id_area, string $lang, int $id = 0) : void
	{
		$this->dict->get_wordarray(array('form', 'contexts'));

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

		$fields = $form_fields->render();

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

		$view->content = new X4View_core('editor');

        $submit = AdminUtils_helper::submit_btn($id_area, 'contexts', $id, $item->xlock);
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, $submit, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

        $view->render(true);
	}

	/**
	 * Register Edit / New Context form
	 */
	private function editing(int $id, array $_post) : void
	{
		$msg = ($id)
			? AdminUtils_helper::chk_priv_level($_post['id_area'], 'contexts', $id, 'edit')
			: AdminUtils_helper::chk_priv_level($_post['id_area'], '_context_creation', 0, 'create');

		if (is_null($msg))
		{
			$post = array(
				'id_area' => $_post['id_area'],
				'lang' => $_post['lang'],
				'name' => strtolower($_post['name']),
				'xkey' => X4Utils_helper::slugify($_post['name'])
			);

			$mod = new Context_model();

			$check = $mod->exists($post, $id);
			if ($check)
            {
				$msg = AdminUtils_helper::set_msg(false, '', $this->dict->get_word('_CONTEXT_ALREADY_EXISTS', 'msg'));
            }
			else
			{
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
                        AdminUtils_helper::set_priv($_SESSION['xuid'], $result[0], 'contexts', $post['id_area']);

						// add item into dictionary
						$mod->check_dictionary($post, 1);
					}
				}

				$msg = AdminUtils_helper::set_msg($result);

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
	 * Delete context form
	 */
	public function delete(int $id) : void
	{
		$mod = new Context_model();
		$item = $mod->get_by_id($id, 'contexts', 'id, id_area, lang, name, code');

		// only added context can be deleted
		if ($item->code > 100)
		{
			$this->dict->get_wordarray(array('form', 'contexts'));

			$fields = [];
			$fields[] = array(
				'label' => null,
				'type' => 'hidden',
				'value' => $id,
				'name' => 'id'
			);

			if (X4Route_core::$post)
			{
				$this->deleting($item);
				die;
			}
            $view = new X4View_core('modal');
            $view->title = _DELETE_CONTEXT;

			$view->content = new X4View_core('delete');

			$view->content->item = $item->name;
            $view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
                '@click="submitForm(\'delete\')"');
			$view->render(true);
		}
	}

	/**
	 * Delete context
	 */
	private function deleting(stdClass $item) : void
	{
		$msg = AdminUtils_helper::chk_priv_level($item->id_area, 'contexts', $item->id, 'delete');
		if (is_null($msg))
		{
			$mod = new Context_model();
			$result = $mod->delete($item->id);

			$msg = AdminUtils_helper::set_msg($result);

			if ($result[1])
            {
				AdminUtils_helper::delete_priv('contexts', $item->id);

				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'contexts/index/'.$item->id_area.'/'.$item->lang
				);
			}
		}
		$this->response($msg);
	}
}
