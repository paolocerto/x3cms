<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// article edit form

// build the form
$fields = array();
$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="band double-inner-pad clearfix"><div id="left-box" class="four-fifth md-three-fourth sm-two-third xs-one-whole">'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $referer,
    'name' => 'from'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $item->bid,
    'name' => 'bid'
);

$lmod = new Language_model();

    // advanced editing

    // area
    $amod = new Area_model();

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<div class="band clearfix"><div class="one-fourth sm-one-half pad-right xs-one-whole xs-pad-none">'
    );
    $fields[] = array(
        'label' => _AREA,
        'type' => 'select',
        'value' => $item->id_area,
        'options' => array($amod->get_areas(), 'id', 'name'),
        'name' => 'id_area',
        'extra' => 'class="w-full"'
    );
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => 'module|'.BASE_URL.'articles/refresh_module|id_area',
        'name' => 'spinner1_data'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div><div class="one-fourth sm-one-half pad-right xs-one-whole sm-pad-none">'
    );

    // language
    $fields[] = array(
        'label' => _LANGUAGE,
        'type' => 'select',
        'value' => $item->lang,
        'options' => array($lmod->get_languages(), 'code', 'language'),
        'name' => 'lang',
        'extra' => 'class="w-full spinner spin2"'
    );
    // value = id_to_update|url|ids_to_get
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => 'code_context|'.BASE_URL.'articles/refresh_context|id_area|lang',
        'name' => 'spinner2_data'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div><div class="one-fourth sm-one-half pad-right xs-one-whole xs-pad-none">'
    );

    // contexts
    $cmod = new Context_model();
    $fields[] = array(
        'label' => _CONTEXT,
        'type' => 'select',
        'value' => $item->code_context,
        'options' => array($cmod->get_contexts($item->id_area, $item->lang), 'code', 'name'),
        'name' => 'code_context',
        'extra' => 'class="w-full spin2"'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->code_context,
        'name' => 'old_context'
    );

    // value = id_to_update|url|ids_to_get
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => 'id_page|'.BASE_URL.'articles/refresh_pages|id_area|lang|code_context',
        'name' => 'spinner3_data'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div><div class="one-fourth sm-one-half xs-one-whole">'
    );

    $fields[] = array(
        'label' => _PAGE,
        'type' => 'select',
        'value' => $item->id_page,
        'options' => ($item->id_page) ? array($cmod->get_pages($item->id_area, $item->lang, 1), 'id', 'name') : array(),
        'name' => 'id_page',
        'extra' => 'class="w-full"'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div></div>'
    );
}
else
{
    // simple editing

    // hidden fields
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
        'value' => $item->code_context,
        'name' => 'code_context'
    );
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->code_context,
        'name' => 'old_context'
    );
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->id_page,
        'name' => 'id_page'
    );
}

// classification section
$fields[] = array(
    'label' => _NAME,
    'type' => 'text',
    'value' => stripslashes($item->name),
    'name' => 'name',
    'rule' => 'required',
    'extra' => 'class="w-full"'
);

// content
$fields[] = array(
    'label' => _CONTENT,
    'type' => 'textarea',
    'value' => $item->content,
    'name' => 'content'
);

if (EDITOR_SCRIPTS)
{
    // content
    $fields[] = array(
        'label' => _SCRIPT,
        'type' => 'textarea',
        'value' => htmlentities($item->js),
        'name' => 'js',
        'extra' => 'class="NoEditor"',
        'suggestion' => _SCRIPT_MSG
    );
}
else
{
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->js,
        'name' => 'js'
    );
}

// plugin section
$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<h3>'._PLUGIN.'</h3>
                <div class="band clearfix"><div class="one-half pad-right">'
);

$plugin = new X4Plugin_model();
// for APC
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $item->module,
    'name' => 'old_module'
);
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $item->param,
    'name' => 'old_param'
);

$fields[] = array(
    'label' => _MODULE,
    'type' => 'select',
    'value' => $item->module,
    'options' => array($plugin->get_modules($item->id_area, 0), 'name', 'title', ''),
    'name' => 'module',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div class="one-half pad-left">'
);

$fields[] = array(
    'label' => _PARAM,
    'type' => 'text',
    'value' => $item->param,
    'name' => 'param',
    'extra' => 'class="w-full"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div></div>
        <div id="right-box" class="one-fifth md-one-fourth sm-one-third xs-one-whole xs-hidden">'
);

if (!$time_window)
{
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->date_in,
        'name' => 'old_date_in'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->date_in,
        'name' => 'time_in'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => '',
        'name' => 'date_out'
    );
}
else
{
    // time window section
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<h2>'._TIME_WINDOW.'</h2><div class="band clearfix inner-pad"><div class="one-half sm-one-whole">'
    );
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->date_in,
        'name' => 'time_in'
    );
    $fields[] = array(
        'label' => _START_DATE,
        'type' => 'text',
        'value' => date('Y-m-d', $item->date_in),
        'name' => 'date_in',
        'rule' => 'required|date',
        'extra' => 'class="date date_toggled large"  autocomplete="off"'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div><div class="one-half sm-one-whole">'
    );

    $fields[] = array(
        'label' => _END_DATE,
        'type' => 'text',
        'value' => ($item->date_out == 0) ? '' : date('Y-m-d', $item->date_out),
        'name' => 'date_out',
        'rule' => 'date',
        'extra' => 'class="date date_toggled large"  autocomplete="off"',
        'suggestion' => _NO_END_MSG
    );

    // classification section
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div></div>'
    );
}
// classification section
$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<h2>'._ORGANIZATION.'</h2>'
);

// categories
$camod = new Category_model();
$fields[] = array(
    'label' => _CATEGORY,
    'type' => 'select',
    'value' => $item->category,
    'options' => array($camod->get_categories($item->id_area, $item->lang), 'name', 'title', 0),
    'name' => 'category',
    'extra' => 'class="large"'
);
// xkeys
$fields[] = array(
    'label' => _KEYS,
    'type' => 'text',
    'value' => $item->xkeys,
    'name' => 'xkeys',
    'extra' => 'class="large"'
);

// tags
$fields[] = array(
    'label' => _TAGS,
    'type' => 'text',
    'value' => $item->tags,
    'name' => 'tags',
    'extra' => 'class="large"'
);

// author
$fields[] = array(
    'label' => _AUTHOR,
    'type' => 'text',
    'value' => empty($item->author) ? $_SESSION['mail'] : $item->author,
    'name' => 'author',
    'rule' => 'required',
    'extra' => 'class="large"'
);

if (EDITOR_OPTIONS)
{
    // options section
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<h2>'._OPTIONS.'</h2>
                    <div class="band clearfix">
                        <div class="one-half sm-one-whole">'
    );

    $fields[] = array(
        'label' => _SHOW_AUTHOR,
        'type' => 'checkbox',
        'value' => $item->show_author,
        'name' => 'show_author',
        'checked' => $item->show_author
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div><div class="one-half sm-one-whole">'
    );

    $fields[] = array(
        'label' => _SHOW_DATE,
        'type' => 'checkbox',
        'value' => $item->show_date,
        'name' => 'show_date',
        'checked' => $item->show_date
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div><div class="one-half sm-one-whole">'
    );

    $fields[] = array(
        'label' => _SHOW_TAGS,
        'type' => 'checkbox',
        'value' => $item->show_tags,
        'name' => 'show_tags',
        'checked' => $item->show_tags
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div><div class="one-half sm-one-whole">'
    );

    $fields[] = array(
        'label' => _SHOW_ACTIONS,
        'type' => 'checkbox',
        'value' => $item->show_actions,
        'name' => 'show_actions',
        'checked' => $item->show_actions
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div></div>'
    );
}

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>'
);
/*
// TODO: maybe in the future
$fields[] = array(
    'label' => _SCHEMA,
    'type' => 'textarea',
    'value' => $item->xschema,
    'name' => 'xschema',
    'extra' => 'class="mceNoEditor"'
);
*/
