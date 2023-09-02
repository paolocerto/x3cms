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
 * Default Message controller
 *
 * @package X4WEBAPP
 */
class X4Msg extends X4Controller_core
{
	/**
	 * Template to use
	 */
	public $template = 'message';

	/**
	 * Page not found
	 *
	 * @return string
	 */
	public function page_not_found()
	{
		echo 'Page not found';
	}

	/**
	 * Default call
	 *
	 * @param string	method name
	 * @param array		array of arguments
	 * @return string
	 */
	public function __call(string $method, array $args)
	{
		// Disable auto-rendering
		$this->auto_render = FALSE;

		// By defining a __call method, all pages routed to this controller
		// that result in 404 errors will be handled by this method, instead of
		// being displayed as "Page Not Found" errors.
		echo 'This text is generated by __call.<br />You ask this page: '.X4Route_core::getRoute();
	}

} // End Msg Controller
