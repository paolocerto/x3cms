<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// selection import form

// build the form
$fields = [];

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $lang,
    'name' => 'lang'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $area,
    'name' => 'area'
);


$xdata = '{
    xwhat: "",
    xstr: "",
    xinfo: "",
    xfound: "",
    init() {
        this.xwhat = document.getElementById("what").value;
        this.xstr = document.getElementById("wsearch").value;
    },
    filterKeys() {
        this.xinfo = "";
        if (this.xstr.length > 0 && this.xstr.length < 2) {
            this.xfound = "";
            this.xinfo = "'.str_replace('XXXRELATEDXXX', 2, _TOO_SHORT).'";
        } else {
            let formData = getFormData("selection", {});
            fetch(root + "dictionary/keys_search", {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(txt => {
                this.xfound = txt;
            })
            .catch(() => {
                this.xfound = "";
            });
        }

    }
}';//  \\\'\\\'

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div x-data=\''.$xdata.'\' x-cloak class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<h4 class="mt-6">'._IMPORT_INTO.' '.$lang.'/'.$area.'</h4>'
);

$fields[] = array(
    'label' => _SECTION,
    'type' => 'select',
    'value' => '',
    'name' => 'what',
    'options' => array($sections, 'value', 'option'),
    'rule' => 'required',
    'extra' => 'class="w-full" x-model="xwhat" @change="filterKeys()"'
);

$fields[] = array(
    'label' => _FILTER,
    'type' => 'text',
    'value' => '',
    'name' => 'wsearch',
    'extra' => 'class="w-full" autocomplete="off" x-model="xstr" @keyup="filterKeys()"',
    'rule' => 'minlength-3',
    'suggestion' => _FILTER_MSG,
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="m-0"><p x-show="xinfo.length>0" class="failed md:px-10 px-4 py-4" x-html="xinfo"></p></div>
        <div x-html="xfound"></div>'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);
