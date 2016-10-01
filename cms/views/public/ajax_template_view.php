<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// close button
if (isset($container)) 
	echo '<div class="aright sbox"><a class="xsmall" href="#" onclick="close_pop(\''.$container.'\');"  title="'._CLOSE.'">'._CLOSE.'</a></div>';

echo $content;
