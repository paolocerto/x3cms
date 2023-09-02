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
 * Controller for Site
 *
 * @package X3CMS
 */
class Sites_controller extends X3ui_controller
{
	protected $cases = array(
		'sites' => array('sites', 'btm'),
		'by_page' => array('languages', 'btm'),
		'context_order' => array('themes', 'btm'),
		'category_order' => array('users', 'btm')
	);

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
	 * Show site status
	 *
	 * @return  void
	 */
	public function _default()
	{
		$this->show();
	}

	/**
	 * Show site
	 *
	 * @param   integer  $tab Enable disable tabber view
	 * @return  void
	 */
	public function show(int $tab = 0)
	{
		// load dictionary
		$this->dict->get_wordarray(array('sites'));

		// get page
		$page = $this->get_page('sites');
		$navbar = array($this->site->get_bredcrumb($page));

        $mod = new Site_model();

		if ($tab)
		{
			$view = new X4View_core('tabber');
            $view->tabber_name = 'tabber';
			$view->title = _SITE_MANAGER;
			$menu = $this->site->get_menus($page->id_area);

			$view->tabs = $menu['sidebar'];
			$view->tkeys = array('name', 'url', 'url', $page->url);
			$view->tabber_container = 'tdown';

			$view->down = new X4View_core('sites/sites');
            $view->down->items = $mod->get_items($_SESSION['xuid']);
			$view->down->navbar = $navbar;
			$view->down->page = $page;
		}
		else
		{
			$view = new X4View_core('sites/sites');
            $view->items = $mod->get_items($_SESSION['xuid']);
			$view->navbar = $navbar;
			$view->page = $page;
		}
		$view->render(TRUE);
	}

	/**
	 * Sites filter
	 *
	 * @return  void
	 */
	public function filter()
	{
		if ($_SESSION['xuid'] == 1)
		{
		    echo '<a class="btf" href="'.BASE_URL.'sites/edit" title="Add a new site"><i class="fas fa-plus fa-lg"></i></a>
<script>
window.addEvent("domready", function()
{
    buttonize("filters", "btf", "modal");
});
</script>';
		}
		else
		{
			echo '';
		}
	}

	/**
	 * Change site status
	 *
	 * @param   integer  $id Site ID
	 * @param   integer  $value value to set (0 = off, 1 = on)
	 * @return  void
	 */
	public function offline(int $id, int $value = 0)
	{
	    $this->dict->get_words();

		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'sites', $id, 4);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();

			// do action
			$result = $this->site->update($id, array('xon' => $value));
			// clear cache
			APC && apcu_clear_cache();

			// set message
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
	 * Site config form (use Ajax)
	 *
	 * @param   integer  $id Site ID
	 * @return  void
	 */
	public function config(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('sites', 'form'));

		$mod = new Site_model();

		// get params
		$params = $mod->get_params($id);

		if (empty($params))
		{
		    $params = $mod->init_params($id);
		}

		$site = $mod->get_by_id($id);;

		// build the form
		$form_fields = new X4Form_core('site_edit', '', array('fields' => array()));
		$form_fields->id = $id;
		$form_fields->site = $site;
        	$form_fields->params = $params;

		// get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'configure');
			if ($e)
			{
				$this->configure($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

		// contents
		$view = new X4View_core('editor');
		$view->title = _SITE_CONFIG.': '.$site->domain;

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
	 * Register the site configuration
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function configure(array $_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'sites', $_post['id'], 3);

		if (is_null($msg))
		{
            		$mod = new Site_model();

			// get parameters before update
			$params = $mod->get_params($_post['id']);

			// build update array
			$sql = array();
			foreach ($params as $i)
			{
				// handle _post
				switch($i->xtype)
				{
					case '0|1':
						$val = intval(isset($_post[$i->name]));
						break;
					case 'IMG':
						$val = $_post[$i->name];
						break;
					default:
						$val = $_post[$i->name];
						break;
				}

				// if the new value is different then update
				if ($val != $i->xvalue)
                {
					$sql[$i->id] = $val;
                }
			}

			// do update
			$plugin = new X4Plugin_model();
			$result = $plugin->update_param($sql);
			APC && apcu_delete(SITE.'param'.$_post['id']);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// set what update
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => 'topic',
					'url' => BASE_URL.'sites/show/1',
					'title' => null
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Edit site form (use Ajax)
	 *
	 * @param   integer  $id Site ID
	 * @return  void
	 */
	public function edit($id = 0)
	{
		// load dictionary
		$this->dict->get_wordarray(array('form', 'sites'));

		// get object
		$site = ($id)
		    ? $this->site->get_by_id($id)
		    : new Obj_site();

		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $id,
			'name' => 'id'
		);
		$fields[] = array(
			'label' => _X3CMS.' '._VERSION,
			'type' => 'text',
			'value' => $site->version,
			'name' => 'version',
			'extra' => 'class="large" disabled="disabled"'
		);
		$fields[] = array(
			'label' => _KEYCODE,
			'type' => 'text',
			'value' => $site->xcode,
			'name' => 'xcode',
			'extra' => 'class="large"'
		);
		$fields[] = array(
			'label' => _DOMAIN,
			'type' => 'text',
			'value' => $site->domain,
			'name' => 'domain',
			'rule' => 'required|url',
			'extra' => 'class="large"'
		);

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
            {
				$this->editing($_POST);
            }
			else
            {
				$this->notice($fields);
            }
			die;
		}

		// contents
		$view = new X4View_core('editor');
		$view->title = _EDIT_SITE;

		// form builder
		$view->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
			'onclick="setForm(\'editor\');"');
		$view->render(TRUE);
	}

	/**
	 * Register site data
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing(array $_post)
	{
		$msg = null;
		// check permission

		$msg = $_post['id']
		    ? AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'sites', $_post['id'], 4)
		    : null;

		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'xcode' => X4Utils_helper::slugify($_post['xcode']),
				'domain' => $_post['domain']
			);

            $result = ($_post['id'])
                ? $this->site->update($_post['id'], $post)
                : $this->site->insert($post);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// set what update
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => 'topic',
					'url' => BASE_URL.'sites/show/1',
					'title' => null
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Clear cache
	 *
	 * @return  void
	 */
	public function clear_cache()
	{
		$files = glob(APATH.'files/tmp/*');
		foreach ($files as $i)
		{
			unlink($i);
		}

		// set message
		$this->dict->get_words();
		$msg = AdmUtils_helper::set_msg(true);
		$msg->update[] = array(
			'element' => 'topic',
			'url' => BASE_URL.'sites/show/1',
			'title' => null
		);
		$this->response($msg);
	}

	/**
	 * Clear APC cache
	 *
	 * @return  void
	 */
	public function clear_apc()
	{
		// clear cache
		APC && apcu_clear_cache();

		// set message
		$this->dict->get_words();
		$msg = AdmUtils_helper::set_msg(true);
		$msg->update[] = array(
			'element' => 'topic',
			'url' => BASE_URL.'sites/show/1',
			'title' => null
		);
		$this->response($msg);
	}
}
