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
 * Controller for Flmngr file manager for Tiny MCE
 *
 * @package X3CMS
 */
class Flmngr_controller extends X4Cms_controller
{
	/**
	 * Constructor
	 * check if user is logged
	 */
	public function __construct()
	{
		parent::__construct();

	}

    /**
	 * Init Flmngr
	 */
	public function _default() : void
	{
        require_once PATH . '/vendor/autoload.php';
		\EdSDK\FlmngrServer\FlmngrServer::flmngrRequest(
            array(
                'dirFiles' => FFPATH.'x3_/filemanager',
            )
        );


	}

}
