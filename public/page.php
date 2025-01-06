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
	$_SESSION['token'] = uniqid(rand(), true);
}

/**
 * Define ROOT: the path from the Document Root of the server to the folder that contains the site with X3CMS
 * Relative path
 */
$root = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
define('ROOT', $root.'/');

/**
 * Define PATH: the CMS ROOT extended to the folder that contains the site with X3CMS
 * Absolute path
 */
define('PATH', str_replace('public', '', $_SERVER['DOCUMENT_ROOT']));

/**
 * Define SPATH: the path to x4webapp folder
 * Absolute path
 */
define('SPATH', PATH.'system/');

/**
 * Define APATH: the path to x3cms folder
 * Absolute path
 */
define('APATH', PATH.'cms/');

/**
 * Define TPATH: the path to themes folder
 * Absolute path
 */
define('TPATH', PATH.'themes/');

/**
 * Define DOMAIN: a code based upon domain name
 */
// X3 cli doesn't handle HTTP_HOST
if (isset($_SERVER['HTTP_HOST']))
{
    define('_DOMAIN_',  $_SERVER['HTTP_HOST'].$root);
}

/**
 * Define FFPATH: the path to file folder
 * Absolute path
 */
define('FFPATH', $_SERVER['DOCUMENT_ROOT'].'/files/');

unset($root);

// GO!

// for benchmarking
define('X4START_TIME', microtime(true));
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
