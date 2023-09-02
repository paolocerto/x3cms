<?php
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// Bootstrap file
// Here some configuration and go!

// --- start config

// folder path to x4webapp
$system_path = 'system/';

// folder path to x3cms
$app_path = 'cms/';

// --- end config


/**
 * Initialize sessions
 */
ini_set('session.gc_maxlifetime', 3*3600);
!ini_get('session.auto_start')
    ? session_start()
    : '';
$SID = session_id();

/**
 * Set a random token
 * Used to verify that the form is executed from the site
 */
if (!isset($_SESSION['token']))
{
	$_SESSION['token'] = uniqid(rand(),TRUE);
}

/**
 * Define ROOT: the path from the Document Root of the server to the folder that contains the site with X3CMS
 * Relative path
 */
$root = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
define('ROOT', $root.'/');

/**
 * Define PATH: the DOCUMENT ROOT extended to the folder that contains the site with X3CMS
 * Absolute path
 */
define('PATH', $_SERVER['DOCUMENT_ROOT'].ROOT);

/**
 * Define SPATH: the path to x4webapp folder
 * Absolute path
 */
define('SPATH', PATH.$system_path);

/**
 * Define APATH: the path to x3cms folder
 * Absolute path
 */
define('APATH', PATH.$app_path);

/**
 * Define DOM: a code based upon domain name
 */
define('_DOMAIN_',  $_SERVER['HTTP_HOST']);   //$_SERVER['SERVER_NAME']);

/**
 * Define FFPATH: the path to file folder
 * Absolute path
 */
define('FFPATH', PATH.$app_path.'files/');


unset($system_app, $app_path, $file, $root, $path);

// GO!

// for benchmarking
define('X4START_TIME', microtime(TRUE));
define('X4START_MEMORY', memory_get_usage());

/**
 * Composer autoload
 */
// if you use composer
// better if you put it only where you need it
// require 'vendor/autoload.php';

/**
 * Load Configuration
 */
include(APATH.'config/config.php');

/**
 * Set Timezone
 */
if (isset($_SESSION['timezone']))
{
	date_default_timezone_set($_SESSION['timezone']);
}
else
{
	date_default_timezone_set(TIMEZONE);
}

/**
 * Load Router
 */
include(SPATH.'core/X4Route_core.php');

/**
 * Load Core
 */
include(SPATH.'core/X4Core_core.php');

// Start the core
if (defined('X3CLI'))
{
    // When called from x3
    X4Core_core::setCore($default, $db_config, $cli);
}
else
{
    X4Core_core::setCore($default, $db_config);
}
