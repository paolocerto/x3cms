<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// user edit form

// only superadmin can set superadmin
if ($_SESSION['level'] < 5)
{
    $tmp = array_pop($levels);
}

// build the form
$fields = array();

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $user->id_group,
    'name' => 'id_group'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $group->id_area,
    'name' => 'id_area'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<h4 class="mt-6">'._GROUP.': '.$group->name.'</h4>'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div>'
);


$fields[] = array(
    'label' => ucfirst(_LANGUAGE),
    'type' => 'select',
    'value' => $user->lang,
    'options' => array($languages, 'code', 'language'),
    'name' => 'lang',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _USERNAME,
    'type' => 'text',
    'value' => $user->username,
    'name' => 'username',
    'suggestion' => _USERNAME_RULE,
    'rule' => 'required|minlength§6|alphanumeric',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);

// password
if ($id)
{
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<div class="col-span-2">
            <h4 class="text-center">'._PASSWORD_CHANGE_MSG.'</h4>
            </div>'
    );
    $rule = '';
}
else
{
    // for a new user you must insert a password
    $rule = 'required|';
}

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div>'
);

$fields[] = array(
    'label' => _PASSWORD,
    'type' => 'password',
    'value' => '',
    'name' => 'password',
    'suggestion' => _PASSWORD_RULE,
    'rule' => $rule.'password|minlength§6',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _REPEAT_PASSWORD,
    'type' => 'password',
    'value' => '',
    'name' => 'password2',
    'rule' => $rule.'equal-password',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div class="col-span-2">'
);

$fields[] = array(
    'label' => _DESCRIPTION,
    'type' => 'textarea',
    'value' => $user->description,
    'name' => 'description',
    'sanitize' => 'string',
    'rule' => 'required'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _EMAIL,
    'type' => 'text',
    'value' => $user->mail,
    'name' => 'mail',
    'rule' => 'required|mail',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _PHONE,
    'type' => 'text',
    'value' => $user->phone,
    'name' => 'phone',
    'rule' => 'phone',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _LEVEL,
    'type' => 'select',
    'value' => $user->level,
    'options' => array($levels, 'id', 'name'),
    'name' => 'level',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

// permissions on areas

$fields[] = array(
    'label' => _DOMAIN,
    'type' => 'select',
    'value' => X4Array_helper::obj2array($aprivs, '', 'id_area'),
    'options' => array($areas, 'id', 'name'),
    'multiple' => 4,
    'name' => 'domain',
    'rule' => 'required',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div></div>'
);
