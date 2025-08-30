<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// modal view

$width = (isset($wide))
    ? $wide
    : 'md:inset-x-6 lg:inset-x-1/4';

$away = (isset($away))
    ? '@click.away="close()"'
    : '';

$close = (isset($close))
    ? $close
    : '<a @click="close()"><i class="fa-solid fa-circle-xmark fa-2x"></i></a>';

?>
<div <?php echo $away ?> class="px-2 pt-6 md:pt-18 overflow-hidden">
    <div
        id="xmodal"
        class="xmodal fixed overflow-y-auto inset-x-2 <?php echo $width ?> mx-auto
            p-4 md:p-8 lg:p-10 rounded-xl shadow-2xl max_h80 bg-white text-gray-700"
    >
        <div class="modal_head flex flex-row items-center justify-between mb-10">
            <h2><?php echo $title ?></h2>
            <?php echo $close ?>
        </div>
        <?php echo $content ?>
    </div>
</div>
