<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// x4search global_uninstall

$mod_name = 'x4search';
$required = array();

$sql = array();
$sql[] = 'DELETE FROM param WHERE xrif = \''.$mod_name.'\'';
$sql[] = 'DELETE FROM dictionary WHERE what = \''.$mod_name.'\'';

$sql[] = 'DELETE FROM privtypes WHERE name = \'x4_search\'';
$sql[] = 'DELETE FROM gprivs WHERE what = \'x4_search\'';
$sql[] = 'DELETE FROM uprivs WHERE privtype = \'x4_search\'';
$sql[] = 'DELETE FROM dictionary WHERE xkey = \''.strtoupper($i).'\'';

$sql[] = 'DELETE FROM modules WHERE name = \''.$mod_name.'\'';
