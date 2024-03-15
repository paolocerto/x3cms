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
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::logged();
	}

	/**
	 * Show categories
	 */
	public function _default() : void
	{
		$this->index(2, X4Route_core::$lang);
	}

	/**
	 * Show categories
	 */
	public function index(int $id_area, string $lang, string $tag = '') : void
	{
		// load dictionary
		$this->dict->get_wordarray(array('categories', 'articles'));

		$area = new Area_model();
		list($id_area, $areas) = $area->get_my_areas($this->site->data->id, $id_area);

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
	 */
	private function actions(int $id_area, string $lang, string $tag = '') : string
	{
		return '<a class="link" @click="popup(\''.BASE_URL.'categories/edit/'.$id_area.'/'.$lang.'/'.$tag.'\')" title="'._NEW_CATEGORY.'">
            <i class="fa-solid fa-lg fa-circle-plus"></i>
        </a>';
	}

	/**
	 * Change status
	 */
	public function set(string $what, int $id_area, int $id, int $value = 0) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($id_area, 'categories', $id, $what);
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
	 */
	public function edit(int $id_area, string $lang, string $tag = '', int $id = 0) : void
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

        // can user edit?
        $submit = AdmUtils_helper::submit_btn($id_area, 'categories', $id, $item->xlock);

		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, $submit, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

		$view->render(true);
	}

	/**
	 * Register Edit / New Category form
	 */
	private function editing(int $id, array $_post) : void
	{
		$msg = null;
		// check permission
		$msg = ($id)
			? AdmUtils_helper::chk_priv_level($_post['id_area'], 'categories', $_post['id'], 'edit')
			: AdmUtils_helper::chk_priv_level($_post['id_area'], '_category_creation', 0, 'create');

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
                    if (!$id)
                    {
                        Admin_utils_helper::set_priv($_SESSION['xuid'], $result[0], 'categories', $post['id_area']);
                    }

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
	 * Delete category form
	 */
	public function delete(int $id) : void
	{
		$this->dict->get_wordarray(array('form', 'categories'));

		$mod = new Category_model();
		$item = $mod->get_by_id($id, 'categories', 'id, id_area, lang, tag, title');

		$fields = array();
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
        $view->title = _DELETE_CATEGORY;

		$view->content = new X4View_core('delete');
		$view->content->item = $item->title;

		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');

		$view->render(true);
	}

	/**
	 * Delete category
	 */
	private function deleting(stdClass $item) : void
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($item->id_area, 'categories', $item->id, 'delete');

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
				AdmUtils_helper::delete_priv('categories', $item->id);

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
