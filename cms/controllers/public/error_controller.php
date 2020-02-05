<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
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
	public function message($what = '')
	{
		// load dictionary
		$this->dict->get_words();
		
		// get page
		$page = $this->get_page('msg');
		$view = new X4View_core(AdmUtils_helper::set_tpl($page->tpl, $this->site->area->theme));
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
