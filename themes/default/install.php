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

// styles
$styles = [
    ['what' => 'section', 'style' => 'bg-fucsia', 'description' => 'background fucsia'],
    ['what' => 'section', 'style' => 'bg-orange', 'description' => 'background orange'],
    ['what' => 'section', 'style' => 'bg-petroleum', 'description' => 'background petroleum'],
    ['what' => 'section', 'style' => 'bg-violet', 'description' => 'background violet'],
    ['what' => 'section', 'style' => 'bg-military', 'description' => 'background military'],
    ['what' => 'section', 'style' => 'fucsium', 'description' => 'gradient fucsia -> petroleum'],
    ['what' => 'section', 'style' => 'orangium', 'description' => 'gradient orange -> petroleum'],
    ['what' => 'section', 'style' => 'fucsange', 'description' => 'gradient fucsia -> orange'],
    ['what' => 'article', 'style' => 'overlay1 rounded-2xl p-8', 'description' => 'ultra lightgray transparent rounded'],
    ['what' => 'article', 'style' => 'overlay2 rounded-2xl p-8', 'description' => 'lightgray transparent rounded'],
    ['what' => 'article', 'style' => 'overlay3 rounded-2xl p-8', 'description' => 'gray transparent rounded'],
    ['what' => 'article', 'style' => 'border-2 border-neutral-200 rounded-2xl p-8', 'description' => 'lightgray bordered rounded'],
    ['what' => 'article', 'style' => 'border-2 border-neutral-200 p-8', 'description' => 'lightgray bordered not-rounded'],
    ['what' => 'article', 'style' => 'bg-fucsia rounded-2xl p-8', 'description' => 'background fucsia rounded'],
    ['what' => 'article', 'style' => 'bg-orange rounded-2xl p-8', 'description' => 'background orange rounded'],
    ['what' => 'article', 'style' => 'bg-petroleum rounded-2xl p-8', 'description' => 'background petroleum rounded'],
    ['what' => 'article', 'style' => 'bg-violet rounded-2xl p-8', 'description' => 'background violet rounded'],
    ['what' => 'article', 'style' => 'bg-military rounded-2xl p-8', 'description' => 'background military rounded'],
];

// theme
$sql = "INSERT INTO themes (updated, name, description, styles, version, xon) VALUES (NOW(), '$theme', 'Default theme', '".json_encode($styles)."', '$version', 0)";

// templates
$templates = array();
$templates[] = "INSERT INTO templates (updated, name, js, css, id_theme, description, settings, sections, xon) VALUES (NOW(), 'base', 'script', 'base', XXX, 'Base page template', '{\"s1\":{\"locked\":1,\"bgcolor\":\"default\",\"fgcolor\":\"default\",\"columns\":1,\"width\":\"fullwidth\",\"class1\":\"\",\"class2\":\"\"},\"sn\":{\"locked\":0,\"bgcolor\":\"#ffffff\",\"fgcolor\":\"#444444\",\"columns\":4,\"width\":\"100\",\"class1\":\"\",\"class2\":\"\"}}', 1, 1)";

// menus
$menus = array();
$menus[] = "INSERT INTO menus (updated, id_theme, name, description, xon) VALUES (NOW(), XXX, 'menu_top', 'Top menu', 1)";
