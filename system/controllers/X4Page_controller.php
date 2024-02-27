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
	protected $admitted = array('login', 'login/recovery', 'login/reset', 'signup');

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
	public function plugin($module, $control = '', $a = '', $b = '', $c = '', $d = '')
	{
		$mod = new X4Plugin_model();
		if ($mod->exists($module, $this->site->area->id) && file_exists(PATH.'plugins/'.$module.'/'.$module.'_plugin.php'))
		{
			X4Core_core::auto_load($module.'/'.$module.'_plugin');
			$plugin = ucfirst($module.'_plugin');
			$m = new $plugin($this->site);
			$m->plugin($control, $a, $b, $c, $d);
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
				if (file_exists(APATH.'controllers/'.X4Route_core::$area.'/login.php'))
				{
					X4Utils_helper::logged($page->id_area, X4Route_core::$area.'/login');
				}
				else
				{
					X4Utils_helper::logged($page->id_area, 'public/home');
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
}
