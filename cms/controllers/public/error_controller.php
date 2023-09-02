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
 * Error controller
 *
 * @package X3CMS
 */
class Error_controller extends X4Cms_controller
{
	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::offline($this->site->site->xon, BASE_URL.'msg/message/offline');
	}

	/**
	 * Display error message
	 *
	 * @param   string	$what Dictionary what
	 * @return  void
	 */
	public function message(string $what = '')
	{
		// load dictionary
		$this->dict->get_words();

		// get page
		$page = $this->get_page('msg');
		$view = new X4View_core(X4Theme_helper::set_tpl($page->tpl));
		$view->site = $this->site;
		$view->page = $page;

		// reset base URL
		X4Utils_helper::set_base_url(ROOT.LL.X4Route_core::$area.'/'.DC);

		// get menu
		$view->menus = $this->site->get_menus($page->id_area);

		// get message
		$view->sections = array($this->dict->get_message(_WARNING, strtoupper($what), 'msg'));
		$view->render(TRUE);
	}
}
