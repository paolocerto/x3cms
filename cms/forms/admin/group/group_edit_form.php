<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// group edit form

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

if ($id)
{
    // update a group
    $area = $mod->get_by_id($item->id_area, 'areas', 'title');
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<h4 class="mt-6">'._AREA.': '.$area->title.'</h4>'
    );
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->id_area,
        'name' => 'id_area'
    );
}
else
{
    $fields[] = array(
        'label' => _AREA,
        'type' => 'select',
        'value' => '',
        'options' => array($mod->get_areas(), 'id', 'title'),
        'name' =>'id_area',
        'extra' => 'class="w-full"'
    );
}

$fields[] = array(
    'label' => _NAME,
    'type' => 'text',
    'value' => $item->name,
    'name' => 'name',
    'rule' => 'required',
    'extra' => 'class="w-full"'
);
$fields[] = array(
    'label' => _DESCRIPTION,
    'type' => 'textarea',
    'value' => $item->description,
    'name' => 'description',
    'rule' => 'required',
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);
