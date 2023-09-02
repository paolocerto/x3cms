<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// file upload form

// build the form
$fields = array();

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<p class="text-sm">'.ucfirst(_FILE_SIZES).' '.MAX_W.'x'.MAX_H.' px - '.ceil(MAX_IMG/1024).' MB / '.ceil(MAX_DOC/1024).' MB</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>'
);

$fields[] = array(
    'label' => _AREA,
    'type' => 'select',
    'value' => $id_area,
    'name' => 'id_area',
    'options' => array($areas, 'id', 'title'),
    'multiple' => 4,
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _CATEGORY,
    'type' => 'text',
    'value' => ($ctg == '-') ? '' : $ctg,
    'name' => 'category',
    'extra' => 'class="w-full"',
    'rule' => 'required'
);

$fields[] = array(
    'label' => _SUBCATEGORY,
    'type' => 'text',
    'value' => ($sctg == '-') ? '' : $sctg,
    'name' => 'subcategory',
    'extra' => 'class="w-full"',
    'rule' => 'required'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="w-full"
        x-on:drop="$refs.dnd.classList.remove(\'bg2\')"
        x-on:drop.prevent="selectFile(\'filename\', Object.values($event.dataTransfer.files))"
        x-on:dragover.prevent="$refs.dnd.classList.add(\'bg2\')"
        x-on:dragleave.prevent="$refs.dnd.classList.remove(\'bg2\')"
    >
        <label x-ref="dnd" class="text-center cursor-pointer h-1/2 rounded bg p-12"
            for="filename"
        >
            <h3 class="text-3xl">'._DROP_MSG.'</h3>
        </label>'
);

$fields[] = array(
    'label' => null,
    'alabel' => _FILE,
    'type' => 'file',
    'value' => '',
    'name' => 'filename',
    'rule' => 'required',
    'multiple' => 5,
    'extra' => 'class="hidden" @change="selectFile(\'filename\', Object.values($event.target.files))"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div x-show="files[\'filename\'] != null && files[\'filename\'].length > 0">
        <table class="table-auto">
        <tr>
            <th>'._FILE_LIST.'</th>
            <th>'._DESCRIPTION.'</th>
            <th>Info</th>
            <th></th>
        </tr>
        <template x-for="(file, index) in files[\'filename\']">
            <tr>
                <td class="align-middle" x-text="file.name"></td>
                <td x-html="altInput(index, file.name)" class="p-0"></td>
                <td class="w-8 text-sm">
                    <span x-text="file.type"></span><br>
                    <span x-text="humanFileSize(file.size)"></span>
                </td>
                <td class="align-middle w-3">
                    <a class="link" @click="removeFile(\'filename\', index)"><i class="fa-solid fa-lg fa-trash warn"></i></a>
                </td>
            </tr>
        </template>
        </table>
    </div>'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);
