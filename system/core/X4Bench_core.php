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
 * Benchmark class
 * THIS FILE IS DERIVED FROM KOHANA
 * 
 * @package X4WEBAPP
 */
final class X4Bench_core
{
	/**
	 * Benchmark info
	 *
	 * @static
	 * @param   string   output string
	 * @return  string
	 */
	public static function info($output)
	{
		// memory usage in MB
		$memory = (memory_get_usage() - X4START_MEMORY) / 1024 / 1024;
		// page execution time
		$time = microtime(TRUE) - X4START_TIME;
		// Default DB
		$driver = 'X4'.X4Core_core::$db['default']['db_type'].'_driver';
		$db = new $driver('default');
		
		$out = str_replace(
			array
			(
				'{x4wa_version}',
				'{execution_time}',
				'{memory_usage}',
				'{included_files}',
				'{queries}'
			),
			array
			(
				X4VERSION,
				number_format($time, 4),
				number_format($memory, 2).'MB',
				count(get_included_files()),
				$db::$queries
			),
			$output
		);
		
		return $out;
	}

}
