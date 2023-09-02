<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// category edit form

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
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>'
);

$fields[] = array(
    'label' => _AREA,
    'type' => 'select',
    'value' => $item->id_area,
    'options' => array($areas, 'id', 'name'),
    'name' => 'id_area',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _LANGUAGE,
    'type' => 'select',
    'value' => $item->lang,
    'options' => array($languages, 'code', 'language'),
    'name' => 'lang',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);

$fields[] = array(
    'label' => _TITLE,
    'type' => 'text',
    'value' => $item->title,
    'name' => 'title',
    'rule' => 'required',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => _CATEGORY_TAG,
    'type' => 'text',
    'value' => $item->tag,
    'name' => 'tag',
    'extra' => 'class="w-full"',
    'suggestion' => _CATEGORY_TAG_MSG
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);