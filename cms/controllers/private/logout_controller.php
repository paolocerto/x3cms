<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
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
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Perform logout
	 * Destroy session and redirect to home
	 *
	 * @return  void
	 */
	public function _default()
	{
		// destroy session
		session_unset();
		session_destroy();
		
		// redirect to home
		header('Location: '.ROOT);
		die;
	}
}
