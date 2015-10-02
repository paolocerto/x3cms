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
 * Config file
 * here you set all
 */

// start config

// default items
$default = array();
$default['x3default_route'] = 'public/home';
//$default['x3default_error'] = 'error';

// here you can set association by extra areas and existent folders
//$default['foo'] = 'public';
//$default['foo_id'] = 4;
//$default['bar'] = 'private';
//$default['bar_id'] = 5;

// global items
define('EXT', '.php');					// paranoics could change php files extension
define('SEP', '/');					// URL separator (not used)
define('_TRAIT_', ' - ');				// text separator
define('BR', '<br />');
define('NL', "\n");
define('RN', "\r\n");

define('SERVICE', 'site_name'); 		// used by mailer as From name
define('COOKIE', 'your_cookie_name');	// set your cookie name
define('SITE', 'sitekey');				// site key used for checking login status: use an unique key to distinguish between different sites in the same domain

// security
define('HASH', 'md5');					// hash method (md5, sha1, sha512 or whatever you want, up to 128 char) DO NOT CHANGE AFTER INSTALLATION!

// localization
define('TIMEZONE', 'Europe/Rome');		// set time zone (see http://www.php.net/manual/en/timezones.php for a list of supported timezones)

define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i');

// multilevel menu
define('MAX_MENU_DEEP', 3);				// keep this lower for better performance

// logs
define('LOGS', true);					// disable this for better performance

// use APC
define('APC', false);					// if true the most frequently executed queries will be cached

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

