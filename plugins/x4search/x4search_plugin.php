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
 * X4search plugin
 *
 * @package		X3CMS
 */
class X4search_plugin extends X4Plugin_core implements X3plugin
{
	/**
	 * Constructor
	 * Initialize dict
	 *
	 * @param	object	$site, site object
	 * @return	void
	 */
	public function __construct($site)
	{
		parent::__construct($site);
		$this->dict = new X4Dict_model(X4Route_core::$area, X4Route_core::$lang);
	}
	
	/**
	 * Default method
	 * Display a search form
	 *
	 * @param object	$page object
	 * @param array		$args array of args
	 * @param string	$param parameter (empty)
	 * @return string
	 */
	public function get_module($page, $args, $param = '')
	{
		// load dictionary
		$this->dict->get_wordarray(array('x4search'));
		
		// get plugin configuration
		$conf = $this->site->get_module_param('x4search', $page->id_area);
		
		// set label
		$label = ($conf['label']) 
			? '<label for="search">'._X4SEARCH_LABEL.'</label>'
			: '';
			
		// set placeholder
		$placeholder = ($conf['placeholder']) 
			? 'placeholder="'._X4SEARCH_PLACEHOLDER.'"' 
			: '';
		
		$out = '<form id="fsearch" method="post" action="'.BASE_URL.'search">
					'.$label.'
					<div class="row no-gap">
						<div class="col-xs-8 no-pad">
							<input type="text" class="large" name="search" id="search" '.$placeholder.' />
						</div>
						<button class="col-xs-4" type="submit">'._X4SEARCH_BUTTON.'</button>
					</div>
				</form>';
		return '<div id="x4search" class="sm-hidden">'.$out.'</div>';
	}
	
	/**
	 * call plugin actions
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$control action name
	 * @param   mixed	$a
	 * @param   mixed	$b
	 * @param   mixed	$c
	 * @param   mixed	$d
	 * @return  void
	 */
	public function call_plugin($id_area, $control, $a, $b, $c, $d) 
	{
		// none
	}
}
