<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// word edit form

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
    'value' => $item->lang,
    'name' => 'lang'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $item->area,
    'name' => 'area'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

if ($id)
{
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->what,
        'name' => 'what'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->xkey,
        'name' => 'xkey'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<h4 class="mt-6">'.$item->xkey.'</h4>'
    );
}
else
{
    $fields[] = array(
        'label' => _SECTION,
        'type' => 'text',
        'value' => $item->what,
        'name' => 'what',
        'rule' => 'required',
        'extra' => 'class="w-full"'
    );

    $fields[] = array(
        'label' => _KEY,
        'type' => 'text',
        'value' => $item->xkey,
        'name' => 'xkey',
        'rule' => 'required',
        'extra' => 'class="w-full uppercase"'
    );
}

// check for value
// we save text as HTML (we replace \n with <br>)
// but if the text contains HTML we don't replace \n with <br>

$value = str_replace(array('<br />', '<br>', '<br/>'), "\n", $item->xval);
if (strip_tags($value) == $value)
{
    // no other html inside the string
    $value = str_replace(array("\n", '<br />', '<br>', '<br/>'), array('', "\n", "\n", "\n"), $item->xval);
}
else
{
    $value = $item->xval;
}

$fields[] = array(
    'label' => _WORD,
    'type' => 'textarea',
    'value' => $value,
    'name' => 'xval',
    'rule' => 'required'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);