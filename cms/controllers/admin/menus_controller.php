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
 * Controller for Menu items
 *
 * @package X3CMS
 */
class Menus_controller extends X3ui_controller
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
	 * Show menus
	 */
	public function index(int $id_theme, string $theme_name) : void
	{
		// load dictionary
		$this->dict->get_wordarray(array('menus'));

		// get page
		$page = $this->get_page('menus/index');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = $this->actions($id_theme);

		// content
		$view->content = new X4View_core('themes/menu_list');
		$view->content->id_theme = $id_theme;
		$view->content->theme = $theme_name;

		$mod = new Menu_model();
		$view->content->menus = $mod->get_menus_by_theme($id_theme);
		$view->render(true);
	}

	/**
	 * Menus actions
	 */
	private function actions(int $id_theme) : string
	{
		return '<a class="link" @click="popup(\''.BASE_URL.'menus/edit/'.$id_theme.'\')" title="'._NEW_MENU.'">
                <i class="fa-solid fa-lg fa-circle-plus"></i>
            </a>';
	}

	/**
	 * Change status
	 */
	public function set(string $what, int $id, int $value = 0) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level(1, 'menus', $id, $what);
		if (is_null($msg))
		{
			// do action
			$menus = new Menu_model();
			$result = $menus->update($id, array($what => $value));
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
	 * New / Edit menu form
	 */
	public function edit(int $id_theme, int $id = 0) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'menus'));

		// get object
		$mod = new Menu_model();
		$item = ($id)
			? $mod->get_by_id($id)
			: new Menu_obj($id_theme);

		// build the form
		$form_fields = new X4Form_core('menu/menu_edit');
		$form_fields->id = $id;
		$form_fields->item = $item;

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
			? _EDIT_MENU
			: _ADD_MENU;

		// contents
		$view->content = new X4View_core('editor');
        // can user edit?
        $submit = AdmUtils_helper::submit_btn(1, 'menus', $id, $item->xlock);
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, $submit, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');
		$view->render(true);
	}

	/**
	 * Register Edit / New Menu form
	 */
	private function editing(int $id, array $_post) : void
	{
		$msg = null;
		// check permission
		$msg = ($_post['id'])
		    ? AdmUtils_helper::chk_priv_level(1, 'menus', $_post['id'], 'edit')
            : AdmUtils_helper::chk_priv_level(1, '_menu_creation', 0, 'create');

		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'id_theme' => $_post['id_theme'],
				'name' => $_post['name'],
				'description' => $_post['description']
			);

			$mod = new Menu_model();

			// update or insert
			$result = ($_post['id'])
                ? $mod->update($_post['id'], $post)
                : $mod->insert($post);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			if ($result[1])
			{
                if (!$id)
                {
                    Admin_utils_helper::set_priv($_SESSION['xuid'], $result[0], 'menus', 1);
                }

				$theme = $mod->get_var($post['id_theme'], 'themes', 'name');
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'menus/index/'.$post['id_theme'].'/'.$theme
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Delete Menu form
	 */
	public function delete(int $id) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'menus'));

		// get object
		$mod = new Menu_model();
		$item = $mod->get_by_id($id, 'menus', 'id, name, id_theme');

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
        $view->title = _DELETE_MENU;

		// contents
		$view->content = new X4View_core('delete');

		$view->content->item = $item->name;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

	/**
	 * Delete Menu
	 */
	private function deleting(stdClass $item) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level(1, 'menus', $item->id, 'delete');
		if (is_null($msg))
		{
			// action
			$mod = new Menu_model();
			$result = $mod->delete($item->id);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// clear useless permissions
			if ($result[1])
            {
				AdmUtils_helper::delete_priv('menus', $item->id);

				// set what update
				$theme = $mod->get_var($item->id_theme, 'themes', 'name');
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'menus/index/'.$item->id_theme.'/'.$theme
				);
			}
		}
		$this->response($msg);
	}
}
