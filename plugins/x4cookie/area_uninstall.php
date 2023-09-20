<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */


// x4cookie area_uninstall

// plugin name
$mod_name = 'x4cookie';

$sql = array();

// required
$required = array();

$a = new Area_model();
$area = $a->get_by_id($plugin->id_area);

// dictionary
$sql[] = 'DELETE FROM dictionary WHERE what = \''.$mod_name.'\' AND area = \''.$area->name.'\'';

// delete parameters
$sql[] = 'DELETE FROM param WHERE xrif = \''.$mod_name.'\' AND id_area = '.$area->id;

$sql[] = 'DELETE FROM modules WHERE id = '.intval($id);