<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// user privs form

$xdata = '{
    xfilter: "",
    xsetter: -1,
    filter(str) {
        return (this.xfilter == "" || str.includes(this.xfilter));
    },
    setter() {
        if (this.xsetter != -1) {
            let elements = document.forms["editpriv"].getElementsByTagName("select");
            for (i = 0; i < elements.length; i++) {
                if (this.xfilter == "" || elements[i].name.includes(this.xfilter)) {
                    elements[i].value = this.xsetter;
                }
            }
            this.xsetter = -1;
        }
    }
}';

// build the form
$fields = array();
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $id_user,
    'name' => 'id'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $user->id_group,
    'name' => 'id_group'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $id_area,
    'name' => 'id_area'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $table,
    'name' => 'table'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div x-data=\''.$xdata.'\' class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

// tables without items
$nodetail = array('areas', 'sites');

// tables for administrators
$onlyadmin = array('themes', 'templates', 'menus', 'xgroups', 'users', 'languages', 'sites', 'privs');

// tables if advanced editing
$exclude = (ADVANCED_EDITING)
    ? array('contents', 'logs')
    : array('blocks', 'sections', 'logs');

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

foreach ($what as $t)
{
    if ($table == 0)
    {
        // only abstract permissions
        if (substr($t->privtype, 0, 1) == '_')
        {
            $fields[] = array(
                'label' => null,
                'type' => 'html',
                'value' => '<div x-show="filter(\''.$t->privtype.'\')">'
            );

            $fields[] = array(
                'label' => constant(strtoupper($t->privtype)),
                'type' => 'select',
                'value' => $t->level,
                'name' => $t->privtype,
                'options' => array($levels, 'id', 'name', [0, '']),
                'extra' => 'class="w-full"'
            );

            $fields[] = array(
                'label' => null,
                'type' => 'html',
                'value' => '</div>'
            );
        }
    }
    else
    {
        // only real permissions on tables
        if (substr($t->privtype, 0, 1) != '_' && !in_array($t->privtype, $exclude))
        {
            // relative to admin area or not only for administrators
            if ($id_area == 1 || !in_array($t->privtype, $onlyadmin))
            {
                // you can edit detail only on real tables
                $detail_link = (substr($t->privtype, 0, 3) != 'x4_')
                    ? '<a class="link" @click="popup({url: \''.BASE_URL.'users/permissions/'.$id_user.'/'.$id_area.'/'.$t->privtype.'\', js: \''.$js_url.'\'})" title="'._EDIT_DETAIL_PRIV.'">'._EDIT_DETAIL_PRIV.'</a>'
                    : '';

                // if in tables with items
                if (!in_array($t->privtype, $nodetail) && !empty($detail_link))
                {
                    $fields[] = array(
                        'label' => null,
                        'type' => 'html',
                        'value' => '<div x-show="filter(\''.$t->privtype.'\')">'
                    );

                    $fields[] = array(
                        'label' => constant(strtoupper($t->privtype)),
                        'type' => 'select',
                        'value' => $t->level,
                        'name' => $t->privtype,
                        'options' => array($levels, 'id', 'name', [0, '']),
                        'suggestion' => '',
                        'extra' => 'class="w-full"'
                    );

                    // only with real tables
                    $fields[] = array(
                        'label' => null,
                        'type' => 'html',
                        'value' => '</div>
                            <div class="place-self-center">
                                '.$detail_link.'
                            </div>'
                    );
                }
                else
                {
                    $fields[] = array(
                        'label' => null,
                        'type' => 'html',
                        'value' => '<div class="col-span-2" x-show="filter(\''.$t->privtype.'\')">'
                    );

                    $fields[] = array(
                        'label' => constant(strtoupper($t->privtype)),
                        'type' => 'select',
                        'value' => $t->level,
                        'name' => $t->privtype,
                        'options' => array($levels, 'id', 'name', [0, '']),
                        'extra' => 'class="w-full"'
                    );

                    $fields[] = array(
                        'label' => null,
                        'type' => 'html',
                        'value' => '</div>'
                    );
                }
            }
        }
    }

    // old value memo
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $t->level,
        'name' => 'old_'.$t->privtype
    );
}

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);
