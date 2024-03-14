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
 * Controller for Categories
 *
 * @package X3CMS
 */
class Dictionary_controller extends X3ui_controller
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
	 * Redirect to language list
	 */
	public function _default() : void
	{
		header('Location: '.BASE_URL.'languages');
		die;
	}

	/**
	 * Show dictionary words by Language code, Area name and key
	 */
	public function keys(string $lang = '', string $area = 'public') : void
	{
        $lang = (empty($lang))
            ? X4Route_core::$lang
            : $lang;

	    // load dictionary
		$this->dict->get_wordarray(array('dictionary'));

        // get area info
        $id_area = X4Route_core::get_id_area($area);
		$area_mod = new Area_model();
		list($id_area, $areas) = $area_mod->get_my_areas($this->site->data->id, $id_area);

        // get query string from filter
        $qs = X4Route_core::get_query_string();

        // keys, sections of the dictionary
        $mod = new Dictionary_model();
        $keys = $mod->get_keys($lang, $area);


        // handle filters
        $qs['xstr'] = $qs['xstr'] ?? '';
        $qs['xwhat'] = $qs['xwhat'] ?? '';
        // check empty what
        if (!empty($keys) && empty($qs['xwhat']) && empty($qs['xstr']))
        {
            $qs['xwhat'] = $keys[0]->what;
        }

        // get page
        $page = $this->get_page('dictionary/keys');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
        $view->actions = $this->actions($lang, $area, $qs['xwhat']);

        // contents
        $view->content = new X4View_core('languages/words');
        $view->content->keys = $keys;
        $view->content->id_area = $id_area;

        // get items
        if (empty($qs['xstr']))
        {
            $view->content->items = $mod->get_words($lang, $area, $qs['xwhat']);
        }
        else
        {
            $qs['xwhat'] = '';
            $view->content->items = $mod->search_words($area, $qs['xstr']);
        }

        $view->content->what = $qs['xwhat'];
        $view->content->qs = $qs;

        // area switcher
        $view->content->area = $area;
        $view->content->areas = $areas;

        // language switcher
        $view->content->lang = $lang;
        if (MULTILANGUAGE)
        {
            $lang = new Language_model();
            $view->content->langs = $lang->get_languages();
        }
        // to fix charset
        header('Content-Type: text/html; charset=utf-8');

        $view->render(true);
	}

	/**
	 * Dictionary actions
	 */
	private function actions(string $lang, string $area, string $what = '') : string
	{
		return '<a class="link" @click="popup(\''.BASE_URL.'dictionary/clean/'.$area.'\')" title="'._DICTIONARY_DELETE_DUPLICATES.'">
            <i class="fa-solid fa-lg fa-broom"></i>
        </a>
        <a class="link" @click="popup(\''.BASE_URL.'dictionary/import/'.$lang.'/'.$area.'\')" title="'._IMPORT_KEYS.'">
            <i class="fa-solid fa-upload fa-lg"></i>
        </a>
        <a class="link" @click="popup(\''.BASE_URL.'dictionary/edit/'.$lang.'/'.$area.'?xwhat='.$what.'\')" title="'._NEW_WORD.'">
            <i class="fa-solid fa-lg fa-circle-plus"></i>
        </a>';
	}

	/**
	 * Change status
	 */
	public function set(string $what, int $id_area, int $id, int $value = 0) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($id_area, 'dictionary', $id, $what);
		if (is_null($msg))
		{
			// do action
			$dict = new Dictionary_model();
			$result = $dict->update($id, array($what => $value));

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
	 * Edit dictionary word form
	 */
	public function edit(string $lang, string $area, int $id = 0) : void
	{
        // load dictionary
		$this->dict->get_wordarray(array('form', 'dictionary'));

        $qs = X4Route_core::get_query_string();
        $qs['xwhat'] = $qs['xwhat'] ?? '';

		// get object
		$mod = new Dictionary_model();
        $item = ($id)
			? $mod->get_by_id($id)
			: new Word_obj($qs['xwhat']);

        // build the form
		$form_fields = new X4Form_core('dictionary/word_edit');
		$form_fields->id = $id;
		$form_fields->item = $item;

		// get the fields array
		$fields = $form_fields->render();

        $id_area = X4Route_core::get_id_area($area);

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->editing($id, $id_area, $_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        $view = new X4View_core('modal');
        $view->title = _EDIT_WORD;
		// content
		$view->content = new X4View_core('editor');
        // can user edit?
        $submit = AdmUtils_helper::submit_btn($id_area, 'dictionary', $id, $item->xlock);
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, $submit, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');
		$view->render(true);
	}

	/**
	 * Register Edit dictionary word
	 */
	private function editing(int $id, int $id_area, array $_post) : void
	{
		$msg = null;
        // check permissions
        $msg = ($id)
            ? AdmUtils_helper::chk_priv_level($id_area, 'dictionary', $id_area, 'edit')
            : AdmUtils_helper::chk_priv_level($id_area, '_word_creation', 0, 'create');

		if (is_null($msg))
		{
			// handle _post
            // handle _post
			$post = array(
				'lang' => $_post['lang'],
				'area' => $_post['area'],
				'what' => X4Utils_helper::slugify($_post['what'], true, true),
				'xkey' => strtoupper(trim($_post['xkey']))
			);

            $value = trim($_post['xval']);
            if (strip_tags($value) == $value)
            {
                // no HTML so we replace \n with <br>
                $value = nl2br($value);
            }
			$post['xval'] = $value;

			// update
			$mod = new Dictionary_model();

            // check if words already exists
			$check = $mod->exists($id, $post);
			if ($check)
            {
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_XKEY_ALREADY_EXISTS', 'msg'));
            }
			else
			{
                $obj = $mod->get_by_id($id);
                // update or insert
				if ($id)
				{
                    $result = $mod->update($id, $post);
				}
				else
				{
				    $result = $mod->insert($post);

                    if ($result[1])
                    {
                        // permissions
                        $perm = new Permission_model();
                        $array[] = array(
                            'action' => 'insert',
                            'id_what' => $result[0],
                            'id_user' => $_SESSION['xuid'],
                            'level' => 4
                        );
                        $perm->pexec('dictionary', $array, $post['id_area']);
                    }
                }

                // set message
                $msg = AdmUtils_helper::set_msg($result);

                // set what update
                if ($result[1])
                {
                    // reset cache
                    APC && apcu_delete(SITE.'dict'.$obj->area.$obj->lang.$obj->what);

                    $msg->update = array(
                        'element' => 'page',
                        'url' => BASE_URL.'dictionary/keys/'.$post['lang'].'/'.$post['area'].'?xwhat='.$post['what']
                    );
                }
            }
		}
		$this->response($msg);
	}

	/**
	 * Delete dictionary word form
	 */
	public function delete(int $id) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'dictionary'));

		// get object
		$mod = new Dictionary_model();
		$item = $mod->get_by_id($id, 'dictionary', 'id, area, xkey');

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
			$this->deleting($item);
			die;
		}

        $view = new X4View_core('modal');
		$view->title = _DELETE_WORD;
		// contents
        $view->content = new X4View_core('delete');
		$view->content->item = $item->xkey;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

	/**
	 * Delete dictionary word
	 */
	private function deleting(stdClass $item) : void
	{
		$msg = null;
		// check permission
        $id_area = X4Route_core::get_id_area($item->area);
		$msg = AdmUtils_helper::chk_priv_level($id_area, 'dictionary', $item->id, 'delete');

		if (is_null($msg))
		{
			// do action
			$mod = new Dictionary_model();
			$result = $mod->delete($id);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// clear useless permissions
			if ($result[1])
            {
				$perm = new Permission_model();
				$perm->deleting_by_what('dictionary', $id);

				// set what update
				$msg->update = array(
					'element' => 'page',
					'url' => $_SERVER['HTTP_REFERER']
				);
			}
		}
		$this->response($msg);
	}

    /**
	 * Remove duplicates in dictionary table
	 */
	public function clean(string $area) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'dictionary'));

		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => 1,
			'name' => 'id'
		);

		// if submitted
		if (X4Route_core::$post)
		{
			$this->cleaning($area);
			die;
		}

        $view = new X4View_core('modal');
        $view->title = _DICTIONARY_DELETE_DUPLICATES;

		// contents
		$view->content = new X4View_core('delete');
		$view->content->item = _DICTIONARY_DELETE_DUPLICATES_MSG;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

	/**
	 * Remove duplicates
	 */
	private function cleaning($area) : void
	{
		$msg = null;
		// check permission
        $id_area = X4Route_core::get_id_area($area);
		$msg = AdmUtils_helper::chk_priv_level($id_area, '_word_creation', 0, 'create');

		if (is_null($msg))
		{
			// do action
			$mod = new Dictionary_model();
			$result = $mod->remove_duplicates();

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// clear useless permissions
			if ($result[1])
            {
                // set what update
				$msg->update = array(
					'element' => 'page',
					'url' => $_SERVER['HTTP_REFERER']
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Import all dictionary words from another area
	 */
	public function import(string $lang, string $area) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'dictionary'));

        $mod = new Dictionary_model();

        $form_fields = new X4Form_core('dictionary/word_import');
		$form_fields->lang = $lang;
		$form_fields->area = $area;
        $form_fields->sections = $mod->get_section_options();

		// get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'import');
			if ($e)
			{
				$this->importing($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        $view = new X4View_core('modal');
        $view->title = _IMPORT_KEYS;

		// contents
		$view->content = new X4View_core('editor');


		// form builder
		$view->content->form = X4Form_helper::doform('import', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'import\')"');

		$view->render(true);
	}

	/**
	 * Perform the importing of words
	 */
	private function importing(array $_post) : void
	{
		$msg = null;
		// check permission
        $id_area = X4Route_core::get_id_area($_post['area']);
		$msg = AdmUtils_helper::chk_priv_level($id_area, '_key_import', 0, 'create');

		if (is_null($msg))
		{
			// get key
			list($lang, $area, $what) = explode('-', $_post['what']);

			// handle _post
			$post = array(
				'lang' => $_post['lang'],
				'area' => $_post['area'],
				'what' => $what,
				'xon' => 1
			);

			// get words to import
			$mod = new Dictionary_model();

			if ($what == 'ALL')
			{
				// import all sections in an area
				$sections = $mod->get_sections($lang, $area);

				$result = true;
				foreach ($sections as $s)
				{
					// get words in section
					$words = $mod->get_words_to_import($lang, $area, $s->what, $post['lang'], $post['area']);

					if (!empty($words))
					{
						$post['what'] = $s->what;

						// import
						foreach ($words as $i)
						{
							$post['xkey'] = $i->xkey;
							$post['xval'] = $i->xval;

							// insert
							$result = $mod->insert($post);
						}
					}
				}

				// set what for redirect
				$what = 'global';
			}
			else
			{
				// import only one section
				$words = $mod->get_words_to_import($lang, $area, $what, $post['lang'], $post['area']);

				$result = true;

				// import
				foreach ($words as $i)
				{
					$post['xkey'] = $i->xkey;
                    $post['xval'] = $i->xval;

					// insert
					$result = $mod->insert($post);
				}
			}

			$msg = AdmUtils_helper::set_msg($result);

			// set what update
			if ($result[1])
			{
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'dictionary/keys/'.$post['lang'].'/'.$post['area'].'/'.$what
				);
			}
		}
		$this->response($msg);
	}
}
