<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// X4get_by_key module installer

// plugin version
$version = '0.9';

// compatibility
$compatibility = '0.9.99 STABLE';

// plugin name
$mod_name = 'x4get_by_key';

// requirements
$required = array();

// sql0 is the array of queries for global use (tables, privtypes, admin, admin dictionary)
// sql1 is the array of queries for specific area use (parameters, dictionary, module)
$sql0 = $sql1 = array();

// administration priv
$sql0[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, 'x4_get_by_key', 'X4_GET_BY_KEY', 1)";
$sql0[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, 'x4_get_by_key', 4, 1)";

// groups
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'groups', 'X4_GET_BY_KEY', 'Raggruppa articoli per chiave', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'groups', 'X4_GET_BY_KEY', 'Group articles by key', 0, 1)";

// it
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x4get_by_key', '_X4GET_BY_KEY_CONFIGURATOR_MSG', 'Seleziona una chiave per filtrare gli articoli', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x4get_by_key', '_X4GET_BY_KEY_OPTION', 'Chiave articoli', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', 'admin', 'x4get_by_key', '_X4GET_BY_TAG_OPTION', 'Visualizza Tag', 0, 1)";
// en
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x4get_by_key', '_X4GET_BY_KEY_CONFIGURATOR_MSG', 'Select an article key to filter articles', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x4get_by_key', '_X4GET_BY_KEY_OPTION', 'Key', 0, 1)";
$sql0[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', 'admin', 'x4get_by_key', '_X4GET_BY_TAG_OPTION', 'Show tags', 0, 1)";

// AREA QUERIES
// it
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4get_by_key', '_X4GET_BY_KEY_FILTER', 'filtra per Tag', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'it', '".$area->name."', 'x4get_by_key', '_X4GET_BY_KEY_UNFILTER', 'Rimuovi filtro', 0, 1)";
// en
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4get_by_key', '_X4GET_BY_KEY_FILTER', 'filter by Tag', 0, 1)";
$sql1[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), 'en', '".$area->name."', 'x4get_by_key', '_X4GET_BY_KEY_UNFILTER', 'remove filter', 0, 1)";

// module
$sql1[] = "INSERT INTO modules (updated, id_area, name, title, configurable, admin, searchable, pluggable, version, xon) VALUES (NOW(), $id_area, '$mod_name', 'Articles by key', 0, 0, 0, 1, '$version', 0)";
