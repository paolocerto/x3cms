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
 * JShrink Helper
 *
 * @package X4WEBAPP
 */
class X4JShrink_helper
{
	/**
	 * Minimize JS code
	 *
	 * @param	string	$js code
	 * @return	string
	 */
	public static function minimize($js)
	{
		require_once PATH . '/vendor/autoload.php';
        return \JShrink\Minifier::minify($js, array('flaggedComments' => false));
	}

}
