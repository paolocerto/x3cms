<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

echo stripslashes(X4Utils_helper::build_menu('A', $menus['admin_global'], 1, 5, 'id="nav"'));
?>
<script>
$('nav').MooDropMenu();
linking('#nav li a');
</script>
