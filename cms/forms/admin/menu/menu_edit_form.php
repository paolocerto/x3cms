<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// menu edit form

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
    'value' => $item->id_theme,
    'name' => 'id_theme'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);


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
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);