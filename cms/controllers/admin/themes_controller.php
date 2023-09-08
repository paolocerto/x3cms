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
		$view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
        $view->actions = '';

        $view->content = new X4View_core('themes/theme_list');
        $view->content->page = $page;

		$mod = new Theme_model();
		// installed themes
		$view->content->theme_in= $mod->get_installed();
		// installable themes
		$view->content->theme_out = $mod->get_installable();

		$view->render(TRUE);
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
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'themes', $id, $val);
		if (is_null($msg))
		{
			// do action
			$mod = new Theme_model();
			$result = $mod->update($id, array($what => $value));
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
	 * Install a theme
	 *
	 * @param   string	$theme_name Theme name
	 * @return  void
	 */
	public function install(string $theme_name)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_theme_install', 0, 4);
		if (is_null($msg))
		{
			// perform the install
			$theme = new Theme_model();
			$result = $theme->install($theme_name);

			// if result is an array an error occurred
			if (is_array($result))
			{
				$this->notice(false, '_theme_not_installed');
				die;
			}
			else
			{
				// installed
				// set message
				$this->dict->get_words();
				$msg = AdmUtils_helper::set_msg(true);

				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'themes'
				);
			}
		}
		$this->response($msg);
	}

    /**
	 * New / Edit theme styles
	 *
	 * @param   integer	$id theme
	 * @return  void
	 */
	public function edit(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'themes', 'sections', 'articles'));

		// get object
		$mod = new Theme_model();
        $item = $mod->get_by_id($id);

		// build the form
		$form_fields = new X4Form_core('theme/theme_edit');
		$form_fields->id = $id;
		$form_fields->item = $item;
        $form_fields->tr = $this->decompose($item->styles, 'js_fields', 1);

        $form_fields->js_fields = $this->js_fields;

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
        $view->title = _THEME_EDIT.' - '.$item->name;
        $view->wide = 'md:w-2/3 lg:w-2/3';
		// content
		$view->content = new X4View_core('editor');
        $view->content->msg = _THEME_EDIT_MSG;
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', 'enctype="multipart/form-data"',
            '@click="submitForm(\'editor\')"');
        $view->render(TRUE);
	}

    // fields for rule configurator
    public $js_fields = [
        ['name' => 'what', 'rule' => 'required', 'type' => 'text'],
        ['name' => 'style', 'rule' => 'required', 'type' => 'text'],
        ['name' => 'description', 'rule' => 'required', 'type' => 'text']
    ];

    /**
	 * Return recorded selected options
	 *
	 * @param   string 	$str Encoded options
     * @param   string  $fields name of the array with configuration
	 * @param   boolean	$move With or without direction buttons
	 * @param   boolean	$echo Return or echo
	 * @return  string
	 */
	public function decompose(string $str = '', string $fields = '', int $move = 0, int $echo = 0, $data = '')
	{
        // load dictionaries
		$this->dict->get_words();

        $res = AdmUtils_helper::decompose($str, $this->$fields, $move, $echo);

		if ($echo)
		{
		    // AJAX call
		    echo $res;
		}
		else
		{
		    return $res;
		}
    }

    /**
	 * Register Edit
	 *
	 * @access	private
	 * @param   integer $id item ID (if 0 then is a new item)
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing($id, $_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'themes', $id, 3);
		if (is_null($msg))
		{
			// handle _post
			$post = array(
                'styles' => $_post['styles']
            );

			$mod = new Theme_model();
            // update
			$result = $mod->update($id, $post);

            // set message
            $msg = AdmUtils_helper::set_msg($result);

            // set what update
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
	 * Uninstall a theme (use Ajax)
	 *
	 * @param   integer	$id Theme ID
	 * @return  void
	 */
	public function uninstall(int $id)
	{
		// load dictionary
		$this->dict->get_wordarray(array('form', 'themes'));

		// get object
		$theme = new Theme_model();
		$item = $theme->get_by_id($id, 'themes', 'id, name');

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
        $view->title = _UNINSTALL_THEME;

		// contents
		$view->content = new X4View_core('uninstall');

		$view->content->item = $item->name;

		// form builder
		$view->content->form = X4Form_helper::doform('uninstall', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(TRUE);
	}

	/**
	 * Perform the uninstall
	 *
	 * @access	private
	 * @param   object  $item
	 * @return  void
	 */
	private function uninstalling(stdClass $item)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'themes', $item->id, 4);

		if (is_null($msg))
		{
			// do action
			$mod = new Theme_model();
			$result = $mod->uninstall($item->id, $item->name);

			// check the result
			if (is_array($result))
			{
				$this->notice(false, '_theme_not_uninstalled');
				die;
			}
			else
			{
				// uninstalled

				// set message
				$msg = AdmUtils_helper::set_msg(true);

				if ($result)
                {
                    // clear useless permissions
				    $perm = new Permission_model();
					$perm->deleting_by_what('themes', $item->id);
                }

				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'themes'
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Minimize css files
	 *
     * @param   integer	$id_theme
     * @param   string $name
	 * @return void
	 */
	public function minimize(int $id_theme, string $name)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'themes', $id_theme, 4);
		if (is_null($msg))
		{
			// do action
			$res = 1;

			// get the templates in the theme
			$mod = new Theme_model();

			// CSS section
			$path = PATH.'themes/'.$name.'/css/';
			$items = $mod->get_css($id_theme);
			foreach ($items as $i)
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
			foreach ($items as $i)
			{
				if (file_exists($path.$i->js.'.js'))
				{
					$txt = file_get_contents($path.$i->js.'.js');
					$txt = X4JShrink_helper::minimize($txt, array('flaggedComments' => false));
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
            {
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'themes'
				);
            }
		}
		$this->response($msg);
	}
}
