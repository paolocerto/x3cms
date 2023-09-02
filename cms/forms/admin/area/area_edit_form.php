<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// area edit form

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
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

// user can't change names of default areas
if ($id == 0 || $id > 3)
{
    $fields[] = array(
        'label' => _NAME,
        'type' => 'text',
        'value' => $item->name,
        'name' => 'name',
        'rule' => 'required|minlength§3',
        'extra' => 'class="w-full"'
    );
}
else
{
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<h4 class="mt-4">'._AREA.': '.$item->name.'</h4>'
    );
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->name,
        'name' => 'name'
    );
}

$fields[] = array(
    'label' => _TITLE,
    'type' => 'text',
    'value' => $item->title,
    'name' => 'title',
    'rule' => 'required',
    'extra' => 'class="w-full"'
);
$fields[] = array(
    'label' => _DESCRIPTION,
    'type' => 'textarea',
    'value' => $item->description,
    'name' => 'description'
);

// theme section
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $item->id_theme,
    'name' => 'old_id_theme'
);

// admin area can't change theme
if ($id != 1)
{
    $theme = new Theme_model();
    $fields[] = array(
        'label' => _THEME,
        'type' => 'select',
        'value' => $item->id_theme,
        'name' => 'id_theme',
        'options' => array($theme->get_installed(), 'id', 'description'),
        'extra' => 'class="w-full"'
    );
}
else {
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->id_theme,
        'name' => 'id_theme'
    );
}

// areas subsequent to the default can be set as private
if ($id == 0 || $id > 3)
{

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div>'
);

    $fields[] = array(
        'label' => _FOLDER,
        'type' => 'select',
        'value' => $item->folder,
        'name' => 'folder',
        'options' => array($mod->get_folders(), 'folder', 'folder'),
        'extra' => 'class="w-full"'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div><div>'
    );

    $fields[] = array(
        'label' => _PRIVATE,
        'type' => 'checkbox',
        'value' => 1,
        'name' => 'private',
        'checked' => $item->private
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div></div>'
    );
}
else
{
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->folder,
        'name' => 'folder'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->private,
        'name' => 'private'
    );
}

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div>'
);

    // languages section
    $lang = new Language_model();
    $fields[] = array(
        'label' => _ENABLED_LANGUAGES,
        'type' => 'select',
        'value' => $lang->get_alang_array($id),
        'options' => array($lang->get_languages(), 'code', 'language'),
        'name' => 'languages',
        'rule' => 'required',
        'extra' => 'class="w-full"',
        'multiple' => 4
    );

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

    $fields[] = array(
        'label' => _DEFAULT_LANG,
        'type' => 'select',
        'value' => $item->code,
        'options' => array($lang->get_languages(), 'code', 'language'),
        'name' => 'lang',
        'rule' => 'inarray§languages',
        'extra' => 'class="w-full"'
    );

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div></div>'
);
