<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// x4flags global_uninstall

$mod_name = 'x4flags';
$required = array();

$sql = array();

$privtypes = array('x4_flags');
foreach ($privtypes as $i)
{
    $sql[] = 'DELETE FROM privs WHERE what = \''.$i.'\'';
}

$sql[] = 'DELETE FROM param WHERE xrif = \''.$mod_name.'\'';
$sql[] = 'DELETE FROM dictionary WHERE xkey = \'X4_FLAGS\'';
$sql[] = 'DELETE FROM modules WHERE name = \''.$mod_name.'\'';
