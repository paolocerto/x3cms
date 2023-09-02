<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// default theme installer

$theme = 'default';
$version = '3';

// theme
$sql = "INSERT INTO themes (updated, name, description, version, xon) VALUES (NOW(), '$theme', 'Default theme', '$version', 0)";

// templates
$templates = array();
$templates[] = "INSERT INTO templates (updated, name, js, css, id_theme, description, settings, sections, xon) VALUES (NOW(), 'base', 'script', 'base', XXX, 'Base page template', '{\"s1\":{\"locked\":1,\"bgcolor\":\"default\",\"fgcolor\":\"default\",\"columns\":1,\"width\":\"fullwidth\",\"class1\":\"\",\"class2\":\"\"},\"sn\":{\"locked\":0,\"bgcolor\":\"#ffffff\",\"fgcolor\":\"#444444\",\"columns\":4,\"width\":\"100\",\"class1\":\"\",\"class2\":\"\"}}', 1, 1)";

// menus
$menus = array();
$menus[] = "INSERT INTO menus (updated, id_theme, name, description, xon) VALUES (NOW(), XXX, 'menu_top', 'Top menu', 1)";
