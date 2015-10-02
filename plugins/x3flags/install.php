<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// X3flags module installer

// plugin version
$version = '0.5';

// compatibility
$compatibility = '0.4.3 STABLE';

// plugin name
$mod_name = 'x3flags';

// requirements
$required = array();

// sql0 is the array of queries for global use (tables, privtypes, admin, admin dictionary)
// sql1 is the array of queries for specific area use (parameters, dictionary, module)
$sql0 = $sql1 = array();

// module parameters
$sql1[] = "INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), $id_area, '$mod_name', 'flags', 'Flags or text', '0|1', '0', 1, 0, 1)";
$sql1[] = "INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), $id_area, '$mod_name', 'short_text', 'Short text', '0|1', '0', 1, 0, 1)";
$sql1[] = "INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), $id_area, '$mod_name', 'show_all', 'Show all languages', '0|1', '1', 1, 0, 1)";

// module
$sql1[] = "INSERT INTO modules (updated, id_area, name, description, configurable, admin, searchable, version, xon) VALUES (NOW(), $id_area, '$mod_name', 'Language switcher', 1, 0, 0, $version, 0)";
