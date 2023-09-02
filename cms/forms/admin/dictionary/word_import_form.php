<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// word import form

// build the form
$fields = array();

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $lang,
    'name' => 'lang'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $area,
    'name' => 'area'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<h4 class="mt-6">'._IMPORT_INTO.' '.$lang.'/'.$area.'</h4>'
);

$fields[] = array(
    'label' => _SECTION,
    'type' => 'select',
    'value' => '',
    'name' => 'what',
    'options' => array($sections, 'value', 'option'),
    'rule' => 'required',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);