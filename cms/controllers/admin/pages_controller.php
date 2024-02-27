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
 * Controller for Page items
 *
 * @package X3CMS
 */
class Pages_controller extends X3ui_controller
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
	 * Show pages
	 *
	 * @return  void
	 */
	public function _default()
	{
		$this->index(2, '', 'home');
	}

	/**
	 * Show pages
	 * As default display public area pages
	 * Display all child pages of a given page
	 * If the page is the home of the area, then you also view the home
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang language code
	 * @param   string  $xfrom page URL of origin
	 * @return  void
	 */
	public function index(int $id_area, string $lang = '', string $xfrom = 'home')
	{
	    $area = new Area_model();
	    list($id_area, $areas) = $area->get_my_areas(0, $id_area);

		// initialize parameters
		$lang = (empty($lang))
			? X4Route_core::$lang
			: $lang;

		$xfrom = str_replace('§', '/', urldecode($xfrom));

		// load dictionary
		$this->dict->get_wordarray(array('pages', 'sections', 'msg'));
        // get page
        $page = $this->get_page('pages');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page), array('areas' => 'index'));
		$view->actions = $this->actions($id_area, $lang, $xfrom);

		$view->content = new X4View_core('pages/pages');

		// content
		$mod = new Page_model($id_area, $lang);
		$view->content->id_area = $id_area;
		$view->content->lang = $lang;
		$view->content->xfrom = $xfrom;
		$view->content->area = $mod->get_var($id_area, 'areas', 'name');

		$obj = $mod->get_page($xfrom);
		$view->content->page = ($obj)
			? $obj
			: new Page_obj($id_area, $lang);

		// pages to show
		$view->content->pages = $mod->get_pages($xfrom, $view->content->page->deep);
		// available menus
		$mod = new Menu_model();
		$view->content->menus = $mod->get_menus($id_area, '', 'id');
		// language switcher
        if (MULTILANGUAGE)
        {
		    $lang = new Language_model();
		    $view->content->langs = $lang->get_languages();
        }
		// area switcher
		$view->content->areas = $areas;

		$view->render(true);
	}

	/**
	 * Pages actions
	 *
     * @access	private
	 * @return  void
	 */
	private function actions(int $id_area, string $lang, string $xfrom = '')
	{
		return '<a class="link" @click="popup(\''.BASE_URL.'areas/map/'.$id_area.'/'.$lang.'\')" title="'._SITE_MAP.'"><i class="fa-solid fa-lg fa-location-dot"></i></i></a>
				<a class="link" @click="popup(\''.BASE_URL.'pages/add/'.$id_area.'/'.$lang.'/'.$xfrom.'\')" title="'._NEW_PAGE.'"><i class="fa-solid fa-lg fa-circle-plus"></i></a>';
	}

	/**
	 * Change status
	 *
	 * @param   string  $what field to change
     * @param   integer $id_area
	 * @param   integer $id ID of the item to change
	 * @param   integer $value value to set (0 = off, 1 = on)
	 * @return  void
	 */
	public function set(string $what, int $id_area, int $id, int $value = 0)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($id_area, 'pages', $id, $what);
		if (is_null($msg))
		{
			// do action
			$mod = new Page_model($id_area, X4Route_core::$lang, $id);
			$result = $mod->update($id, array($what => $value), 'pages');

			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);

			// set update
			$msg->update = array(
				'element' => 'page',
				'url' => $_SERVER['HTTP_REFERER']
			);
		}
		$this->response($msg);
	}

	/**
	 * Add page form
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   string	$xfrom Page URL of parent page
	 * @return  mixed
	 */
	public function add(int $id_area, string $lang, string $xfrom)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'pages'));

		// build the form
		$form_fields = new X4Form_core('page/page_add');
		$form_fields->id_area = $id_area;
		$form_fields->lang = $lang;
        $form_fields->xfrom = $xfrom;
        $form_fields->mod = new Page_model($id_area, $lang);

		// get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->adding($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        $view = new X4View_core('modal');
        $view->title = _ADD_PAGE;

		// contents
		$view->content = new X4View_core('editor');
		// form builder
		$view->content->form = X4Form_helper::doform(
            'editor',
            BASE_URL.'pages/add/'.$id_area.'/'.$lang.'/'.$xfrom,
            $fields,
            array(_RESET, _SUBMIT, 'buttons'),
            'post',
            '',
            '@click="submitForm(\'editor\')"'
        );

		$view->render(true);
	}

	/**
	 * Register new page
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function adding(array $_post)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_post['id_area'], '_page_creation', 0, 'create');
		if (is_null($msg))
		{
			// remove slash from url
			if ($_post['id_area'] > 1)
            {
				$_post['name'] = str_replace('/', '-', $_post['name']);
            }
			// handle _post
			$post = array(
				'lang' => $_post['lang'],
				'id_area' => $_post['id_area'],
				'url' => X4Utils_helper::slugify($_post['name'], true),
				'name' => $_post['name'],
				'title' => $_post['name'],
				'description' => $_post['description'],
				'xfrom' => $_post['xfrom'],
				'tpl' => $_post['tpl']
			);

			// load model
			$mod = new Page_model($_post['id_area'], $_post['lang']);

			// check if a page with the same URL already exists
			$check = (boolean) $mod->exists($post['url']);
			if ($check)
			{
				$msg = AdmUtils_helper::set_msg(
                    false,
                    '',
                    $this->dict->get_word('_PAGE_ALREADY_EXISTS', 'msg')
                );
			}
			else
			{
                // get deep
                $page_from = $mod->get_from($post['xfrom']);
                $post['deep'] = $page_from->deep + 1;

				// set css for the template of the new page
				$tmod = new Template_model();
				$css = $tmod->get_css($_post['id_area'], $_post['tpl']);
				$post['css'] = $css;

				// set xrif for admin pages
				$post['xid'] = ($_post['id_area'] == 1)
					 ? 'pages'
					 : '';

				// insert the new page
				$result = $mod->insert_page($post, $this->site->data->domain);

				// set message
				$msg = AdmUtils_helper::set_msg($result);

				// set what update
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
                    $perm->pexec('pages', $array, $post['id_area']);

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
	 * Change page position
	 *
	 * @param   integer  $id Page ID
	 * @return  void
	 */
	public function move(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'pages'));

		// get object
		$mod = new Page_model(2, X4Route_core::$lang, $id);
		$page = $mod->get_by_id($id);

		// build the form
		$form_fields = new X4Form_core('page/page_move');
		$form_fields->id = $id;
		$form_fields->page = $page;
        $form_fields->pages = $mod->get_pages('', 0, $page->url);

        $from = $mod->get_page($page->xfrom);
        $form_fields->from = $from;

        $items = $mod->get_subpages($page->xfrom, $page->id_menu);

        $form_fields->siblings = X4Form_helper::get_options(
            $items,
            'xpos',
            'name',
            $page->xpos,
            [1, 'As first']
        );

		// get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->moving($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        $view = new X4View_core('modal');
        $view->title = $page->name.': '._MENU_AND_ORDER;

		// contents
		$view->content = new X4View_core('pages/page_move');
        $view->content->page = $page;
        $view->content->from = $from;

        // can user edit?
        $submit = AdmUtils_helper::submit_btn($page->id_area, 'pages', $id, $page->xlock);
		// form builder
		$view->content->form = X4Form_helper::doform(
            'editor',
            BASE_URL.'pages/move/'.$id,
            $fields,
            array(_RESET, $submit, 'buttons'),
            'post',
            '',
            '@click="submitForm(\'editor\')"'
        );

		$view->render(true);
	}


    /**
	 * Subpages
	 *
	 * @param   integer $id_area
     * @param   string  $lang
     * @param   string  $xfrom
     * @param   integer $id_menu
	 * @return  string
	 */
	public function subpages(int $id_area, string $lang, string $xfrom, int $id_menu)
	{
		$mod = new Page_model($id_area, $lang);

        $items = $mod->get_subpages($xfrom, $id_menu);
        $parent = $mod->get_page($xfrom);

        $a = [
            'from_menu' => $parent->id_menu,
            'subpages' => X4Form_helper::get_options(
                $items,
                'xpos',
                'name',
                '',
                [1, 'As first']
            )
        ];
        header('Content-type: application/json');
		echo json_encode($a);
	}

    /**
	 * Save new position
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function moving(array $_post)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_post['id_area'], 'pages', $_post['id'], 'edit');
		if (is_null($msg))
		{
			// get object
			$mod = new Page_model(2, X4Route_core::$lang, $_post['id']);
			$page = $mod->get_by_id(
                $_post['id'],
                'pages',
                'id, id_area, lang, url, xfrom, id_menu, xpos, deep'
            );

			// this pages cannot be changed
			$no_change = array('home', 'msg', 'search');

            // id menù?
            if ($_post['xfrom'] == 'home' && $_post['id_menu'] > 0)
            {
                $id_menu = $_post['id_menu'];
            }
            else
            {
                $id_menu = (isset($_post['in_menu']) && $_post['from_menu'] > 0)
                    ? $_post['from_menu']
                    : 0;
            }

			// handle _post
			$post = array(
				'xfrom' => (!in_array($page->url, $no_change)) ? $_post['xfrom'] : $page->xfrom,
				'hidden' => intval(isset($_post['hidden'])),
                'id_menu' => $id_menu,
				'fake' => intval($_post['id_menu'] > 0 && isset($_post['fake'])),
                'xpos' => $_post['xpos']
			);

            // update page data
            $result = $mod->update_page($page, $post, $this->site->data->domain);

            // clear cache
            APC && apcu_clear_cache();

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
	 * Edit SEO data of a page (use Ajax)
	 *
	 * @param   integer  $id Page ID
	 * @return  void
	 */
	public function seo(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'pages'));

		// get object
		$mod = new Page_model(2, X4Route_core::$lang, $id);
		$page = $mod->get_page_by_id($id);

		// build the form
		$form_fields = new X4Form_core('page/page_seo');
		$form_fields->id = $id;
		$form_fields->page = $page;
        $form_fields->mod = $mod;

		// get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->reg_seo($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        $view = new X4View_core('modal');
        $view->title = _SEO_TOOLS;

		// contents
		$view->content = new X4View_core('editor');
        // can user edit?
        $submit = AdmUtils_helper::submit_btn($page->id_area, 'pages', $id, $page->xlock);
		// form builder
		$view->content->form = X4Form_helper::doform(
            'editor',
            BASE_URL.'pages/seo/'.$id,
            $fields,
            array(_RESET, $submit, 'buttons'),
            'post',
            '',
            '@click="submitForm(\'editor\')"');

		$view->render(true);
	}

	/**
	 * Register SEO data
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function reg_seo(array $_post)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_post['id_area'], 'pages', $_post['id'], 'edit');

		if (is_null($msg))
		{
			// get object
			$mod = new Page_model(2, X4Route_core::$lang, $_post['id']);
			$page = $mod->get_by_id($_post['id'], 'pages');

			// this pages cannot be changed
			$no_change = array('home', 'msg', 'search');

			// remove slash from url
			if ($page->id_area > 1)
            {
				$_post['url'] = str_replace('/', '-', $_post['url']);
            }

			// handle _post
			$post = array(
				'url' => (!in_array($page->url, $no_change))
                    ? X4Utils_helper::slugify($_post['url'])
                    : $page->url,
				'name' => $_post['name'],
				'title' => $_post['title'],
				'description' => $_post['description'],
				'xkeys' => $_post['xkeys'],
				'robot' => $_post['robot'],
				'redirect_code' => $_post['redirect_code'],
				'redirect' => $_post['redirect'],
				'tpl' => $_post['tpl']
			);

			// check if a page with the same URL already exists
			$check = (boolean) $mod->exists($post['url'], $_post['id']);
			if ($check)
			{
				$msg = AdmUtils_helper::set_msg(
                    false,
                    '',
                    $this->dict->get_word('_PAGE_ALREADY_EXISTS', 'msg')
                );
			}
			else
			{
				if ($page->tpl != $post['tpl'])
				{
					// set css for the page
					$tmod = new Template_model();
					$css = $tmod->get_css($page->id_area, $post['tpl']);
					$post['css'] = $css;

					// reset page sections
					$tmod->reset_sections($page->id_area, $page->id, $post['tpl']);
				}

				// update page data
				$result = $mod->update_page($page, $post, $this->site->data->domain);

				// clear cache
				APC && apcu_clear_cache();

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
		}
		$this->response($msg);
	}

	/**
	 * Delete Page form (use Ajax)
	 *
	 * @param   integer $id Page ID
	 * @return  void
	 */
	public function delete(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'pages'));

		// get object
		$mod = new Page_model(2, X4Route_core::$lang, $id);
		$item = $mod->get_by_id($id, 'pages', 'id, id_area, lang, name');

		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $id,
			'name' =>'id'
		);

		// if submitted
		if (X4Route_core::$post)
		{
			$this->deleting($item);
			die;
		}

        $view = new X4View_core('modal');
        $view->title = _DELETE_PAGE;

		// contents
		$view->content = new X4View_core('delete');

		$view->content->item = $item->name;

		// form builder
		$view->content->form = X4Form_helper::doform(
            'delete',
            $_SERVER["REQUEST_URI"],
            $fields,
            array(null, _YES, 'buttons'),
            'post',
            '',
            '@click="submitForm(\'delete\')"'
        );
		$view->render(true);
	}

	/**
	 * Delete page
	 *
	 * @access	private
	 * @param   stdClass    $item
	 * @return  void
	 */
	private function deleting(stdClass $item)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($item->id_area, 'pages', $item->id, 'delete');
		if (is_null($msg))
		{
			// action
			$mod = new Page_model($item->id_area, $item->lang, $item->id);
			$result = $mod->delete_page($item->id, $this->site->data->domain);

			// clear useless permissions
			if ($result[1])
			{
				$perm = new Permission_model();
				$perm->deleting_by_what('pages', $item->id);
			}

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
	 * Initialize area: create default pages
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string	$lang Language code
	 * @return  void
	 */
	public function init(int $id_area, string $lang)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($id_area, '_page_creation', 0, 'create');

		if (is_null($msg))
		{
			// get object: the area
			$area = new Area_model();
			$a = $area->get_by_id($id_area);

			$mod = new Page_model($id_area, $lang);

			// build the post array
			$post = array();

			if ($id_area == 1)
			{
				// admin area

				// uses admin area with language = SESSION['lang'] as base and duplicates all pages
				$pmod = new Page_model($id_area, $_SESSION['lang']);
				$pages = $pmod->get_pages();

				foreach ($pages as $i)
				{
					$post[] = array($i->url,
						array(
							'lang' => $lang,
							'id_area' => $id_area,
							'xid' => $i->xid,
							'url' => $i->url,
							'name' => $i->name,
							'title' => $i->title,
							'description' => $i->description,
							'xfrom' => $i->xfrom,
							'tpl' => $i->tpl,
							'css' => $i->css,
							'id_menu' => $i->id_menu,
							'xpos' => $i->xpos,
							'deep' => $i->deep,
							'ordinal' => $i->ordinal,
							'xon' => $i->xon
						)
					);
				}
			}
			else
			{
				// other areas

				// home
				$post[] = array('home', array('lang' => $lang,'id_area' => $id_area,'xid' => 'pages','url' => 'home','name' => 'Home page',
					'title' => 'Home page',	'description' => 'Home page','xfrom' => 'home','tpl' => 'base', 'css' => 'base',
					'id_menu' => 0, 'xpos' => 0, 'deep' => 0, 'ordinal' => 'A', 'xon' => 1));
				// msg
				$post[] = array('comunication', array('lang' => $lang,'id_area' => $id_area,'xid' => 'pages','url' => 'msg','name' => 'Communication',
					'title' => 'Communication','description' => 'Communication','xfrom' => 'home','tpl' => 'base', 'css' => 'base',
					'id_menu' => 0, 'xpos' => 2, 'deep' => 1, 'ordinal' => 'A0000002', 'hidden' => 1, 'xlock' => 1,'xon' => 1));
				// search
				$post[] = array('search', array('lang' => $lang,'id_area' => $id_area,'xid' => 'pages','url' => 'search','name' => 'Search result',
					'title' => 'Search result','description' => 'Search result','xfrom' => 'home','tpl' => 'base', 'css' => 'base',
					'id_menu' => 0, 'xpos' => 3, 'deep' => 1, 'ordinal' => 'A0000003', 'hidden' => 1, 'xlock' => 1,'xon' => 1));

				// if is a private area
				if ($a->private)
				{
					// exit
					$post[] = array('logout', array('lang' => $lang,'id_area' => $id_area,'xid' => 'pages','url' => 'logout','name' => 'Logout',
						'title' => 'Logout','description' => 'Logout','xfrom' => 'home','tpl' => 'base', 'css' => 'base',
						'id_menu' => 0, 'xpos' => 4, 'deep' => 1, 'ordinal' => 'A0000004', 'hidden' => 0, 'xlock' => 1,'xon' => 1));
				}
			}

			// create default articles
			$result = $mod->initialize_area($id_area, $lang, $post);

			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);

			if ($result[1])
			{
				// create default contexts
				$mod->initialize_context($id_area, $lang);

				// refactory permissions
				$mod = new Permission_model();
				$mod->refactory($_SESSION['xuid']);

				// set update
				$msg->update = array(
					'element' => 'page',
					'url' => $_SERVER['HTTP_REFERER']
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Duplicate an area for another language (secret method)
	 * If you need to add another language to an area you can call this script
	 * /admin/pages/duplicate_area_lang/ID_AREA/OLD_LANG/NEW_LANG
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

		$mod = new Page_model();

		// duplicate
		$res = $mod->duplicate_area_lang($id_area, $old_lang, $new_lang);

        if ($res[1])
        {
			// refactory permissions
			$mod = new Permission_model();
			$mod->refactory($_SESSION['xuid']);

            echo '<h1>CONGRATULATIONS!</h1>';
            echo '<p>The changes on the database are applied.</p>';

            // print instructions for manual changes
            echo '<p>Follow this instructions to perform manual changes.</p>
            <ul>
                <li>Install the following modules: '.implode(', ', $res[0]).' and configure them if needed</li>
            </ul>
            <p>Done!</p>

            <p>NOTE: this operation acts on the pages and articles of the CMS, if you use plugins you have to check if you need to duplicate contents.</p>';
        }
        else
        {
            echo '<h1>WARNING!</h1>';
            echo '<p>Something went wrong, changes are not applied.</p>';
        }
		die;
	}
}
