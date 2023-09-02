<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
 
// x3flags area_uninstall

$mod_name = 'x3flags';
$required = array();
$sql = array();

$a = new Area_model();
$area = $a->get_by_id($plugin->id_area);

$sql[] = 'DELETE FROM param WHERE xrif = \''.$mod_name.'\' AND id_area = '.$area->id;
$sql[] = 'DELETE FROM dictionary WHERE what = \''.$mod_name.'\' AND area = \''.$area->name.'\'';
$sql[] = 'DELETE FROM modules WHERE id = '.intval($id);
