<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// X3search module installer

// plugin version
$version = '0.3';

// compatibility
$compatibility = '0.5 STABLE';

// plugin name
$mod_name = 'x3search';

// requirements
$required = array();

// sql0 is the array of queries for global use (tables, privtypes, admin, admin dictionary)
// sql1 is the array of queries for specific area use (parameters, dictionary, module)
$sql0 = $sql1 = array();

// module parameters
$sql1[] = "INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), $id_area, '$mod_name', 'label', 'Show label', '0|1', '0', 1, 0, 1)";
$sql1[] = "INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), $id_area, '$mod_name', 'placeholder', 'Show placeholder', '0|1', '1', 1, 0, 1)";

// dictionary
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x3search', '_X3SEARCH_LABEL', 'Cerca nel sito', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x3search', '_X3SEARCH_PLACEHOLDER', 'Cerca nel sito', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x3search', '_X3SEARCH_BUTTON', 'Cerca', 0, 1)";
// en
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x3search', '_X3SEARCH_LABEL', 'Search', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x3search', '_X3SEARCH_PLACEHOLDER', 'Search in the website', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x3search', '_X3SEARCH_BUTTON', 'Search', 0, 1)";

// module
$sql1[] = "INSERT INTO modules (updated, id_area, name, description, configurable, admin, searchable, version, xon) VALUES (NOW(), $id_area, '$mod_name', 'Search form', 1, 0, 0, '$version', 0)";
