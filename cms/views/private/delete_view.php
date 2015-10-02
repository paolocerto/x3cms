<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
?>
<h3><?php echo $title ?></h3>
<p><?php 

if (isset($msg)) 
	echo $msg;

?>
<br /><?php echo _ARE_YOU_SURE_DELETE ?> <strong>'<?php echo $item ?>'</strong>?</p>
<?php echo $form ?>
