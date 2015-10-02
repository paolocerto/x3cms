<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
 
/**
 * Controller for Template items
 * 
 * @package X3CMS
 */
class Templates_controller extends X3ui_controller
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
	 * Show template list by theme
	 *
	 * @param   integer $id_theme Theme ID
	 * @param   string  $theme_name Theme name
	 * @return  void
	 */
	public function xlist($id_theme, $theme_name)
	{
		// load dictionary
		$this->dict->get_wordarray(array('templates'));
		
		// get page
		$page = $this->get_page('templates/xlist');
		
		// content
		$view = new X4View_core('container');
		
		$view->content = new X4View_core('themes/template_list');
		$view->content->page = $page;
		$view->content->id_theme = $id_theme;
		$view->content->theme = $theme_name;
		
		$mod = new Template_model();
		// installed templates
		$view->content->tpl_in = $mod->get_tpl_installed($id_theme);
		// installable templates
		$view->content->tpl_out = $mod->get_tpl_installable($id_theme, $theme_name);
		$view->render(TRUE);
	}
	
	/**
	 * Templates filter
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
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'templates', $id, $val);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// do action
			$mod = new Template_model();
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
	 * Install a new template (use Ajax)
	 *
	 * @param   integer $id_theme Theme ID
	 * @param   string	$template_name Template name
	 * @return  void
	 */
	public function install($id_theme, $template_name)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'templates', 'sections'));
		
		// content
		$view = new X4View_core('editor');
		$view->title = _INSTALL_TEMPLATE;
		
		// check the template name
		if (strstr(urldecode($template_name), ' ') != '') 
			$view->form = '<h2>'._WARNING.'</h2><p>'._INVALID_TEMPLATE.BR.'<strong>>>&nbsp;'.urldecode($template_name).'&nbsp;<<</strong><br />&nbsp;</p>';
		else 
		{
			// get theme object
			$mod = new Theme_model();
			$theme = $mod->get_by_id($id_theme);
			
			// build the form
			$fields = array();
			$fields[] = array(
				'label' => null,
				'type' => 'hidden', 
				'value' => $id_theme,
				'name' => 'id_theme'
			);
			$fields[] = array(
				'label' => null,
				'type' => 'hidden', 
				'value' => $template_name,
				'name' => 'name',
				'extra' => 'class="large"'
			);
			$fields[] = array(
				'label' => null,
				'type' => 'html', 
				'value' => '<h4>'._TEMPLATE.': '.$template_name.'</h4>',
			);
			$fields[] = array(
				'label' => _DESCRIPTION,
				'type' => 'textarea', 
				'value' => '',
				'name' => 'description',
				'extra' => 'class="large"'
			);
			
			// load available CSS style sheets
			$fields[] = array(
				'label' => _CSS,
				'type' => 'select',
				'value' => 'base',
				'options' => array($this->get_css($theme->name), 'value', 'option'),
				'name' => 'css',
				'extra' => 'class="large"'
			);
			
			$fields[] = array(
				'label' => _SECTIONS,
				'type' => 'text',
				'value' => 1,
				'name' => 'sections',
				'rule' => 'required|numeric',
				'extra' => 'class="aright large"'
			);
			
			// if submitted
			if (X4Route_core::$post)
			{
				$e = X4Validation_helper::form($fields, 'editor');
				if ($e) 
				{
					$this->installing($_POST);
				}
				else 
					$this->notice($fields);
				die;
			}
			
			// form builder
			$view->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
				'onclick="setForm(\'editor\');"');
		}
		$view->render(TRUE);
	}
	
	/**
	 * Perform template install
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function installing($_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_template_install', 0, 4);
		
		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'name' => $_post['name'],
				'css' => $_post['css'],
				'id_theme' => $_post['id_theme'],
				'description' => $_post['description'],
				'sections' => $_post['sections']
			);
			
			$mod = new Template_model();
			$result = $mod->insert($post);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
				
			// add permission on new template
			if ($result[1]) 
			{
				$perm = new Permission_model();
				$array[] = array(
						'action' => 'insert', 
						'id_what' => $result[0], 
						'id_user' => $_SESSION['xuid'], 
						'level' => 4);
				$res = $perm->pexec('templates', $array, 1);
				
				$theme = $mod->get_var($post['id_theme'], 'themes', 'name');
				$msg->update[] = array(
					'element' => 'tdown', 
					'url' => BASE_URL.'templates/xlist/'.$post['id_theme'].'/'.$theme,
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Return an array of CSS files by theme
	 *
	 * @param   string	$theme Theme name
	 * @return  array	Array of objects
	 */
	private function get_css($theme)
	{
		// css file list
		$css = array();
		$files = glob(PATH.'themes/'.$theme.'/css/*');
		foreach ($files as $i) 
		{
			$name = str_replace(array('screen.css', '.css'), '', basename($i));
			$css[] = array('v' => $name, 'o' => $name);
		}
		
		return X4Utils_helper::array2obj($css, 'v', 'o');
	}
	
	/**
	 * Uninstall template form (use Ajax)
	 *
	 * @param   integer $id Template ID
	 * @return  void
	 */
	public function uninstall($id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'templates'));
		
		// get object
		$mod = new Template_model();
		$obj = $mod->get_by_id($id, 'templates', 'name, id_theme');
		
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
			$this->uninstalling($_POST);
			die;
		}
		
		// contents
		$view = new X4View_core('uninstall');
		$view->title = _UNINSTALL_TEMPLATE;
		$view->msg = '';
		$view->item = $obj->name;
		
		// form builder
		$view->form = X4Form_helper::doform('uninstall', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '', 
			'onclick="setForm(\'uninstall\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Uninstalling template
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function uninstalling($_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'templates', $_post['id'], 4);
		
		if (is_null($msg))
		{
			// do action
			$mod = new Template_model();
			$result = $mod->uninstall($_post['id']);
			
			if (is_array($result)) {
				// set error
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_template_not_uninstalled'));
			}
			else {
				// set message
				$msg = AdmUtils_helper::set_msg($result);
				
				if ($result) 
				{
					// clear useless permissions
					$perm = new Permission_model();
					$perm->deleting_by_what('templates', $_post['id']);
					
					$theme = $mod->get_var($_post['id_theme'], 'themes', 'name');
					$msg->update[] = array(
						'element' => 'tdown', 
						'url' => BASE_URL.'templates/xlist/'.$_post['id_theme'].'/'.$theme,
						'title' => null
					);
				}
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Edit template/css form (use Ajax)
	 *
	 * @param   string	$what case (template|css)
	 * @param   string	$theme Theme name
	 * @param   integer $id Template ID
	 * @return  void
	 */
	public function edit($what, $theme, $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'template'));
		
		// get object
		$mod = new Template_model();
		$item = $mod->get_by_id($id, 'templates', 'id_theme, name, css');
		
		// path to file
		$file = ($what == 'template') ? 
			PATH.'themes/'.$theme.'/templates/'.$item->name.'.php' : 
			PATH.'themes/'.$theme.'/css/'.$item->css.'.css';
		
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
			'value' => $item->id_theme,
			'name' => 'id_theme'
		);
		$fields[] = array(
			'label' => _FILE,
			'type' => 'textarea',
			'value' => htmlentities($this->replace(1, file_get_contents($file))),
			'name' => 'code',
			'extra' => 'class="editfile"',
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		
		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e) 
			{
				$this->editing($_POST, $file);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
		
		// contents
		$view = new X4View_core('editor');
		$view->title = _EDIT.' '.$item->name;
		
		// form builder
		$view->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'editor\');"');
		
		$view->js = '
<script>
window.addEvent("domready", function()
{
	$("code").addClass("code");
});
</script>';
		
		$view->render(TRUE);
	}
	
	/**
	 * Clean/restore template file
	 *
	 * @access	private
	 * @param   integer	$in_out Action switcher
	 * @param   string	$str File text
	 * @return  string
	 */
	private function replace($in_out, $str)
	{
		// final version of strings (as they appear into template)
		$final = array (
			'{x4wa_version}',
			'{execution_time}',
			'{memory_usage}',
			'{included_files}',
			'{queries}',
			'<script ',
			'</script>'
		);
		
		// temporary version of strings
		$tmp = array (
			'{x_x4wa_version}',
			'{x_execution_time}',
			'{x_memory_usage}',
			'{x_included_files}',
			'{x_queries}',
			'<!--script ',
			'</script-->'
		);
		
		return ($in_out) 
			? str_replace($final, $tmp, $str) 
			: str_replace($tmp, $final, $str);
	}
	
	/**
	 * Register edited file
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @param   string	$file File path
	 * @return  void
	 */
	private function editing($_post, $file)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'templates', $_post['id'], 2);
		
		if (is_null($msg))
		{
			// get file permission
			$fileperm = substr(sprintf('%o', fileperms($file)), -3);
			if ($fileperm != 777) 
			{
				// set file permission
				chmod($file, 0777);
			}
			
			// update file content
			$check = file_put_contents($file, $this->replace(0, stripslashes($_post['code'])));
			chmod($file, 0755);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// set what update
			if ($result[1])
			{
				$theme = $mod->get_var($_post['id_theme'], 'themes', 'name');
				$msg->update[] = array(
					'element' => 'tdown', 
					'url' => BASE_URL.'templates/xlist/'.$_post['id_theme'].'/'.$theme,
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
}
