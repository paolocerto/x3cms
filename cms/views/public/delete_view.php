<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

?>
<h2><?php echo $title ?></h2>
<p>
<?php

if (isset($msg))
	echo $msg;

?>
<br /><?php echo _ARE_YOU_SURE_DELETE ?> <strong>'<?php echo $item ?>'</strong>?</p>
<?php echo $form ?>
