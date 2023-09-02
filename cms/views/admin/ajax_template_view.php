<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
 
?>
<div class="inner">
<?php 
// close button
if (isset($close)) 
{
	echo '<div id="close-modal" title="'._CLOSE.'"><span class="fas fa-times fa-lg"></span></div>';
}
// msg
if (isset($_SESSION['msg']) && !empty($_SESSION['msg']))
{
	echo '<div id="msg"><div><p>'.$_SESSION['msg'].'</p></div></div>';
	unset($_SESSION['msg']);
}

echo $content;

// js
if (isset($js)) 
{
	echo $js;
}
?>
</div>
