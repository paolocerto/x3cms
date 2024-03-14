<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

/**
 * Logout Controller for private area
 *
 * @package X3CMS
 */
class Logout_controller extends X4Cms_controller
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Perform logout
	 * Destroy session and redirect to home
	 */
	public function _default() : void
	{
		// destroy session
		session_unset();
		session_destroy();

		// redirect to home
		header('Location: '.ROOT);
		die;
	}
}
