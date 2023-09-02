<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// login form

// build the form
$fields = array();

// check if user used remember me
if (isset($_COOKIE[COOKIE.'_login']))
{
    list($usr, $hidden_pwd) = explode('-', $_COOKIE[COOKIE.'_login']);
    $pwd = '12345678';
    $chk = true;
}
else
{
    $usr = $pwd = '';
    $chk = false;
}

// antispam control
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => md5(time().SALT),
    'name' => 'antispam'
);
$fields[] = array(
    'label' => _USERNAME,
    'type' => 'text',
    'value' => $usr,
    'name' => 'username',
    'rule' => 'required',
    'sanitize' => 'string',
    'extra' => 'class="w-full"'
);
$fields[] = array(
    'label' => _PASSWORD,
    'type' => 'password',
    'value' => $pwd,
    'name' => 'password',
    'rule' => 'required|minlength§5',
    'sanitize' => 'string',
    'extra' => 'class="w-full"'
);

if ($chk)
{
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $hidden_pwd,
        'name' => 'hpwd'
    );
}
/*
$fields[] = array(
    'label' => _REMEMBER_ME,
    'type' => 'checkbox',
    'value' => '1',
    'name' => 'remember_me',
    'checked' => $chk
);
*/

// if site is on line and user is unknown or fails his login add captcha
if ($site->xon && !$chk && isset($_SESSION['failed']))
{
    $fields[] = array(
        'label' => _CAPTCHA,
        'type' => 'text',
        'value' => '',
        'name' => 'captcha',
        'rule' => 'required|captcha',
        'suggestion' => _CAPTCHA_MSG,
        'extra' => 'class="w-full" autocomplete="off"'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<div class="text-center" x-html="captcha"></div>',
    );
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<p class="text-sm"><a href="javascript:void(0)" @click="load_captcha()" title="reload" id="reload_captcha">'._RELOAD_CAPTCHA.'</a></p>'
    );
}