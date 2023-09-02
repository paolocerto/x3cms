<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// close button
echo '<div id="close-modal" title="'._CLOSE.'"><i class="fas fa-times fa-lg"></i></div>';

if (isset($js))
{
	echo $js;
}
?>
<h2><?php echo $title ?></h2>
<p>
<?php

// show message
if (isset($msg))
{
	echo $msg.BR;
}

echo _ARE_YOU_SURE_DELETE .' <strong>'.$item.'</strong>?</p>';

echo $form;
