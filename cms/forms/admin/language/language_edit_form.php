<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// language edit form

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

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div>'
);

$fields[] = array(
    'label' => _CODE,
    'type' => 'text',
    'value' => $item->code,
    'name' => 'code',
    'rule' => 'required|minlength§2|maxlength§2',
    'extra' => 'class="w-full"',
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _LANGUAGE,
    'type' => 'text',
    'value' => $item->language,
    'name' => 'language',
    'rule' => 'required',
    'extra' => 'class="w-full"',
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);

$fields[] = array(
    'label' => _RTL_LANGUAGE,
    'type' => 'checkbox',
    'value' => $item->rtl,
    'name' => 'rtl',
    'checked' => $item->rtl
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);