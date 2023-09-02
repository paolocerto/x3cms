<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// delete view

if (isset($title))
{
    echo '<h2>'.$title.'</h2>';
}

echo '<div class="bg-white text-gray-700 px-4 md:px-8 pb-8"><p>';
// show message
if (isset($msg))
{
	echo $msg.BR;
}

echo _ARE_YOU_SURE_DELETE .' <strong>'.$item.'</strong>?</p></div>';

echo $form;
