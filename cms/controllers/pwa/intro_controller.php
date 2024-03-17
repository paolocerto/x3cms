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
 * Intro Controller for pwa
 * This way you can show a public page inside the private area
 *
 * @package X3CMS
 */
class Intro_controller extends X4Cms_controller
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
	public function _default(string $start = '') : void
	{
        // THIS IS TO USE PAGE CONTENTS WITHOUT PRIVATE CHECK

        // get home page
        // this is a trick to not use a real page
		$page = $this->site->get_page('home');

        if (!is_object($page) || !isset($page->id))
        {
            header('Location: '.BASE_URL.'msg/message/_page_not_found');
            die;
        }

        $this->dict->get_wordarray(array('pwa'));

        $view = new X4View_core(X4Theme_helper::set_tpl($page->tpl));
        $view->page = $page;
        $view->args = X4Route_core::$args;

        $content = new X4View_core('public/x3players_pwa', 'x3players');
        $content->login = ($start == 'login')
            ? 1
            : 0;

        $article = $this->dict->build_message('', '');
        $article->replace_html($content->__toString());

		$sections = $this->site->get_sections($page->id);

		$sections[1]['a'] = array($article);
		$view->sections = $sections;
		$view->render(true);
    }
}