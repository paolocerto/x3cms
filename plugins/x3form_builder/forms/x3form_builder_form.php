<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// Form builder form

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
$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>'
);

$fields[] = array(
    'label' => _X3FB_FORM_NAME,
    'type' => 'text',
    'value' => $item->name,
    'name' => 'name',
    'rule' => 'required',
    'suggestion' => _X3FB_FORM_NAME_SUGGESTION,
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _X3FB_TITLE,
    'type' => 'text',
    'value' => $item->title,
    'name' => 'title',
    'rule' => 'required',
    'suggestion' => _X3FB_TITLE_SUGGESTION,
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);

$fields[] = array(
    'label' => _DESCRIPTION,
    'type' => 'textarea',
    'value' => $item->description,
    'name' => 'description'
);

$mailto = explode('|', $item->mailto);

$c = 0;
foreach ($mailto as $i)
{
    $fields[] = array(
        'label' => _X3FB_MAILTO,
        'type' => 'text',
        'value' => $i,
        'name' => 'mailto'.$c,
        'rule' => 'mail',
        'extra' => 'class="w-full"'
    );
    $c++;
}

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $c,
    'name' => 'mailto_num'
);

$fields[] = array(
    'label' => _X3FB_MAILTO,
    'type' => 'text',
    'value' => '',
    'name' => 'mailto'.$c,
    'rule' => 'mail',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>'
);

$fields[] = array(
    'label' => _X3FB_MSG_OK,
    'type' => 'textarea',
    'value' => $item->msg_ok,
    'name' => 'msg_ok',
    'rule' => 'required'
    );

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _X3FB_MSG_FAILED,
    'type' => 'textarea',
    'value' => $item->msg_failed,
    'name' => 'msg_failed',
    'rule' => 'required'
    );

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _X3FB_SUBMIT_BUTTON,
    'type' => 'text',
    'value' => $item->submit_button,
    'name' => 'submit_button',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _X3FB_RESET_BUTTON,
    'type' => 'text',
    'value' => $item->reset_button,
    'name' => 'reset_button',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div></div>'
);