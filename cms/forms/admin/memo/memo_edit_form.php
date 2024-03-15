<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// memo edit form

$xdata = '{
    tinit(id_area, lang, api_key) {
        if (tinymce) {
            // reset
            tinymce.remove();
        }
        setTimeout(function(){tiny(id_area, lang, api_key);},200);
    }
}';

// build the form
$fields = array();

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $item->id,
    'name' => 'id'
);

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $item->lang,
    'name' => 'lang'
);

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $item->url,
    'name' => 'url'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div
                    x-data=\''.$xdata.'\'
                    x-init="tinit('.$id_area.',\''.$item->lang.'\', \''.FLMNGR_API_KEY.'\')"
                    class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">
                <p>'._MEMO_MSG.'</p>
                <div class="grid grid-cols-5 md:grid-cols-6 gap-4">
                    <div class="col-span-4 md:col-span-5">'
);

$fields[] = array(
    'label' => _TITLE,
    'type' => 'text',
    'value' => $item->title,
    'name' => 'title',
    'rule' => 'required',
    'extra' => 'class="w-full" autocomplete="off"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _MEMO_PERSONAL,
    'type' => 'checkbox',
    'value' => 1,
    'name' => 'personal',
    'checked' => $item->personal
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);

$fields[] = array(
    'label' => _MEMO,
    'type' => 'textarea',
    'value' => $item->description,
    'name' => 'description',
    'rule' => 'required',
    'extra' => 'class="w-full tinymce"'
);

if ($item->id)
{
    $fields[
    ] = array(
        'label' => _DELETE,
        'type' => 'checkbox',
        'value' => 1,
        'name' => 'delete',
        'checked' => 0
    );
}

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);