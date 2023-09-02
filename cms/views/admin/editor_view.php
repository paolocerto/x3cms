<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

echo '<script defer src="'.THEME_URL.'js/basic.js"></script>';
if (isset($js))
{
	echo $js;
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
    echo '<div id="formloader" class="hidden"><i class="fas fa-sync fa-spin fa-5x on" aria-hidden="true"></i></div>';
}

// show optional message
if (isset($msg))
{
	echo '<div class="bg-white text-gray-700 md:px-8 px-4" style="border:1px solid white">'.$msg.'</div>';
}

if (isset($msg_error))
{
	// here we output the error message
    echo $msg_error;
}
else
{
	// here we put the error message with AJAX
	echo '<div class="m-0" x-html="error_msg"></div>';
}

echo $form;

// load tinyMCE
if (isset($tinymce))
{
   // echo $tinymce;
}
