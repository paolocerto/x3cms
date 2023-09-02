<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// user permissions form

$fields = array();
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $id_user,
    'name' => 'id_user'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $id_area,
    'name' => 'id_area'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $table,
    'name' => 'what'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div
        x-data="{xval: document.getElementById(\'resetter\').value,sync(){setForAll(this.xval);}}"
        class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

$c = 0;
// if table is not empty
if ($what)
{
    $fields[] = array(
        'label' => _ALL_DETAIL_PRIV,
        'type' => 'select',
        'value' => '',
        'name' => 'resetter',
        'options' => array($levels, 'id', 'name', [0, '']),
        'extra' => 'class="w-full" x-model="xval" @change="sync()"'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">'
    );

    // each record
    foreach ($what as $i)
    {
        $fields[] = array(
            'label' => null,
            'type' => 'html',
            'value' => '<div>'
        );

        $value = is_null($i->level) ? 0 : $i->level;
        $fields[] = array(
            'label' => null,
            'type' => 'hidden',
            'value' => $i->id,
            'name' => 'id_'.$c
            );
        $fields[] = array(
            'label' => null,
            'type' => 'hidden',
            'value' => $value,
            'name' => 'old_value_'.$c
            );
        $fields[] = array(
            'label' => $i->name,
            'type' => 'select',
            'value' => $value,
            'name' => 'value_'.$c,
            'options' => array($levels, 'id', 'name', [0, '']),
            'suggestion' => (strlen($i->description) > 100) ? '' : strip_tags($i->description),
            'extra' => 'class="w-full resettable"'
        );

        $fields[] = array(
            'label' => null,
            'type' => 'html',
            'value' => '</div>'
        );
        $c++;
    }

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div>'
    );
}

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);