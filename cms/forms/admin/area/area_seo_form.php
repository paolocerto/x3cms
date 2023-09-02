<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// area SEO form

// build the form
$fields = array();
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $id_area,
    'name' => 'id_area'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="relative w-full mx-auto px-6 py-6 overflow-hidden bg-white">'
);

$c = 0;
// for each enabled language
foreach ($items as $i)
{
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<div x-data="{ open: false }" class="cursor-pointer group">
        <button @click="open = !open" class="bg2 rounded flex items-center justify-between w-full p-4 text-left select-none mb-1">
            <span>'.ucfirst($i->language).'</span>
            <svg class="w-4 h-4 duration-200 ease-out" :class="{ \'rotate-180\': open }" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </button>
        <div x-show="open" @click.away="open = false" x-transition:enter.duration.300ms x-transition:leave.duration.50ms x-cloak>
            <div class="p-4 pt-0">'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $i->id,
        'name' => 'id_'.$c
    );
    $fields[] = array(
        'label' => _NAME,
        'type' => 'text',
        'value' => $i->title,
        'name' => 'title_'.$c,
        'rule' => 'required',
        'extra' => 'class="w-full"'
    );
    $fields[] = array(
        'label' => _DESCRIPTION,
        'type' => 'textarea',
        'value' => $i->description,
        'name' => 'description_'.$c
    );
    $fields[] = array(
        'label' => _KEYS,
        'type' => 'textarea',
        'value' => $i->keywords,
        'name' => 'keywords_'.$c
    );
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div>
            </div>
        </div>'
    );
    $c++;
}

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);