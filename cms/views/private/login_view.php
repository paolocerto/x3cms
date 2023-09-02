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
<div id="content">
	<h2><?php echo _X3CMS ?> - <?php echo _LOGIN ?></h2>
<?php

// set message
if (isset($msg)) 
{
	echo '<h3>'._WARNING.'</h3><p>'.$msg.'</p>';
}
echo $form;
?>
</div>
<script type="text/javascript">
eval("document.getElementById('username').focus();");
</script>
