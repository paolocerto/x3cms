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
 * Controller for Admin area Help
 *
 * @package X3CMS
 */
class Help_controller extends X3ui_controller
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
	 * Default method
	 *
	 * @return  void
	 */
	public function _default()
	{
		// load dictionaries
		$this->dict->get_wordarray(array('help'));

		// get page
		$page = $this->get_page('help');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = '';

		$view->content = new X4View_core('tabber');

		$view->content->title = _HELP;

		$view->content->tabs = array(
			_HELP_ON_SITE => ['url', BASE_URL.'help/local'],
			_HELP_ON_LINE => ['url', BASE_URL.'help/online/'.$page->lang]
		);

		$view->render(TRUE);
	}

	/**
	 * Help on site
	 *
	 * @param   boolean  $render Switch the rendering
	 * @return  void
	 */
	public function local(bool $render = true)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('help'));

		// get page
		$page = $this->get_page('help');

		$view = new X4View_core('sites/help');
		$view->page = $page;

		$mod = new Help_model();
		$view->items = $mod->get_subpages($page);

		if ($render)
        {
			$view->render(TRUE);
        }
		else
        {
			return $view;
        }
	}

	/**
	 * Help on line
	 *
	 * @param   string  $lang Language code
	 * @return  void
	 */
	public function online(string $lang)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('home'));

		// content
		$view = new X4View_core('container_two');

		// get left content
		$lcontent = @file_get_contents('http://www.x3cms.net/'.$lang.'/help/home/'.$this->remotize());

		// get right data: the index of the help on line
		$rcontent = @file_get_contents('http://www.x3cms.net/'.$lang.'/help/index/'.$this->remotize());

		// return contents or error message
		$view->right = (empty($rcontent))
			? '<p>'._UNABLE_TO_CONNECT.'</p>'
			: '<div id="index">'.$rcontent.'</div>';

		$src = array('src="/cms');
		$rpl = array('src="http://www.x3cms.net/cms');

		$view->left = (empty($lcontent))
			? '<p>'._UNABLE_TO_CONNECT.'</p>'
			: '<div id="help">'.str_replace($src, $rpl, $lcontent).'</div>';

		$view->render(TRUE);
	}

	/**
	 * Build the remote suffix
	 *
	 * @param   string  $lang Language code
	 * @param   string  $page Page URL
	 * @return  void
	 */
	private function remotize()
	{
		return str_replace('/', '-', BASE_URL.'help/hol/').'/1';
	}

	/**
	 * Help on line
	 *
	 * @param   string  $lang
     * @param   string  $area
     * @param   string  $url Remote URL
     * @param   string  $suffix
	 * @return  void
	 */
	public function hol(string $lang, string $area, string $page, string $suffix = '')
	{
		// load dictionaries
		$this->dict->get_wordarray(array('home'));

		// left
		$view = new X4View_core('container');

		// get remote contents
		$content = @file_get_contents('https://www.x3cms.net/'.$lang.'/help/'.$page.'/'.$this->remotize());

		$src = array('src="/cms');
		$rpl = array('src="https://www.x3cms.net/cms');

		// return contents or error message
		$view->content = (empty($content))
			? '<p>'._UNABLE_TO_CONNECT.'</p>'
			: '<div id="help">'.str_replace($src, $rpl, $content).'</div>';

		$view->render(TRUE);
	}

	/**
	 * Help filter
	 *
	 * @return  void
	 */
	public function filter(string $lang)
	{
		echo '';
	}
}
