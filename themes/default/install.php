<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// default theme installer

$theme = 'default';
$version = '3';

// styles
$styles = [
    ['what' => 'section', 'style' => 'bg-fucsia', 'description' => 'background fucsia'],
    ['what' => 'section', 'style' => 'bg-orange', 'description' => 'background orange'],
    ['what' => 'section', 'style' => 'bg-petroleum', 'description' => 'background petroleum'],
    ['what' => 'section', 'style' => 'bg-violet', 'description' => 'background violet'],
    ['what' => 'section', 'style' => 'bg-military', 'description' => 'background military'],
    ['what' => 'section', 'style' => 'fucsium', 'description' => 'gradient fucsia -> petroleum'],
    ['what' => 'section', 'style' => 'orangium', 'description' => 'gradient orange -> petroleum'],
    ['what' => 'section', 'style' => 'fucsange', 'description' => 'gradient fucsia -> orange'],
    ['what' => 'article', 'style' => 'overlay1 rounded-2xl p-8', 'description' => 'ultra lightgray transparent rounded'],
    ['what' => 'article', 'style' => 'overlay2 rounded-2xl p-8', 'description' => 'lightgray transparent rounded'],
    ['what' => 'article', 'style' => 'overlay3 rounded-2xl p-8', 'description' => 'gray transparent rounded'],
    ['what' => 'article', 'style' => 'border-2 border-neutral-200 rounded-2xl p-8', 'description' => 'lightgray bordered rounded'],
    ['what' => 'article', 'style' => 'border-2 border-neutral-200 p-8', 'description' => 'lightgray bordered not-rounded'],
    ['what' => 'article', 'style' => 'bg-fucsia rounded-2xl p-8', 'description' => 'background fucsia rounded'],
    ['what' => 'article', 'style' => 'bg-orange rounded-2xl p-8', 'description' => 'background orange rounded'],
    ['what' => 'article', 'style' => 'bg-petroleum rounded-2xl p-8', 'description' => 'background petroleum rounded'],
    ['what' => 'article', 'style' => 'bg-violet rounded-2xl p-8', 'description' => 'background violet rounded'],
    ['what' => 'article', 'style' => 'bg-military rounded-2xl p-8', 'description' => 'background military rounded'],
];

// theme
$sql = "INSERT INTO themes (updated, name, description, styles, version, xon) VALUES (NOW(), '$theme', 'Default theme', '".json_encode($styles)."', '$version', 0)";

// templates
$templates = array();

// settings template base
$t1 = [
    's1' => [
        'locked' => 'y',
        'bgcolor' => 'default',
        'fgcolor' => 'default',
        'columns' => 1,
        'col_sizes' => '1',
        'width' => 'fullwidth',
        'height' => 'free',
        'style' => '',
        'class' => '',
        'col_settings' => [
            'bg0' => '',
            'fg0' => '',
            'style0' => '',
            'class0' => ''
        ]
    ],
    'sn' => [
        'bgcolor' => '#ffffff',
        'fgcolor' => '#444444',
        'columns' => 3,
        'col_sizes' => '2+1',
        'width' => 'container mx-auto',
        'height' => 'free',
        'style' => '',
        'class' => '',
        'col_settings' => [
            'bg0' => '',
            'fg0' => '',
            'style0' => '',
            'class0' => '',
            'bg1' => '',
            'fg1' => '',
            'style1' => '',
            'class1' => ''
        ]
    ]
];

// settings template two
$t2 = [
    's1' => [
        'locked' => 'y',
        'bgcolor' => 'default',
        'fgcolor' => 'default',
        'columns' => 1,
        'col_sizes' => '1',
        'width' => 'fullwidth',
        'height' => 'free',
        'style' => '',
        'class' => '',
        'col_settings' => [
            'bg0' => '',
            'fg0' => '',
            'style0' => '',
            'class0' => ''
        ]
    ],
    's2' => [
        'locked' => 'y',
        'bgcolor' => 'default',
        'fgcolor' => 'default',
        'columns' => 1,
        'col_sizes' => '1',
        'width' => 'fullwidth',
        'height' => 'free',
        'style' => '',
        'class' => '',
        'col_settings' => [
            'bg0' => '',
            'fg0' => '',
            'style0' => '',
            'class0' => ''
        ]
    ],
    'sn' => [
        'bgcolor' => '#ffffff',
        'fgcolor' => '#444444',
        'columns' => 3,
        'col_sizes' => '2+1',
        'width' => 'container mx-auto',
        'height' => 'free',
        'style' => '',
        'class' => '',
        'col_settings' => [
            'bg0' => '',
            'fg0' => '',
            'style0' => '',
            'class0' => '',
            'bg1' => '',
            'fg1' => '',
            'style1' => '',
            'class1' => ''
        ]
    ]
];

$templates[] = "INSERT INTO templates (updated, name, js, css, id_theme, description, settings, sections, xon) VALUES (NOW(), 'base', 'script', 'base', XXX, 'Base page template', '".json_encode($t1)."', 1, 1)";
$templates[] = "INSERT INTO templates (updated, name, js, css, id_theme, description, settings, sections, xon) VALUES (NOW(), 'two', 'script', 'base', XXX, 'Two columns template','".json_encode($t2)."', 2, 1)";

// menus
$menus = array();
$menus[] = "INSERT INTO menus (updated, id_theme, name, description, xon) VALUES (NOW(), XXX, 'menu_top', 'Top menu', 1)";
