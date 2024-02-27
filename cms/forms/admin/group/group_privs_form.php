<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// group privs form

$xdata = '{
    xfilter: "",
    xsetter: -1,
    filter(str) {
        return (this.xfilter == "" || str.includes(this.xfilter));
    },
    setter() {
        if (this.xsetter != -1) {
            let elements = document.forms["editor"].getElementsByTagName("select");
            for (i = 0; i < elements.length; i++) {
                if (this.xfilter == "" || elements[i].name.includes(this.xfilter)) {
                    elements[i].value = this.xsetter;
                }
            }
            this.xsetter = -1;
        }
    }
}';


// remove superadmin
$tmp = array_pop($levels);

// build the form
$fields = array();

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $id_group,
    'name' => 'id'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $private,
    'name' => 'xrif'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div x-data=\''.$xdata.'\' class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div>'
);

$fields[] = array(
    'label' => _FILTER,
    'type' => 'text',
    'value' => '',
    'name' => 'xfilter',
    'extra' => 'class="w-full" x-model="xfilter" placeholder="'._FILTER_MSG.'"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _SET_ALL,
    'type' => 'select',
    'value' => '',
    'name' => 'xsetter',
    'options' => array($levels, 'id', 'name', [-1, '']),
    'extra' => 'class="w-full" x-model="xsetter" @change="setter()"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);

foreach ($types as $i)
{
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<div x-show="filter(\''.$i->name.'\')">'
    );

    // actual permission level
    $value = (isset($gprivs[$i->name]))
        ? $gprivs[$i->name]
        : 0;

    $fields[] = array(
        'label' => constant($i->description),
        'type' => 'select',
        'value' => $value,
        'name' => $i->name,
        'options' => array($levels, 'id', 'name', [0, 'no privs']),
        'extra' => 'class="w-full"'
    );
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $value,
        'name' => 'old_'.$i->name
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div>'
    );
}

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);
