<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// Default theme uninstaller

$sql = array();

// theme
$sql[] = "DELETE FROM themes WHERE id = ".$id_theme;

// templates
$sql[] = "DELETE FROM templates WHERE id_theme = ".$id_theme;

// menu themes
$sql[] = "DELETE FROM menus WHERE id_theme = ".$id_theme;
