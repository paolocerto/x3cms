<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */

/**
 * Controller class
 * THIS FILE IS DERIVED FROM KOHANA
 *
 * @package X4WEBAPP
 */
abstract class X4Controller_core
{

	/**
	 * Loads URI, and Input into this controller.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		if (X4Core_core::$insta == NULL)
		{
			// Set the instance to the first controller loaded
			X4Core_core::$insta = $this;
		}
	}

	/**
	 * Handles methods that do not exist.
	 *
	 * @param   string  method name
	 * @param   array   arguments
	 * @return  void
	 */
	public function __call(string $method, array $args)
	{
		// Default to showing a 404 page
		die('system.404');
	}

	/**
	 * Includes a View within the controller scope.
	 *
	 * @param   string  view filename
	 * @param   array   array of view variables
	 * @return  string
	 */
	public function load_view(string $filename, array $input_data)
	{
		if ($filename == '') return;

		// Buffering on
		ob_start();

		// Import the view variables to local namespace
		extract($input_data, EXTR_SKIP);

		// Views are straight HTML pages with embedded PHP, so importing them
		// this way insures that $this can be accessed as if the user was in
		// the controller, which gives the easiest access to libraries in views
		include $filename;

		// Fetch the output and close the buffer
		return ob_get_clean();
	}

}
