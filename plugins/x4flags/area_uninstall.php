<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// x4flags area_uninstall

$mod_name = 'x4flags';
$required = array();
$sql = array();

$a = new Area_model();
$area = $a->get_by_id($plugin->id_area);

$privtypes = array('x4_flags');
foreach ($privtypes as $i)
{
    $sql[] = 'DELETE FROM privs WHERE id_area = \''.$area->id.'\' AND what = \''.$i.'\'';
}

$sql[] = 'DELETE FROM param WHERE xrif = \''.$mod_name.'\' AND id_area = '.$area->id;
$sql[] = 'DELETE FROM modules WHERE id = '.intval($id);
