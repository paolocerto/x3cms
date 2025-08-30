<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// x3banners bar

$xdata = '';
if ($banner->auto_hide)
{
    $xdata = 'x-data=\'{
        seconds:0,
        setup(t) {
            this.seconds = t;
            var obj = this;
            tmx = setInterval(function(){obj.updateTimer()},1000);
        },
        updateTimer() {
            var obj = this;
            this.seconds--;
            if (this.seconds <= 0) {
                clearInterval(tmx);
            }
        }
    }\' x-init="setup('.$banner->auto_hide.')" x-show="seconds > 0" x-transition.opacity.duration.500ms';
}

?>

<script>var tmx;</script>
<style>
#banner_top a {color: <?php echo $banner->link_color ?>}
#topic .section:first-of-type {padding-top:6em !important;}
</style>
<div
    id="banner_top"<?php echo $xdata ?>
    style="background:<?php echo $banner->bg_color1.';'.$gradient.'color:'.$banner->fg_color ?>"
    class="w-full z-10 shadow-lg brightness-125 hover:brightness-100 text-center"
>
    <div class="max-w-screen-lg pt-6 pb-4 mx-auto"><?php echo $banner->description ?></div>
</div>
