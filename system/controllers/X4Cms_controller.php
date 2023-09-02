<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */

/**
 * X4Cms specialized controller
 *
 * @package		X3CMS
 */
class X4Cms_controller extends X4Controller_core
{
	/**
	 * @var dictionary model
	 */
	protected $dict;

	/**
	 * @var site model
	 */
	protected $site;

	/**
	 * Constructor
	 * Set site and dict, define BASE_URL, THEME_URL and X4WebApp version
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->site = new X4Site_model();
		$this->dict = new X4Dict_model(X4Route_core::$area, X4Route_core::$lang);

		// set the lang if required
		$lang = (MULTILANGUAGE)
		    ? X4Route_core::$lang.'/'
		    : '';

		// to avoid double define
		if (!defined('BASE_URL'))
		{
			// define BASE_URL
			if (X4Route_core::$area != 'public')
			{
				define('BASE_URL', ROOT.$lang.X4Route_core::$area.'/');
			}
			else
			{
				define('BASE_URL', ROOT.$lang);
			}

			define('RTL', $this->site->site->rtl);
			define('THEME_URL', ROOT.'themes/'.$this->site->area->theme.'/');
			define('X4VERSION', 0.5);
			define('X3VERSION', $this->site->site->version);
		}
	}

	/**
	 * Get the page information by url
	 *
	 * @param	string	$url url/controller name
	 * @return	object	page object
	 */
	public function get_page(string $url)
	{
		$page = $this->site->get_page(str_replace('_', '-', $url));
		if ($page)
		{
			// return page object
			return $page;
		}
		else
		{
			// page not found
			$this->__call('', array());
		}
	}

	/**
	 * Action for undefined method
	 * redirect to msg
     *
	 * @param   string  method name
	 * @param   array   arguments
	 * @return  void
	 */
	public function __call(string $method, array $args)
	{
		header('Location: '.BASE_URL.'msg/message/_page_not_found');
		die;
	}
}

/**
 * Interface for X3CMS plugins
 */
interface X3plugin_controller
{
	/**
	 * Default method
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang Language code
	 * @param   integer $pp Pagination index
	 * @param   string  $str search string
	 * @return  void
	 */
	public function mod(int $id_area = 0, string $lang = '', int $pp = 0, string $str = '');

}
