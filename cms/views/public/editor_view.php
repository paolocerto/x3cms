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

// msg
if (isset($_SESSION['msg']) && !empty($_SESSION['msg']))
{
	echo '<div id="msg" class="warning"><p>'.$_SESSION['msg'].'</p></div>';
	unset($_SESSION['msg']);
}

echo '<div class="block"><h2>'.$title.'</h2>'.$form.'</div>';
