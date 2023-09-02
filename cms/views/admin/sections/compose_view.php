<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// page compose view

// x-data for page composer
$page_composer = '{
    pageId: 0,
    cols: {},
    changes: false,
    setup(pageId, containers, cols) {
        this.pageId = pageId;
        this.cols = cols;
        let elements = [];
        containers.forEach(function(value) {
            elements.push(document.getElementById(value));
        });
        // for reference inside dragula
        let obj = this;
        // set drag
        dragula(elements, {
            revertOnSpill: true,
            invalid: function (el, handle) {
                if (el.classList.contains("listitem")) {
                    return false;
                }
                return el.tagName !== "DIV";
            },
            moves: function (el) {
                if (el.classList.contains("nodrag")) {
                    return false;
                }
                return true;
            }
        })
        .on("drop", function (el, target, source, sibling) {
            var bid = el.id;

            // update article
            obj.updateArticle(bid, target.id);

            var targetId = target["id"].split("-");
            var sourceId = source["id"].split("-");

            if (sourceId[0] == "context") {
                // update context counter
                let event = new CustomEvent("compose"+sourceId[1], {detail: source.id});
                window.dispatchEvent(event);
            } else {
                // update order in previous section
                obj.updateSort(sourceId[1], bid, 0);
            }

            if (targetId[0] == "context") {
                // update context counter
                let event = new CustomEvent("compose"+targetId[1], {detail: target.id});
                window.dispatchEvent(event);
            } else {
                // update order
                obj.updateSort(targetId[1], bid, sibling);
                // warn for unsaved changes
                obj.changes = true;
            }
        });
    },
    updateSort(progressive, bid, sibling) {
        // get previous order
        let tmp = document.getElementById("sort-"+progressive).value;
        //console.log(tmp);
        let sort = (tmp == "")
            ? []
            : JSON.parse(tmp);
        let index;
        switch (sibling)
        {
            case 0:
                // we have to remove bid from sort
                index = sort.indexOf(bid);
                sort.splice(index, 1);
                break;
            case null:
                // we have to add at the end of sort
                sort.push(bid);
                break;
            default:
                // we have to insert before sibling.id
                index = sort.indexOf(sibling.id);
                sort.splice(index, 0, bid);
                break;
        }
        // update
        document.getElementById("sort-"+progressive).value = JSON.stringify(sort);
        this.updateSize(progressive, sort);
    },
    updateArticle(bid, container) {
        document.getElementById(bid).classList.remove("softwarn");
        fetch(root+"sections/get_article/"+this.pageId+"/"+container+"/"+bid, {
            method: "GET",
            headers: { "Content-Type": "text/html" }
        })
        .then(res => res.text())
        .then(txt => {
            document.getElementById(bid).innerHTML = txt;
        })
        .catch(() => {
            document.getElementById(bid).classList.add("softwarn");
        });
    },
    updateSize(progressive, sort) {
        let sizes = this.cols["section-"+progressive].split("+");
        let n = sizes.length;
        for (var i = 0; i < sort.length; i++) {
            // reset
            let bid = sort[i];
            document.getElementById(bid).classList.remove("md:col-span-2", "md:col-span-3", "md:col-span-4", "md:col-span-5", "md:col-span-6");
            let pos = i % n;
            if (sizes[pos] > 1) {
                document.getElementById(bid).classList.add("md:col-span-"+sizes[pos]);
            }
        }
    }
}';
// x-data for context accordions
$context_accordion = '{
    open: false,
    count: 0,
    setup(id) {
        this.count = "("+document.querySelectorAll("#"+id+" .listitem").length+")";
    },
    update(id) {
        this.count = "("+document.querySelectorAll("#"+id+" .listitem").length+")";
    }
}';

// to setup pageComposer Alpine componenet
// containers
$containers = array();
// columns subdivision
$sizes = array();

// sections
$left = '';
// contexts with articles
$right = '';

// to store articles order in each section
$artts = array();
// to hide already in the page
$published = array();

// SECTIONS
foreach ($sections as $i)
{
	// get settings
	$settings = json_decode($i->settings, true);

    // handle cols subdivision
    $csizes = isset($settings['col_sizes'])
        ? explode('+', $settings['col_sizes'])
        : array_fill(0, $settings['columns'], 1);
    // this is the real number of columns with subdivion
    $nc = sizeof($csizes);

    $edit = '';
    if ($_SESSION['level'] >= 3)
	{
		// you can edit settings
		$edit = ' <a class="link" @click="popup(\''.BASE_URL.'sections/edit/'.$pagetoedit->id_area.'/'.$pagetoedit->id.'/'.$i->id.'\')" title="'._EDIT.'">
            <i class="fa-solid fa-pen-to-square"></i>
        </a> ';
	}
    // section title
	$left .= '<h4 class="mt-4">'.$edit._SECTION.' '.$i->progressive.': '.$i->name.' <span class="text-xs">('.$settings['columns'].'/'.$settings['col_sizes'].')</span></h4>';

    // add container and section size for Alpine
	$containers[] = 'section-'.$i->progressive;
    $sizes['section-'.$i->progressive] = $settings['col_sizes'];

	$left .= '<div id="section-'.$i->progressive.'" class="grid grid-cols-'.$settings['columns'].' gap-2 p-2 border-2 border-color-gray-500 rounded">';

    $artts[$i->progressive] = [];

    // ad articles
	if (isset($i->articles) && !empty($i->articles))
	{
		$artt = $mod->get_articles($pagetoedit->id_area, $pagetoedit->lang, $i->articles);

		// if there are articles
		if ($artt)
		{
			// article counter
			$c = 0;
			foreach ($artt as $ii)
			{
				$published[] = $ii->bid;
				$m = (empty($ii->module))
					? _TRAIT_
					: $ii->module;

				$p = (empty($ii->param))
					? _TRAIT_
					: $ii->param;

                // in most complex case we have subs
                // get the column we are in
                $ci = $c % $nc;
                // get the span to use
                $col = ($csizes[$ci] > 1)
                    ? 'md:col-span-'.$csizes[$ci]
                    : '';

                // handle colors
                $style = '';
                if (isset($settings['col_settings']))
                {
                    if (isset($settings['col_settings']['bg'.$c]) && $settings['col_settings']['bg'.$c] != '#FFFFFF')
                    {
                        $style = 'background:'.$settings['col_settings']['bg'.$c];
                    }

                    if (isset($settings['col_settings']['fg'.$c]) && $settings['col_settings']['fg'.$c] != '#444444')
                    {
                        $style = 'color:'.$settings['col_settings']['fg'.$c];
                    }
                }

                // DEBUG
				$left .= '<div class="listitem '.$col.' rounded py-2 px-4 bg-gray-100 cursor-move" id="'.$ii->bid.'" style="'.$style.'">
                    <div class="relative h-full pb-16">
                        <div class="border-b border-gray-200"><b>'.stripslashes($ii->name).'</b>'._TRAIT_.'<a class="link" @click="pager(\''.BASE_URL.'articles/edit/'.$pagetoedit->id_area.'/'.$pagetoedit->lang.'/'.$ii->code_context.'/'.$ii->bid.'\')" title="'._EDIT.'">'._EDIT.'</a></div>
                        '.stripslashes($ii->content).'
                        <div class="absolute bottom-0 w-full border-t border-gray-200 space-x-6"><span>'._MODULE.': '.$m.'</span><span>'._PARAM.': '.$p.'</span></div>
                    </div>
				</div>';

                $artts[$i->progressive][] = $ii->bid;
				$c++;
			}
		}
	}

    // here we store order
	$left .= '</div>
		<input type="hidden" name="sort-'.$i->progressive.'" id="sort-'.$i->progressive.'" value=\''.json_encode($artts[$i->progressive]).'\'  />';
}

// ARTICLES by context code
foreach ($codes as $i)
{
	$const = '_CONTEXT_'.strtoupper($i->xkey);

	$context = (defined($const))
		? constant($const)
		: $dict->get_word($const, 'articles', $pagetoedit->lang);

    $containers[] = 'context-'.$i->xkey;

    $tmp = [];
	foreach ($articles as $ii)
	{
		if($ii->code_context == $i->code)
		{
			$a = array_shift($articles);
			// only if no published
			if (!in_array($a->bid, $published))
			{
				$tmp[] = '<div class="listitem rounded py-2 px-4 bg-gray-100 cursor-move" id="'.$a->bid.'"><strong>'.stripslashes($a->name).'</strong></div>';
			}
		}
	}
    $tmp[] = '<div class="nodrag py-2 px-4">'._DROP_HERE.'</div>';

    $n = sizeof($tmp);
    $right .= '<div
                x-data=\''.$context_accordion.'\'
                x-init="setup(\'context-'.$i->xkey.'\')"
                x-on:compose-'.$i->xkey.'.window="update($event.detail)"
            >
            <button @click="open = !open" class="cursor-pointer bg2 rounded flex items-center justify-between w-full py-2 px-4 text-left select-none mb-1">'.$context.' <span class="text-sm" x-text="count"></span></button>
            <div id="context-'.$i->xkey.'" x-show="open" class="grid grid-cols-1 gap-y-1" x-cloak>
                '.implode('', $tmp).'
            </div>
        </div>';
}
?>
<div x-data='<?php echo $page_composer ?>' x-init='setup(<?php echo $pagetoedit->id ?>, <?php echo json_encode($containers) ?>, <?php echo json_encode($sizes) ?>)' class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">

	<div class="md:col-span-2 lg:col-span-3">
		<h1><span class="hidden md:inline-block"><?php echo _COMPOSE_EDITOR.' </span> <a class="link" @click="pager(\''.BASE_URL.'pages/index/'.$pagetoedit->id_area.'/'.$pagetoedit->lang.'/'.$pagetoedit->xfrom.'/1\')" title="">'.$pagetoedit->name.'</a>'._TRAIT_.$pagetoedit->area.'/'.$pagetoedit->lang ?></h1>
		<h2><?php echo _SECTIONS ?><span class="warn" x-show="changes"><?php echo _TRAIT_._UNSAVED_CHANGES ?></span></h2>
		<p><?php echo _SECTIONS_MSG ?></p>

<?php

	echo '<form id="compose" action="'.BASE_URL.'sections/compositing" method="post" onsubmit="return false">
			<input type="hidden" name="id_area" id="id_area" value="'.$pagetoedit->id_area.'" />
			<input type="hidden" name="lang" id="lang" value="'.$pagetoedit->lang.'" />
			<input type="hidden" name="id_page" id="id_page" value="'.$pagetoedit->id.'" />
			<input type="hidden" name="snum" id="snum" value="'.sizeof($sections).'" />';

	echo $left.'<div class="buttons"><button id="esopmoc" type="button" @click="submitForm(\'compose\')">'._SUBMIT.'</button></div>
			</form>';
?>
	</div>

	<div>
		<h2><?php echo _ARTICLES_LIST ?></h2>
		<p><?php echo _ARTICLES_MSG ?></p>
		<div class="mt-4">
<?php
	echo $right;

if (!empty($layout))
{
	echo '<div x-data="{ open: false }">
        <button @click="open = !open" class="cursor-pointer bg2 rounded flex items-center justify-between w-full py-2 px-4 text-left select-none mb-1">
            '._SECTIONS.'
        </button>
        <div x-show="open" x-cloak><img src="'.$layout.'" alt="layout"  class="mt-4 mx-auto" /></div>
    </div>';
}
?>
		</div>
	</div>
</div>