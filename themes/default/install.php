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
$version = '1.4';

// theme
$sql = "INSERT INTO themes (updated, name, description, version, xon) VALUES (NOW(), '$theme', 'Default theme', '$version', 0)";

// templates
$templates = array();
$templates[] = "INSERT INTO templates (updated, name, css, id_theme, description, sections, xon) VALUES (NOW(), 'base', 'base', XXX, 'Base page template (two columns)', 3, 1)";
$templates[] = "INSERT INTO templates (updated, name, css, id_theme, description, sections, xon) VALUES (NOW(), 'one', 'base', XXX, 'One column template (one columns)', 2, 1)";
$templates[] = "INSERT INTO templates (updated, name, css, id_theme, description, sections, xon) VALUES (NOW(), 'offline', 'offline', XXX, 'Offline template', 2, 1)";

// menus
$menus = array();
$menus[] = "INSERT INTO menus (updated, id_theme, name, description, xon) VALUES (NOW(), XXX, 'menu_top', 'Top menu', 1)";
$menus[] = "INSERT INTO menus (updated, id_theme, name, description, xon) VALUES (NOW(), XXX, 'menu_left', 'Left menu', 1)";
