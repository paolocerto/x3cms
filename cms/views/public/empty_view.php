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
<script>
<?php

// set redirect
if (isset($location) && !empty($location)) 
{
    if ($location == 'back')
    {
        echo 'window.history.back();';
    }
    else
    {
        // set redirect
	echo 'location.href=\''.$location.'\';';
    }
}
else
{
    // set reload
	echo 'window.location.reload();';
}
?>
</script>
