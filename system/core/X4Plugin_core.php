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
	public function __construct(X4Site_model $site)
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
	public function check_args(array &$args, array $values)
	{
		$n = sizeof($values);
		for ($i = 0; $i < $n; $i++)
		{
			if (!isset($args[$i]) || empty($args[$i]))
            {
				$args[$i] = $values[$i];
            }
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
	public function get_module(stdClass $page, array $args, string $param = '');

	/**
	 * call plugin actions
	 *
	 * @param   string	$control action name
	 * @param   string	$a
	 * @param   string	$b
	 * @param   string	$c
	 * @param   string	$d
	 * @return  void
	 */
	 public function plugin(string $control, string $a, string $b, string $c, string $d);
}
