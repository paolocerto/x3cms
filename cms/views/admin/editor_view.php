<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// load tinyMCE
if (isset($tinymce)) 
{
	echo $tinymce;
}


if (isset($js))
{
	echo $js;
}

// close button
if (!isset($close))
{
	echo '<div id="close-modal" title="'._CLOSE.'"><i class="fa fa-times fa-lg"></i></div>';
}

// show title
if (isset($title))
{
	echo '<h2>'.$title.'</h2>';
}

// show message
if (isset($msg))
{
	echo $msg;
}

if (isset($msg_error))
{
    echo $msg_error;
}
else
{
    echo '<div class="msg-container"></div>';
}

echo $form;