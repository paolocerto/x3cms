<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// module config form

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
    'value' => $item->name,
    'name' => 'xrif'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $item->id_area,
    'name' =>'id_area'
);


$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

// Build specific fields
foreach ($params as $i)
{
    $tmp = array(
        'label' => ucfirst(str_replace('_', ' ', $i->name))._TRAIT_.$i->description,
        'value' => $i->xvalue,
        'name' => $i->name,
        'rule' => ''
    );

    switch($i->xtype) {
        case 'HIDDEN':
            // do nothing
            break;
        case '0|1':
        case 'BOOLEAN':
            // boolean
            $tmp['type'] = 'checkbox';
            $tmp['suggestion'] = _ON.'/'._OFF;
            if ($i->xvalue == '1') $tmp['checked'] = 1;
            break;
        case 'IMG':
            // TODO: manage image set as param
            break;
        case 'DECIMAL':
            // decimal
            $tmp['type'] = 'text';
            $tmp['suggestion'] = $i->xtype;
            $tmp['extra'] = 'class="w-full text-right"';
            $tmp['rule'] = 'numeric|max§1';
            break;
        case 'INTEGER':
            // integer
            $tmp['type'] = 'text';
            $tmp['suggestion'] = $i->xtype;
            $tmp['extra'] = 'class="w-full text-right"';
            $tmp['rule'] = 'numeric';
            break;
        case 'EMAIL':
            // email
            $tmp['type'] = 'text';
            $tmp['suggestion'] = $i->xtype;
            $tmp['extra'] = 'class="w-full"';
            $tmp['rule'] = 'mail';
            break;
        case 'BLOB':
            // long string
            $tmp['type'] = 'textarea';
            $tmp['suggestion'] = $i->xtype;
            $tmp['extra'] = 'class="w-full"';
            break;
        default:
            // string
            $tmp['type'] = 'text';
            $tmp['suggestion'] = $i->xtype;
            $tmp['extra'] = 'class="w-full"';
            break;
    }

    if ($i->required == '1')
    {
        $tmp['rule'] = 'required|'.$tmp['rule'];
    }

    if ($i->xtype != 'HIDDEN')
    {
        $fields[] = $tmp;
    }
}

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);