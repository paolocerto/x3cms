<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// Form builder blacklist item form

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
    'type' => 'hidden',
    'value' => $item->id_area,
    'name' => 'id_area'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $item->lang,
    'name' => 'lang'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

if ($id)
{
    $fields[] = array(
        'label' => _X3FB_BLACKLIST_ITEM,
        'type' => 'text',
        'value' => $item->name,
        'name' => 'name',
        'rule' => 'required',
        'extra' => 'class="w-full"'
    );
}
else
{
    $fields[] = array(
        'label' => _X3FB_BLACKLIST_ITEM,
        'type' => 'textarea',
        'value' => $item->name,
        'name' => 'name',
        'rule' => 'required',
        'suggestion' => _X3FB_BLACKLIST_ITEM_MSG
    );
}

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);