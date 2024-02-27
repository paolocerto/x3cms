<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// modal view

$width = (isset($wide))
    ? $wide
    : 'md:inset-x-6 lg:w-2/3 xl:w-1/3';

$away = (isset($away))
    ? '@click.away="close()"'
    : '';

?>
<div id="modal" <?php echo $away ?> class="pt-8 md:pt-16" >
    <div
        class="fixed max-h-full overflow-y-auto inset-x-2 <?php echo $width ?> mx-auto
            rounded shadow-2xl xmodal"
    >
        <div>
            <div class="bg-white text-gray-700 md:px-8 md:pt-8 px-4 py-4">
                <div class="flex flex-row items-center justify-between">
                    <h3 class="my-3 font-bold tracking-tight"><?php echo $title ?></h3>
                    <a class="link" @click="close()" @keyup.escape.window="close()">
                        <i class="fa-solid fa-2x fa-circle-xmark" ></i>
                    </a>
                </div>
            </div>
            <div>
                <?php echo $content ?>
            </div>
        </div>

    </div>
</div>
