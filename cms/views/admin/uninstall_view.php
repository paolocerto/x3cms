<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

echo '<div id="close-modal" title="'._CLOSE.'"><i class="fa fa-times fa-lg"></i></div>';
echo '<h2>'.$title.'</h2><p>';

if (!empty($msg))
{
	echo $msg.'<br />';
}

echo _ARE_YOU_SURE_UNINSTALL.' <strong>"'.$item,'"</strong>?</p>'.$form;
