<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */


if (isset($title))
{
	echo '<h3>'.$title.'</h3>';
}
?>
<p><?php

if (isset($msg))
{
	echo $msg.BR.BR;
}

if (isset($no_delete))
{
    echo $no_delete;
}
else
{
    echo _ARE_YOU_SURE_DELETE.' <strong>'.$item.'</strong>?';
}
echo '</p>';
echo $form;
