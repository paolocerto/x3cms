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
<i class="fa fa-refresh fa-spin fa-3x" aria-hidden="true"></i>
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
