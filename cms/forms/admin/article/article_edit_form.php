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

$xdata = '{
    xid_area: document.getElementById("id_area").value,
    //area_options:[],
    xlang: document.getElementById("lang").value,
    lang_options: document.getElementById("lang").innerHTML,
    xcontext: document.getElementById("code_context").value,
    context_options: document.getElementById("code_context").innerHTML,
    xid_page: document.getElementById("id_page").value,
    page_options: document.getElementById("id_page").innerHTML,
    xmodule: document.getElementById("module").value,
    module_options: document.getElementById("module").innerHTML,
    xparam: document.getElementById("param").value,
    tinit(id_area, lang, api_key) {
        if (tinymce) {
            // reset
            tinymce.remove();
        }
        setTimeout(function(){tiny(id_area, lang, api_key);},200);
    },
    refresh(id_area) {
        // load all lists
        this.xid_area = id_area;
        // reload languages
        if (this.xid_area > 0) {
            fetch(root + "articles/refresh_languages/"+this.xid_area, {
                method: "GET",
                headers: { "Content-Type": "text/html" }
            })
            .then(res => res.text())
            .then(txt => {
                this.lang_options = txt;
                this.contexts();
            })
            .catch(() => {
                this.lang_options = zero_option;
            });
        }
    },
    contexts() {
        // reload contexts
        fetch(root + "articles/refresh_contexts/"+this.xid_area+"/"+this.xlang, {
            method: "GET",
            headers: { "Content-Type": "text/html" }
        })
        .then(res => res.text())
        .then(txt => {
            this.context_options = txt;
            // reload pages
            this.pages();
        })
        .catch(() => {
            this.context_options = zero_option;
        });
    },
    pages() {
        if (this.xcontext != 1) {
            console.log(this.xcontext);
            this.page_options = [];
        } else {
            fetch(root + "articles/refresh_pages/"+this.xid_area+"/"+this.xlang, {
                method: "GET",
                headers: { "Content-Type": "text/html" }
            })
            .then(res => res.text())
            .then(txt => {
                this.page_options = txt;
                this.modules();
            })
            .catch(() => {
                this.page_options = zero_option;
            });
        }
    },
    modules() {
        // reload modules
        fetch(root + "articles/refresh_modules/"+this.xid_area, {
            method: "GET",
            headers: { "Content-Type": "text/html" }
        })
        .then(res => res.text())
        .then(txt => {
            this.module_options = txt;
            this.xparam = "";
        })
        .catch(() => {
            this.module_options = zero_option;
        });
    },
    config() {
        if (this.xmodule == "") {
            this.xparam = "";
        } else {
            let url = root + "articles/param/"+this.xid_area+"/"+this.xlang+"/"+this.xid_page+"/"+this.xmodule+"/"+this.xparam;
            let event = new CustomEvent("popup", {detail: url});
            window.dispatchEvent(event);
        }
    }
}';

// build the form
$fields = array();

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div
            class="w-full pt-6"
            x-data=\''.$xdata.'\'
            x-init="tinit('.$item->id_area.',\''.$item->lang.'\', \''.FLMNGR_API_KEY.'\')"
            x-cloak
        >
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <div class="md:col-span-2 lg:col-span-3 xl:col-span-4">'
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

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $item->code_context,
    'name' => 'old_context'
);

if ($item->id_page == 0 || $bid == 'x3')
{
    // new article
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>'
    );
    $mod = new Area_model();
    $fields[] = array(
        'label' => _AREA,
        'type' => 'select',
        'value' => $item->id_area,
        'options' => array($mod->get_areas(), 'id', 'name'),
        'name' => 'id_area',
        'extra' => 'class="w-full" x-model="xid_area" @change="refresh($event.target.value)"'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div><div>'
    );

    $mod = new Language_model();
    $fields[] = array(
        'label' => _LANGUAGE,
        'type' => 'select',
        'value' => $item->lang,
        'options' => array($mod->get_alanguages($item->id_area), 'code', 'language'),
        'name' => 'lang',
        'extra' => 'class="w-full" x-model="xlang" @change="xlang=$event.target.value;refresh(xid_area)" x-html="lang_options"'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div><div>'
    );

    $mod = new Context_model();
    $fields[] = array(
        'label' => _CONTEXT,
        'type' => 'select',
        'value' => $item->code_context,
        'options' => array($mod->get_contexts($item->id_area, $item->lang), 'code', 'name'),
        'name' => 'code_context',
        'extra' => 'class="w-full" x-model="xcontext" @change="xcontext=$event.target.value;pages()" x-html="context_options"'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div><div>'
    );

    $fields[] = array(
        'label' => _PAGE,
        'type' => 'select',
        'value' => $item->id_page,
        'options' => array($mod->get_pages($item->id_area, $item->lang), 'id', 'name', [0, '']),
        'name' => 'id_page',
        'extra' => 'class="w-full" x-model="xid_page" @change="xid_page=$event.target.value" x-html="page_options"'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div></div>'
    );
}
else
{
    // hidden fields in existext articles
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->id_area,
        'name' => 'id_area',
        'extra' => 'x-model="xid_area"'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->lang,
        'name' => 'lang',
        'extra' => 'x-model="xlang"'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->code_context,
        'name' => 'code_context',
        'extra' => 'x-model="xcontext"'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => $item->id_page,
        'name' => 'id_page',
        'extra' => 'x-model="xid_page"'
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
    'name' => 'content',
    'extra' => 'class="tinymce"'
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
    'value' => '<h3 class="mt-6">'._PLUGIN.'</h3>
                <div class="grid grid-cols-2 gap-4"><div>'
);

$plugin = new X4Plugin_model();
$fields[] = array(
    'label' => _MODULE,
    'type' => 'select',
    'value' => $item->module,
    'options' => array($plugin->get_modules($item->id_area, 0), 'name', 'title', ''),
    'name' => 'module',
    'extra' => 'class="w-full" x-model="xmodule" @change="xmodule=$event.target.value;config()" x-html="module_options"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div><div>'
);

$fields[] = array(
    'label' => _PARAM,
    'type' => 'text',
    'value' => $item->param,
    'name' => 'param',
    'extra' => 'class="w-full" x-model="xparam"  @change="xparam=$event.target.value;" @focus="config()"'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div></div>
            </div>
            <div>'
);

if (!$time_window)
{
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
        'value' => '<h2>'._TIME_WINDOW.'</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>'
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
        'case' => 'date',
        'value' => date('Y-m-d', $item->date_in),
        'name' => 'date_in',
        'rule' => 'required|date',
        'extra' => 'class="w-full date date_toggled"  autocomplete="off"'
    );

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div><div>'
    );

    $fields[] = array(
        'label' => _END_DATE,
        'type' => 'text',
        'case' => 'date',
        'value' => ($item->date_out == 0) ? '' : date('Y-m-d', $item->date_out),
        'name' => 'date_out',
        'rule' => 'date',
        'extra' => 'class="w-full date date_toggled"  autocomplete="off"',
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
    'value' => '<h2 class="mt-4">'._ORGANIZATION.'</h2>'
);

// categories
$camod = new Category_model();
$ctgs = $camod->get_categories($item->id_area, $item->lang);
if (!empty($ctgs))
{
    $fields[] = array(
        'label' => _CATEGORY,
        'type' => 'select',
        'value' => $item->category,
        'options' => array($ctgs, 'name', 'title', 0),
        'name' => 'category',
        'extra' => 'class="w-full"'
    );
}
else
{
    $fields[] = array(
        'label' => null,
        'type' => 'hidden',
        'value' => '',
        'name' => 'category'
    );
}

// xkeys
$fields[] = array(
    'label' => _KEYS,
    'type' => 'text',
    'value' => $item->xkeys,
    'name' => 'xkeys',
    'extra' => 'class="w-full"'
);

// tags
$fields[] = array(
    'label' => _TAGS,
    'type' => 'text',
    'value' => $item->tags,
    'name' => 'tags',
    'extra' => 'class="w-full"'
);

// author
$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $_SESSION['mail'],
    'name' => 'author',
    'rule' => 'required'
);

if (EDITOR_OPTIONS)
{
    // options section
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<h2>'._OPTIONS.'</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>'
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
        'value' => '</div><div>'
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
        'value' => '</div><div>'
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
        'value' => '</div><div>'
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
    'value' => '</div></div></div>'
);