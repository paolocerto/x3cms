<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// page add form

// build the form
$fields = array();
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $lang,
    'name' =>'lang'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $id_area,
    'name' =>'id_area'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

$fields[] = array(
    'label' => _NAME,
    'type' => 'text',
    'value' => '',
    'name' => 'name',
    'rule' => 'required',
    'extra' => 'class="w-full"'
);
$fields[] = array(
    'label' => _DESCRIPTION,
    'type' => 'textarea',
    'value' => '',
    'name' => 'description'
);
$fields[] = array(
    'label' => _FROM_PAGE,
    'type' => 'select',
    'value' => str_replace('§', '/', urldecode($xfrom)),
    'options' => array($mod->get_pages(), 'url', 'title'),
    'name' => 'xfrom',
    'rule' => 'required',
    'extra' => 'class="w-full"'
);
$fields[] = array(
    'label' => _TEMPLATE,
    'type' => 'select',
    'value' => '',
    'options' => array($mod->get_templates(), 'name', 'description'),
    'name' =>'tpl',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);
