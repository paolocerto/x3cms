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
 * Model for Module Items
 *
 * @package X3CMS
 */
class Modules_controller extends X3ui_controller
{
	/**
	 * Constructor
	 * set the default table
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::logged();
	}

	/**
	 * Default
	 */
	public function _default() : void
	{
		$this->index(2, 'public');
	}

	/**
	 * Show modules
	 */
	public function index(int $id_area = 2, string $area = 'public') : void
	{
		// load dictionary
		$this->dict->get_wordarray(array('modules'));

		$amod = new Area_model();
	    list($id_area, $areas) = $amod->get_my_areas($this->site->data->id, $id_area);

		// get page
		$page = $this->get_page('modules');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = '';

        $view->content = new X4View_core('modules/module_list');

		$view->content->id_area = $id_area;
		$view->content->area = $amod->get_by_id($id_area);

		// get installed and installable plugins
		$mod = new X4Plugin_model();
		$view->content->plugged = $mod->get_installed($id_area);

		$view->content->uninstall = AdmUtils_helper::get_ulevel(1, $_SESSION['xuid'], '_module_uninstall');

		$chk = AdmUtils_helper::get_ulevel(1, $_SESSION['xuid'], '_module_install');
		$view->content->pluggable = (!$chk || $chk->level < 4)
		    ? array()
		    : $mod->get_installable($id_area);

		// area switcher
		$view->content->areas = $areas;

		$view->render(true);
	}

	/**
	 * Change status
	 */
	public function set(string $what, int $id_area, int $id, int $value = 0) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($id_area, 'modules', $id, $what);
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
				$msg->update = array(
					'element' => 'page',
					'url' => $_SERVER['HTTP_REFERER']
				);
            }
		}
		$this->response($msg);
	}

	/**
	 * Plugin configuration form
	 */
	public function config(int $id) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('modules', 'form'));

		// get object
		$mod = new X4Plugin_model();
		$item = $mod->get_by_id($id);

		// get params
		$params = $mod->get_param($item->name, $item->id_area);

		// build the form
        $form_fields = new X4Form_core('module/module_config');
		$form_fields->item = $item;
        $form_fields->params = $params;

		// get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'configure');
			if ($e)
			{
				$this->configure($item, $_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        $view = new X4View_core('modal');
        $view->title = _MODULE_CONFIG.': '.$item->name;

		// contents
		$view->content = new X4View_core('editor');

		// form builder
		$view->content->form = X4Form_helper::doform('configure', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'configure\')"');

		$view->render(true);
	}

	/**
	 * Register Plugin configuration
	 */
	private function configure(stdClass $item, array $_post) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level(
            $item->id_area,
            str_replace(array('x3', 'x4'), array('x3_', 'x4_'), $_post['xrif']),
            $_post['id'],
            'configure'
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
            // reset APC
			APC && apcu_delete(SITE.'mod_param'.$item->name.$item->id_area);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// set what update
			if ($result[1])
			{
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'modules/index/'.$_post['id_area'].'/'.$_post['xrif']
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Install a plugin
	 */
	public function install(int $id_area, string $plugin_name) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($id_area, '_module_install', 0, 'create');
		if (is_null($msg))
		{
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
					// add permission
					$mod = new Permission_model();
					$mod->refactory($_SESSION['xuid']);

					// refresh deep, xpos and ordinal for each lang in admin area
					$mod = new Menu_model();
                    $languages = $mod->get_languages(1);
                    foreach($languages as $lang)
                    {
                        $ordinal = $mod->get_ordinal_by_url(1, $lang->code, 'modules');
					    $mod->ordinal(1, $lang->code, 'modules', $ordinal);
                    }

					$msg->update = array(
						'element' => 'page',
						'url' => $_SERVER['HTTP_REFERER']
					);
				}
			}
		}
		$this->response($msg);
	}

	/**
	 * Uninstall a plugin
	 */
	public function uninstall(int $id) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'modules'));

		// get obj
		$mod = new X4Plugin_model();
		$item = $mod->get_by_id($id, 'modules', 'id, id_area, name');

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
			$this->uninstalling($item);
			die;
		}
        $view = new X4View_core('modal');
        $view->title = _UNINSTALL_PLUGIN;
		// contents
		$view->content = new X4View_core('uninstall');
		$view->content->item = $item->name;

		// form builder
		$view->content->form = X4Form_helper::doform('uninstall', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'uninstall\')"');
		$view->render(true);
	}

	/**
	 * Uninstall the plugin
	 */
	private function uninstalling(stdClass $item) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($item->id_area, 'modules', $item->id, 'delete');
		if (is_null($msg))
		{
			// do action
			$mod = new X4Plugin_model();
			$result = $mod->uninstall($item->id);

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
					AdmUtils_helper::delete_priv('modules', $item->id);

                    $msg->update = array(
                        'element' => 'page',
                        'url' => $_SERVER['HTTP_REFERER']
                    );
                }
			}
		}
		$this->response($msg);
	}

	/**
	 * Show Plugin's instructions
	 */
	public function help(string $module, string $lang) : void
	{
		// load dictionary
		$this->dict->get_wordarray(array('modules'));

        $view = new X4View_core('modal');
        $view->title = _MODULE_INSTRUCTIONS.': '.$module;
        $view->wide = ' xl:w-2/3';

		// contents
		$view->content = new X4View_core('editor');
		$view->content->form = '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">
            <pre class="text-sm">'.nl2br(htmlspecialchars(file_get_contents(PATH.'plugins/'.$module.'/instructions_'.$lang.'.txt'))).'</pre>
            </div>';

		$view->render(true);
	}

	/**
	 * Duplicate modules from an area for another language (secret method)
	 * If you need to add another language to an area you can call this script
	 * /admin/modules/duplicate_area_lang/ID_AREA/OLD_LANG/NEW_LANG
	 */
	public function duplicate_area_lang(int $id_area, string $old_lang, string $new_lang) : void
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
