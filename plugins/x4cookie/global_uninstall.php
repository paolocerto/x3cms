<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */


// x4cookie global_uninstall

// plugin name
$mod_name = 'x4cookie';

$sql = array();

// required
$required = array();

// dictionary
$sql[] = 'DELETE FROM dictionary WHERE what = \''.$mod_name.'\'';

$privtypes = array('x4_cookies');
foreach ($privtypes as $i)
{
	$sql[] = 'DELETE FROM privtypes WHERE name = \''.$i.'\'';
	$sql[] = 'DELETE FROM gprivs WHERE what = \''.$i.'\'';
	$sql[] = 'DELETE FROM uprivs WHERE privtype = \''.$i.'\'';
	$sql[] = 'DELETE FROM dictionary WHERE xkey = \''.strtoupper($i).'\'';
}

// delete parameters
$sql[] = 'DELETE FROM param WHERE xrif = \''.$mod_name.'\'';

$sql[] = 'DELETE FROM modules WHERE id = '.intval($id);