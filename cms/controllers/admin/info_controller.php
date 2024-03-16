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
 * Controller for Admin area Info
 *
 * @package X3CMS
 */
class Info_controller extends X3ui_controller
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
		$this->dict->get_wordarray(array('info'));

		// get page
		$page = $this->get_page('info');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = AdmUtils_helper::link(
            'memo',
            'inf0:'.$page->lang,
            [],
            _MEMO
        );

        $view->content = new X4View_core('tabber');
        $view->content->title = $page->icon.' '._SITE_INFO;
        $view->content->tabs = array(
            'Default' => ['view', 'sites/info'],
            'Apache' => ['view', 'sites/apache'],
            'Mysql' => ['view', 'sites/mysql'],
            'Php' => ['view', 'sites/php'],
        );
		$view->render(true);
	}

    /**
	 * Check hostname
	 */
	private function chk_gethostname() : string
	{
		return (function_exists('gethostname'))
            ? gethostname()
            : 'Unknown';
	}

}
