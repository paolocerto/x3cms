<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */

/**
 * X4page controller extends X4Cms_controllerfor pages of the site
 *
 * @package		X3CMS
 */
class X4Page_controller extends X4Cms_controller 
{
	/**
	 * Admitted URLs without login
	 */
	protected $admitted = array('login', 'login/recovery', 'login/reset');
	
	/**
	 * Constructor
	 * Check the site status
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$url = (MULTILANGUAGE)
		    ? X4Route_core::$lang.'/offline'
		    : 'offline';
		    
		if (isset($_SESSION['uid']))
		{
		    $url = str_replace('offline', 'logout', $url);
		}
		    
		X4Utils_helper::offline($this->site->site->xon, BASE_URL.$url);
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
	public function call_plugin($module, $id_area = '', $control = '', $a = '', $b = '', $c = '', $d = '')
	{
		$mod = new X4Plugin_model();
		if ($mod->exists($module, $id_area) && file_exists(PATH.'plugins/'.$module.'/'.$module.'_plugin.php')) 
		{
			X4Core_core::auto_load($module.'/'.$module.'_plugin');
			$plugin = ucfirst($module.'_plugin');
			$m = new $plugin($this->site);
			$m->call_plugin($id_area, $control, $a, $b, $c, $d);
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
		$b0 = 255;
		if ($bg0 == '_default')
		{
			$bg0 = $b0;
		}
		
		X4Form_helper::captcha(5, 'whitrabt.ttf', array($bg0, $bg1, $bg2));
	}
	
	/**
	 * Generic page override __call
	 *
	 * @param string	url/controller name
	 * @param array		array of arguments
	 * @return void
	 */
	public function __call($url, $args)
	{
		// dict
		$this->dict->get_words();
		// get page data
		$page = $this->site->get_page($url);
		if ($page) 
		{
			// check login if area is private 
			if ($this->site->area['private'] && !in_array($url, $this->admitted)) 
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
			$view = new X4View_core(X4Utils_helper::set_tpl($page->tpl));
			$view->page = $page;
			$view->args = $args;
			
			// get menus
			$view->menus = $this->site->get_menus($page->id_area);
			$view->navbar = array($this->site->get_bredcrumb($page));
			
			// get sections
			$view->sections = $this->site->get_sections($page->id);
			$view->render(true);
		}
		else 
		{
			// check for redirects
			$url = X4Route_core::get_uri();
			$mod = new X4Plugin_model();
			$redirect = $mod->check_redirect(array('Page_model'), $url);

			if (!$redirect)
			{
                header('HTTP/1.0 404 Not Found');
                header('Location: '.BASE_URL.'msg/message/_page_not_found');
			}
			else
			{
			// redirect to
			    header('Location: '.$this->site->site->domain.'/'.$redirect->url, true, $redirect->redirect_code);
			}
		}
	}
}
