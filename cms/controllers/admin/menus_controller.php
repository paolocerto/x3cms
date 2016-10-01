<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
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
	public function index($id_theme, $theme_name)
	{
		// load dictionary
		$this->dict->get_wordarray(array('menus'));
		
		// get page
		$page = $this->get_page('menus/index');
		
		// content
		$view = new X4View_core('container');
		
		$view->content = new X4View_core('themes/menu_list');
		$view->content->page = $page;
		$view->content->id_theme = $id_theme;
		$view->content->theme = $theme_name;
		
		$menu = new Menu_model();
		$view->content->menus = $menu->get_menus_by_theme($id_theme);
		$view->render(TRUE);
	}
	
	/**
	 * Menus filter
	 *
	 * @return  void
	 */
	public function filter($id_theme)
	{
		// load the dictionary
		$this->dict->get_wordarray(array('menus'));
		
		echo '<a class="btf" href="'.BASE_URL.'menus/edit/'.$id_theme.'" title="'._NEW_MENU.'"><i class="fa fa-plus fa-lg"></i></a>
<script>
window.addEvent("domready", function()
{
	buttonize("filters", "btf", "modal");
});
</script>';
	}
	
	/**
	 * Change status
	 *
	 * @param   string  $what field to change
	 * @param   integer $id ID of the item to change
	 * @param   integer $value value to set (0 = off, 1 = on)
	 * @return  void
	 */
	public function set($what, $id, $value = 0)
	{
		$msg = null;
		// check permission
		$val = ($what == 'xlock') 
			? 4 
			: 3;
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'menus', $id, $val);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// do action
			$menus = new Menu_model();
			$result = $menus->update($id, array($what => $value));
			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);
			
			// set update
			if ($result[1])
				$msg->update[] = array(
					'element' => $qs['div'],
					'url' => urldecode($qs['url']),
					'title' => null
				);
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
	public function edit($id_theme, $id = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'menus'));
		
		// get object
		$menu = new Menu_model();
		$m = ($id) 
			? $menu->get_by_id($id)
			: new Menu_obj($id_theme);
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id,
			'name' => 'id'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $m->id_theme,
			'name' => 'id_theme'
		);
		$fields[] = array(
			'label' => _NAME,
			'type' => 'text', 
			'value' => $m->name,
			'name' => 'name',
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		$fields[] = array(
			'label' => _DESCRIPTION,
			'type' => 'textarea', 
			'value' => $m->description,
			'name' => 'description',
			'extra' => 'class="large"'
		);
		
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
		
		// contents
		$view = new X4View_core('editor');
		$view->title = ($id) 
			? _EDIT_MENU 
			: _ADD_MENU;
		
		// form builder
		$view->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'editor\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Register Edit / New Menu form data
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing($id, $_post)
	{
		$msg = null;
		// check permission
		if ($_post['id']) 
			$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'menus', $_post['id'], 2);
		else 
			$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_menu_creation', 0, 4);
			
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
			if ($_post['id']) 
				$result = $mod->update($_post['id'], $post);
			else 
			{
				$result = $mod->insert($post);
				
				// add pemission
				if ($result[1])
				{
					$perm = new Permission_model();
					$array[] = array(
							'action' => 'insert', 
							'id_what' => $result[0], 
							'id_user' => $_SESSION['xuid'], 
							'level' => 4);
					$result = $perm->pexec('menus', $array, 1);
				}
			}
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			if ($result[1])
			{
				$theme = $mod->get_var($post['id_theme'], 'themes', 'name');
				$msg->update[] = array(
					'element' => 'tdown', 
					'url' => BASE_URL.'menus/index/'.$post['id_theme'].'/'.$theme,
					'title' => null
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
	public function delete($id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'menus'));
		
		// get object
		$menu = new Menu_model();
		$obj = $menu->get_by_id($id, 'menus', 'name, id_theme');
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id,
			'name' => 'id'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $obj->id_theme,
			'name' => 'id_theme'
		);
		
		// if submitted
		if (X4Route_core::$post)
		{
			$this->deleting($_POST);
			die;
		}
		
		// contents
		$view = new X4View_core('delete');
		$view->title = _DELETE_MENU;
		$view->item = $obj->name;
		
		// form builder
		$view->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '', 
			'onclick="setForm(\'delete\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Delete Menu
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function deleting($_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'menus', $_post['id'], 4);
		
		if (is_null($msg))
		{
			// action
			$mod = new Menu_model();
			$result = $mod->delete($_post['id']);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// clear useless permissions
			if ($result[1]) {
				$perm = new Permission_model();
				$perm->deleting_by_what('menus', $_post['id']);
				
				// set what update
				$theme = $mod->get_var($_post['id_theme'], 'themes', 'name');
				$msg->update[] = array(
					'element' => 'tdown', 
					'url' => BASE_URL.'menus/index/'.$_post['id_theme'].'/'.$theme,
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Refresh menu order
	 * Called via Ajax
	 *
	 * @param   integer $id Page ID
	 * @param   integer	$holder Menu ID
	 * @param   string 	$orders Encoded string, for each menu you have a section, each section contains the list of Page ID in menu
	 * @return  void
	 */
	public function menu($id, $holder, $orders)
	{
		$msg = null;
		if (!is_null($id) && is_numeric($id))
		{
		    // check permission
		    $msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'pages', $id, 3);
		    
		    if (is_null($msg))
		    {
		        // refresh order
		        $menu = new Menu_model();
		        $result = $menu->menu($id, substr($holder, 1), $orders);
		        
		        // set message
		        $this->dict->get_words();
		        $msg = AdmUtils_helper::set_msg($result);
		    }
		}
		$this->response($msg);
	}
}
