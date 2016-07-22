<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
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
		$this->detail('default', 1);
	}
	
	private function chk_gethostname()
	{
		if (function_exists('gethostname'))
			return gethostname();
		else
			return 'Unknown';
	}
	
	public function detail($case = 'default', $tab = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('info'));
		
		// get page
		$page = $this->get_page('info');
		$mod = new Category_model();
		
		if ($tab)
		{
			$view = new X4View_core('tabber');
			$view->title = _SITE_INFO;
			
			$hn = $this->chk_gethostname();
			
			$view->tabs = array('default' => array(_INFO_SERVER.' '.$hn, 'info/detail/default'),
					'apache' => array(apache_get_version(), 'info/detail/apache'),
					'mysql' => array('MySQL '.$mod->get_attribute('SERVER_VERSION'), 'info/detail/mysql'),
					'php' => array('PHP '.phpversion(), 'info/detail/php'),
				);
			$view->on = $case;
			
			$view->down = new X4View_core('container');
			$view->tabber_container = 'tdown';
			
			$view->down->content = new X4View_core('sites/info');
			$view->down->content->page = $page;
			$view->down->content->case = $case;
			
			if ($case == 'mysql')
			{
				$view->down->content->sinfo = $mod->get_attribute('SERVER_INFO');
			}
		}
		else
		{
			$view = new X4View_core('container');
			
			$view->content = new X4View_core('sites/info');
			$view->content->page = $page;
			$view->content->case = $case;
			
			if ($case == 'mysql')
			{
				$view->content->sinfo = $mod->get_attribute('SERVER_INFO');
			}
		}
		
		$view->render(TRUE);
	}
	
	/**
	 * Info filter
	 *
	 * @return  void
	 */
	public function filter()
	{
		echo '';
	}
}
