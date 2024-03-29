<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// x4search module installer

// plugin version
$version = '0.5';

// compatibility
$compatibility = '0.9.0 STABLE';

// plugin name
$mod_name = 'x4search';

// requirements
$required = array();

// sql0 is the array of queries for global use (tables, privtypes, admin, admin dictionary)
// sql1 is the array of queries for specific area use (parameters, dictionary, module)
$sql0 = $sql1 = array();

$sql0[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, 'x4_search', 'X4_SEARCH', 1)";
$sql0[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, 'x4_search', 4, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'groups', 'X4_SEARCH', 'Motore di ricerca interno', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'groups', 'X4_SEARCH', 'Internal search engine', 0, 1)";

// module parameters
$sql1[] = "INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), $id_area, '$mod_name', 'label', 'Show label', '0|1', '0', 1, 0, 1)";
$sql1[] = "INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), $id_area, '$mod_name', 'placeholder', 'Show placeholder', '0|1', '1', 1, 0, 1)";

// dictionary
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4search', '_X4SEARCH_LABEL', 'Cerca nel sito', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4search', '_X4SEARCH_PLACEHOLDER', 'Cerca nel sito', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4search', '_X4SEARCH_BUTTON', 'Cerca', 0, 1)";
// en
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4search', '_X4SEARCH_LABEL', 'Search', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4search', '_X4SEARCH_PLACEHOLDER', 'Search in the website', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4search', '_X4SEARCH_BUTTON', 'Search', 0, 1)";

// module
$sql1[] = "INSERT INTO modules (updated, id_area, name, title, configurable, admin, searchable, pluggable, version, xon) VALUES (NOW(), $id_area, '$mod_name', 'Search form', 1, 0, 0, 1, '$version', 0)";
