<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

$msg = (empty($msg)) 
	? 'An error occurred' 
	: $msg;
?>
<h1><?php echo $title ?></h1>
<p><?php echo $msg ?></p>
