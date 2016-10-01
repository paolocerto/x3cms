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
<script>
<?php
if (isset($location)) echo 'location.href=\''.$location.'\';';
else echo 'window.location.reload();';
?>
</script>
