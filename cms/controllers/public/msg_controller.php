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
 * Controller for Msg
 *
 * @package X3CMS
 */
class Msg_controller extends X4Cms_controller
{
	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Empty message can be called when happens an unknnown error
	 *
	 * @param   string	$what Dictionary what
	 * @param   string	$xkey Dictionary key
	 * @return  void
	 */
	public function empty_msg(string $what = 'msg', string $xkey = '')
	{
		// check the key
		$xkey = (empty($xkey))
			? '_UNKNOW_ERROR'
			: $xkey;

		// set the session message
		$_SESSION['msg'] = $this->dict->get_word($xkey, $what);

		$view = new X4View_core('empty');
		$view->render(true);
	}

	/**
	 * Display system messages
	 *
	 * @param   string	$what Dictionary section
	 * @param   string	$personal_msg Alternative message
	 * @return  void
	 */
	public function message(string $what = '', string $personal_msg = '')
	{
		// load dictionary
		$this->dict->get_words();

		// get page
		$page = $this->get_page('msg');
		$view = new X4View_core(X4Theme_helper::set_tpl($page->tpl));
		$view->page = $page;

		// get menus
		$view->navbar = array($this->site->get_bredcrumb($page));
		$view->menus = $this->site->get_menus($page->id_area);

		// content
		$view->args = X4Route_core::$args;

		$qs = (!empty(X4Route_core::$query_string))
		    ? X4Route_core::get_query_string()
		    : array();

		// set title
		if (isset($qs['ok']))
		{
		    switch($qs['ok'])
		    {
		    case 1:
		        $title = _CONGRATULATIONS;
		        break;
		    case 2:
		        $title = _MSG_OK;
		        break;
		    }
		}
		else
		{
			$title = _WARNING;
		}

		//check personal message
		$checked_msg = strip_tags(urldecode($personal_msg));

		// get message
		$msg = (empty($personal_msg) || empty($checked_msg))
			? $this->dict->get_message($title, strtoupper($what), 'msg')
			: $this->dict->build_message($title, $checked_msg);

		$sections = $this->site->get_sections($page->id);

		$sections[1]['a'] = array($msg);
		$view->sections = $sections;
		$view->render(true);
	}

	/**
	 * Override __call to avoid circular calls
	 */
	public function __call(string $method, array $args)
	{
		$this->empty_msg();
	}
}
