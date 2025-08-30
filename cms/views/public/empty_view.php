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
<script>
<?php

// set redirect
if (isset($location) && !empty($location))
{
    switch ($location)
    {
        case 'back':
            echo 'window.history.back();';
            break;
        case 'blank':
            // require URL
            echo 'window.open(\''.$url.'\', \'_blank\');window.location.reload();';
            break;
        default:
            // set redirect
	        echo 'location.href=\''.$location.'\';';
            break;
    }
}
else
{
    // set reload
	echo 'window.location.reload();';
}
?>
</script>
