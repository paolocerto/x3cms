<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// profile form

// build the form
$fields = array();

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="w-full md:w-2/3 px-4 md:m-auto pt-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>'
);

$fields[] = array(
    'label' => _LANGUAGE,
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
    'rule' => 'required|alphanumeric|minlength§5',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<h4 class="text-center py-4">'._PASSWORD_CHANGE_MSG.'</h4>'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>'
);

$fields[] = array(
    'label' => _PASSWORD,
    'type' => 'password',
    'value' => '',
    'name' => 'password',
    'suggestion' => _PASSWORD_RULE,
    'rule' => 'password|minlength§6',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' =>  _REPEAT_PASSWORD,
    'type' => 'password',
    'value' => '',
    'name' => 'password2',
    'rule' => 'equal-password',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
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
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>'
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
    'value' => '</div></div></div>'
);