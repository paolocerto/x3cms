<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
 
/**
 * Controller for REST API calls
 * Use Restler2
 * 
 * @package X3CMS
 */
class Api_controller extends X4Cms_controller
{
	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		X4Core_core::auto_load('restler_library');
	}
	
	/**
	 * Generic API override __call
	 *
	 * @param string	url/controller name
	 * @param array		array of arguments
	 * @return void
	 */
	public function __call($url, $args)
	{
		// load API class
		$check = X4Core_core::auto_load($url.'_api');
		
		// if API exists
		if ($check)
		{
			// call Restler
			$r = new Restler();
			$r->setSupportedFormats('JsonFormat', 'XmlFormat');
			$r->addAPIClass($url);
			$r->addAuthenticationClass('SimpleAuth');
			$r->handle();
		}
		else 
		{
			return false;
		}
	}
}
