<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// x3banners Edit form

$xdata = '{
    setup() {
        var bg = new JSColor("#bg_color");
        var fg = new JSColor("#fg_color");
        var link = new JSColor("#link_color");
    }
}';

// to handle file\'s label
$file_array = array();
// to handle optional JS
$js_array = array();

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
    'type' => 'hidden',
    'value' => $item->id_area,
    'name' => 'id_area'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $item->lang,
    'name' => 'lang'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div
        x-data="small_editor()"
        x-init="tinit('.$item->id_area.',\''.$item->lang.'\')"
        x-cloak
        class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

$fields[] = array(
    'label' => _TITLE,
    'type' => 'text',
    'value' => stripslashes($item->title),
    'name' => 'title',
    'rule' => 'required',
    'extra' => 'class="w-full"',
);
$fields[] = array(
    'label' => _DESCRIPTION,
    'type' => 'textarea',
    'value' => $item->description,
    'name' => 'description',
    'extra' => 'class="tinymce"'
);

$fields[] = array(
    'label' => _X3BANNERS_ID_PAGE,
    'type' => 'select',
    'value' => $item->id_page,
    'name' => 'id_page',
    'options' => array($pages, 'id', 'name', [0, '']),
	'rule' => 'required|numeric',
    'extra' => 'class="text-right w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div>'
);

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => 'Y-m-d H:i',
    'name' => 'datetime_format'
);

$fields[] = array(
    'label' => _X3BANNERS_START_DATE,
    'type' => 'text',
    'value' => $item->start_date,
    'name' => 'start_date',
	'rule' => 'required|datetime',
	'suggestion' => _X3BANNERS_START_DATE_MSG,
    'case' => 'datetime-local',
	'extra' => 'class="w-full" autocomplete="off"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _X3BANNERS_END_DATE,
    'type' => 'text',
    'value' => $item->end_date,
    'name' => 'end_date',
	'rule' => 'required|datetime|after§start_date',
	'suggestion' => _X3BANNERS_END_DATE_MSG,
    'case' => 'datetime-local',
	'extra' => 'class="w-full" autocomplete="off"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div
        x-data=\''.$xdata.'\' x-init="setup()"
        class="grid grid-cols-1 md:grid-cols-3 gap-4"
    >
        <div>'
);
$fields[] = array(
    'label' => _X3BANNERS_BG_COLOR,
    'type' => 'text',
    'value' => stripslashes($item->bg_color),
    'name' => 'bg_color',
	'rule' => 'required',
	'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _X3BANNERS_FG_COLOR,
    'type' => 'text',
    'value' => stripslashes($item->fg_color),
    'name' => 'fg_color',
	'rule' => 'required',
	'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _X3BANNERS_LINK_COLOR,
    'type' => 'text',
    'value' => stripslashes($item->link_color),
    'name' => 'link_color',
	'rule' => 'required',
	'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);

$fields[] = array(
    'label' => _X3BANNERS_AUTO_HIDE,
    'type' => 'text',
    'value' => $item->auto_hide,
    'name' => 'auto_hide',
	'rule' => 'required|numeric|min§0',
	'suggestion' => _X3BANNERS_AUTO_HIDE_MSG,
    'extra' => 'class="text-right w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);
