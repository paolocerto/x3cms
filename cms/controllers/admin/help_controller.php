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
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::logged();
	}

	/**
	 * Default method
	 */
	public function _default() : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('help'));

		// get page
		$page = $this->get_page('help');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = AdmUtils_helper::link(
            'memo',
            'help:'.$page->lang,
            [],
            _MEMO
        );

		$view->content = new X4View_core('tabber');
		$view->content->title = $page->icon.' '._HELP;

		$view->content->tabs = array(
			_HELP_ON_SITE => ['url', BASE_URL.'help/local'],
			_HELP_ON_LINE => ['url', BASE_URL.'help/online/'.$page->lang]
		);

		$view->render(true);
	}

	/**
	 * Help on site
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
			$view->render(true);
        }
		else
        {
			return $view;
        }
	}

	/**
	 * Help on line
	 */
	public function online(string $lang) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('home'));

		// content
		$view = new X4View_core('container_two');

		// get left content
		$lcontent = @file_get_contents('https://www.x3cms.net/'.$lang.'/help2/home/'.$this->remotize());

		// get right data: the index of the help on line
		$rcontent = @file_get_contents('https://www.x3cms.net/'.$lang.'/help2/index/'.$this->remotize());

		// return contents or error message
		$view->right = (empty($rcontent))
			? '<p>'._UNABLE_TO_CONNECT.'</p>'
			: '<div id="index">'.$rcontent.'</div>';

		$src = array('src="/cms');
		$rpl = array('src="https://www.x3cms.net/cms');

		$view->left = (empty($lcontent))
			? '<p>'._UNABLE_TO_CONNECT.'</p>'
			: '<div id="help">'.str_replace($src, $rpl, $lcontent).'</div>';

		$view->render(true);
	}

	/**
	 * Build the remote suffix
	 */
	private function remotize() : string
	{
		return str_replace('/', '-', BASE_URL.'help/hol/').'/1';
	}

	/**
	 * Help on line
	 */
	public function hol(string $lang, string $area, string $page, string $suffix = '') : void
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

		$view->render(true);
	}

	/**
	 * Help actions
	 */
	public function actions(string $lang) : string
	{
		return '';
	}

}
