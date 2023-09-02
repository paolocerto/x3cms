<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// load tinyMCE
if (isset($tinymce)) 
{
	echo $tinymce;
}

if (isset($js))
{
	echo '<script src="'.THEME_URL.'js/basic.js"></script>';
	echo $js;
}

// close button
if (!isset($close))
{
	echo '<div id="close-modal" title="'._CLOSE.'"><i class="fas fa-times fa-lg"></i></div>';
}

// show super title

if (isset($super_title))
{
	echo '<h1>'.$super_title.'</h1>';
}
// show title
if (isset($title))
{
	echo '<h2>'.$title.'</h2>';
}

if (isset($loader))
{
    echo '<div id="formloader" class="hidden"><i class="fas fa-sync fa-spin fa-5x orange" aria-hidden="true"></i></div>';
}

// show optional message
if (isset($msg))
{
	echo $msg;
}

if (isset($msg_error))
{
	// here we output the error message
    echo $msg_error;
}
else
{
	// here we put the error message with AJAX
	echo (isset($close))
		? '<div id="msg"></div>'
		: '<div class="msg-container"></div>';
}

echo $form;
