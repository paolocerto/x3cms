<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// x4get_by_key global_uninstall

$mod_name = 'x4get_by_key';
$required = array();
$sql = array();

$sql[] = 'DELETE FROM param WHERE xrif = \''.$mod_name.'\'';
$sql[] = 'DELETE FROM dictionary WHERE what = \''.$mod_name.'\'';
$sql[] = 'DELETE FROM modules WHERE id = '.intval($id);
