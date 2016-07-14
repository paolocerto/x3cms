<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// x3get_by_key global_uninstall

$mod_name = 'x3get_by_key';
$required = array();
$sql = array();

$sql[] = 'DELETE FROM dictionary WHERE what = \''.$mod_name.'\'';

$sql[] = 'DELETE FROM modules WHERE id = '.intval($id);
