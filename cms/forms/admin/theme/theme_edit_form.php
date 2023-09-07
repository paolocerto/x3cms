<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// theme edit form

// build the form
$fields = array();
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $id,
    'name' => 'id'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);


$start_value = empty($item->styles)
? '[]'
: $item->styles;

$fields[] = array(
'label' => null,
'type' => 'html',
'value' => '<div
    x-data="configurator()"
    x-init=\'setup('.json_encode($js_fields).', '.$start_value.', "styles", "tcomposer", "themes/decompose/", "js_fields", 1)\'
    x-cloak
>'
);
$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-8 gap-4">
            <div>'
);

$options = ['section', 'article'];
$fields[] = array(
    'label' => _THEME_WHAT,
    'type' => 'select',
    'value' => '',
    'options' => array(X4Array_helper::simplearray2obj($options), 'value', 'option', ''),
    'name' => 'what',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div class="col-span-3">'
);

$fields[] = array(
    'label' => _THEME_STYLE,
    'type' => 'text',
    'value' => '',
    'name' => 'style',
    'extra' => 'class="w-full"',
    'suggestion' => _THEME_STYLE_MSG
);

$fields[] = array(
'label' => null,
'type' => 'html',
'value' => '</div><div class="col-span-3">'
);

$fields[] = array(
    'label' => _DESCRIPTION,
    'type' => 'text',
    'value' => '',
    'name' => 'description',
    'extra' => 'class="w-full"'
);

$fields[] = array(
'label' => null,
'type' => 'html',
'value' => '</div><div>'
);

$fields[] = array(
'label' => null,
'type' => 'html',
'value' => '<label class="font-xs">&nbsp;</label>
    <button type="button" class="btn link" @click="addItem()">
        <i class="fas fa-plus fa-lg"></i>
    </button>'
);

$fields[] = array(
'label' => null,
'type' => 'html',
'value' => '</div></div>
    <table id="tcomposer">'.$tr.'</table>'
);

$fields[] = array(
'label' => null,
'type' => 'textarea',
'value' => $item->styles,
'name' => 'styles',
'extra' => 'class="hidden"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);
