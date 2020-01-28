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
 * Controller for Theme items
 * 
 * @package X3CMS
 */
class Themes_controller extends X3ui_controller
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
	 * Show themes
	 *
	 * @return  void
	 */
	public function _default()
	{
		// load dictionary
		$this->dict->get_wordarray(array('themes', 'msg'));
		
		// get page
		$page = $this->get_page('themes');
		
		// content
		$view = new X4View_core('container');
		
		$view->content = new X4View_core('themes/theme_list');
		$view->content->page = $page;
		
		$theme = new Theme_model();
		// installed themes
		$view->content->theme_in= $theme->get_installed();
		// installable themes
		$view->content->theme_out = $theme->get_installable();
		
		$view->render(TRUE);
	}
	
	/**
	 * Themes filter
	 *
	 * @return  void
	 */
	public function filter()
	{
		echo '';
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
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'themes', $id, $val);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// do action
			$mod = new Theme_model();
			$result = $mod->update($id, array($what => $value));
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
	 * Install a theme
	 *
	 * @param   string	$theme_name Theme name
	 * @return  void
	 */
	public function install($theme_name)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_theme_install', 0, 4);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// perform the install
			$theme = new Theme_model();
			$result = $theme->install($theme_name);
			
			// if result is an array an error occurred
			if (is_array($result)) 
			{
				$this->notice(false, '_theme_not_installed');
				die;
			}
				//X4Utils_helper::set_error($result, '_theme_not_installed');
			else 
			{
				// installed
				// set message
				$this->dict->get_words();
				$msg = AdmUtils_helper::set_msg(true);
				// add permission on new theme
				if ($result) 
				{
					$perm = new Permission_model();
					$array[] = array(
							'action' => 'insert', 
							'id_what' => $result, 
							'id_user' => $_SESSION['xuid'], 
							'level' => 4);
					$result = $perm->pexec('themes', $array, 1);
					// refactory permissions
					$perm->refactory_table($_SESSION['xuid'], array(1), 'themes');
					$perm->refactory_table($_SESSION['xuid'], array(1), 'templates');
					$perm->refactory_table($_SESSION['xuid'], array(1), 'menus');
				}
				
				$msg->update[] = array(
					'element' => 'tdown', 
					'url' => BASE_URL.'themes',
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Uninstall a theme (use Ajax)
	 *
	 * @param   integer	$id Theme ID
	 * @return  void
	 */
	public function uninstall($id)
	{
		// load dictionary
		$this->dict->get_wordarray(array('form', 'themes'));
		
		// get object
		$theme = new Theme_model();
		$obj = $theme->get_by_id($id, 'themes', 'name');
		
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
			'value' => $obj->name,
			'name' => 'name'
		);
		
		// if submitted
		if (X4Route_core::$post)
		{
			$this->uninstalling($_POST);
			die;
		}
		
		// contents
		$view = new X4View_core('uninstall');
		$view->title = _UNINSTALL_THEME;
		$view->msg = '';
		$view->item = $obj->name;
		
		// form builder
		$view->form = X4Form_helper::doform('uninstall', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '', 
			'onclick="setForm(\'uninstall\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Perform the uninstall
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function uninstalling($_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'themes', $_post['id'], 4);
		
		if (is_null($msg))
		{
			// do action
			$mod = new Theme_model();
			$result = $mod->uninstall($_post['id'], $_post['name']);
			
			// check the result
			if (is_array($result)) 
			{
				$this->notice(false, '_theme_not_uninstalled');
				die;
				//X4Utils_helper::set_error($result, '_theme_not_uninstalled');
			}
			else 
			{
				// uninstalled
				
				// set message
				$msg = AdmUtils_helper::set_msg(true);
				
				// clear useless permissions
				$perm = new Permission_model();
				if ($result) 
					$perm->deleting_by_what('themes', $_post['id']);
				
				$msg->update[] = array(
					'element' => 'tdown', 
					'url' => BASE_URL.'themes',
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Minimize css files
	 *
	 * @return void
	 */
	public function minimize($id_theme, $name)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'themes', $id_theme, 4);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// do action
			$res = 1;
			
			// get the templates in the theme
			$mod = new Theme_model();
			
			// CSS section
			$path = PATH.'themes/'.$name.'/css/';
			$items = $mod->get_css($id_theme);
			foreach($items as $i)
			{
				if (file_exists($path.$i->css.'.css'))
				{
					$txt = file_get_contents($path.$i->css.'.css');
					$txt = $mod->compress_css($txt);
					$chk = file_put_contents($path.$i->css.'.min.css', $txt);
					
					if (!$chk)
					{
						$res = 0;
					}
				}
			}
			
			// JS section
			X4Core_core::auto_load('jshrink_library');
			
			$path = PATH.'themes/'.$name.'/js/';
			$items = $mod->get_js($id_theme);
			foreach($items as $i)
			{
				if (file_exists($path.$i->js.'.js'))
				{
					$txt = file_get_contents($path.$i->js.'.js');
					$txt = Minifier::minify($txt, array('flaggedComments' => false));
					$chk = file_put_contents($path.$i->js.'.min.js', $txt);
					
					if (!$chk)
					{
						$res = 0;
					}
				}
			}
			
			$result= array(0, $res);
		
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
}
