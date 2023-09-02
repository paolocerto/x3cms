<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// group privs form

// remove superadmin
$tmp = array_pop($levels);

// build the form
$fields = array();

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $id_group,
    'name' => 'id'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $private,
    'name' => 'xrif'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">'
);

foreach ($types as $i)
{
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<div>'
    );

    // actual permission level
    $value = (isset($gprivs[$i->name]))
        ? $gprivs[$i->name]
        : 0;

    $fields[] = array(
        'label' => constant($i->description),
        'type' => 'select',
        'value' => $value,
        'name' => $i->name,
        'options' => array($levels, 'id', 'name', [0, 'no privs']),
        'extra' => 'class="w-full"'
    );
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $value,
        'name' => 'old_'.$i->name
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div>'
    );
}

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);
