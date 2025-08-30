<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// page move form

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
    'value' => $page->id_area,
    'name' => 'id_area'
);

$xdata = '{
        xid_area: '.$page->id_area.',
        xlang: "'.$page->lang.'",
        xxfrom: "'.$page->xfrom.'",
        xfrom_menu: '.$from->id_menu.',
        xid_menu: '.$page->id_menu.',
        xin_menu: '.intval($page->id_menu > 0).',
        xxpos: '.$page->xpos.',
        xsiblings: "'.str_replace(array(NL, '"'), array('', '\"'), $siblings).'",
        subpages_menu() {
            this.xid_menu = this.xfrom_menu;
            this.subpages();
        },
        subpages() {
            //console.log([this.xid_area, this.xlang, this.xxfrom, this.xfrom_menu]);
            fetch(root+"pages/subpages/"+this.xid_area+"/"+this.xlang+"/"+this.xxfrom+"/"+parseInt(this.xid_menu)+"/1", {
                method: "GET",
                headers: { "Content-Type": "text/html" }
            })
            .then(res => res.json())
            .then(json => {
                this.xfrom_menu = json.from_menu;
                this.xsiblings = json.subpages;
                this.xxpos = 1;
            })
            .catch(() => {

            });
        }
    }';
$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div
                class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white"
                x-data=\''.$xdata.'\'
                x-cloak
            >'
);

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => 0,
    'name' => 'from_menu',
    'extra' => 'x-model="xfrom_menu"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '
        <div
            class="grid grid-cols-1 md:grid-cols-2 gap-x-4">
            <div>'
);

$fields[] = array(
    'label' => _FROM_PAGE,
    'type' => 'select',
    'value' => '',
    'options' => array($pages, 'url', 'deep_title'),
    'name' =>'xfrom',
    'rule' => 'required',
    'extra' => 'class="w-full" x-model="xxfrom" @change="subpages()" '
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _NOT_IN_MAP,
    'type' => 'checkbox',
    'value' => $page->hidden,
    'name' => 'hidden',
    'checked' => $page->hidden
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div x-show="xxfrom==\'home\'" @change="subpages()">'
);

    // here if the page is a subpage of home you have the option to choose the menù
    $mod = new Menu_model();
    $fields[] = array(
        'label' => _MENU,
        'type' => 'select',
        'value' => $page->id_menu,
        'options' => array($mod->get_menus($page->id_area), 'id', 'name', [0, 'No menù']),
        'name' =>'id_menu',
        //'rule' => 'required',
        'extra' => 'class="w-full" x-model="xid_menu"'
    );

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div x-show="xxfrom!=\'home\' && xfrom_menu > 0">'
);

    // else you have only the option in menù or not in menù
    $fields[] = array(
        'label' => _IN_MENU,
        'type' => 'checkbox',
        'value' => 1,
        'name' => 'in_menu',
        'checked' => intval($page->id_menu > 0),
        'extra' => 'x-model="xin_menu" @change="subpages_menu()"'
    );

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

    $fields[] = array(
        'label' => _PAGE_POSITION,
        'type' => 'select',
        'value' => 0,
        'options' => array(),
        'name' =>'xpos',
        //'rule' => 'required',
        'extra' => 'class="w-full" x-model="xxpos" x-html="xsiblings"'
    );

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div x-show="xin_menu">'
);

$fields[] = array(
    'label' => _FAKE_PAGE,
    'type' => 'checkbox',
    'value' => $page->fake,
    'name' => 'fake',
    'checked' => $page->fake,
    'rule' => 'requiredif§action§!',
    'suggestion' => _FAKE_PAGE_MSG
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div x-show="xin_menu">'
);

    $fields[] = array(
        'label' => _ACTION_PAGE,
        'type' => 'text',
        'value' => $page->action,
        'name' =>'action',
        'extra' => 'class="w-full"',
        'suggestion' => _ACTION_PAGE_MSG
    );

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);
