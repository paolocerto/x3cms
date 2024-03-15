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
 * Controller for Sections
 *
 * @package X3CMS
 */
class Sections_controller extends X3ui_controller
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
	 * Show sections by page
	 */
	public function index(int $id_area, int $id_page) : void
	{
		// load the dictionary
		$this->dict->get_wordarray(array('sections'));

        $lang = X4Route_core::$lang;

		// get page
		$page = $this->get_page('sections');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = AdmUtils_helper::link(
                'memo',
                'sections:'.$lang,
                [],
                _MEMO
            ).$this->actions($id_area, $lang, $id_page, '');

        $mod = new Section_model();
		// get page template sections
		$tpl_sections = $mod->get_template_data($id_page, 'sections');

		// contents
		$view->content = new X4View_core('sections/sections');
        $view->content->xpage = $mod->get_by_id($id_page, 'pages', 'id, id_area, lang, name, url, xfrom');

		$view->content->sections = $tpl_sections;

		$items = $mod->get_items($id_page);

		$n = $mod->count_default_sections($id_page);
		if ($n != $tpl_sections)
		{
			// create default sections
			$mod->initialize($id_area, $id_page);
			$items = $mod->get_items($id_page);
		}
		$view->content->items = $items;

		$view->render(true);
	}

	/**
	 * Compose actions
	 */
	public function actions(int $id_area, string $lang, int $id_page, string $page = 'compose') : string
	{
		if ($page == 'compose')
		{
			return '<a class="link" @click="pager(\''.BASE_URL.'sections/index/'.$id_area.'/'.$id_page.'\')" title="'._SECTIONS.'">
                <i class="fa-regular fa-object-group fa-lg"></i>
            </a>
            <a class="link" @click="pager(\''.BASE_URL.'articles/edit/'.$id_area.'/'.$lang.'/1/x3/'.$id_page.'\')" title="'._NEW_ARTICLE.'">
                <i class="fa-solid fa-lg fa-circle-plus"></i>
            </a>';
		}
		else
		{
            return '<a class="link" @click="popup(\''.BASE_URL.'sections/edit/'.$id_area.'/'.$id_page.'\')" title="'._SECTION_NEW.'">
                <i class="fa-solid fa-lg fa-circle-plus"></i>
            </a>';
		}
	}

	/**
	 * Change status
	 */
	public function set(string $what, int $id_area, int $id, int $value = 0) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($id_area, 'sections', $id, $what);
		if (is_null($msg))
		{
			// do action
			$mod = new Section_model();
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
	 * New / Edit section form
	 */
	public function edit(int $id_area, int $id_page, int $id = 0) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'sections'));

		// get object
		$mod = new Section_model();

        // get theme styles
        $theme_styles = $mod->get_theme_styles($id_page);

		if ($id)
		{
			$item = $mod->get_by_id($id);
		}
		else
		{
            // get model settings, we use those if the template have not settings
			$settings = json_encode($mod->settings);

            // get page template settings
			$tpl_settings = $mod->get_template_data($id_page, 'settings');
			if (!empty($tpl_settings))
			{
				$tps = json_decode($tpl_settings, true);

                // $tps['sn'] is the settings for the a New Section
                if (isset($tps['sn']) && !empty($tps['sn']))
				{
                    $tmp = $tps['sn'];
                    // template could have less or more or different keys of model settings
                    foreach ($mod->settings as $k => $v)
                    {
                        if (!isset($tmp[$k]))
                        {
                            $tmp[$k] = $v;
                        }
                    }
					$settings = json_encode($tmp);
				}
			}
			$item = new Section_obj($id_area, $id_page, $settings);
		}

		// build the form
		$form_fields = new X4Form_core('section/section_edit');
		$form_fields->id = $id;
		$form_fields->item = $item;
        $form_fields->mod_settings = $mod->settings;
		$form_fields->theme_styles = $theme_styles;
		// get the fields array
		$fields = $form_fields->render();

		// get the file_array
		$file_array = $form_fields->__get('file_array');

        // get js array
        //$js_array = $form_fields->__get('js_array');

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->editing($id, $_POST, $file_array);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        $view = new X4View_core('modal');
        $view->title = ($id)
			? _SECTION_EDIT.' - #'.$item->progressive
			: _SECTION_NEW;
        $view->wide = 'md:w-2/3 lg:w-2/3';
		// content
		$view->content = new X4View_core('editor');
        $view->content->msg = _SECTION_EDIT_MSG;
        // can user edit?
        $submit = AdmUtils_helper::submit_btn($item->id_area, 'sections', $id, $item->xlock);
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, $submit, 'buttons'), 'post', 'enctype="multipart/form-data"',
            '@click="submitForm(\'editor\')"');
        $view->render(true);
	}

	/**
	 * Register Edit / New item
	 */
	private function editing($id, $_post, $file_array) : void
	{
		$msg = null;
		// check permission
		$msg = ($id)
			? AdmUtils_helper::chk_priv_level($_post['id_area'], 'sections', $id, 'edit')
			: AdmUtils_helper::chk_priv_level($_post['id_area'], '_section_creation', 0, 'create');
		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'id_area' => $_post['id_area'],
				'name' => strtolower($_post['name']),
				'id_page' => $_post['id_page']
			);

			// used only with files
			$error = array();
			$mod = new Section_model();

            // get the previuos
            if ($id)
            {
                $settings = json_decode($mod->get_var($id, 'sections', 'settings'), true);
            }
            else
            {
                // do we have settings?
                // get the settings from the template
                $settings = $mod->get_template_data($post['id_page'], 'settings');
                if (!is_null($settings))
                {
                    // set template settings
                    $settings = json_decode($settings, true);
                    $settings = isset($settings['sn'])
                        ? $settings['sn']
                        : $settings['s1'];
                }
                else
                {
                    // set default settings from model
                    $settings = $mod->settings;
                }
            }

			// get data for settings
            $settings = array(
                'columns' => $_post['columns'],
                'col_sizes' => $_post['col_sizes'],
                'bgcolor' => $_post['bgcolor'],
                'fgcolor' => $_post['fgcolor'],
                'width' => $_post['width'],
                'height' => $_post['height'],
                'style' => $_post['stylex'],
                'class' => $_post['classx'],
            );

            // col_sizes
            if (empty($settings['col_sizes']))
            {
                $cs = array_fill(0, $settings['columns'], '1');
                $settings['col_sizes'] = implode('+', $cs);
            }

            // handle column settings
            $nc = sizeof(explode('+', $settings['col_sizes']));
            $tmp = array();
            for ($i = 0; $i < $nc; $i++)
            {
                if (isset($_post['bg'.$i]))
                {
                    // store data
                    $tmp['bg'.$i] = isset($_post['bg'.$i.'_reset'])
                        ? ''
                        : $_post['bg'.$i];
                    $tmp['fg'.$i] = isset($_post['fg'.$i.'_reset'])
                        ? ''
                        : $_post['fg'.$i];
                    $tmp['style'.$i] = $_post['style'.$i];
                    $tmp['class'.$i] = $_post['class'.$i];
                }
            }
            $settings['col_settings'] = $tmp;

            // check for default colors
            if (empty($settings['bgcolor']) || isset($_post['bgcolor_default']))
            {
                $settings['bgcolor'] = 'default';
            }

            if (empty($settings['fgcolor']) || isset($_post['fgcolor_default']))
            {
                $settings['fgcolor'] = 'default';
            }

            // there are files?
            $w4k = 3840;
            $h4k = 2160;

            $path = APATH.'files/'.SPREFIX.'/filemanager/';
            $sizes = array($w4k, $h4k, 'RESIZE', $w4k, $h4k);
            $mimes = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');

            // delete previous
            if (isset($_post['delete_img_h']))
            {
                if (!empty($settings['img_h'])) unlink($path.'img/'.$settings['img_h']);
                $settings['img_h'] = '';
            }

            // handle img_h
            if (!empty($_FILES['img_h']['name']))
            {
                // upload file
                $filename = X4Files_helper::upload('img_h', $path, '', 0, $sizes, $mimes);

                // check for errors
                if (!is_array($filename))
                {
                    $settings['img_h'] = $filename;
                }
                else
                {
                    $error = array_merge($error, $filename[0]);
                }
            }

            // delete previous
            if (isset($_post['delete_img_v']))
            {
                if (!empty($settings['img_v'])) unlink($path.'img/'.$settings['img_v']);
                $settings['img_v'] = '';
            }

            if (!empty($_FILES['img_v']['name']))
            {
                // upload file
                $filename = X4Files_helper::upload('img_v', $path, '', 0, $sizes, $mimes);

                // check for errors
                if (!is_array($filename))
                {
                    $settings['img_v'] = $filename;
                }
                else
                {
                    $error = array_merge($error, $filename[0]);
                }
            }
            $post['settings'] = json_encode($settings);

			if (empty($error))
			{
				// update or insert
				if ($id)
				{
					$result = $mod->update($id, $post);
				}
				else
				{
					// get the progressive of the new section
					$post['progressive'] = $mod->get_max_pos($post['id_area'], $post['id_page']) + 1;

                    // set template settings
					$post['settings'] = json_encode($settings);
					$result = $mod->insert($post);
				}

				// set message
				$msg = AdmUtils_helper::set_msg($result);

				// set what update
				if ($result[1])
				{
                    if (!$id)
                    {
                        Admin_utils_helper::set_priv($_SESSION['xuid'], $result[0], 'sections', $post['id_area']);
                    }

					$msg->update = array(
						'element' => 'page',
						'url' => BASE_URL.'sections/index/'.$post['id_area'].'/'.$post['id_page']
					);
				}
			}
			else
			{
				// build msg
				$str = array();
				foreach ($error as $k => $v)
				{
					// each field
					foreach ($v as $i)
					{
						// each error
						$str[] = $file_array[$k]._TRAIT_.$this->dict->get_word(strtoupper($i), 'msg');
					}
				}
				$msg = AdmUtils_helper::set_msg(false, '', implode('<br />', $str));
			}
		}
		$this->response($msg);
	}

	/**
	 * Delete section form
	 */
	public function delete(int $id) : void
	{
		// get object
		$mod = new Section_model();
		$item = $mod->get_by_id($id, 'sections', 'id, id_area, name, progressive, id_page');

		// load dictionaries
		$this->dict->get_wordarray(array('form', 'sections'));

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
        $view->title = _SECTION_DELETE;
		// contents
		$view->content = new X4View_core('delete');

		$view->content->item = '#'.$item->progressive.' - '.$item->name;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

	/**
	 * Delete section
	 */
	private function deleting(stdClass $item) : void
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($item->id_area, 'sections', $item->id, 'delete');

		if (is_null($msg))
		{
			$mod = new Section_model();
			$result = $mod->delete($item->id);

			$msg = AdmUtils_helper::set_msg($result);

			if ($result[1])
            {
				AdmUtils_helper::delete_priv('sections', $item->id);

				$msg->update = array(
					'element' => 'page',
					'url' => $_SERVER['HTTP_REFERER']
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Move item
	 */
	public function ordering(int $id_area, int $id_page) : void
	{
		$msg = null;
        $msg = AdmUtils_helper::chk_priv_level($id_area, 'pages', $id_page, 'order');
		if (is_null($msg) && X4Route_core::$input)
		{
            // handle post
            $_post = X4Route_core::$input;
			$elements = $_post['sort_order'];

            // do action
		    $mod = new Section_model();
            $items = $mod->get_items($id_page);

			// get fixed sections
			$n = $mod->get_template_data($id_page, 'sections');

			$result = array(0, 1);
			if ($items && !empty($elements))
			{
				foreach ($items as $i)
				{
					$p = array_search($i->id, $elements) + 1;
                    if ($i->progressive > $n && $i->progressive != $p+$n)
					{
						$res = $mod->update($i->id, array('progressive' => $p+$n));
						if ($res[1])
						{
							$result = $res;
						}
					}
				}
			}

			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);
		}
		$this->response($msg);
	}

	/**
	 * Page compositing
	 */
	public function compose(int $id_page, string $by = 'name') : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('sections', 'form', 'articles'));

		// get object
		$mod = new Page_model(2, X4Route_core::$lang);
        // page to edit
		$epage = $mod->get_page_by_id($id_page);

		// get page
		$page = $this->get_page('sections/compose');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = AdmUtils_helper::link(
                'memo',
                'sections:compose:'.X4Route_core::$lang,
                [],
                _MEMO
            ).$this->actions($epage->id_area, $epage->lang, $id_page, 'compose');

		// content
		$view->content = new X4View_core('sections/compose');
		$view->content->pagetoedit = $epage;

		$smod = new Section_model();
		$view->content->mod = $smod;

		// get contexts
		$view->content->dict = $this->dict;
		$view->content->codes = $smod->get_contexts($epage->id_area, $epage->lang);

		// get articles in area/language
		$view->content->articles = $smod->get_articles_to_publish($epage, $by);

		// get sections
		$sections = $smod->get_sections($id_page);
		if (empty($sections))
		{
		    // initialize
		    //$n = $smod->count_default_sections($id_page);
		    // create default sections
			$smod->initialize($epage->id_area, $id_page);
		    $sections = $smod->get_sections($id_page);
		}
		$view->content->sections = $sections;
		$view->content->referer = urlencode('sections/compose/'.$id_page);

		// template image
		$theme = $mod->get_theme($epage->id_area);
		$view->content->layout = (file_exists(PATH.'themes/'.$theme->name.'/img/'.$epage->tpl.'.png'))
			? ROOT.'themes/'.$theme->name.'/img/'.$epage->tpl.'.png'
			: '';

		$view->render(true);
	}

	/**
	 * Get article to show in composer
	 * During composition of page contents, user drag and drop articles from context list to page sections and vice versa
	 * When an article move the system calls this method
	 */
	public function get_article(int $id_page, string $destination, string $bid) : void
	{
		// load dictionary
		$this->dict->get_wordarray(array('articles'));

		// get object
		$mod = new Section_model();
		$art = $mod->get_by_bid($bid);

        // page
        $page = $mod->get_by_id($id_page, 'pages', 'id_area, lang');

        // target
        $target = explode('-', $destination);
        if ($target[0] == 'context')
        {
            // moved to context
            echo '<strong>'.stripslashes($art->name).'</strong>';
        }
        else
        {
            // moved to section
            // plugin info
            $m = (empty($art->module))
                ? _TRAIT_
                : $art->module;

            // parameter info
            $p = (empty($art->param))
                ? _TRAIT_
                : $art->param;

            // return article
            echo '<div class="relative h-full pb-16">
                <div class="border-b border-gray-200"><b>'.stripslashes($art->name).'</b>'._TRAIT_.'<a class="link" @click="pager(\''.BASE_URL.'articles/edit/'.$page->id_area.'/'.$page->lang.'/'.$art->code_context.'/'.$art->bid.'\')" title="'._EDIT.'">'._EDIT.'</a></div>
                '.stripslashes($art->content).'
                <div class="absolute bottom-0 w-full border-t border-gray-200 space-x-6"><span>'._MODULE.': '.$m.'</span><span>'._PARAM.': '.$p.'</span></div>
            </div>';
        }
	}

	/**
	 * Register page's composition
	 * Use _POST data
	 */
	public function compositing() : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_POST['id_area'], 'pages', $_POST['id_page'], 'edit');

		if (is_null($msg) && X4Route_core::$post)
		{
			// handle _POST
			$sections = array();
			$post = array(
				'id_area' => $_POST['id_area'],
				'id_page' => $_POST['id_page'],
				'xon' => 1
			);

            $mod = new Section_model();

			// handle _POST for each section
			for ($i = 1; $i <= $_POST['snum']; $i++)
			{
                $post['progressive'] = $i;
                // json_encoded array of bids
				$sort = $_POST['sort-'.$i];

			    // disable strange behaviour
			    if (strlen($sort) >= 32)
			    {
                    $post['articles'] = $sort;
                    $sections[] = $post;
                    // update draft
                    $artts = json_decode($sort);
                    foreach ($artts as $bid)
                    {
                        $art = $mod->get_by_bid($bid);
                        if (!$art->code_context)
                        {
                            $mod->recode($post['id_area'], 'pages', $bid, $post['id_page']);
                        }
                    }
                }
				else
				{
					// empty sections
					$post['articles'] = json_encode([]);
					$sections[] = $post;
				}
			}

			// register composition
			$result = $mod->compose($sections);
			APC && apcu_delete(SITE.'sections'.$post['id_page']);

			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);

			// add permissions on new sections
			if ($result[1])
			{
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'sections/compose/'.$post['id_page']
				);
			}
		}
		$this->response($msg);
	}
}
