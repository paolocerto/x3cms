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
$styles = [];

// theme
$sql = "INSERT INTO themes (updated, name, description, styles, version, xon) VALUES (NOW(), '$theme', 'Default theme', '".json_encode($styles)."', '$version', 0)";

// templates
$templates = [];

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
        'width' => 'container mx-auto',
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
$templates[] = "INSERT INTO templates (updated, name, js, css, id_theme, description, settings, sections, xon) VALUES (NOW(), 'side', 'script', 'base', XXX, 'Sidebar template','".json_encode($t2)."', 2, 1)";

// menus
$menus = [];
$menus[] = "INSERT INTO menus (updated, id_theme, name, description, xon) VALUES (NOW(), XXX, 'menu_top', 'Top menu', 1)";
$menus[] = "INSERT INTO menus (updated, id_theme, name, description, xon) VALUES (NOW(), XXX, 'menu_left', 'Left menu', 1)";
