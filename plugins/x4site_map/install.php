<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// X4site_map module installer

// plugin version
$version = '0.6';

// compatibility
$compatibility = '0.4.3 STABLE';

// plugin name
$mod_name = 'x4site_map';

// requirements
$required = array();

// sql0 is the array of queries for global use (tables, privtypes, admin, admin dictionary)
// sql1 is the array of queries for specific area use (parameters, dictionary, module)
$sql0 = $sql1 = array();

// administration priv
$sql0[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, 'x4_sitemap', 'X4_SITEMAP', 1)";
$sql0[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, 'x4_sitemap', 4, 1)";

$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'groups', 'X4_SITEMAP', 'Gestione mappa del sito', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'groups', 'X4_SITEMAP', 'Gestione mappa del sito', 0, 1)";

// module
$sql1[] = "INSERT INTO modules (updated, id_area, name, title, configurable, admin, searchable, pluggable, version, xon) VALUES (NOW(), $id_area, '$mod_name', 'Area map', 0, 0, 0, 1, $version, 0)";
