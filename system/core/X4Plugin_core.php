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
	 */
	public function __construct(X4Site_model $site)
	{
		$this->site = $site;
	}

	/**
	 * Set empty arguments for plugin
	 */
	public function check_args(array &$args, array $values) : void
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
	 */
	public function get_module(stdClass $page, array $args, string $param = '') : mixed;

	/**
	 * call plugin actions
	 */
	 public function plugin(string $control, mixed $a, mixed $b, mixed $c, mixed $d) : void;
}
