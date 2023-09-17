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
 * Controller for Categories
 *
 * @package X3CMS
 */
class Categories_controller extends X3ui_controller
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
	 * Show categories
	 *
	 * @return  void
	 */
	public function _default()
	{
		$this->index(2, X4Route_core::$lang);
	}

	/**
	 * Show categories
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string 	$tag
	 * @return  void
	 */
	public function index(int $id_area, string $lang, string $tag = '')
	{
		// load dictionary
		$this->dict->get_wordarray(array('categories', 'articles'));

		$area = new Area_model();
		list($id_area, $areas) = $area->get_my_areas($id_area);

		$lang = (empty($lang))
			? X4Route_core::$lang
			: $lang;

		// get page
		$page = $this->get_page('categories');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = $this->actions($id_area, $lang, $tag);

		$mod = new Category_model();
		$tags = $mod->get_tags($id_area, $lang);

        // contents
		$view->content = new X4View_core('categories/category_list');
		$view->content->items = $mod->get_categories($id_area, $lang, $tag);

		// tag switcher
		$view->content->tag = $tag;
		$view->content->tags = $tags;

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
	 * Categories actions
	 *
	 * @return  void
	 */
	private function actions(int $id_area, string $lang, string $tag = '')
	{
		return '<a class="link" @click="popup(\''.BASE_URL.'categories/edit/'.$id_area.'/'.$lang.'/'.$tag.'\')" title="'._NEW_CATEGORY.'">
            <i class="fa-solid fa-lg fa-circle-plus"></i>
        </a>';
	}

	/**
	 * Change status
	 *
	 * @param   string	$what field to change
	 * @param   integer $id ID of the item to change
	 * @param   integer $value value to set (0 = off, 1 = on)
	 * @return  void
	 */
	public function set(string $what, int $id, int $value = 0)
	{
		$msg = null;
		// check permission
		$val = ($what == 'xlock')
			? 4
			: 3;
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'categories', $id, $val);
		if (is_null($msg))
		{
			// do action
			$mod = new Category_model();
			$result = $mod->update($id, array($what => $value));

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
	 * New / Edit category form
	 *
	 * @param   integer	$id_area Area ID
     * @param   string 	$lang Language code
     * @param   string 	$tag
	 * @param   integer	$id Category ID
	 * @return  void
	 */
	public function edit(int $id_area, string $lang, string $tag = '', int $id = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'categories'));

		// get object
		$mod = new Category_model();
		$item = ($id)
			? $mod->get_by_id($id)
			: new Category_obj($id_area, $lang, $tag);


        $form_fields = new X4Form_core('category/category_edit');
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
			? _EDIT_CATEGORY
			: _ADD_CATEGORY;

		// content
		$view->content = new X4View_core('editor');

		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

		$view->render(true);
	}

	/**
	 * Register Edit / New Category form data
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
			? AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'categories', $_post['id'], 3)
			: AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_category_creation', 0, 4);

		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'id_area' => $_post['id_area'],
				'lang' => $_post['lang'],
				'title' => $_post['title'],
				'name' => X4Utils_helper::slugify($_post['title']),
				'tag' => X4Utils_helper::slugify($_post['tag'])
			);

			$mod = new Category_model();

			// check if category already exists
			$check = $mod->exists($post, $id);
			if ($check)
            {
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_CATEGORY_ALREADY_EXISTS', 'msg'));
            }
			else
			{
				// update or insert
				$result = ($id)
					? $mod->update($_post['id'], $post)
                    : $mod->insert($post);

				// set message
				$msg = AdmUtils_helper::set_msg($result);

				// set what update
				if ($result[1])
				{
					$msg->update = array(
						'element' => 'page',
						'url' => BASE_URL.'categories/index/'.$post['id_area'].'/'.$post['lang'].'/'.$post['tag']
					);
				}
			}
		}
		$this->response($msg);
	}

	/**
	 * Delete category form (use Ajax)
	 *
	 * @param   integer $id Category ID
	 * @return  void
	 */
	public function delete(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'categories'));

		// get object
		$mod = new Category_model();
		$item = $mod->get_by_id($id, 'categories', 'id, id_area, lang, tag, title');

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
        $view->title = _DELETE_CATEGORY;
		// contents
		$view->content = new X4View_core('delete');
		$view->content->item = $item->title;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');

		$view->render(true);
	}

	/**
	 * Delete category
	 *
	 * @access	private
	 * @param   stdClass	$item Category Obj
	 * @return  void
	 */
	private function deleting(stdClass $item)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'categories', $item->id, 4);

		if (is_null($msg))
		{
			// do action
			$mod = new Category_model();
			$result = $mod->delete($item->id);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// clear useless permissions
			if ($result[1])
            {
				$perm = new Permission_model();
				$perm->deleting_by_what('categories', $item->id);

				// set what update
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'categories/index/'.$item->id_area.'/'.$item->lang.'/'.$item->tag
				);
			}
		}
		$this->response($msg);
	}
}
