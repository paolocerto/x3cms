<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
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

// msg
if (isset($_SESSION['msg']) && !empty($_SESSION['msg']))
{
	echo '<div id="msg" class="warning"><p>'.$_SESSION['msg'].'</p></div>';
	unset($_SESSION['msg']);
}

if (isset($msg)) 
{
	echo '<p>'.$msg.'</p>';
}
echo $form.'</div>';

