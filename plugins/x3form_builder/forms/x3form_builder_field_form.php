<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// Form builder field form

// xtype options
$opts = array(
    array('v' => 'html', 'o' => _X3FB_HTML._TRAIT_._X3FB_HTML_SUGGESTION),
    array('v' => 'hidden', 'o' => _X3FB_HIDDEN._TRAIT_._X3FB_HIDDEN_SUGGESTION),
    array('v' => 'text', 'o' => _X3FB_TEXT._TRAIT_._X3FB_TEXT_SUGGESTION),
    array('v' => 'textarea', 'o' => _X3FB_TEXTAREA._TRAIT_._X3FB_TEXTAREA_SUGGESTION),
    array('v' => 'select', 'o' => _X3FB_SELECT._TRAIT_._X3FB_SELECT_SUGGESTION),
    array('v' => 'checkbox', 'o' => _X3FB_CHECK._TRAIT_._X3FB_CHECK_SUGGESTION),
    array('v' => 'radio', 'o' => _X3FB_RADIO._TRAIT_._X3FB_RADIO_SUGGESTION),
    array('v' => 'singleradio', 'o' => _X3FB_SINGLERADIO._TRAIT_._X3FB_SINGLERADIO_SUGGESTION),
    array('v' => 'file', 'o' => _X3FB_FILE._TRAIT_._X3FB_FILE_SUGGESTION),
    array('v' => 'fieldset', 'o' => _X3FB_FIELDSET._TRAIT_._X3FB_FIELDSET_SUGGESTION),
    //array('v' => 'recaptcha', 'o' => _X3FB_CAPTCHA._TRAIT_._X3FB_RECAPTCHA_SUGGESTION)
);

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
    'type' => 'hidden',
    'value' => $item->id_form,
    'name' => 'id_form'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

$fields[] = array(
    'label' => _X3FB_XTYPE,
    'type' => 'select',
    'value' => $item->xtype,
    'name' => 'xtype',
    'options' => array(X4Array_helper::array2obj($opts, 'v', 'o'), 'value', 'option', ''),
    'rule' => 'required',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => _X3FB_HELP
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>'
);

$fields[] = array(
    'label' => _X3FB_LABEL,
    'type' => 'text',
    'value' => htmlspecialchars($item->label ?? ''),
    'name' => 'label',
    'suggestion' => _X3FB_LABEL_SUGGESTION,
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _X3FB_NAME,
    'type' => 'text',
    'value' => $item->name,
    'name' => 'name',
    'rule' => 'required',
    'suggestion' => _X3FB_NAME_SUGGESTION,
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);

$fields[] = array(
    'label' => _X3FB_SUGGESTION,
    'type' => 'textarea',
    'value' => $item->suggestion,
    'name' => 'suggestion',
    'suggestion' => _X3FB_SUGGESTION_SUGGESTION
);
$fields[] = array(
    'label' => _X3FB_VALUE,
    'type' => 'text',
    'value' => htmlspecialchars($item->value ?? ''),
    'name' => 'value',
    'extra' => 'class="w-full"',
    'suggestion' => _X3FB_VALUE_SUGGESTION
);
$fields[] = array(
    'label' => null,
    'type' => 'html',
    'name' => 'none',
    'value' => '<h3 class="mt-6">'._X3FB_RULE.'</h3><p>'._X3FB_RULE_SUGGESTION.'</p>'
);

$rules = empty($item->rule)
    ? '[]'
    : $item->rule;

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div
        x-data="configurator()"
        x-init=\'setup('.json_encode($js_fields).', '.$rules.', "xrule", "tcomposer", "x3form_builder/decompose/", "js_fields", 1, "checkRule")\'
        x-cloak
    >'
);
$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>'
);

    // available validation rules
    $rules = X4Validation_helper::$rules;
    $fields[] = array(
        'label' => _X3FB_RULE_NAME,
        'type' => 'select',
        'value' => '',
        'options' => array(X4Array_helper::array2obj($rules, 'value', 'option'), 'value', 'option', ''),
        'name' => 'rule_name',
        'extra' => 'class="w-full" @change="composer_change()"'
    );

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

    // available fields in the form
    $others = $mod->get_related($item->id_form);
    $fields[] = array(
        'label' => _X3FB_FIELD_PARAM,
        'type' => 'select',
        'value' => '',
        'options' => array($others, 'name', 'name', ''),
        'name' => 'field_value',
        'extra' => 'class="w-full" @change="composer_change()"'
    );

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

    $fields[] = array(
        'label' => _X3FB_VALUE_PARAM,
        'type' => 'text',
        'value' => '',
        'name' => 'param_value',
        'extra' => 'class="w-full" @keydown="composer_change()"'
    );

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<label class="font-xs">&nbsp;</label>
            <button type="button" class="btn link" @click="addItem()">
                <i class="fas fa-plus fa-lg"></i>
            </button>'
    );

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);

// build saved options
$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<table id="tcomposer">'.$tr.'</table>'
);

$fields[] = array(
    'label' => null,
    'type' => 'textarea',
    'value' => $item->rule,
    'name' => 'xrule',
    'extra' => 'class="hidden"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);

$fields[] = array(
    'label' => _X3FB_EXTRA,
    'type' => 'text',
    'value' => htmlspecialchars($item->extra ?? ''),
    'name' => 'extra',
    'suggestion' => _X3FB_EXTRA_SUGGESTION,
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);