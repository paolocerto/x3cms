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
	public function index(int $id_theme, string $theme_name)
	{
		// load dictionary
		$this->dict->get_wordarray(array('templates'));

		// get page
		$page = $this->get_page('templates/index');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = '';

		// content
		$view->content = new X4View_core('themes/template_list');
		$view->content->id_theme = $id_theme;
		$view->content->theme = $theme_name;

		$mod = new Template_model();
		// installed templates
		$view->content->tpl_in = $mod->get_tpl_installed($id_theme);
		// installable templates
		$view->content->tpl_out = $mod->get_tpl_installable($id_theme, $theme_name);
		$view->render(true);
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
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'templates', $id, $val);
		if (is_null($msg))
		{
			// do action
			$mod = new Template_model();
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
	 * Install a new template (use Ajax)
	 *
	 * @param   integer $id_theme Theme ID
	 * @param   string	$template_name Template name
	 * @return  void
	 */
	public function install(int $id_theme, string $template_name)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'templates', 'sections'));

        $view = new X4View_core('modal');
        $view->title = _INSTALL_TEMPLATE;

		// content
		$view->content = new X4View_core('editor');

		// check the template name
		if (strstr(urldecode($template_name), ' ') != '')
        {
			$view->content->form = '<h2>'._WARNING.'</h2><p>'._INVALID_TEMPLATE.BR.'<strong>>>&nbsp;'.urldecode($template_name).'&nbsp;<<</strong><br />&nbsp;</p>';
        }
		else
		{
			// get theme object
			$mod = new Theme_model();

			// build the form
            $form_fields = new X4Form_core('template/template_install');
            $form_fields->id_theme = $id_theme;
            $form_fields->template_name = $template_name;
            $form_fields->theme = $mod->get_by_id($id_theme);

            // get the fields array
		    $fields = $form_fields->render();

			// if submitted
			if (X4Route_core::$post)
			{
				$e = X4Validation_helper::form($fields, 'editor');
				if ($e)
				{
					$this->installing($_POST);
				}
				else
                {
					$this->notice($fields);
                }
				die;
			}

			// form builder
			$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
                '@click="submitForm(\'editor\')"');
		}
		$view->render(true);
	}

	/**
	 * Perform template install
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function installing(array $_post)
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
				$theme = $mod->get_var($post['id_theme'], 'themes', 'name');
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'templates/index/'.$post['id_theme'].'/'.$theme
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
	private function get_css(string $theme)
	{
		// css file list
		$css = array();
		$files = glob(PATH.'themes/'.$theme.'/css/*');
		foreach ($files as $i)
		{
			$name = str_replace(array('screen.css', '.css'), '', basename($i));
			$css[] = array('v' => $name, 'o' => $name);
		}

		return X4Array_helper::array2obj($css, 'v', 'o');
	}

	/**
	 * Uninstall template form (use Ajax)
	 *
	 * @param   integer $id Template ID
	 * @return  void
	 */
	public function uninstall(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'templates'));

		// get object
		$mod = new Template_model();
		$item = $mod->get_by_id($id, 'templates', 'id, name, id_theme');

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
        $view->title = _UNINSTALL_TEMPLATE;

		// contents
		$view->content = new X4View_core('uninstall');
		$view->content->item = $item->name;

		// form builder
		$view->content->form = X4Form_helper::doform('uninstall', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'uninstall\')"');
		$view->render(true);
	}

	/**
	 * Uninstalling template
	 *
	 * @access	private
	 * @param   object  $item
	 * @return  void
	 */
	private function uninstalling(stdClass $item)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'templates', $item->id, 4);

		if (is_null($msg))
		{
			// do action
			$mod = new Template_model();
			$result = $mod->uninstall($item->id);

			if (is_array($result)) {
				// set error
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_template_not_uninstalled'));
			}
			else
            {
				// set message
				$msg = AdmUtils_helper::set_msg($result);

				if ($result)
				{
					// clear useless permissions
					$perm = new Permission_model();
					$perm->deleting_by_what('templates', $item->id);

					$theme = $mod->get_var($item->id_theme, 'themes', 'name');
					$msg->update = array(
						'element' => 'page',
						'url' => BASE_URL.'templates/index/'.$_post['id_theme'].'/'.$theme
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
	public function edit(string $what, string $theme, int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'template'));

		// get object
		$mod = new Template_model();
		$item = $mod->get_by_id($id, 'templates', 'id_theme, name, css');

		// path to file
		$file = ($what == 'template')
            ? PATH.'themes/'.$theme.'/templates/'.$item->name.'.php'
            : PATH.'themes/'.$theme.'/css/'.$item->css.'.css';

		// build the form
		$form_fields = new X4Form_core('template/template_edit');
		$form_fields->id = $id;
		$form_fields->item = $item;
        $form_fields->code = htmlentities($this->replace(1, file_get_contents($file)));

		// get the fields array
		$fields = $form_fields->render();

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

        $view = new X4View_core('modal');
        $view->title = _EDIT.' '.$item->name;
        $view->wide = ' xl:w-2/3';

		// contents
		$view->content = new X4View_core('editor');

		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
        '@click="submitForm(\'editor\')"');

		$view->render(true);
	}

	/**
	 * Clean/restore template file
	 *
	 * @access	private
	 * @param   integer	$in_out Action switcher
	 * @param   string	$str File text
	 * @return  string
	 */
	private function replace(int $in_out, string $str)
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
	private function editing(array $_post, string $file)
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
			if ($check)
			{
                $mod = new Theme_model();
				$theme = $mod->get_var($_post['id_theme'], 'themes', 'name');
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'templates/index/'.$_post['id_theme'].'/'.$theme
				);
			}
		}
		$this->response($msg);
	}
}
