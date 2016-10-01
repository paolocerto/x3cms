<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// x4site_map area_uninstall

$mod_name = 'x4site_map';
$required = array();
$sql = array();

$sql[] = 'DELETE FROM modules WHERE id = '.intval($id);
