<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// X3get_by_key module installer

// plugin version
$version = '0.2';

// compatibility
$compatibility = '0.4.3 STABLE';

// plugin name
$mod_name = 'x3get_by_key';

// requirements
$required = array();

// sql0 is the array of queries for global use (tables, privtypes, admin, admin dictionary)
// sql1 is the array of queries for specific area use (parameters, dictionary, module)
$sql0 = $sql1 = array();

// module
$sql1[] = "INSERT INTO modules (updated, id_area, name, description, configurable, admin, searchable, version, xon) VALUES (NOW(), $id_area, '$mod_name', 'Articles by key', 0, 0, 0, '$version', 0)";
