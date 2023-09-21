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
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::logged();
	}

	/**
	 * Show menus
	 *
	 * @param   integer $id_theme Theme ID
	 * @param   string	$theme_name Theme name
	 * @return  void
	 */
	public function index(int $id_theme, string $theme_name)
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
	 *
	 * @return  void
	 */
	private function actions(int $id_theme)
	{
		return '<a class="link" @click="popup(\''.BASE_URL.'menus/edit/'.$id_theme.'\')" title="'._NEW_MENU.'">
                <i class="fa-solid fa-lg fa-circle-plus"></i>
            </a>';
	}

	/**
	 * Change status
	 *
	 * @param   string  $what field to change
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
		$msg = AdmUtils_helper::chk_priv_level(1, $_SESSION['xuid'], 'menus', $id, $val);
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
	 * New / Edit menu form (use Ajax)
	 *
	 * @param   integer  $id_theme Theme id
	 * @param   integer  $id item ID (if 0 then is a new item)
	 * @return  void
	 */
	public function edit(int $id_theme, int $id = 0)
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

		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');
		$view->render(true);
	}

	/**
	 * Register Edit / New Menu form data
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing(int $id, array $_post)
	{
		$msg = null;
		// check permission
		$msg = ($_post['id'])
		    ? AdmUtils_helper::chk_priv_level(1, $_SESSION['xuid'], 'menus', $_post['id'], 2)
            : AdmUtils_helper::chk_priv_level(1, $_SESSION['xuid'], '_menu_creation', 0, 4);

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
                    // permissions
                    $perm = new Permission_model();
                    $array[] = array(
                        'action' => 'insert',
                        'id_what' => $result[0],
                        'id_user' => $_SESSION['xuid'],
                        'level' => 4
                    );
                    $perm->pexec('menus', $array, 1);
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
	 * Delete Menu form (use Ajax)
	 *
	 * @param   integer $id Menu ID
	 * @return  void
	 */
	public function delete(int $id)
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
	 *
	 * @access	private
	 * @param   object  $item
	 * @return  void
	 */
	private function deleting(stdClass $item)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level(1, $_SESSION['xuid'], 'menus', $item->id, 4);
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
				$perm = new Permission_model();
				$perm->deleting_by_what('menus', $item->id);

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

	/**
	 * Refresh menu order
	 *
	 * @param   integer $id Page ID
	 * @param   string	$holder Menu ID
	 * @param   string 	$orders Encoded string, for each menu you have a section, each section contains the list of Page ID in menu
	 * @return  void
	 */
	public function menu(int $id_page, string $holder, string $orders)
	{
		$msg = null;
		if (!is_null($id_page) && is_numeric($id_page))
		{
		    // check permission
		    $msg = AdmUtils_helper::chk_priv_level(1, $_SESSION['xuid'], 'pages', $id_page, 3);

		    if (is_null($msg))
		    {
		        // refresh order
		        $menu = new Menu_model();
		        $result = $menu->menu($id_page, substr($holder, 1), $orders);

		        // set message
		        $this->dict->get_words();
		        $msg = AdmUtils_helper::set_msg($result);
		    }
		}
		$this->response($msg);
	}
}
