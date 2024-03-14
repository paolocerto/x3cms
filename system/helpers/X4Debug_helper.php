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
 * Debug Helper
 *
 * @package X4WEBAPP
 */
class X4Debug_helper
{
	/**
	 * Print Debug informations about a var
	 */
	public static function dump(string $varName, mixed $var, bool $die = false) : string
	{
		// Only in debug mode
		if (DEBUG)
		{
			echo $varName.': '.gettype($var).BR;
			if (is_array($var) || is_object($var))
            {
				print_r($var);
            }
			else
            {
				echo '*'.$var.'*';
            }

			if ($die)
            {
				die;
            }
		}
	}

}
