<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// tiny MCE
if (isset($tinymce))
{
	echo $tinymce;
}

echo '<div class="block">';

// title
if (isset($title))
{
	echo '<h2>'.$title.'</h2>';
}

if (isset($msg))
{
	echo '<p>'.$msg.'</p>';
}
echo $form.'</div>';
