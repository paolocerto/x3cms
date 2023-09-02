<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
 
?>
<div id="login" class="shadow">
	<h2><?php echo _RECOVERY_SUBJECT ?></h2>
	<p><?php echo _RESET_MSG ?></p>
<?php
// message
if (isset($msg)) 
{
	echo '<div id="msg"><p>'.$msg.'</p></div>';
}
echo $form;
?>
</div>
