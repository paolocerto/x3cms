<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
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

    /**
	 * Check hostname
	 *
	 * @return  string
	 */
	private function chk_gethostname()
	{
		return (function_exists('gethostname'))
            ? gethostname()
            : 'Unknown';
	}

    /**
	 * Info detail
	 *
     * @param   string  $case
     * @param   string  $tab
	 * @return  void
	 */
	public function detail(string $case = 'default', int $tab = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('info'));

		// get page
		$page = $this->get_page('info');
		$mod = new Category_model();

		if ($tab)
		{
			$view = new X4View_core('tabber');
            $view->tabber_name = 'tabber';
			$view->title = _SITE_INFO;

			$hn = $this->chk_gethostname();

			$view->tabs = array('default' => array(_INFO_SERVER.' '.$hn, 'info/detail/default'),
					'apache' => array(apache_get_version(), 'info/detail/apache'),
					'mysql' => array('MySQL '.$mod->get_attribute('SERVER_VERSION'), 'info/detail/mysql'),
					'php' => array('PHP '.phpversion(), 'info/detail/php'),
				);
			$view->on = $case;
			$view->tabber_container = 'tdown';

			$view->down = new X4View_core('sites/info');
			$view->down->page = $page;
			$view->down->case = $case;

			if ($case == 'mysql')
			{
				$view->down->sinfo = $mod->get_attribute('SERVER_INFO');
			}
		}
		else
		{
			$view = new X4View_core('container');

			$view = new X4View_core('sites/info');
			$view->page = $page;
			$view->case = $case;

			if ($case == 'mysql')
			{
				$view->sinfo = $mod->get_attribute('SERVER_INFO');
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
