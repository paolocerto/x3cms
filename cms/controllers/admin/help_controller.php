<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
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
		// load dictionaries
		$this->dict->get_wordarray(array('help'));
		
		// get page
		$page = $this->get_page('help');
		
		$view = new X4View_core('tabber');
		$view->title = _HELP;
		$view->on = 'local';
		$view->tabs = array(
			'local' => array(_HELP_ON_SITE, BASE_URL.'help/local'),
			'online' => array(_HELP_ON_LINE, BASE_URL.'help/online/'.$page->lang)
		);
		
		$view->down = $this->local(false);
		$view->tabber_container = 'tdown';
		
		$view->render(TRUE);
	}
	
	/**
	 * Help on site
	 *
	 * @param   boolean  $render Switch the rendering
	 * @return  void
	 */
	public function local($render = true)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('help'));
		
		// get page
		$page = $this->get_page('help');
		
		$view = new X4View_core('container');
		
		$view->content = new X4View_core('sites/help');
		$view->content->page = $page;
		
		$mod = new Help_model();
		$view->content->items = $mod->get_subpages($page);
		
		if ($render)
			$view->render(TRUE);
		else
			return $view;
	}
	
	/**
	 * Help on line
	 *
	 * @param   string  $lang Language code
	 * @return  void
	 */
	public function online($lang)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('home'));
		
		// content
		$view = new X4View_core('container_two');
		
		// right
		$view->right = new X4View_core('left');
		
		// left
		$view->content = new X4View_core('left');
		
		// get left content
		$lcontent = @file_get_contents('http://www.x3cms.net/'.$lang.'/help/home/'.$this->remotize());
		
		// get right remote contents
		$rcontent = @file_get_contents('http://www.x3cms.net/'.$lang.'/help/index/'.$this->remotize());
		
		// return contents or error message
		$view->right->left = (empty($rcontent)) 
			? '<p>'._UNABLE_TO_CONNECT.'</p>' 
			: '<div id="index">'.$rcontent.'</div>';
		
		$src = array('src="/cms');
		$rpl = array('src="http://www.x3cms.net/cms');
		
		$view->content->left = (empty($lcontent)) 
			? '<p>'._UNABLE_TO_CONNECT.'</p>' 
			: '<div id="help">'.str_replace($src, $rpl, $lcontent).'</div>';
		
		$view->render(TRUE);
	}
	
	/**
	 * Build the remote suffix
	 *
	 * @param   string  $lang Language code
	 * @param   string  $page Page URL
	 * @return  void
	 */
	private function remotize()
	{
		return str_replace('/', '-', BASE_URL.'help/hol/').'/1';
	}
	
	/**
	 * Help on line
	 *
	 * @param   string  $url Remote URL
	 * @return  void
	 */
	public function hol($lang, $area, $page, $suffix = '')
	{
		// load dictionaries
		$this->dict->get_wordarray(array('home'));
		
		// left
		$view = new X4View_core('container');
		
		// get remote contents
		$content = @file_get_contents('http://www.x3cms.net/'.$lang.'/help/'.$page.'/'.$this->remotize());
		
		$src = array('src="/cms');
		$rpl = array('src="http://www.x3cms.net/cms');
		
		// return contents or error message
		$view->content = (empty($content)) 
			? '<p>'._UNABLE_TO_CONNECT.'</p>' 
			: '<div id="help">'.str_replace($src, $rpl, $content).'</div>';
			
		$view->render(TRUE);
	}
	
	/**
	 * Help filter
	 *
	 * @return  void
	 */
	public function filter($lang)
	{
		echo '';
	}
}
