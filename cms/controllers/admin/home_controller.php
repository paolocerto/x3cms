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
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::logged();
	}

	/**
	 * Redirect to home method
	 *
	 * @return  void
	 */
	public function _default()
	{
		$this->start();
	}

	/**
	 * Reload to home method
	 *
     * @param   string $url
	 * @return  void
	 */
	public function redirect(string $url)
	{
		$page = $this->get_page('home');
		$view = new X4View_core(X4Theme_helper::set_tpl($page->tpl));
		$view->page = $page;

		$view = new X4View_core('loading');
		$view->location = urldecode($this->site->site->domain.'/'.$url);
		$view->render(true);
	}

	/**
	 * Admin area home page
	 * This page displays Notices and Bookmarks
	 *
	 * @param   string  $start_page URL of first page to load
	 * @return  void
	 */
	public function start(string $start_page = 'home§dashboard')
	{
		// load dictionaries
		$this->dict->get_wordarray(array('home'));
        $qs = X4Route_core::get_query_string();

		// get page
		$page = $this->get_page('home');
		$view = new X4View_core(X4Theme_helper::set_tpl('x3ui'));
		$view->page = $page;

        $view->menus = $this->site->get_menus(1);

        $view->start_page = BASE_URL.str_replace('§', '/', urldecode($start_page)).'?'.http_build_query($qs);

		// languages
		$mod = new Language_model();
		$view->langs = $mod->get_alanguages($page->id_area);
		$view->render(true);
	}

	/**
	 * Admin area dashboard
	 * This page displays Notices and Bookmarks
	 *
	 * @return  void
	 */
	public function dashboard()
	{
		// load dictionaries
		$this->dict->get_wordarray(array('widgets', 'home'));

		// get page
		$page = $this->get_page('home');

        // contents
		$view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = '';

		$view->content = new X4View_core('home');
		// notices
		$view->content->notices = (NOTICES) ? $this->get_notices($page->lang) : '';
		// widgets
		$mod = new Widget_model();
		$view->content->widgets = $mod->widgets();

		$view->render(true);
	}

	/**
	 * Admin area menu
	 *
	 * @return  void
	 */
	public function menu()
	{
		// content
		$view = new X4View_core('menu');
		$view->menus = $this->site->get_menus(1);
		$view->render(true);
	}

	/**
	 * Get notices from x3cms.net
	 *
	 * @param   string  $lang Language code
	 * @return  string
	 */
	private function get_notices(string $lang)
	{
		$contextOptions = [
			'ssl' => [
				'verify_peer' => false,
				"verify_peer_name"=>false,
			],
		];
		$context = stream_context_create($contextOptions);

		$url = 'https://www.x3cms.net/en/public/call_plugin/x3notices/2/notices/'.urlencode($this->site->site->version).'/'.md5($this->site->site->xcode).'/'.$lang;

		// get remote contents
		$content = @file_get_contents($url, false, $context);

		// return contents or error message
		return (empty($content))
			? '<p>'._UNABLE_TO_CONNECT.'</p>'
			: $content;
	}
}
