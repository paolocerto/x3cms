<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// template install form

// build the form
$fields = array();

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $id_theme,
    'name' => 'id_theme'
);

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $template_name,
    'name' => 'name',
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<h3 class="mt-6">'._TEMPLATE.': '.$template_name.'</h3>',
);
$fields[] = array(
    'label' => _DESCRIPTION,
    'type' => 'textarea',
    'value' => '',
    'name' => 'description',
);

// load available CSS style sheets
$fields[] = array(
    'label' => _CSS,
    'type' => 'select',
    'value' => 'base',
    'options' => array($this->get_css($theme->name), 'value', 'option'),
    'name' => 'css',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => _SECTIONS,
    'type' => 'text',
    'value' => 1,
    'name' => 'sections',
    'rule' => 'required|numeric',
    'extra' => 'class="w-full text-right"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);
