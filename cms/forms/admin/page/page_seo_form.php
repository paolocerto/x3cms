<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// page seo form

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
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">
        <div class="relative w-full mx-auto overflow-hidden">'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div x-data="{ open: false }" class="cursor-pointer group">
    <button @click="open = !open" class="bg2 rounded flex items-center justify-between w-full p-4 text-left select-none mb-1">
        <span>'._TEMPLATE.'</span>
        <svg class="w-4 h-4 duration-200 ease-out" :class="{ \'rotate-180\': open }" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
    </button>
    <div x-show="open" @click.away="open = false" x-transition:enter.duration.300ms x-transition:leave.duration.50ms x-cloak>
        <div class="px-2 pb-4">'
);

$fields[] = array(
    'label' => _TEMPLATE,
    'type' => 'select',
    'value' => $page->tpl,
    'options' => array($mod->get_templates(), 'name', 'description'),
    'name' =>'tpl',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>
        </div>
    </div>'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div x-data="{ open: false }" class="cursor-pointer group">
    <button @click="open = !open" class="bg2 rounded flex items-center justify-between w-full p-4 text-left select-none mb-1">
        <span>'._SEO_TOOLS.'</span>
        <svg class="w-4 h-4 duration-200 ease-out" :class="{ \'rotate-180\': open }" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
    </button>
    <div x-show="open" @click.away="open = false" x-transition:enter.duration.300ms x-transition:leave.duration.50ms x-cloak>
        <div class="px-2 pb-4">'
);

$fields[] = array(
    'label' => _URL,
    'type' => 'text',
    'value' => $page->url,
    'name' => 'url',
    'rule' => 'required',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>'
);

$fields[] = array(
    'label' => _NAME,
    'type' => 'text',
    'value' => $page->name,
    'name' => 'name',
    'rule' => 'required',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _TITLE,
    'type' => 'text',
    'value' => $page->title,
    'name' => 'title',
    'rule' => 'required',
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
    'value' => $page->description,
    'name' => 'description'
);

$fields[] = array(
    'label' => _KEYS,
    'type' => 'textarea',
    'value' => $page->xkeys,
    'name' => 'xkeys'
);

$fields[] = array(
    'label' => _ROBOT,
    'type' => 'text',
    'value' => $page->robot,
    'name' => 'robot',
    'suggestion' => _ROBOT_MSG,
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div>'
);

$codes = array(301, 302);
$fields[] = array(
    'label' => _REDIRECT_CODE,
    'type' => 'select',
    'value' => $page->redirect_code,
    'name' => 'redirect_code',
    'options' => array(X4Array_helper::simplearray2obj($codes, 'value', 'option'), 'value', 'option', ''),
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div class="md:col-span-3">'
);

$fields[] = array(
    'label' => _REDIRECT,
    'type' => 'text',
    'value' => $page->redirect,
    'name' => 'redirect',
    'rule' => 'requiredif§redirect_code§!',
    'suggestion' => _REDIRECT_MSG,
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>
        </div>
    </div>'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>
    </div>'
);