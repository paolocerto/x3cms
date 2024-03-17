<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X4WEBAPP
 */

/**
 * X4page controller extends X4Cms_controller for pages of the site
 *
 * @package		X3CMS
 */
class X4Page_controller extends X4Cms_controller
{
	/**
	 * Admitted URLs without login
	 */
	protected $admitted = array('login', 'recovery', 'signin', 'msg', 'intro');

	/**
	 * Constructor
	 * Check the site status
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();

		$url = 'offline';
		if (isset($_SESSION['uid']))
		{
		    $url = str_replace('offline', 'logout', $url);
		}

		X4Utils_helper::offline($this->site->data->xon, BASE_URL.$url);
	}

	/**
	 * Call home page if method is empty
	 *
	 * @return void
	 */
	public function _default()
	{
		$this->__call('home', array());
	}

	/**
	 * Call the specified plugin method
	 *
	 * @param string	$module plugin name
	 * @param integer	$id_area area ID
	 * @param string	$control method name
	 * @param mixed		$a unspecified variable
	 * @param mixed		$b unspecified variable
	 * @param mixed		$c unspecified variable
	 * @param mixed		$d unspecified variable
	 * @return void
	 */
	public function plugin(string $module, string $control = '', $a = '', $b = '', $c = '', $d = '')
	{
		$mod = new X4Plugin_model();

		if ($mod->exists($module, $this->site->area->id) && file_exists(PATH.'plugins/'.$module.'/'.$module.'_plugin.php'))
		{
			X4Core_core::auto_load($module.'/'.$module.'_plugin');
			$plugin_name = ucfirst($module.'_plugin');
			$plugin = new $plugin_name($this->site);
			$plugin->plugin($control, $a, $b, $c, $d);
		}
		else
		{
		    header('HTTP/1.0 404 Not Found');
		    header('Location: '.BASE_URL.'msg/message/_page_not_found');
		}
	}

	/**
	 * Build captcha image
	 *
	 * @param integer background
	 * @return image file
	 */
	public function captcha($bg0 = 255, $bg1 = 255, $bg2 = 255)
	{
		X4Form_helper::captcha(5, 'whitrabt.ttf', array($bg0, $bg1, $bg2));
	}

	/**
	 * Generic page override __call
	 *
	 * @param string	url/controller name
	 * @param array		array of arguments
	 * @return void
	 */
	public function __call(string $method, array $args)
	{
        // is the area active?
        if (!$this->site->area->xon)
        {
            // redirect to public
            header('Location: /'.$this->site->area->lang.'/public/home');
        }
		// dict
		$this->dict->get_words();
		// get page data
		$page = $this->site->get_page($method);
		if ($page)
		{
		    if (!isset($_SESSION['xuid']) && !empty($page->redirect))
            {
                // redirect to
                $location = (substr($page->redirect, 0, 4) == 'http')
                    ? $page->redirect
                    : BASE_URL.$page->redirect;
                header('Location: '.$location, true, $page->redirect_code);
            }

			// check login if area is private
			if ($this->site->area->private && !in_array($method, $this->admitted))
            {
                $login_page = $this->site->get_page('login');
                if ($login_page !== false && file_exists(APATH.'controllers/'.$this->site->area->folder.'/login.php'))
                {
                    X4Utils_helper::logged($page->id_area, X4Route_core::$area.'/login');
                }
                else
                {
                    // get default public for this site
                    $id_area = $this->site->data->default_area;
                    X4Utils_helper::logged($page->id_area, X4Route_core::get_area_by_id($id_area).'/home');
                }
            }

			// set view
			$view = new X4View_core(X4Theme_helper::set_tpl($page->tpl));
			$view->page = $page;
			$view->args = $args;

			// get menus
			$view->menus = $this->site->get_menus();
			$view->navbar = array($this->site->get_bredcrumb($page));

			// get sections
			$view->sections = $this->site->get_sections($page->id);
			$view->render(true);
		}
		else
		{
            header('HTTP/1.0 404 Not Found');
            header('Location: '.BASE_URL.'msg/message/_page_not_found');
		}
	}

    /**
     * Load controller
     */
    private function loadController()
    {
        // load dictionary
		$this->dict->get_words();

		// get page
		$page = $this->get_page('msg');
		$view = new X4View_core(X4Theme_helper::set_tpl($page->tpl));
		$view->page = $page;

		// get menus
		$view->navbar = array($this->site->get_bredcrumb($page));
		$view->menus = $this->site->get_menus();

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
            case 0:
                $title = _WARNING;
                break;
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
}
