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
<i class="fas fa-refresh fa-spin fa-3x" aria-hidden="true"></i>
<script>
<?php
if (isset($location))
{
	// set redirect
	echo 'location.href="'.$location.'";';
}
else
{
	// set reload
	echo 'window.location.reload();';
}
?>
</script>
