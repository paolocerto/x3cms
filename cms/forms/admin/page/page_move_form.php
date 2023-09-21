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
    'value' => $page->id_area,
    'name' => 'id_area'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div
                class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white"
                x-data=\'{
                    xid_area: '.$page->id_area.',
                    xlang: "'.$page->lang.'",
                    xxfrom: "'.$page->xfrom.'",
                    xfrom_menu: '.$from->id_menu.',
                    xid_menu: '.$page->id_menu.',
                    xin_menu: '.(($page->id_menu > 0) ? 'true' : 'false').',
                    xxpos: '.$page->xpos.',
                    xsiblings: "'.str_replace(array(NL, '"'), array('', '\"'), $siblings).'",
                    subpages_menu() {
                        if (this.xin_menu) {
                            this.xid_menu = this.xfrom_menu;
                        } else {
                            this.xid_menu = 0;
                        }
                        this.subpages();
                    },
                    subpages() {
                        //console.log([this.xid_area, this.xlang, this.xxfrom, this.xfrom_menu]);
                        fetch(root+"pages/subpages/"+this.xid_area+"/"+this.xlang+"/"+this.xxfrom+"/"+this.xid_menu+"/1", {
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
                }\'
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
            class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
        'label' => 'In menu',
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
        'label' => 'After',
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
    'suggestion' => _FAKE_PAGE_MSG
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