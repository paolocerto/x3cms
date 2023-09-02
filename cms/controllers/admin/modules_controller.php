<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
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
	public function index(int $id_area = 2, string $area = 'public')
	{
		// load dictionary
		$this->dict->get_wordarray(array('modules'));

		$amod = new Area_model();
	    list($id_area, $areas) = $amod->get_my_areas($id_area);

	    $view = new X4View_core('modules/module_list');

		// get page
		$page = $this->get_page('modules');
		$navbar = array($this->site->get_bredcrumb($page));
		$view->navbar = $navbar;
		$view->page = $page;

		$view->id_area = $id_area;
		$view->area = $amod->get_by_id($id_area);

		// get installed and installable plugins
		$mod = new X4Plugin_model();
		$view->plugged = $mod->get_installed($id_area);

		$view->uninstall = AdmUtils_helper::get_ulevel(1, $_SESSION['xuid'], '_module_uninstall');

		$chk = AdmUtils_helper::get_ulevel(1, $_SESSION['xuid'], '_module_install');
		$view->pluggable = (!$chk || $chk->level < 4)
		    ? array()
		    : $mod->get_installable($id_area);

		// area switcher
		$view->areas = $areas;

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
	public function set(string $what, int $id, int $value = 0)
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
            {
				$msg->update[] = array(
					'element' => $qs['div'],
					'url' => urldecode($qs['url']),
					'title' => null
				);
            }
		}
		$this->response($msg);
	}

	/**
	 * Plugin configuration form (use Ajax)
	 *
	 * @param   integer  $id Module ID
	 * @return  void
	 */
	public function config(int $id)
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
		foreach ($params as $i)
		{
		    $tmp = array(
		        'label' => ucfirst(str_replace('_', ' ', $i->name))._TRAIT_.$i->description,
		        'value' => $i->xvalue,
		        'name' => $i->name,
		        'rule' => ''
		    );

			switch($i->xtype) {
			    case 'HIDDEN':
			        // do nothing
			        break;
				case '0|1':
				case 'BOOLEAN':
				    // boolean
				    $tmp['type'] = 'checkbox';
				    $tmp['suggestion'] = _ON.'/'._OFF;
				    if ($i->xvalue == '1') $tmp['checked'] = 1;
				    break;
				case 'IMG':
					// TODO: manage image set as param
					break;
				case 'DECIMAL':
				    // decimal
				    $tmp['type'] = 'text';
				    $tmp['suggestion'] = $i->xtype;
				    $tmp['extra'] = 'class="aright large"';
				    $tmp['rule'] = 'numeric|max§1';
				    break;
				case 'INTEGER':
					// integer
                    $tmp['type'] = 'text';
                    $tmp['suggestion'] = $i->xtype;
                    $tmp['extra'] = 'class="aright large"';
                    $tmp['rule'] = 'numeric';
					break;
				case 'EMAIL':
					// email
                    $tmp['type'] = 'text';
                    $tmp['suggestion'] = $i->xtype;
                    $tmp['extra'] = 'class="large"';
                    $tmp['rule'] = 'mail';
					break;
				case 'BLOB':
					// long string
                    $tmp['type'] = 'textarea';
                    $tmp['suggestion'] = $i->xtype;
                    $tmp['extra'] = 'class="large"';
					break;
				default:
					// string
					$tmp['type'] = 'text';
                    $tmp['suggestion'] = $i->xtype;
                    $tmp['extra'] = 'class="large"';
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
	 * @param   stdClass 	$plugin plugin
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function configure(stdClass $plugin, array $_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level(
            $_SESSION['xuid'],
            str_replace(array('x3', 'x4'), array('x3_', 'x4_'), $_post['xrif']),
            $_post['id'], 3
        );	// 'modules'

		if (is_null($msg))
		{
			// get parama
			$mod = new X4Plugin_model();
			$params = $mod->get_param($_post['xrif'], $_post['id_area']);

			// build queries
			$sql = array();
			foreach ($params as $i)
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
			APC && apcu_delete(SITE.'mod_param'.$plugin->name.$plugin->id_area);

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
	public function install(int $id_area, string $plugin_name)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_module_install', 0, 4);
		if (is_null($msg))
		{
			//$qs = X4Route_core::get_query_string();

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
				foreach ($result as $i)
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
	public function uninstall(int $id)
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
	 * @param   stdClass 	$obj Plugin Objject
	 * @return  void
	 */
	private function uninstalling(stdClass $obj)
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
	public function help(string $module, string $lang)
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
	public function duplicate_area_lang(int $id_area, string $old_lang, string $new_lang)
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
