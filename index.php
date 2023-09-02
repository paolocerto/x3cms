<?php
/**
 * X3 CMS - A smart Content Management System
 *
 * @author	Paolo Certo
 * @copyright (c) CBlu.net di Paolo Certo
 * @license	https://www.gnu.org/licenses/gpl-3.0.html
 * @package	X3CMS
 */

// Redirect to X3CMS installer
// Before install redirect to INSTALL folder

$path = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']);

if (is_dir($path.'/INSTALL'))
{
	header('location: INSTALL');
	die;
}
else
{
	// print error message
	echo '<h1>Warning</h1><p><strong>INSTALL folder does not exist.</strong></p>';
}
