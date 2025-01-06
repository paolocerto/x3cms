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
 * Controller for REST API calls
 * Use Restler
 *
 * @package X3CMS
 */
class Api_controller extends X4Cms_controller
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
        X4Core_core::auto_load('restler_library');
	}

	/**
	 * Generic API override __call
	 */
	public function __call(string $method, array $args = []) : void
	{
		// if API exists
		if (file_exists(APATH.'apis/'.$method.'_api.php'))
		{
            require_once APATH.'apis/'.$method.'_api.php';

			$r = new Restler();
            $r->setSupportedFormats('JsonFormat');  // , 'XmlFormat'
			$r->addAPIClass(ucfirst($method));
            //$r->addAuthenticationClass('SimpleAuth');
			$r->handle();
		}
		else
		{
			echo '';
		}
	}
}
