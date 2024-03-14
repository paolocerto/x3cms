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
 * Login Controller for private area
 * As default this controller use the users table
 * If you want to use another table you must change the controller behaviour
 *
 * @package X3CMS
 */
class Login_controller extends X4Cms_controller
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Login page
	 */
	public function _default() : void
	{
        // THIS IS TO USE PAGE CONTENTS WITHOUT PRIVATE CHECK

        // get page
		$page = $this->site->get_page(str_replace('_', '-', 'login'));
        if (!is_object($page) || !isset($page->id))
        {
            header('Location: '.BASE_URL.'msg/message/_page_not_found');
            die;
        }

        // load dictionary
		$this->dict->get_words();

        $view = new X4View_core(X4Theme_helper::set_tpl($page->tpl));
        $view->page = $page;
        $view->args = X4Route_core::$args;

        // get menus
        $view->menus = array();
        $view->navbar = array($this->site->get_bredcrumb($page));

        // get sections
        $view->sections =  $this->site->get_sections($page->id);
        $view->render(true);
    }
}