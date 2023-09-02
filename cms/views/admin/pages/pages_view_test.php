<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// drag and drop examples
// https://codepen.io/lgaud/pen/abVEwgz
// https://codepen.io/ranjan-purbey/pen/xoEMOM this is what I'm following

echo '<div class="switcher">';
// language switcher
if (MULTILANGUAGE)
{
	echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
	foreach ($langs as $i)
	{
		$on = ($i->code == $page->lang) ? 'class="link"' : 'class="dark"';
		echo '<a '.$on.' @click="pager(\''.BASE_URL.'pages/index/'.$page->id_area.'/'.$i->code.'\')" title="'._SWITCH_LANGUAGE.'">'.ucfirst($i->language).'</a>';
	}
	echo '</div>';
}

// area switcher
echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
foreach ($areas as $i)
{
	$on = ($i->id == $id_area) ? 'class="link"' : 'class="dark"';
	echo '<a '.$on.' @click="pager(\''.BASE_URL.'pages/index/'.$i->id.'/'.$lang.'\')" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a>';
}
echo '</div></div>';

// to avoid slash problems
$xfrom = str_replace('/', '§', $page->url);

?>
<h1 class="mt-6"><?php echo _PAGE_LIST.' \''.$area.'\''._TRAIT_._LANGUAGE.' \''.$lang ?>'</h1>
<p><?php echo _MENU_AND_ORDER ?></p>
<?php

// do not delete pages
$no_del = array('home', 'msg', 'search', 'logout', 'offline');

// do not move pages
$no_menu = array('home', 'msg', 'search', 'offline', 'x3admin');

if (isset($page->url) && $page->url != 'home')
{
	// parent page
	$parent = str_replace('/', '§', $page->xfrom);
	echo '<p><a class="link" @click="pager(\''.BASE_URL.'pages/index/'.$page->id_area.'/'.$page->lang.'/'.$parent.'/1\')" title="'._GO_BACK.'">
        <i class="fa-solid fa-circle-arrow-left fa-lg"></i>
        '.stripslashes($page->name).'</a>
    </p>';
}

// menu arrangement
$amenu = array();
if ($page->url == 'home')
{
	foreach ($menus as $i)
	{
		$amenu[$i->id] = $i->description;
	}
}
else
{
	foreach ($menus as $i)
	{
		if ($i->id == $page->id_menu) $amenu[$i->id] = $i->description;
	}
}

if (!empty($pages))
{
    $items = [];

    $c = 0;
    $tmp = '';
	foreach ($pages as $i)
	{
		if ($i->url != 'x3admin')
        {
			$statuses = AdmUtils_helper::statuses($i);

			$actions = '';

			// check permission
			if (($i->level > 1 && $i->xlock == 0) || $i->level >= 3)
            {
				$actions = (ADVANCED_EDITING)
                    ? AdmUtils_helper::link('edit', 'sections/compose/'.$i->id)
                    : AdmUtils_helper::link('edit', 'articles/edit/'.$i->id_area.'/'.$i->lang.'/1/0/'.$i->id);

				// manager user
				if ($i->level > 2)
				{
					$actions .= (in_array($i->url, $no_del))
						? '<a><i class="far fa-lightbulb fa-lg off"></i></a>'
						: AdmUtils_helper::link('xon', 'pages/set/xon/'.$i->id.'/'.(($i->xon+1)%2), $statuses);

					$actions .= AdmUtils_helper::link('settings', 'pages/seo/'.$i->id);

					// admin user
					if ($i->level >= 4)
					{
						// add sections editing
						if (ADVANCED_EDITING)
						{
							$actions .= ' <a class="link" @click="pager(\''.BASE_URL.'sections/index/'.$i->id_area.'/'.$i->id.'\')" title="'._SECTIONS.'">
                                <i class="fa-solid fa-table-layout fa-lg"></i>
                            </a>';
						}
						$actions .= AdmUtils_helper::link('xlock', 'pages/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);

						$actions .= (!in_array($i->url, $no_del))
						    ? AdmUtils_helper::link('delete', 'pages/delete/'.$i->id)
                            : '<a><i class="fa-solid fa-lg fa-trash off"></i></a>';
					}
				}
			}


/*
			// menus
			if ($memo != $i->id_menu && isset($m[$i->id_menu]))
			{
				// check if is the next menu
				foreach ($m as $k => $v)
				{
					echo '</ul>
						<input type="hidden" name="sort'.$c.'" id="sort'.$c.'" value="'.implode(', ', $ids).'" />
						<div class="titlebar">'._MENU.': '.stripslashes($v).'</div><ul class="nomargin zebra min-height" id="m'.$k.'">';
					$c++;
					$ids = array();
					$sort[$c] = '#m'.$k;
					unset($m[$k]);
					if ($k == $i->id_menu)
					{
						break;
					}
				}
			}
*/
            $draggable = (!in_array($i->url, $no_menu))
                ? 'true'
                : 'true';

            $items[$c] = $i->id;

            $tmp .= '<div
                class="w-full flex relative"
                :draggable="'.$draggable.'"
                :class="{\'bg\': dragging === '.$c.'}"
                @dragstart="dragging = '.$c.'"
                @dragend="dragging = null"
            >
                <div class="w-full">
                    <table>
                        <tr>
                            <td><a class="link" @dispatch(\'pager\', \''.BASE_URL.'pages/index/'.$i->id_area.'/'.$i->lang.'/'.str_replace('/', '§', $i->url).'/1\')"  title="'._SUBPAGES.'">'.stripslashes($i->name).'</a></td>
                            <td class="w-20 flex space-x-2 justify-end">'.$actions.'</td>
                        </tr>
                    </table>
                </div>
                <div class="absolute inset-0 opacity-50"
                    x-show.transition="dragging !== null"
                    :class="{\'bg\': dropping ===  '.$c.'}"
                    @dragenter.prevent="if( '.$c.' !== dragging) {dropping = '.$c.'}"
                    @dragleave="if(dropping === '.$c.') dropping = null"
                ></div>
            </div>';

            $c++;
		}
	}

    echo '<div
            class="w-full"
            x-data="xsortable()"
            x-cloak
            x-init=\'setup('.$page->id_area.', "'.$page->lang.'", "'.$page->url.'", '.json_encode($items).')\'
            @drop.prevent="dropPrevent()"
            @dragover.prevent="$event.dataTransfer.dropEffect = &quot;move&quot;"
        >
            <div
                class="w-full flex relative"
                :draggable="false"
            >
                <table><tr><th class="text-left pl-4">'.ucfirst(_PAGES).'</td><th class="w-20">'._ACTIONS.'</th></tr></table>
            </div>
            '.$tmp.'
        </div>';



    /*
	// empty menus
	foreach ($amenu as $k => $v)
	{
		echo '</ul>
			<input type="hidden" name="sort'.$c.'" id="sort'.$c.'" value="'.implode(', ', $ids).'" />
			<div class="titlebar">'._MENU.': '.stripslashes($v).'</div><ul class="nomargin min-height" id="m'.$k.'">';
		$c++;
		$ids = array();
		$sort[$c] = '#m'.$k;
		unset($m[$k]);
	}


    */
?>
<p>&nbsp;</p>
</div>


<?php
}
else
{
	if ($_SESSION['level'] > 3)
	{
		if (!isset($page->url) || $page->url == 'home')
		{
			echo '<p>'._NO_SUBPAGES._TRAIT_.'<a class="link" @click="pager(\''.BASE_URL.'pages/init/'.$id_area.'/'.$lang.'\')" title="'._INIZIALIZE_AREA.'">'._INIZIALIZE_AREA.'</a></p>';
		}
		else
		{
			echo '<p>'._NO_SUBPAGES.'</p>';
		}
	}
	else
	{
		echo '<p>'._NOT_PERMITTED.'</p>';
	}
}
