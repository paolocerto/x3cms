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


$svg = '<svg
class="w-4 h-4
duration-200 ease-out"
:class="{ \'rotate-180\': activeAccordion==id }"
viewBox="0 0 24 24"
xmlns="http://www.w3.org/2000/svg"
fill="none"
stroke="currentColor"
stroke-width="2"
stroke-linecap="round"
stroke-linejoin="round"
>
<polyline points="6 9 12 15 18 9"></polyline>
</svg>';

$xdata1 = '{
    activeAccordion: "accordion-1",
    setActiveAccordion(id) {
        this.activeAccordion = (this.activeAccordion == id) ? "" : id
    }
}';

$xdata2 = '{
    setup() {
        let bg1 = new JSColor("#bg_color1");
        let bg2 = new JSColor("#bg_color2");
        let fg = new JSColor("#fg_color");
        let link = new JSColor("#link_color");
    }
}';

// build the form
$fields = [];
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
        x-data=\''.$xdata1.'\'
        x-cloak
        class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div x-data="{ id: $id(\'accordion\') }" class="cursor-pointer group">
    <button @click="setActiveAccordion(id)" class="bg2 rounded flex items-center justify-between w-full p-4 text-left select-none mb-1">
        <span>'._X3BANNERS_ITEM.'</span>
        '.$svg.'
    </button>
    <div x-show="activeAccordion==id" x-collapse x-cloak>
        <div class="p-4 pt-0">'
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
    'label' => null,
    'type' => 'html',
    'value' => '<div x-data="small_editor()" x-init="tinit('.$item->id_area.',\''.$item->lang.'\')" x-cloak>'
);

$fields[] = array(
    'label' => _DESCRIPTION,
    'type' => 'textarea',
    'value' => $item->description,
    'name' => 'description',
    'extra' => 'class="tinymce"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);

$fields[] = array(
    'label' => _X3BANNERS_ID_PAGE,
    'type' => 'select',
    'value' => $item->id_page,
    'name' => 'id_page',
    'options' => array($pages, 'id', 'name', ['', '']),
	'rule' => 'required|numeric',
    'extra' => 'class="text-right w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div></div>'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div x-data="{ id: $id(\'accordion\') }" class="cursor-pointer group">
    <button @click="setActiveAccordion(id)" class="bg2 rounded flex items-center justify-between w-full p-4 text-left select-none mb-1">
        <span>'._SETTINGS.'</span>
        '.$svg.'
    </button>
    <div x-show="activeAccordion==id" x-collapse x-cloak>
        <div class="p-4 pt-0 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>'
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
        x-data=\''.$xdata2.'\' x-init="setup()"
        class="grid grid-cols-1 md:grid-cols-2 gap-4"
    >
        <div>'
);

$fields[] = array(
    'label' => _X3BANNERS_BG_COLOR,
    'type' => 'text',
    'value' => stripslashes($item->bg_color1),
    'name' => 'bg_color1',
	'rule' => 'required',
	'extra' => 'class="w-full"'
);

$gradient = $item->gradient
    ? 'true'
    : 'false';

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div class="pt-8" x-data="{xgradient: '.$gradient.'}" x-cloak>'
);


$fields[] = array(
    'label' => null,
    'alabel' => _X3BANNERS_GRADIENT,
    'type' => 'checkbox',
    'value' => 1,
    'name' => 'gradient',
    'extra' => 'xinline x-model="xgradient"',
    'checked' => $item->gradient
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div x-show="xgradient">'
);

$fields[] = array(
    'label' => _X3BANNERS_BG_COLOR.' 2',
    'type' => 'text',
    'value' => stripslashes($item->bg_color2),
    'name' => 'bg_color2',
	'extra' => 'class="w-full"',
    'suggestion' => _X3BANNERS_BG_COLOR_MSG,
);


$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div><div>'
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
    'value' => '</div><div class="md:col-span-2">'
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
    'value' => '</div></div></div></div>'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);
