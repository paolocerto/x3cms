<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// memos list view

?>
<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">

<div class="grid grid-cols-1 gap-4">
<?php

foreach ($items as $i)
{
    $author = ($i->email == 'nomail')
        ? $i->author
        : '<a href="mailto:'.$i->email.'">'.$i->author.'</a>';

    $edit = ($i->xuid != $_SESSION['xuid'])
        ? ''
        : '<a class="link" @click="popup(\''.BASE_URL.'memo/edit/'.$i->url.'/'.$i->id.'\')" title="'._EDIT.'">
                <i class="fa-solid fa-lg fa-pen-to-square"></i>
            </a>';

    echo '<div class="mb-4 p-4 bg-slate-100 rounded">
        <div class="flex flex-row space-x-4">
            <div class="flex-1">
                <p class="font-bold">
                    '.$author.' -
                    <span class="text-xl ">'.$i->title.'</span>
                </p>
            </div>
            <div class="flex-none">
                '.$edit.'
            </div>
        </div>
        <div class="mt-4 p-4 border-l-4 border-slate-500">'.$i->description.'</div>

    </div>';
}

?>
</div>
</div>
