<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// x4search global_uninstall

$mod_name = 'x4search';
$required = array();

$sql = array();
$sql[] = 'DELETE FROM param WHERE xrif = \''.$mod_name.'\'';
$sql[] = 'DELETE FROM dictionary WHERE what = \''.$mod_name.'\'';
$sql[] = 'DELETE FROM modules WHERE name = \''.$mod_name.'\'';
