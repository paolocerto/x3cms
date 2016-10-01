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
<!DOCTYPE html>
<html>
<body>
<div id="close-modal"><i class="fa fa-times fa-lg"></i></div>
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
</body>
</html>
