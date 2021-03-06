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
 * Controller for Msg
 * 
 * @package X3CMS
 */
class Msg_controller extends X4Cms_controller
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
	 * Empty message can be called when happens an unknnown error 
	 *
	 * @param   string	$what Dictionary what
	 * @param   string	$xkey Dictionary key
	 * @return  void
	 */
	public function empty_msg($what = 'msg', $xkey = '')
	{
		// check the key
		$xkey = (empty($xkey)) 
			? '_UNKNOW_ERROR' 
			: $xkey;
			
		// set the session message
		$_SESSION['msg'] = $this->dict->get_word($xkey, $what);
		
		$view = new X4View_core('empty');
		$view->render(TRUE);
	}
	
	/**
	 * Display system messages
	 *
	 * @param   string	$what Dictionary what
	 * @return  void
	 */
	public function message($what = '')
	{
		// load global dictionary
		$this->dict->get_words();
		
		// get page
		$page = $this->get_page('msg');
		$view = new X4View_core(X4Utils_helper::set_tpl($page->tpl));
		$view->page = $page;
		
		// get menus
		$view->menus = $this->site->get_menus($page->id_area);
		$view->navbar = array($this->site->get_bredcrumb($page));
		
		// content
		$view->args = X4Route_core::$args;
		$view->content = new X4View_core('msg');
		$view->content->title = _WARNING;
		
		// load the message
		$view->content->msg = $this->dict->get_word($what, 'msg');
		$view->render(TRUE);
	}
	
	/**
	 * Override __call to avoid circular calls
	 */
	public function __call($method, $arguments)
	{
		$this->empty_msg();
	}
}
