<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

$msg = (empty($msg))
	? 'An error occurred'
	: $msg;
?>
<h1 class="text-center"><?php echo $title ?></h1>
<div class="boxed"><p><?php echo $msg ?></p></div>
