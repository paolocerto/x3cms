<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
?>
<div class="inner">
<?php 
// close button
if (isset($container)) 
	echo '<div class="aright sbox xsmall"><a href="#" onclick="close_pop(\''.$container.'\');"  title="'._CLOSE.'">'._CLOSE.'</a></div>';

// msg
if (isset($_SESSION['msg']) && !empty($_SESSION['msg']))
{
	echo '<div id="msg"><p>'.$_SESSION['msg'].'</p></div>';
	unset($_SESSION['msg']);
}
echo $content;
?>
</div>
