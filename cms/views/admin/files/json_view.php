<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// Outout JSON, including the correct content-type
header('Content-type: application/json');

$a = array();
foreach ($result as $i)
{
	$a[] = $i->what;
}

echo json_encode($a);
