<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */


// x3banners area_uninstall

// plugin name
$mod_name = 'x3banners';

$sql = array();

// required
$required = array();

$a = new Area_model();
$area = $a->get_by_id($plugin->id_area);

// delete item from table
$sql[] = 'DELETE FROM x3_banners WHERE id_area = '.$area->id;

// dictionary
$sql[] = 'DELETE FROM dictionary WHERE what = \''.$mod_name.'\' AND area = \''.$area->name.'\'';
$sql[] = 'DELETE FROM dictionary WHERE xkey = \'_SEARCH_X3BANNERS\' AND area = \''.$area->name.'\'';

// privtypes, gvprivs, uprivs

$privtypes = array('_x3banners_creation','x3_banners');
foreach ($privtypes as $i)
{
$sql[] = 'DELETE FROM privs WHERE id_area = \''.$area->id.'\' AND what = \''.$i.'\'';
}

$sql[] = 'DELETE FROM modules WHERE id = '.intval($id);
$sql[] = 'DELETE FROM param WHERE xrif = \''.$mod_name.'\' AND area = \''.$area->name.'\'';