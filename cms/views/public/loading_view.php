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
<div class="block text-center padded">
<i class="fas fa-refresh fa-spin fa-3x" aria-hidden="true"></i>
</div>
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
