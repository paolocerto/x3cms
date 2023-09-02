<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// site edit form

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
    'value' => 'site',
    'name' => 'xrif'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => 0,
    'name' => 'id_area'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

foreach ($params as $i)
{
    switch($i->xtype)
    {
        case '0|1':
            $tmp = array(
                'label' => ucfirst(str_replace('_', ' ', $i->name))._TRAIT_.$i->description,
                'type' => 'checkbox',
                'value' => $i->xvalue,
                'name' => $i->name,
                'suggestion' => _ON.'/'._OFF,
                'checked' => $i->xvalue
            );
            break;
        case 'IMG':
            // TODO: set image as param
            break;
        case 'INTEGER':
            $tmp = array(
                'label' => ucfirst(str_replace('_', ' ', $i->name))._TRAIT_.$i->description,
                'type' => 'text',
                'value' => $i->xvalue,
                'name' => $i->name,
                'suggestion' => $i->xtype,
                'extra' => 'class="w-full text-right"',
                'rule' => 'numeric'
            );
            if ($i->required == '1') $tmp['rule'] = 'required|'.$tmp['rule'];
            break;
        case 'EMAIL':
            $tmp = array(
                'label' => ucfirst(str_replace('_', ' ', $i->name))._TRAIT_.$i->description,
                'type' => 'text',
                'value' => $i->xvalue,
                'name' => $i->name,
                'suggestion' => $i->xtype,
                'extra' => 'class="w-full"',
                'rule' => 'mail'
            );
            if ($i->required == '1') $tmp['rule'] = 'required';
            break;
        default:
            $tmp = array(
                'label' => ucfirst(str_replace('_', ' ', $i->name))._TRAIT_.$i->description,
                'type' => 'text',
                'value' => $i->xvalue,
                'name' => $i->name,
                'suggestion' => $i->xtype,
                'extra' => 'class="w-full"'
            );
            if ($i->required == '1') $tmp['rule'] = 'required';
            break;
    }
    $fields[] = $tmp;
}

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);
