<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// article date form

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
    'value' => $item->id_area,
    'name' => 'id_area'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div>'
);

    $fields[] = array(
        'label' => _START_DATE,
        'type' => 'text',
        'case' => 'date',
        'value' => date('Y-m-d', $item->date_in),
        'name' =>'date_in',
        'rule' => 'required|date',
        'extra' => 'class="w-full"  autocomplete="off"'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div><div>'
    );

    $fields[] = array(
        'label' => _END_DATE,
        'type' => 'text',
        'case' => 'date',
        'value' => ($item->date_out > 0) ? date('Y-m-d', $item->date_out) : '',
        'name' =>'date_out',
        'suggestion' => _LEAVE_EMPTY_FOR_UNDEFINED,
        'rule' => 'date|after-date_in',
        'extra' => 'class="w-full"'
    );

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);
