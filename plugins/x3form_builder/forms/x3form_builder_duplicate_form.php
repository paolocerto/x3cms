<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// Form builder duplicate form
// build the form
$fields = array();
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $id_form,
    'name' => 'id_form'
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
    'label' => _X3FB_AREA,
    'type' => 'select',
    'value' => $item->id_area,
    'options' => array($mod->get_areas(), 'id', 'name'),
    'name' => 'id_area',
    'extra' => 'class="w-full"',
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _X3FB_LANG,
    'type' => 'select',
    'value' => $item->lang,
    'options' => array($mod->get_languages(), 'code', 'language'),
    'name' => 'lang',
    'extra' => 'class="w-full"',
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
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
    'value' => '</div>'
);