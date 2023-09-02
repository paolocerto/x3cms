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
 * Controller for Language items
 *
 * @package X3CMS
 */
class Languages_controller extends X3ui_controller
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
	 * Show languages
	 *
	 * @return  void
	 */
	public function _default()
	{
		// load the dictionary
		$this->dict->get_wordarray(array('languages', 'msg'));

		// get page
		$page = $this->get_page('languages');

		$view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = $this->actions();

        $view->content = new X4View_core('languages/language_list');
        $view->content->page = $page;

		$lang = new Language_model();
		$view->content->langs = $lang->get_languages();
		$view->render(TRUE);
	}

	/**
	 * Actions
	 *
     * @access	private
	 * @return  void
	 */
	private function actions()
	{
		return '<a class="link" @click="popup(\''.BASE_URL.'languages/edit\')" title="'._NEW_LANG.'">
            <i class="fa-solid fa-lg fa-circle-plus"></i>
        </a>';
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

		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'languages', $id, $val);
		if (is_null($msg))
		{
			// do action
			$lang = new Language_model();
			$result = $lang->update($id, array($what => $value));

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
	 * New / Edit language form (use Ajax)
	 *
	 * @param   integer  $id item id (if 0 then is a new item)
	 * @return  void
	 */
	public function edit(int $id = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'languages'));

		// get object
		$mod = new Language_model();
		$item = ($id)
			? $mod->get_by_id($id)
			: new Lang_obj();

		// build the form
		$form_fields = new X4Form_core('language/language_edit');
		$form_fields->id = $id;
		$form_fields->item = $item;

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
        $view->title = ($id)
        	? _EDIT_LANG
			: _ADD_LANG;

        // contents
		$view->content = new X4View_core('editor');
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');
		$view->render(TRUE);
	}

	/**
	 * Register Edit / New language data
	 *
	 * @access	private
	 * @param   integer $id item ID (if 0 then is a new item)
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing(int $id, array $_post)
	{
		$msg = null;
		// check permission
		$msg = ($id)
            ? AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'languages', $_post['id'], 3)
            : AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_language_creation', 0, 4);

		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'code' => X4Utils_helper::slugify($_post['code']),
				'language' => $_post['language'],
				'rtl' => intval(isset($_post['rtl']))
			);

			$lang = new Language_model();

			// check if language already exists
			$check = $lang->exists($post, $id);
			if ($check)
            {
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_LANGUAGE_ALREADY_EXISTS', 'msg'));
            }
			else
			{
				// update or insert
				$result = ($id)
                    ? $lang->update($_post['id'], $post)
                    : $lang->insert($post);

				// set message
				$msg = AdmUtils_helper::set_msg($result);

				// set what update
				if ($result[1])
				{
					$msg->update = array(
						'element' => 'page',
						'url' => BASE_URL.'languages'
					);
				}
			}
		}
		$this->response($msg);
	}

	/**
	 * Delete Language form (use Ajax)
	 *
	 * @param   integer $id Language ID
	 * @return  void
	 */
	public function delete(int $id)
	{
		// load dictionary
		$this->dict->get_wordarray(array('form', 'languages'));

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
			$this->deleting($_POST);
			die;
		}

		// get object
		$mod = new Language_model();
		$item = $mod->get_by_id($id, 'languages', 'language');

        $view = new X4View_core('modal');
        $view->title = _DELETE_LANG;

		// contents
		$view->content = new X4View_core('delete');
		$view->content->item = $item->language;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(TRUE);
	}

	/**
	 * Delete language
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function deleting(array $_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'languages', $_post['id'], 4);

		if (is_null($msg))
		{
			// action
			$mod = new Language_model();
			$result = $mod->delete_lang($_post['id']);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// clear useless permissions
			if ($result[1])
			{
				$perm = new Permission_model();
				$perm->deleting_by_what('languages', $_post['id']);

				// set what update
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'languages'
				);
			}
		}
		$this->response($msg);
	}

    /**
	 * Change admin language
	 *
	 * @return  void
	 */
	public function selector()
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'lang'));

		// getavailable languages
		$mod = new Language_model();
        $languages = $mod->get_alanguages(1);

        // build the form
		$fields = array();

        $fields[] = array(
            'label' => null,
            'type' => 'html',
            'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
        );

		$fields[] = array(
			'label' => ucfirst(_LANGUAGE),
			'type' => 'radio',
			'value' => X4Route_core::$lang,
            'options' => array($languages, 'code', 'language'),
			'name' => 'code',
            'checked' => X4Route_core::$lang
		);

        $fields[] = array(
            'label' => null,
            'type' => 'html',
            'value' => '</div>'
        );

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->selecting($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        $view = new X4View_core('modal');
        $view->wide = 'md:w-1/5';
        $view->title = _SWITCH_LANGUAGE;

        // contents
		$view->content = new X4View_core('editor');
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');
		$view->render(TRUE);
	}

    /**
	 * Selecting
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function selecting(array $_post)
	{
		$old_lang = X4Route_core::$lang;
        $new_lang = $_post['code'];

        $res = [0, 1];
         // set message
        $msg = AdmUtils_helper::set_msg($res);

        // set what update
        $msg->update = array(
            'element' => 'redirect',
            'url' => ROOT.$new_lang.'/admin/home/dashboard'
            );
        $this->response($msg);
	}

	/**
	 * Change a language with another
	 * If for whatever reason you need to exchange two languages you can call this script
	 * Both languages have to been set in the system
	 * /admin/languages/switch_languages/OLD_LANG/NEW_LANG
	 *
	 * @param   string	$old_lang Language code you want to replace
	 * @param   string  $new_lang Language code you want to set
	 * @return  string
	 */
	public function switch_languages(string $old_lang, string $new_lang)
	{
		// Comment the next row to enable the method
		die('Operation disabled!');

		// extra tables
		// if you want to add extra table to change insert them in this this array
		$tables = array(
			'articles',
			'categories',
			'contexts',
			'dictionary',
			'pages',
			'users',

		);

		if ($old_lang != $new_lang)
		{
			$mod = new Language_model();

			$chk1 = $mod->get_language_by_code($old_lang);
			$chk2 = $mod->get_language_by_code($new_lang);

			if ($chk1 && $chk2)
			{
				// get areas
				$areas = $mod->get_all('areas');

				echo '<h1>START SWITCHING LANUAGES FROM '.$old_lang.' TO '.$new_lang.'!</h1>';

				$opt = array('FAILED', 'DONE');

				foreach ($areas as $a)
				{
					echo '<p>AREA: '.$a->name.'</p><ul>';

					// here you can select an area to exclude
					foreach ($tables as $t)
					{
						$res = $mod->switch_languages($a->id, $t, $old_lang, $new_lang);
						echo '<li>TABLE: '.$t.' => '.$res[1].'</li>';
					}
					echo '</ul>';
				}

				echo '<h1>FINISHED!</h1>';
				echo '<p>The changes on the database are applied.</p>';
				echo '<p>The number after each table is the number of changes. Please check if there are errors.</p>';

				// print instructions for manual changes
				echo '<p>NOTE: After this operation you could want to change the default language for each area.</p>';

			}
			else
			{
				echo '<h1>WARNING!</h1>';
				echo '<p>One or both languages are not in the languages table.</p>';
			}
		}
		else
		{
			echo '<h1>WARNING!</h1>';
			echo '<p>The old language "'.$old_lang.'" and the new language "'.$new_lang.'" are equal.</p>';
		}
		die;
	}
}
