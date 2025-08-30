<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// keys search in section dictionary form

$fields = [];
$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<h2>'._KEYS_LIST.': '.sizeof($items).'</h2>
        <table>'
);

$c = 0;
foreach ($items as $i)
{
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<tr><td class="w-8 text-center vertical-middle">#'.$i->id.'<br />'
    );

    // checkbox
    $fields[] = array(
        'label' => null,
        'alabel' => _KEY.': '.$i->xkey,
        'type' => 'checkbox',
        'value' => $i->id,
        'name' => 'k'.$c,
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</td><td>'.$i->xkey.' - <span class="text-sm">'.$i->xval.'</span></td></tr>'
    );
    $c++;
}

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</table>'
);

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $c,
    'name' => 'counter'
);