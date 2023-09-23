<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright		(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

/**
 * Config file
 * here you set all
 */

// start config

/**
 * Define SPREFIX: a prefix used for the path to files folder
 */
define('SPREFIX', 'x3_');

/**
 * Define FPATH: the path to files folder
 * Relative path
 */
define('FPATH', ROOT.'cms/files/'.SPREFIX.'/filemanager/');


// set file_folder_prefix used by filemanager
if (!isset($_SESSION['ffprefix']) || $_SESSION['ffprefix'] != SPREFIX)
{
    $_SESSION['ffprefix'] = SPREFIX;
}

$default = array();
// extra config file
// here we store info about extra areas
define('SECRET', md5($_SERVER['DOCUMENT_ROOT']));
if (file_exists(APATH.'files/'.SECRET.'/'.SECRET.'.txt'))
{

    $default = json_decode(file_get_contents(APATH.'files/'.SECRET.'/'.SECRET.'.txt'), true);
}

// default items
$default['x3default_route'] = 'public/home';

// global items
define('EXT', '.php');
define('SEP', '/');					// URL separator (not used)
define('_TRAIT_', ' - ');				// text separator
define('BR', '<br />');
define('NL', "\n");
define('RN', "\r\n");

define('SERVICE', 'site_name'); 		// used by mailer as From name
define('COOKIE', 'your_cookie_name');	// set your cookie name
define('SITE', 'sitekey');				// site key used for checking login status: use an unique key to distinguish between different sites in the same domain

// security
define('HASH', 'sha1');					// hash method (sha1, sha512 or whatever you want, up to 128 char) DO NOT CHANGE AFTER INSTALLATION!

define('SALT', 'aLongSecretComplexString');

// localization
define('TIMEZONE', 'Europe/Rome');		// set time zone (see http://www.php.net/manual/en/timezones.php for a list of supported timezones)

define('DATE_SEP', '-');
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i');

// multilevel menu
define('MAX_MENU_DEEP', 3);				// keep this lower for better performance

// logs
define('LOGS', true);					// disable this for better performance

// scripts in the editor
define('EDITOR_SCRIPTS', false);        // enable a dedicated field for scripts in the editor

// options in the editor
define('EDITOR_OPTIONS', false);        // enable a dedicated section for options in the editor

// use APC
define('APC', false);					// if true the most frequently executed queries will be cached

// Flmngr API Key for TinyMCE
define('FLMNGR_API_KEY', 'FLMNFLMN');   // default API key

// database configuration array
$db_config = array();

// default database
$db_config['default'] = array(
	'db_type' => 'mysql',
	'db_name' => 'x3cms',
	'db_host' => 'localhost',
	'db_socket' => '',
	'db_user' => 'root',
	'db_pass' => 'root',
	'persistent' => false,
	'charset' => 'utf8'
);

// end config

// Set character encoding to UTF8
mb_internal_encoding('UTF-8');
