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
<div id="login">
	<h2 class="pt-4"><?php echo _RECOVERY_SUBJECT ?></h2>
	<p class="my-8"><?php echo _RESET_MSG ?></p>
<?php
// message
if (isset($msg))
{
	echo '<div id="msg"><p class="failed px-4 py-4 rounded">'.$msg.'</p></div>';
}
echo $form;
?>
</div>
