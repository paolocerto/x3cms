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
<script>
<?php

// set redirect
if (isset($location)) 
{
    // set redirect
	echo 'location.href=\''.$location.'\';';
}
else
{
    // set reload
	echo 'window.location.reload();';
}
?>
</script>
