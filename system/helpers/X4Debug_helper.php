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
 * Debug Helper
 * 
 * @package X4WEBAPP
 */
class X4Debug_helper 
{
	/**
	 * Print Debug informations about a var
	 *
	 * @param	string	$name Variable name
	 * @param	mixed	$var Variable to explore
	 * @param	boolean	$die Die after print
	 * @return	string	
	 */
	public static function dump($name, $var, $die = false)
	{
		// Only in debug mode
		if (DEBUG)
		{
			echo $name.': '.gettype($var).BR;
			if (is_array($var) || is_object($var))
				print_r($var);
			else
				echo '*'.$var.'*';
			if ($die)
				die;
		}
	}

}
