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
 * Controller for Articles
 *
 * @package X3CMS
 */
class Articles_controller extends X3ui_controller
{
	/**
	 * tabs cases
	 */
	protected $cases = array(
			'latest_articles' => array('articles/index'),
			'by_page' => array('articles/page'),
			'context_order' => array('articles/context'),
			'category_order' => array('articles/category', 'btm'),
			'author_order' => array('articles/author', 'btm'),
			'key_order' => array('articles/key', 'btm')
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
	 * Show articles (table view)
	 *
	 * @return  void
	 */
	public function _default()
	{
		$this->index(2, X4Route_core::$lang);
	}

	/**
	 * Show articles (table view)
	 * Can show articles by context, by category, by author, by key, by page
	 * Default view is reverse chronological order
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   integer $pp index for pagination
	 * @return  void
	 */
	public function index(int $id_area = 2, string $lang = '' , int $pp = 0)
	{
		// load dictionary
		$this->dict->get_wordarray(array('articles'));

        // get query string from filter
        $qs = X4Route_core::get_query_string();

		$area = new Area_model();
	    list($id_area, $areas) = $area->get_my_areas($id_area);

		// get page
		$page = $this->get_page('articles');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = $this->actions($id_area, $lang);

		$mod = new Article_model();

        // contents
        $view->content = new X4View_core('articles/article_list');

        // handle filters
        $qs['xstr'] = $qs['xstr'] ?? '';
        $qs['xcnt'] = $qs['xcnt'] ?? -1;
        $qs['xctg'] = $qs['xctg'] ?? '';
        $qs['xkey'] = $qs['xkey'] ?? '';
        $qs['xpage'] = $qs['xpage'] ?? 0;
        $qs['xaut'] = $qs['xaut'] ?? 0;

        $view->content->contexts = $mod->get_contexts($id_area, $lang);
        $view->content->categories = $mod->get_all_categories($id_area, $lang);
        $view->content->keys = $mod->get_keys($id_area, $lang);
        $view->content->authors = $mod->get_authors($id_area, $lang);

        $view->content->items = X4Pagination_helper::paginate($mod->get_articles($id_area, $lang, $qs), $pp);

        $mod = new Page_model($id_area, $lang);
        $view->content->pages = $mod->get_pages();

        $view->content->qs = $qs;
		$view->content->pp = $pp;

		// area switcher
		$view->content->id_area = $id_area;
		$view->content->areas = $areas;

		// language switcher
		$view->content->lang = $lang;
        if (MULTILANGUAGE)
        {
		    $lang = new Language_model();
            $view->content->langs = $lang->get_languages();
        }
		$view->render(TRUE);
	}

	/**
	 * Article actions
	 *
     * @access	private
	 * @param   integer $id_area Area ID
	 * @param   string $lang Language code
	 * @return  string
	 */
	public function actions(int $id_area, string $lang)
	{
        return '<a class="link" @click="pager(\''.BASE_URL.'articles/edit/'.$id_area.'/'.$lang.'\')" title="'._NEW_ARTICLE.'">
            <i class="fa-solid fa-lg fa-circle-plus"></i>
        </a>';
	}

	/**
	 * Article bulk action
	 *
	 * @param   integer $id_area Area ID
	 * @param   string $lang Language code
	 * @param   string $bid Article unique code
	 * @return  void
	 */
	public function bulk(int $id_area, string $lang, string $bid)
	{
		$msg = null;
        $_post = X4Route_core::$input;
		if (!empty($_post) && isset($_post['bulk']) && is_array($_post['bulk']) && !empty($_post['bulk']))
		{
            $mod = new Article_model();
            $perm = new Permission_model();

            // NOTE: we here have only bulk_action = delete
            foreach ($_post['bulk'] as $i)
            {
                $msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'articles', $i, 4);
                if (is_null($msg))
                {
                    $result = $mod->delete($i);
                    if ($result[1])
                    {
                        $perm->deleting_by_what('articles', $i);
                    }
                }
            }

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
	 * Change status
	 *
	 * @param   string  $what field to change
	 * @param   integer $id ID of the item to change
	 * @param   integer $value value to set (0 = off, 1 = on)
	 * @return  void
	 */
	public function set(string $what, int $id, int $value = 0)
	{
		// check permissions
		$val = ($what == 'xlock')
			? 4
			: 3;
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'articles', $id, $val);
		if (is_null($msg))
		{
			// do action
			$mod = new Article_model();
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
	 * Change status by bid
	 * bid is the uniqueID of articles
	 *
	 * @param   string	$what field to change
	 * @param   integer $id ID of the item to change
	 * @param   integer $value value to set (0 = off, 1 = on)
	 * @return  void
	 */
	public function set_by_bid(string $what, int $id, int $value = 0)
	{
		// check permissions
		$val = ($what == 'xlock')
			? 4
			: 3;
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'articles', $id, $val);
		if (is_null($msg))
		{
			// do action
			$mod = new Article_model();
			$result = $mod->update_by_bid($id, array($what => $value));

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
	 * Get the list of available areas
	 * Called via Ajax
	 *
	 * @param   integer $id_area Area ID
	 * @return  string 	Options list
	 */
	public function refresh_areas()
	{
		$mod = new Area_model();
		$items = $mod->get_areas();
        echo json_encode($items);
	}

    /**
	 * Get the list of available languages
	 * Called via Ajax
	 *
	 * @param   integer $id_area Area ID
	 * @return  string 	Options list
	 */
	public function refresh_languages(int $id_area)
	{
		$mod = new Language_model();
		$items = $mod->get_alanguages($id_area);
        echo X4Form_helper::get_options($items, 'code', 'language');
	}

	/**
	 * Get the list of available contexts
	 * Called via Ajax
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @return  string 	Options list
	 */
	public function refresh_contexts(int $id_area, string $lang)
	{
		$mod = new Context_model();
		$items = $mod->get_codes($id_area, $lang, 1);
        echo X4Form_helper::get_options($items, 'code', 'name');
	}

	/**
	 * Get the list of available pages
	 * Called via Ajax
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @return  string 	Options list
	 */
	public function refresh_pages(int $id_area, string $lang)
	{
		$mod = new Context_model();
        $items = $mod->get_pages($id_area, $lang);
        echo X4Form_helper::get_options($items, 'id', 'name');
	}

    /**
	 * Get the list of available modules
	 * Called via Ajax
	 *
	 * @param   integer $id_area Area ID
	 * @return  string 	Options list
	 */
	public function refresh_module(int $id_area)
	{
		// get array of modules
		$plugin = new X4Plugin_model();
		$items = $plugin->get_modules($id_area);
        echo X4Form_helper::get_options($items, 'name', 'description', '');
	}

	/**
	 * New / Edit article form
	 * The form is simplified if site use simple editing
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   integer	$code_context Context code
	 * @param   string	$bid, the unique ID of articles
	 * @param   integer	$id_page Page ID (for simple editing)
	 * @param   boolean	$duplicate Duplicate article resetting bid
	 * @return  void
	 */
	public function edit(int $id_area = 2, string $lang = '', int $code_context = 0, string $bid = '0', int $id_page = 0, int $duplicate = 0)
	{
		// set language
		$lang = (empty($lang))
			? X4Route_core::$lang
			: $lang;

		// load dictionaries
		$this->dict->get_wordarray(array('form', 'articles'));

		// referer
		$qs = X4Route_core::get_query_string();
		$referer = (isset($qs['ref']))
			? $qs['ref']
			: '';

		$mod = new Article_model();

		// simple editing
		if ($id_page && $bid != 'x3')
		{
			$bid = $mod->get_bid_by_id_page($id_page);
		}

		// get object
		$item = ($bid && $bid != 'x3')
			? $mod->get_by_bid($id_area, $lang, $bid)
			: new Article_obj($id_area, $lang, $code_context);

		// dedicated page when called from composer
		if ($bid == 'x3')
		{
			$item->id_page = $id_page;
		}

		// check if display or not time window
		$time_window = true;
		if (!ADVANCED_EDITING && $item->id_page)
		{
			$time_window = false;
		}

		// if duplicate reset bid
		if ($duplicate)
		{
			$item->name = _COPY_OF.' '.$item->name;
			$item->bid = $mod->get_new_bid();
		}

		$lmod = new Language_model();

		// build the form
        $form_fields = new X4Form_core('article/article_edit');

		$form_fields->referer = $referer;
		$form_fields->item = $item;
		$form_fields->lmod = $lmod;
        $form_fields->time_window = $time_window;
        $form_fields->bid = $bid;
		// get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->editing($item, $_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        // Set the navbar
		$page = $this->get_page('articles/edit');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page), array('articles' => 'index/'.$id_area.'/'.$lang));
		$view->actions = $this->actions($id_area, $lang);

		// content
		$view->content = new X4View_core('editor');

		$pmod = new Page_model();
		if ($id_page && $bid != 'x3')
		{
			// simple editing
			$page = $pmod->get_by_id($id_page);
			$view->content->super_title = _CONTENT_EDITOR.' <a class="link" @click="pager(\''.BASE_URL.'pages/index/'.$page->id_area.'/'.$page->lang.'/'.$page->xfrom.'/1\')" title="'._GO_BACK.'">'.stripslashes($page->name).'</a>'._TRAIT_.$lang;
			$view->js = '';
		}
		else
		{
			// generic back
			$back = '<a class="link" @click="pager(\''.BASE_URL.'pages/index/'.$id_area.'/'.$lang.'/home\')" title="'._GO_BACK.'">'._PAGES.'</a>';
			if ($bid)
			{
				if ($item->id_page)
				{
					// back to the right page
					$page = $pmod->get_by_id($item->id_page);
					$back = (ADVANCED_EDITING)
						? '<a class="link" @click="pager(\''.BASE_URL.'sections/compose/'.$page->id.'\')" title="'._GO_BACK.'">'.stripslashes($page->name).'</a>'
						: '<a class="link" @click="pager(\''.BASE_URL.'pages/index/'.$page->id_area.'/'.$page->lang.'/'.$page->xfrom.'/1\')" title="'._GO_BACK.'">'.stripslashes($page->name).'</a>';
				}
				$view->content->super_title = $back._TRAIT_._EDIT_ARTICLE._TRAIT_.$lang;
			}
			else
			{
				$view->content->super_title = $back._TRAIT_._ADD_ARTICLE._TRAIT_.$lang;
			}
		}

		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

		// rtl
		if ($lmod->rtl($lang))
		{
			$view->content->tinymce->rtl = 1;
		}
		$view->render(TRUE);
	}

	/**
	 * Article param setting
	 *
	 * @param   integer $id_area Area ID
	 * @param   string $lang Language code
	 * @param   integer $id_page Page ID
	 * @param   string $module Module name
	 * @param   string $param Parameter value
	 * @return  void
	 */
	public function param(int $id_area, string $lang, int $id_page, string $module, string $param = '')
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'articles', $module));

		// get configurator
		$module = ucfirst($module).'_model';
		$mod = new $module('default');

		// build the form
		$fields = $mod->configurator($id_area, $lang, $id_page, $param);

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'configurator');
			if ($e)
			{
			    // handle POST data
			    $p = array();
			    if (isset($_POST['no_options']))
			    {
			        $result = array(0, 1);
			    }
			    else
			    {
                    // get the cases
                    $cases = explode('§', $_POST['options']);
                    foreach ($cases as $case)
                    {
                        $parts = explode('|', $case);
                        foreach ($parts as $i)
                        {
                            if (isset($_POST[$i]) && !empty($_POST[$i]))
                            {
                                $p[] = $_POST[$i];
                            }
                        }
                    }
                    $result = array(0, !empty($p));
                }

				// set message
				$msg = AdmUtils_helper::set_msg($result);
				if (!empty($p))
				{
				    $msg->update = array(
                        'element' => 'field',
                        'field' => 'param',
                        'value' => implode('|', $p)
                    );
				}
				$this->response($msg);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        $view = new X4View_core('modal');
        $view->title = _ARTICLE_PARAM_SETTING;
		// contents
		$view->content = new X4View_core('editor');

		// form builder
		$view->content->form = X4Form_helper::doform('configurator', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'configurator\')"');

		$view->render(TRUE);
	}

	/**
	 * Register New / Edit article data
	 *
	 * @access	private
	 * @param   stdClass $item Article
	 * @param   array	$_post _POST array
	 * @return  void
	 */
	private function editing($item, array $_post)
	{
		$msg = null;
		// check permission
		if ($item->id)
		{
			$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'articles', $item->id, 2);
		}

		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'bid' => $_post['bid'],
				'id_area' => $_post['id_area'],
				'lang' => $_post['lang'],
				'code_context' => $_post['code_context'],
				'category' => $_post['category'],
				'id_page' => isset($_post['id_page']) ? $_post['id_page'] : 0,
				'date_out' => (strlen($_post['date_out']) == 10) ? strtotime($_post['date_out']) : 0,

				'xkeys' => strtolower($_post['xkeys']),
				'name' => $_post['name'],
				'content' => $_post['content'],
				'ftext' => $_post['name'].' '.strip_tags($_post['content']),
				'js' => html_entity_decode($_post['js']),
				'excerpt' => (strstr($_post['content'], '<!--pagebreak-->') !== false) ? 1 : 0,
				'tags' => str_replace(', ', ',', $_post['tags']),

				'author' => $_post['author'],
				'module' => $_post['module'],
				'param' => $_post['param'],
				'id_editor' => $_SESSION['xuid'],

				'xon' => AUTOREFRESH
			);

			if (EDITOR_SCRIPTS)
			{
			    $post['js'] = html_entity_decode($_post['js']);
			}

			if (EDITOR_OPTIONS)
			{
			    $post['show_author'] = intval(isset($_post['show_author']));
				$post['show_date'] = intval(isset($_post['show_date']));
				$post['show_tags'] = intval(isset($_post['show_tags']));
				$post['show_actions'] = intval(isset($_post['show_actions']));
			}

			// adjust date_in value in case of set or update
			if ($item->id == 0 ||   // for new articles to avoid overriding
				(
				    isset($_post['date_in']) && // date_in was set by hand
				    $_post['date_in'] != date('Y-m-d', $_post['time_in']) // and the date is different from the past
				)
			    )
			{
				$post['date_in'] = (isset($_post['date_in']))
                    ? strtotime($_post['date_in']) + (date('G')*60 + date('i'))*60 + date('s')
                    : $_post['time_in'];
			}
			else
			{
                if ($_post['time_in'] < 90000)
                {
                    // fix for wrong time_in
                    $_post['time_in'] = strtotime('-1 week');
                }
				$post['date_in'] = $_post['time_in'];
			}

			// reset date_out for simple editing page content
			if (!ADVANCED_EDITING && $post['id_page'] && $post['code_context'] == 1)
			{
			    // force empty
			    $post['date_out'] = 0;
			}

			// insert article
			$mod = new Article_model();

			// check for context
			// if the code_context is changed we assign a new bid to the article
			// if the id page is changed we assign a new bid
			if (($_post['old_context'] > -1 && $_post['old_context'] != $_post['code_context']) || (isset($_post['id_page']) && $item->id_page != $_post['id_page']))
			{
				$post['bid'] = $mod->get_new_bid();
			}

			$result = $mod->insert($post);
			if (APC)
			{
				apcu_delete(SITE.'abid'.$post['id_area'].$_post['lang'].$_post['bid']);
				if (!empty($post['old_module']))
				{
					apcu_delete(SITE.'pageto'.$post['id_area'].$_post['lang'].$_post['old_module'].$_post['old_param']);
				}
				if (!empty($post['module']))
				{
					apcu_delete(SITE.'pageto'.$post['id_area'].$_post['lang'].$post['module'].$post['param']);
				}
			}

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// response
			if ($result[1])
			{
				$from = (!empty($_post['from']))
                    ? urldecode($_post['from'])
                    : $_SERVER['HTTP_REFERER'];

                $msg->update = array(
                    'element' => 'page',
                    'url' => $from
                );
			}
		}
		$this->response($msg);
	}

	/**
	 * Show article's history
	 * Show all versions of an article
	 * Versions are displayed in reverse chronological order
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string	$bid, unique ID for articles
	 * @return  void
	 */
	public function history(int $id_area, string $lang, string $bid)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('history', 'articles'));

		// get the page
		$page = $this->get_page('articles/history');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page), ['articles' => 'index/'.$id_area.'/'.$lang]);
		$view->actions = '';

		// content
		$view->content = new X4View_core('articles/history');
		$view->content->id_area = $id_area;
		$view->content->lang = $lang;
		$view->content->bid = $bid;

		// left
		$mod = new Article_model();
		$view->content->art = $mod->get_by_bid($id_area, $lang, $bid);
		$view->content->history = $mod->get_history($id_area, $bid);
		$view->render(TRUE);
	}

	/**
	 * Date settings form for time window of article's version (use Ajax)
	 *
	 * @param   integer $id Article ID
	 * @return  void
	 */
	public function setdate(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'history', 'articles'));

		// get object
		$mod = new Article_model();
		$item = $mod->get_by_id($id, 'articles', 'id, date_in, date_out');

		// build the form
        $form_fields = new X4Form_core('article/article_date');
		$form_fields->item = $item;

		// get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->setting_date($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        $view = new X4View_core('modal');
        $view->title = _SET_DATE;
		// content
		$view->content = new X4View_core('editor');

		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

		$view->render(TRUE);
	}

	/**
	 * Register article's time window
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function setting_date(array $_post)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'articles', $_post['id'], 2);
		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'date_in' => strtotime($_post['date_in']),
				'date_out' => (empty($_post['date_out'])) ? 0 : strtotime($_post['date_out'])
			);

			// do action
			$mod = new Article_model();
			$result = $mod->update($_post['id'], $post);

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
	 * Delete Article form (use Ajax)
	 * Delete all versions of an article
	 *
	 * @param   $bid string, unique ID for articles
	 * @return  void
	 */
	public function delete(int $id_area, string $lang, string $bid)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'articles'));

		// get object
		$mod = new Article_model();
		$item = $mod->get_by_bid($id_area, $lang, $bid);

		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $bid,
			'name' => 'bid'
		);

		// if submitted
		if (X4Route_core::$post)
		{
			$this->deleting($item);
			die;
		}
        $view = new X4View_core('modal');
        $view->title = _DELETE_ARTICLE;
		// contents
		$view->content = new X4View_core('delete');
		$view->content->item = $item->name;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(TRUE);
	}

	/**
	 * Delete article
	 * Delete all versions of an article
	 *
	 * @access	private
	 * @param   stdClass $item
	 * @return  void
	 */
	private function deleting(stdClass $item)
	{
		$msg = null;
		// check permissions on articles
		$mod = new Article_model();
		$artt = $mod->get_all_by_bid($item->id_area, $item->lang, $item->bid);
		foreach ($artt as $i)
		{
			if (is_null($msg))
            {
				$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'articles', $i->id, 4);
            }
		}

		// check permissions on pages
		$smod = new Section_model();
		$pp = $smod->get_pages_by_bid($item->bid);
		foreach ($pp as $i)
		{
			if (is_null($msg))
            {
				$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'pages', $i->id, 3);
            }
		}

		if (is_null($msg))
		{
			// do action
			$result = $mod->delete_by_bid($item->id_area, $item->lang, $item->bid);

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
	 * Delete a version of an Article form (use Ajax)
	 *
	 * @param   integer $id Article id
	 * @return  void
	 */
	public function delete_version(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'articles'));

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
			$this->deleting_version($id);
			die;
		}

		// get object
		$mod = new Article_model();
		$item = $mod->get_by_id($id, 'articles', 'name, updated');

        $view = new X4View_core('modal');
        $view->title = _DELETE_ARTICLE;
		// contents
		$view->content = new X4View_core('delete');
		$view->content->item = $item->name.' '.$item->updated;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(TRUE);
	}

	/**
	 * Delete article's version
	 *
	 * @access	private
	 * @param   integer $id article ID
	 * @param   string 	$bid BID code
	 * @return  void
	 */
	private function deleting_version(int $id)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'articles', $id, 4);

		if (is_null($msg))
		{
			// do action
			$mod = new Article_model();
			$result = $mod->delete($id);

			// clear useless permissions
			if ($result[1])
			{
				$perm = new Permission_model();
				$perm->deleting_by_what('articles', $id);
			}

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// set what update
			if ($result[1])
			{
				$msg->update = array(
					'element' => 'page',
					$_SERVER['HTTP_REFERER']
				);
			}
		}
		$this->response($msg);
	}
}
