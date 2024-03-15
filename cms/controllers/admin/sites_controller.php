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
 * Controller for Site
 *
 * @package X3CMS
 */
class Sites_controller extends X3ui_controller
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
	 * Show site status
	 */
	public function _default() : void
	{
		$this->settings();
	}

	/**
	 * Show settings
	 */
	public function settings() : void
	{
		// load dictionary
		$this->dict->get_wordarray(array('sites'));

		// get page
		$page = $this->get_page('sites');

		$view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = AdmUtils_helper::link(
            'memo',
            'sites:'.$page->lang,
            [],
            _MEMO
        );

        $view->content = new X4View_core('sites/settings');
        $view->content->items = $this->site->get_subpages($page->id_area, 'sites');

		$view->render(true);
	}

	/**
	 * Sites actions
	 */
	private function actions() : string
	{
		return ($_SESSION['level'] == 5)
            ? '<a class="link" @click="popup(\''.BASE_URL.'sites/edit\')" title="'._ADD_SITE.'"><i class="fa-solid fa-lg fa-circle-plus"></i></a>'
            : '';
	}

    /**
	 * Show sites
	 */
	public function index() : void
	{
		// load dictionary
		$this->dict->get_wordarray(array('sites'));

		// get page
		$page = $this->get_page('sites/index');

		$view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = AdmUtils_helper::link(
                'memo',
                'sites:'.$page->lang,
                [],
                _MEMO
            ).$this->actions();

        $view->content = new X4View_core('sites/sites');

        $mod = new Site_model();
        $view->content->items = $mod->get_items($_SESSION['xuid']);

		$view->render(true);
	}

    /**
	 * Change param status
	 */
	public function set(string $what, int  $value = 0) : void
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level(1, 'sites', $this->site->data->id, 'xlock');
		if (is_null($msg))
		{
			// do action
			$plugin = new X4Plugin_model();
			$result = $plugin->update_param_by_name(1, 'site', $what, $value);

			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);

			// set update
			if ($result[1])
			{
				$msg->update = array(
					'element' => 'redirect',
					'url' => $_SERVER['HTTP_REFERER']
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Change site status
	 */
	public function offline(int $id, int $value = 0) : void
	{
	    $this->dict->get_words();

		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level(1, 'sites', $id, 'xlock');
		if (is_null($msg))
		{
			// do action
			$result = $this->site->update($id, array('xon' => $value));
			// clear cache
			APC && apcu_clear_cache();

			// set message
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
	 * Site config form
	 */
	public function config(int $id) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('sites', 'form'));

		$mod = new Site_model();

		// get params
		$params = $mod->get_params($id);
        // not initialized?
		if (empty($params))
		{
		    $params = $mod->init_params($id);
		}
		$site = $mod->get_by_id($id);

		// build the form
		$form_fields = new X4Form_core('site/site_config');
		$form_fields->id = $id;
		$form_fields->site = $site;
        $form_fields->params = $params;

		// get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
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

        $view = new X4View_core('modal');
		$view->title = _SITE_CONFIG.': '.$site->domain;

        // contents
        $view->content = new X4View_core('editor');
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

		$view->render(true);
	}

	/**
	 * Register the site configuration
	 */
	private function configure(array $_post) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level(1, 'sites', $_post['id'], 'edit');
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
				$msg->update = array(
					'element' => 'whole',
					'url' => BASE_URL.'sites/index'
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Edit site form
	 */
	public function edit($id = 0) : void
	{
		// load dictionary
		$this->dict->get_wordarray(array('form', 'sites'));

        $mod = new Site_model();

		// get object
		$site = ($id)
		    ? $mod->get_by_id($id)
		    : new Obj_site();

        $form_fields = new X4Form_core('site/site_edit');
        $form_fields->id = $id;
        $form_fields->site = $site;

        // get the fields array
		$fields = $form_fields->render();

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

		$view = new X4View_core('modal');
		$view->title = _EDIT_SITE;

        // contents
        $view->content = new X4View_core('editor');
        // can user edit?
        $submit = AdmUtils_helper::submit_btn(1, 'sites', $id, 1);
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, $submit, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');
		$view->render(true);
	}

	/**
	 * Register site data
	 */
	private function editing(array $_post) : void
	{
		$msg = null;
		// check permission
		$msg = $_post['id']
		    ? AdmUtils_helper::chk_priv_level(1, 'sites', $_post['id'], 'edit')
		    : AdmUtils_helper::chk_priv_level(1, '_site_creation', $_post['id'], 'create');

		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'xcode' => X4Utils_helper::slugify($_post['xcode']),
				'domain' => $_post['domain'],
                'xdatabase' => $_post['xdatabase']
			);

            $result = ($_post['id'])
                ? $this->site->update($_post['id'], $post)
                : $this->site->insert($post);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// set what update
			if ($result[1])
			{
				$msg->update = array(
					'element' => 'whole',
					'url' => BASE_URL.'sites/index'
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Clear cache
	 */
	public function clear_cache() : void
	{
		$files = glob(APATH.'files/tmp/*');
		foreach ($files as $i)
		{
			unlink($i);
		}

		// set message
		$this->dict->get_words();
		$msg = AdmUtils_helper::set_msg(true);
		$msg->update = array(
			'element' => 'page',
			'url' => $_SERVER['HTTP_REFERER']
		);
		$this->response($msg);
	}

	/**
	 * Clear APC cache
	 */
	public function clear_apc() : void
	{
		// clear cache
		APC && apcu_clear_cache();

		// set message
		$this->dict->get_words();
		$msg = AdmUtils_helper::set_msg(true);
		$msg->update = array(
			'element' => 'page',
			'url' => $_SERVER['HTTP_REFERER']
		);
		$this->response($msg);
	}
}
