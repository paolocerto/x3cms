<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// x3form_builder area_uninstall

$mod_name = 'x3form_builder';
$required = array();
$sql = array();

$a = new Area_model();
$area = $a->get_by_id($plugin->id_area);

// delete from tables
$sql[] = 'DELETE FROM x3_forms WHERE id_area = '.$area->id;
$sql[] = 'DELETE FROM x3_forms_fields WHERE id_area = '.$area->id;
$sql[] = 'DELETE FROM x3_forms_results WHERE id_area = '.$area->id;
$sql[] = 'DELETE FROM x3_forms_blacklist WHERE id_area = '.$area->id;

// parameters
$sql[] = 'DELETE FROM param WHERE xrif = \''.$mod_name.'\' AND id_area = '.$area->id;

$privtypes = array(
    '_x3forms_creation',
    '_x3form_fields_creation',
    '_x3form_blacklist_creation',
    'x3_forms',
    'x3_forms_fields',
    'x3_forms_results',
    'x3_forms_blacklist'
);

foreach ($privtypes as $i)
{
    $sql[] = 'DELETE FROM privs WHERE id_area = \''.$area->id.'\' AND what = \''.$i.'\'';
}
// widgets
$sql[] = 'DELETE FROM widgets WHERE name = \''.$mod_name.'\' AND id_area = '.$area->id;
// dictionary
$sql[] = 'DELETE FROM dictionary WHERE what = \''.$mod_name.'\' AND area = \''.$area->name.'\'';
$sql[] = 'DELETE FROM dictionary WHERE what = \'msg\' AND area = \''.$area->name.'\' AND xkey LIKE \'_X3FB_%\'';
$sql[] = 'DELETE FROM modules WHERE id = '.intval($id);

