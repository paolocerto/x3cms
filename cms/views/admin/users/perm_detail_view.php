<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
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
<script src="<?php echo THEME_URL ?>js/basic.js"></script>