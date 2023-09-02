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
<h1><?php echo _ADMIN_AREA ?></h1>
<p><?php echo _HI.' <strong> '.$_SESSION['username'].'</strong>, '._LAST_LOGIN.' '.$_SESSION['last_in'] ?></p>

<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 place-items-stretch space-x-4 mb-20">
<?php
// notices from x3cms.net
if (NOTICES)
{
	echo '<div>
            <div class="bg rounded-t px-4 py-4"><h4>'._NOTICES_AND_UPDATES.'</h4></div>
            <div class="bg2 h-full px-4 pt-4 pb-8">'.$notices.'</div>
        </div>';
}

// widgets
foreach ($widgets as $widget)
{
	echo $widget;
}
?>
</div>
