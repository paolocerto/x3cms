<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

$xdata = '{
    xload: 0,
    init() {
        this.reload();
        var obj = this;
        tmx = setInterval(function(){obj.reload()}, 60000);
    },
    reload() {
        fetch(root+"home/load", {
            method: "GET",
            headers: { "Content-Type": "text/html" }
        })
        .then(res => res.text())
        .then(txt => {
            if (txt.length <= 5) {
                this.xload = txt;
            } else {
                thi.xload = 0;
            }
        });
    }
}';

?>
<h1><?php echo _ADMIN_AREA ?></h1>
<p><?php echo _HI.' <strong> '.$_SESSION['username'].'</strong>, '._LAST_LOGIN.' '.$_SESSION['last_in'] ?></p>

<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-20 mb-20">
<?php
// notices from x3cms.net
if (NOTICES)
{
	echo '<div x-data=\''.$xdata.'\'>
            <div class="bg rounded-t px-4 py-4"><h4>'._NOTICES_AND_UPDATES.'</h4></div>
            <div class="bg2 h-full px-4 pt-4">
                '.$notices.'
                <p class="mt-4">'._SERVER_LOAD.': <span class="font-bold" x-html="xload"></span></p>
            </div>
        </div>';
}

// widgets
foreach ($widgets as $widget)
{
	echo $widget;
}
?>
</div>
