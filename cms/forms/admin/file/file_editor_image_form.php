<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// file editor image form

// build the form
$fields = array();

// editor form
$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<h3> Zoom 1:<span id="zoom_label">1</span></h3>'
);

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $id_file,
    'name' => 'id'
);

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => 1,
    'name' => 'zoom'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div
            class="grid grid-cols-1 md:grid-cols-2 gap-4"
            x-data="imageEditor()"
            x-init="initialize(\'img\', \'image_editor\', \''.FPATH.'img/'.$file->name.'\')"
        >
            <div>'
);

$fields[] = array(
    'label' => _IMAGE_XCOORD,
    'type' => 'text',
    'value' => 0,
    'name' => 'xcoord',
    'rule' => 'numeric',
    'extra' => 'class="text-right w-full" xmodel="xcoord"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _IMAGE_YCOORD,
    'type' => 'text',
    'value' => 0,
    'name' => 'ycoord',
    'rule' => 'numeric',
    'extra' => 'class="text-right w-full" x-model="ycoord"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _IMAGE_WIDTH,
    'type' => 'text',
    'value' => $size[0],
    'name' => 'width',
    'rule' => 'numeric',
    'extra' => 'class="text-right w-full" x-model="width"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _IMAGE_HEIGHT,
    'type' => 'text',
    'value' => $size[1],
    'name' => 'height',
    'rule' => 'numeric',
    'extra' => 'class="text-right w-full" x-model="height"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _IMAGE_LOCK_RATIO,
    'type' => 'checkbox',
    'value' => 1,
    'name' => 'ratio',
    'extra' => 'x-model="ratio"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _IMAGE_AS_NEW,
    'type' => 'checkbox',
    'value' => 1,
    'name' => 'asnew',
    'checked' => 1
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div class="col-span-2">'
);

$fields[] = array(
    'label' => _IMAGE_ROTATE,
    'type' => 'text',
    'case' => 'range',
    'value' => 0,
    'name' => 'rotate',
    'extra' => 'class="w-full" x-model="angle" min="-180" max="180" step="1" @input="rotating()"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<p class="text-center font-bold"><span x-text="angle"></span>&deg;</p>
        '
);
/*
$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div style="overflow:hidden;">
        <img class="thumb mx-auto" id="imagethumb" src="'.FPATH.'img/'.$file->name.'?t='.time().'" />
    </div>'
);
*/
$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);
