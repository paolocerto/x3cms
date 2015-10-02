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
<div id="login">
	<h2><?php echo _LOGIN ?></h2>
<?php
// message
if (isset($msg)) 
{
	echo '<div id="msg"><p>'.$msg.'</p></div>';
}
echo $form;
?>
<div class="acenter xsmall"><a href="<?php echo BASE_URL ?>login/recovery" title="<?php echo _RESET_PWD_TITLE ?>"><?php echo _RESET_PWD ?></a></div>
</div>
<script>
eval("document.getElementById('username').focus();");
</script>
