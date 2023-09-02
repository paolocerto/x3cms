<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// x3form_builder global_uninstall

$mod_name = 'x3form_builder';
$required = array();
$sql = array();


$sql[] = 'DROP TABLE x3_forms';
$sql[] = 'DROP TABLE x3_forms_fields';
$sql[] = 'DROP TABLE x3_forms_results';
$sql[] = 'DROP TABLE x3_forms_blacklist';
$sql[] = 'DELETE FROM pages WHERE xid = \'x3_form_builder\'';
$sql[] = 'DELETE FROM param WHERE xrif = \''.$mod_name.'\'';

// delete widgets
$sql[] = 'DELETE FROM widgets WHERE name = \''.$mod_name.'\'';

// privtypes, gprivs, uprivs
$$privtypes = array(
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
	$sql[] = 'DELETE FROM privtypes WHERE name = \''.$i.'\'';
	$sql[] = 'DELETE FROM gprivs WHERE what = \''.$i.'\'';
	$sql[] = 'DELETE FROM uprivs WHERE privtype = \''.$i.'\'';
	$sql[] = 'DELETE FROM dictionary WHERE xkey = \''.strtoupper($i).'\'';
}

$sql[] = 'DELETE FROM dictionary WHERE what = \''.$mod_name.'\'';
// msg
$sql[] = 'DELETE FROM dictionary WHERE what = \'msg\' AND xkey LIKE \'_X3FB_%\'';

$sql[] = 'DELETE FROM modules WHERE  id = '.intval($id);

