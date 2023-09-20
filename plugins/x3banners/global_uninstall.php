<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */


// x3banners global_uninstall

// plugin name
$mod_name = 'x3banners';

$sql = array();

// required
$required = array();

// drop table
$sql[] = 'DROP TABLE x3_banners';

// delete admin pages
$sql[] = 'DELETE FROM pages WHERE xid = \'x3_banners\' ';

// dictionary
$sql[] = 'DELETE FROM dictionary WHERE what = \''.$mod_name.'\'';
$sql[] = 'DELETE FROM dictionary WHERE xkey = \'_SEARCH_X3BANNERS\'';

// privtypes, gvprivs, uprivs
$privtypes = array('_x3banners_creation','x3_banners');
foreach ($privtypes as $i)
{
	$sql[] = 'DELETE FROM privtypes WHERE name = \''.$i.'\'';
	$sql[] = 'DELETE FROM gprivs WHERE what = \''.$i.'\'';
	$sql[] = 'DELETE FROM uprivs WHERE privtype = \''.$i.'\'';
	$sql[] = 'DELETE FROM privs WHERE what = \''.$i.'\'';
	$sql[] = 'DELETE FROM dictionary WHERE xkey = \''.strtoupper($i).'\'';
}

$sql[] = 'DELETE FROM modules WHERE id = '.intval($id);
$sql[] = 'DELETE FROM param WHERE xrif = \''.$mod_name.'\'';
$sql[] = 'DELETE FROM modules WHERE id = '.intval($id);
