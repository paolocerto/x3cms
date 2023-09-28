<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// X4flags module installer

// plugin version
$version = '1.0';

// compatibility
$compatibility = '0.9.99 STABLE';

// plugin name
$mod_name = 'x4flags';

// requirements
$required = array();

// sql0 is the array of queries for global use (tables, privtypes, admin, admin dictionary)
// sql1 is the array of queries for specific area use (parameters, dictionary, module)
$sql0 = $sql1 = array();

// PRIVTYPES
$sql0[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, 'x4_flags', 'X4_FLAGS', 1)";

// GPRIVS
$sql0[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, 'x4_flags', 4, 1)";

// GROUPS
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'groups', 'X4_FLAGS', 'Manage X4Flags plugin', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'groups', 'X4_FLAGS', 'Gestione modulo X4Flags', 0, 1)";

// module parameters
$sql1[] = "INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), $id_area, '$mod_name', 'flags', 'Flags or text', '0|1', '0', 0, 0, 1)";
$sql1[] = "INSERT INTO param (updated, id_area, xrif, name, description, xtype, xvalue, required, xlock, xon) VALUES (NOW(), $id_area, '$mod_name', 'short_text', 'Short text', '0|1', '0', 0, 0, 1)";

// module
$sql1[] = "INSERT INTO modules (updated, id_area, name, title, configurable, admin, searchable, pluggable, version, xon) VALUES (NOW(), $id_area, '$mod_name', 'Language switcher', 1, 0, 0, 0, $version, 0)";
