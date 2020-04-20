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
 * Model for Module Items
 *
 * @package X3CMS
 */
class Modules_controller extends X3ui_controller 
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::logged();
	}
	
	/**
	 * Show modules
	 *
	 * @return  void
	 */
	public function _default()
	{
		$this->index(2, 'public');
	}
	
	/**
	 * Show modules
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $area Area name
	 * @return  void
	 */
	public function index($id_area = 2, $area = 'public')
	{
		// load dictionary
		$this->dict->get_wordarray(array('modules'));
		
		$area = new Area_model();
	    list($id_area, $areas) = $area->get_my_areas($id_area);
	    
	    $view = new X4View_core('container');
		
		$view->content = new X4View_core('modules/module_list');
		
		// get page
		$page = $this->get_page('modules');
		$navbar = array($this->site->get_bredcrumb($page));
		$view->content->navbar = $navbar;
		$view->content->page = $page;
		
		$view->content->id_area = $id_area;
		$view->content->area = $area->get_by_id($id_area);
		
		// get installed and installable plugins
		$mod = new X4Plugin_model();
		$view->content->plugged = $mod->get_installed($id_area);
		
		$chk = AdmUtils_helper::get_ulevel(1, $_SESSION['xuid'], '_module_install');
		$view->content->pluggable = (!$chk || $chk->level < 4)
		    ? array()
		    : $mod->get_installable($id_area);
		
		// area switcher
		$view->content->areas = $areas;
		
		$view->render(TRUE);
	}
	
	/**
	 * Modules filter
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
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'modules', $id, $val);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// do action
			$plugin = new X4Plugin_model();
			$result = $plugin->update($id, array($what => $value));
			
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
	 * Plugin configuration form (use Ajax)
	 *
	 * @param   integer  $id Module ID
	 * @return  void
	 */
	public function config($id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('modules', 'form'));
		
		// get object
		$plug = new X4Plugin_model();
		$plugin = $plug->get_by_id($id);
		
		// get params
		$params = $plug->get_param($plugin->name, $plugin->id_area);
		
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
			'value' => $plugin->name,
			'name' => 'xrif'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $plugin->id_area,
			'name' =>'id_area'
		);
		
		// Build specific fields
		foreach($params as $i)
		{
			
			switch($i->xtype) {
			    case 'HIDDEN':
			        // do nothing
			        break;
				case '0|1':
				case 'BOOLEAN':
					// boolean
					$tmp = array(
						'label' => ucfirst(str_replace('_', ' ', $i->name))._TRAIT_.$i->description,
						'type' => 'checkbox',
						'value' => $i->xvalue,
						'name' => $i->name,
						'rule' => '',
						'suggestion' => _ON.'/'._OFF
					);
					if ($i->xvalue == '1') $tmp['checked'] = 1;
					break;
				case 'IMG':
					// TODO: manage image set as param
					break;
				case 'INTEGER':
					// integer
					$tmp = array(
						'label' => ucfirst(str_replace('_', ' ', $i->name))._TRAIT_.$i->description,
						'type' => 'text', 
						'value' => $i->xvalue,
						'name' => $i->name,
						'suggestion' => $i->xtype,
						'extra' => 'class="aright large"',
						'rule' => 'numeric'
					);
					break;
				case 'EMAIL':
					// email
					$tmp = array(
						'label' => ucfirst(str_replace('_', ' ', $i->name))._TRAIT_.$i->description,
						'type' => 'text', 
						'value' => $i->xvalue,
						'name' => $i->name,
						'suggestion' => $i->xtype,
						'rule' => 'mail',
						'extra' => 'class="large"'
					);
					break;
				case 'BLOB':
					// long string
					$tmp = array(
						'label' => ucfirst(str_replace('_', ' ', $i->name))._TRAIT_.$i->description,
						'type' => 'textarea', 
						'value' => $i->xvalue,
						'name' => $i->name,
						'rule' => '',
						'suggestion' => $i->xtype,
						'extra' => 'class="large"'
					);
					break;
				default:
					// string
					$tmp = array(
						'label' => ucfirst(str_replace('_', ' ', $i->name))._TRAIT_.$i->description,
						'type' => 'text', 
						'value' => $i->xvalue,
						'name' => $i->name,
						'rule' => '',
						'suggestion' => $i->xtype,
						'extra' => 'class="large"'
					);
					break;
			}
			
			if ($i->required == '1') 
			{
				$tmp['rule'] = 'required|'.$tmp['rule'];
			}
			
			if ($i->xtype != 'HIDDEN')
			{
			    $fields[] = $tmp;
			}
		}
		
		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'configure');
			if ($e) 
			{
				$this->configure($plugin, $_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
		
		// contents
		$view = new X4View_core('editor');
		$view->title = _MODULE_CONFIG.': '.$plugin->name;
		
		// form builder
		$view->form = '<div id="scrolled">'.X4Form_helper::doform('configure', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'configure\');"').'</div>';
		
		$view->js = '
<script>
window.addEvent("domready", function()
{
var myScroll = new Scrollable($("scrolled"));
});
</script>';		
		
		$view->render(TRUE);
	}
	
	/**
	 * Register Plugin configuration
	 *
	 * @access	private
	 * @param   object 	$plugin plugin
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function configure($plugin, $_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'modules', $_post['id'], 3);
		
		if (is_null($msg))
		{
			// get parama
			$mod = new X4Plugin_model();
			$params = $mod->get_param($_post['xrif'], $_post['id_area']);
			
			// build queries
			$sql = array();
			foreach($params as $i) 
			{
				// handle type
				switch($i->xtype) 
				{
				    case 'HIDDEN':
				        // do nothing
				        break;
					case '0|1':
					case 'BOOLEAN':
						$val = intval(isset($_post[$i->name]));
						break;
					case 'IMG':
						// when, a day, X3 CMS will handle IMG parameters
						$val = $_post[$i->name];
						break;
					default:
						$val = $_post[$i->name];
						break;
				}
				
				// if new value is different from old value
				if ($val != $i->xvalue && $i->xtype != 'HIDDEN') 
				{
					$sql[$i->id] = $val;
				}
			}
			
			// update params
			$result = $mod->update_param($sql);
			APC && apc_delete(SITE.'mod_param'.$plugin->name.$plugin->id_area);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// set what update
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => 'topic', 
					'url' => BASE_URL.'modules/index/'.$_post['id_area'].'/'.$_post['xrif'],
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Install a plugin
	 *
	 * @param integer	$id_area Area ID
	 * @param string	$plugin_name Plugin name
	 * @return  void
	 */
	public function install($id_area, $plugin_name)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_module_install', 0, 4);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// load global dictionary
			$this->dict->get_words();
			
			// install the plugin
			$mod = new X4Plugin_model();
			$result = $mod->install($id_area, $plugin_name);
			
			// the result is an array only if an error occurred
			if (is_array($result) && !empty($result)) 
			{
				// build msg
				$str = array();
				foreach($result as $i)
				{
					$str[] = $i['label']._TRAIT_.$this->dict->get_word(strtoupper($i['error'][0]), 'msg');
				}
				$msg = AdmUtils_helper::set_msg(false, '', implode('<br />', $str));
			}
			else 
			{
				// set message
				$msg = AdmUtils_helper::set_msg(true);
				// installed
				if ($result) 
				{
					$area = $mod->get_by_id($id_area, 'areas', 'name');
					// add permission
					$mod = new Permission_model();
					$mod->refactory($_SESSION['xuid']);
					
					// refresh deep, xpos and ordinal
					$mod = new Menu_model();
					$mod->ordinal(1, X4Route_core::$lang, 'modules', 'A0021005');
					
					$msg->update[] = array(
						'element' => 'topic', 
						'url' => BASE_URL.'modules/index/'.$id_area.'/'.$area->name,
						'title' => null
					);
				}
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Uninstall a plugin (use Ajax)
	 *
	 * @param integer	$id Pungin ID
	 * @return  void
	 */
	public function uninstall($id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'modules'));
		
		// get obj
		$mod = new X4Plugin_model();
		$obj = $mod->get_by_id($id, 'modules', 'id, id_area, name');
		
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
			'value' => $obj->id_area,
			'name' => 'id_area'
		);
		
		// if submitted
		if (X4Route_core::$post)
		{
			$this->uninstalling($obj);
			die;
		}
		
		// contents
		$view = new X4View_core('uninstall');
		$view->title = _UNINSTALL_PLUGIN;
		$view->msg = '';
		$view->item = $obj->name;
		
		// form builder
		$view->form = X4Form_helper::doform('uninstall', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '', 
			'onclick="setForm(\'uninstall\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Uninstall the plugin
	 *
	 * @access	private
	 * @param   object 	$obj Plugin Objject
	 * @return  void
	 */
	private function uninstalling($obj)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'modules', $obj->id, 4);
		
		if (is_null($msg))
		{
			// do action
			$mod = new X4Plugin_model();
			$result = $mod->uninstall($obj->id);
			
			// check uninstalling
			if (is_array($result)) 
			{
				$this->notice(false, '_plugin_not_uninstalled');
				die;
			}
				//X4Utils_helper::set_error($result, '_plugin_not_uninstalled');
			else 
			{
				// set message
				$msg = AdmUtils_helper::set_msg(true);
				
				// uninstalled
				if ($result) 
				{
					// clear useless permissions
					$perm = new Permission_model();
					$perm->deleting_by_what('modules', $obj->id);
				}
				
				$area = $mod->get_by_id($obj->id_area, 'areas', 'name');
				$msg->update[] = array(
					'element' => 'topic', 
					'url' => BASE_URL.'modules/index/'.$obj->id_area.'/'.$area->name,
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Show Plugin's instructions
	 *
	 * @param   string 	$module Plugin name
	 * @param   string 	$lang Language code
	 * @return  void
	 */
	public function help($module, $lang)
	{
		// load dictionary
		$this->dict->get_wordarray(array('modules'));
		
		// contents
		$view = new X4View_core('editor');
		$view->title = _MODULE_INSTRUCTIONS.': '.$module;
		$view->form = '<div id="scrolled">
						<pre>'.nl2br(htmlspecialchars(file_get_contents(PATH.'plugins/'.$module.'/instructions_'.$lang.'.txt'))).'</pre>
					</div>'.BR.BR;
					
		$view->js = '
<script>
window.addEvent("domready", function()
{
	var myScroll = new Scrollable($("scrolled"));
});
</script>';

		$view->render(TRUE);
	}
	
	/**
	 * Duplicate an area for another language (secret method)
	 * If you need to add another language to an area you can call this script
	 * /admin/modules/duplicate_area_lang/ID_AREA/OLD_LANG/NEW_LANG
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $old_lang Old language to copy
	 * @param   string  $new_lang New language to set
	 * @return  string
	 */
	public function duplicate_area_lang($id_area, $old_lang, $new_lang)
	{
		// Comment the next row to enable the method
		die('Operation disabled!');
		
		$mod = new X4Plugin_model();
		
		// duplicate
		$res = $mod->duplicate_modules_lang($id_area, $old_lang, $new_lang);
			
        if ($res)
        {
            echo '<h1>CONGRATULATIONS!</h1>';
            echo '<p>The changes on the database are applied.</p>';
        }
        else
        {
            echo '<h1>WARNING!</h1>';
            echo '<p>Something went wrong, changes are not applied.</p>';
        }
		die;
	}
}
