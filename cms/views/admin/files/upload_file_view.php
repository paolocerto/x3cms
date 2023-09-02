<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

echo '<p class="small">'.ucfirst(_FILE_SIZES).' '.MAX_W.'x'.MAX_H.' px - '.ceil(MAX_IMG/1024).' MB / '.ceil(MAX_DOC/1024).' MB</p>';


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
