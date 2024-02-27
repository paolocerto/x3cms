<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

echo '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4"><div>';

$open = false;
foreach ($menus['sidebar'] as $k => $v)
{
    if ($v->xfrom == 'home')
    {
        if ($open && $v->url == 'modules')
        {
            // close open submenu
            echo '</ul></div><div>';
            $open = false;
        }

        // first level links
        switch ($v->url)
        {
            case 'sites':
                echo '<h4 class="font-bold">'._SETTINGS.'</h4>
                    <ul class="menu">';
                $open = true;
                break;
            case 'modules':
                // plugins links
                echo '<h4 class="font-bold">'._PLUGINS.'</h4>
                    <ul class="menu">';

                // link to all plugins
                echo '<li><a
                    @click="$dispatch(\'pager\', \''.BASE_URL.'modules\');close()"
                    @contextmenu="$dispatch(\'blank\', \''.BASE_URL.'modules\')"
                    title="'._ALL_PLUGINS.'"
                >
                    '._ALL_PLUGINS.'
                </a></li>';
                break;

            default:
                echo '<li><a
                    @click="$dispatch(\'pager\', \''.BASE_URL.$v->url.'\');close()"
                    @contextmenu="$dispatch(\'blank\', \''.BASE_URL.$v->url.'\')"
                    title="'.$v->name.'"
                >
                    '.$v->name.'
                </a></li>';
                break;
        }
    }
    else
    {
        if ($_SESSION['level'] == 5 || $v->xfrom != 'settings' || $v->url == 'contexts' || $v->url == 'categories')
        {
            echo '<li><a
                @click="$dispatch(\'pager\', \''.BASE_URL.$v->url.'\');close()"
                @contextmenu="$dispatch(\'blank\', \''.BASE_URL.$v->url.'\')"
                title="'.$v->name.'">'.$v->name.'</a></li>';
        }
    }
}

$xdata = '{
    xname: "",
    xurl: "",
    init() {
        this.xurl = window.location.href;
    },
    enableBtn() {
        let btn = document.getElementById("xsender");
        if (this.xname.length < 3) {
            btn.setAttribute("disabled", true);
        } else {
            btn.removeAttribute("disabled");
        }
    },
    getData() {
        return {name: this.xname, url: this.xurl};
    },
    status(status) {
        let event = new CustomEvent("working", {detail: status});
        window.dispatchEvent(event);
    },
    addBookmark() {
        this.status(true);
        fetch(root + "bookmarks/add/'.X4Route_core::$lang.'", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(this.getData())
        })
        .then(res => res.json())
        .then(json => {
            if (json.message_type == "success") {
                document.getElementById("xbookmarks").innerHTML += json.bookmark;
            } else {
                document.getElementById("xbookmarks").innerHTML += json.error;
            }
            this.status(false);
        })
        .catch((error) => {
            document.getElementById("xbookmarks").innerHTML += "<div class=\"flex\" x-data=\"{ show: true }\" x-show=\"show\" x-init=\"setTimeout(() => show = false, 3000)\">'._MSG_ERROR.'</div>";
        });
        this.xname = "";
        this.enableBtn();
    }
}';

$xdata2 = '{
    xshow: true,
    deleteBookmark(id) {
        fetch(root + "bookmarks/delete/"+id, {
            method: "GET",
            headers: { "Content-Type": "text/html" }
        })
        .then(res => res.text())
        .then(txt => {
            if (txt == 1) {
                this.xshow = false;
            }
        });
    }
}';

// close open submenu
echo '</ul></div>
    <div x-data=\''.$xdata.'\'>
        <h4 class="font-bold mb-2">'._BOOKMARKS.'</h4>
        <div id="xbookmarks">';

foreach ($bookmarks as $i)
{
    echo '<div x-data=\''.$xdata2.'\' x-show="xshow" class="flex flex-row items-center justify-between space-4">
        <div class="flex-1"><a href="'.$i->url.'">'.ucfirst($i->name).'</a></div>
        <div class="flex-initial"><a @click="deleteBookmark('.$i->id.')"><i class="fas fa-trash-alt warn"></i></a></div>
    </div>';
}

echo '</div>';

// add bookmark
echo '<div class="buttons">
        <input type="hidden" name="burl" id="burl" x-model="xurl" />
        <input type="text" class="w-full" name="bname" x-model="xname" @keyup="enableBtn()" placeholder="'._BOOKMARKS_NAME.'" />
        <button type="button" id="xsender" @click="addBookmark()" class="mt-4 btn" disabled>'._BOOKMARKS_ADD.'</button>
    </div>';

echo '</div></div></div>';
