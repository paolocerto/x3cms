<?php
defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// page view
?>
<div id="actions-bar" class="flex">
    <div class="flex-auto px-4 py-3 text-sm text-gray-600">
        <?php echo X4Theme_helper::navbar($breadcrumb, ' . ') ?>
    </div>

    <div class="flex-auto px-6 py-3 text-sm text-gray-600 ">
        <div class="text-right space-x-2">
            <?php echo $actions ?>
        </div>
    </div>
</div>

<div
    id="topic"
    class="px-4 md:px-8 py-3 md:py-5 bg-white rounded-l"
>
    <?php echo $content ?>


<?php
if (DEBUG || DEVEL)
{
    echo X4Bench_core::info('<p class="text-center text-xs mt-8">X4WebApp v. {x4wa_version} &copy; Cblu.net - execution time: {execution_time} - memory usage: {memory_usage} - queries: {queries} - included files: {included_files}</p>');
}
?>
</div>
<script>
/* BASIC JS to handle back and reload actions */
if (document.getElementById('main') == undefined)
{
    document.getElementById('actions-bar').style.display = 'none';
    document.getElementById("topic").style.display = 'none';
    var url = window.location.href.split('/admin/');
    window.location.href = url[0]+'/admin/home/start/'+ url[1].replace(/\//g, '§');
}
</script>
