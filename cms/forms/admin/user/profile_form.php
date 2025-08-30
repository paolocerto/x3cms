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
$min_password_length = AdminUtils_helper::$user_password_length;
$length_msg = str_replace('XXXNUMXXX', $min_password_length, _PWD_LENGTH_MSG);

$messages = [
    'digit'=> _PWD_DIGIT_MSG,
    'capital'=> _PWD_CAPITAL_MSG,
    'lowercase'=> _PWD_LOWERCASE_MSG,
    'symbol'=> _PWD_SYMBOL_MSG,
    'length'=> $length_msg
];

// build the form
$fields = [];

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="w-full lg:w-2/3 px-4 md:m-auto pt-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4">
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
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-x-4">
        <div
            x-data="pwd()"
            x-init=\'setUp('.$min_password_length.', '.json_encode($messages).')\'
            x-cloak
        >'
);

$fields[] = array(
    'label' => _PASSWORD,
    'type' => 'password',
    'value' => '',
    'name' => 'password',
    'suggestion' => _PASSWORD_RULE,
    'rule' => 'password|minlength§'.$min_password_length,
    'extra' => 'class="w-full" x-model="pwd" x-on:keyup="chk_pwd()"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<p class="text-xs" x-show="pwd_msg.length" x-html="pwd_msg"></p>',
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
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-x-4">
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
