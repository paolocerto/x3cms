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
 * Controller for Admin area Home
 *
 * @package X3CMS
 */
class Home_controller extends X3ui_controller
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
	 * Redirect to home method
	 */
	public function _default() : void
	{
		$this->start();
	}

	/**
	 * Reload to home method
	 */
	public function redirect(string $url) : void
	{
		$page = $this->get_page('home');
		$view = new X4View_core(X4Theme_helper::set_tpl($page->tpl));
		$view->page = $page;

		$view = new X4View_core('loading');
		$view->location = urldecode($this->site->data->domain.'/'.$url);
		$view->render(true);
	}

	/**
	 * Admin area home page
	 * This page displays Notices and Bookmarks
	 */
	public function start(string $start_page = 'home§dashboard') : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('home'));
        $qs = X4Route_core::get_query_string();

		// get page
		$page = $this->get_page('home');
		$view = new X4View_core(X4Theme_helper::set_tpl('x3ui'));
		$view->page = $page;

        $view->menus = $this->site->get_menus();

        $view->start_page = BASE_URL.str_replace('§', '/', urldecode($start_page)).'?'.http_build_query($qs);

		// languages
		$mod = new Language_model();
		$view->langs = $mod->get_alanguages($page->id_area);
		$view->render(true);
	}

	/**
	 * Admin area dashboard
	 * This page displays Notices and widgets
	 */
	public function dashboard() : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('widgets', 'home'));

		// get page
		$page = $this->get_page('home');

        // contents
		$view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = AdminUtils_helper::link(
            'memo',
            'home:'.$page->lang,
            [],
            _MEMO
        );

		$view->content = new X4View_core('home');
		// notices
		$view->content->notices = (NOTICES) ? $this->get_notices($page->lang) : '';
		// widgets
		$mod = new Widget_model();
		$view->content->widgets = $mod->widgets();

		$view->render(true);
	}

    /**
	 * Admin server load
	 */
	public function load() : void
	{
		$load = X4System_helper::getServerLoad();
		echo round($load, 2).'%';
	}

	/**
	 * Admin area menu
	 */
	public function menu() : void
	{
        $this->dict->get_wordarray(array('menus'));

        $view = new X4View_core('modal');
        $view->title = _MENUS;
        $view->wide = 'md:inset-x-6 lg:w-2/3 xl:w-2/3';
        $view->away = true;

		// content
		$view->content = new X4View_core('menu');
		$view->content->menus = $this->site->get_menus();
        $view->content->bookmarks = $this->site->get_bookmarks($_SESSION['xuid'], $this->site->area->lang);
		$view->render(true);
	}

	/**
	 * Get notices from x3cms.net
	 */
	private function get_notices(string $lang) : string
	{
		$contextOptions = [
			'ssl' => [
				'verify_peer' => false,
				"verify_peer_name"=>false,
			],
		];
		$context = stream_context_create($contextOptions);

		$url = 'https://x3cms.net/plugin/x3notices/notices/'.urlencode($this->site->data->version).'/'.md5($this->site->data->xcode).'/'.$lang;

		// get remote contents
		$content = @file_get_contents($url, false, $context);

		// return contents or error message
		return (empty($content))
			? '<p>'._UNABLE_TO_CONNECT.'</p>'
			: $content;
	}
}
