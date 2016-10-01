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
 * Helper for plugin handling
 * 
 * @package X4WEBAPP
 */
class X4Plugin_core 
{

	// dictionary obj
	protected $dict;
	// site obj
	protected $site;
	
	/**
	 * Set site obj
	 *
	 * @param object	Site obj
	 * @return void
	 */
	public function __construct($site)
	{
		$this->site = $site;
	}
	
	/**
	 * Set empty arguments for plugin
	 *
	 * @param array		arguments
	 * @param array		array of values
	 * @return void
	 */
	public function check_args(&$args, $values)
	{
		$n = sizeof($values);
		for ($i = 0; $i < $n; $i++) 
		{
			if (!isset($args[$i]) || empty($args[$i])) 
				$args[$i] = $values[$i];
		}
	}
}

/**
 * Interface for X3CMS plugins
 */
interface X3plugin
{
	/**
	 * Default method
	 *
	 * @param	object	$page object
	 * @param	array	$args array of args
	 * @param	string	$param optional parameter
	 * @return	string
	 */
	public function get_module($page, $args, $param = '');
	
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
	 public function call_plugin($id_area, $control, $a, $b, $c, $d);
}
