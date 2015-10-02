<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
?>
<h2><?php echo _EDIT_DETAIL_PRIV ?></h2>
<h4><?php echo _USER.': '.$user->username.BR._TABLE.': '.$table ?></h4>
<div id="hform">

<?php
echo $form;
?>

</div>
