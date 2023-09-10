<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

echo '<div class="switcher">';
// language switcher
if (MULTILANGUAGE)
{
	echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
	foreach ($langs as $i)
	{
		$on = ($i->code == $lang) ? 'class="link"' : 'class="dark"';
		echo '<a '.$on.' @click="pager(\''.BASE_URL.'articles/index/'.$id_area.'/'.$i->code.'\')" title="'._SWITCH_LANGUAGE.'">'.ucfirst($i->language).'</a>';
	}
	echo '</div>';
}

// area switcher
echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
foreach ($areas as $i)
{
	$on = ($i->id == $id_area) ? 'class="link"' : 'class="dark"';
	echo '<a '.$on.' @click="pager(\''.BASE_URL.'articles/index/'.$i->id.'/'.$lang.'\')" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a>';
}
echo '</div></div>';

// filter selector
echo '<form name="xfilter" id="xfilter" action="'.BASE_URL.'articles/index/'.$id_area.'/'.$lang.'" method="GET" onsubmit="return false">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">';

echo '<div>
    <label for="xpage">'.ucfirst(_PAGE).'</label>
    <select id="xpage" name="xpage" class="w-full" @change="filter()">
        '.X4Form_helper::get_options($pages, 'id', 'deep_title', $qs['xpage'], 0).'
    </select>
</div>';

echo '<div>
    <label for="xcnt">'._CONTEXT.'</label>
    <select id="xcnt" name="xcnt" class="w-full" @change="filter()">
        '.X4Form_helper::get_options($contexts, 'code', 'name', $qs['xcnt'], -1).'
    </select>
</div>';

if (!empty($categories))
{
    echo '<div>
        <label for="xctg">'._CATEGORY.'</label>
        <select id="xctg" name="xctg" class="w-full" @change="filter()">
            '.X4Form_helper::get_options($categories, 'name', 'name', $qs['xctg'], '').'
        </select>
    </div>';
}

if (!empty($keys))
{
    echo '<div>
        <label for="xkey">'._KEY.'</label>
        <select id="xkey" name="xkey" class="w-full" @change="filter()">
            '.X4Form_helper::get_options($keys, 'xkeys', 'xkeys', $qs['xkey'], '').'
        </select>
    </div>';
}

if (sizeof($authors) > 1)
{
    echo '<div>
        <label for="xaut">'._AUTHOR.'</label>
        <select id="xaut" name="xaut" class="w-full" @change="filter()">
            '.X4Form_helper::get_options($authors, 'id_editor', 'author', $qs['xaut'], '').'
        </select>
    </div>';
}

echo '<div class="col-span-2">
        <label for="xstr">'._SEARCH_BY_TEXT.'</label>
        <input
            type="text"
            id="xstr"
            name="xstr"
            class="w-full uppercase"
            value="'.$qs['xstr'].'"
            autocomplete="off"
            placeholder="'._ENTER_TO_FILTER.'"
            @keyup="if ($event.key === \'Enter\') { filter(); }" />
	</div>';
echo '</div></form>';

// page name
$title = (isset($page))
	? _TRAIT_.stripslashes($page->name)
	: '';
?>
<h1 class="mt-6"><?php echo _ARTICLE_LIST.$title ?></h1>

<?php
// use pagination
if ($items[0]) {
?>
<table>
	<tr>
		<th class="w-28"></th>
		<th><?php echo _ARTICLES ?></th>
		<th class="w-56"><?php echo _ACTIONS ?></th>
	</tr>
<?php
	foreach ($items[0] as $i)
	{
		$statuses = AdmUtils_helper::statuses($i);

		$actions = '';

		// check permission
		if (($i->level > 1 && $i->xlock == 0) || $i->level >= 3)
		{
			// edit in full page
            $actions = '<a class="link" @click="pager(\''.BASE_URL.'articles/edit/'.$i->id_area.'/'.$i->lang.'/'.$i->code_context.'/'.$i->bid.'\')" title="'._EDIT.'">
                <i class="fa-solid fa-lg fa-pen-to-square"></i>
                </a>';

			// duplicate
			$actions .= '<a class="link" @click="pager(\''.BASE_URL.'articles/edit/'.$i->id_area.'/'.$i->lang.'/'.$i->code_context.'/'.$i->bid.'/0/1\')" title="'._DUPLICATE.'">
                <i class="fa-solid fa-copy fa-lg"></i>
                </a>';

			if ($i->level > 2)
			{
                $actions .= AdmUtils_helper::link('xon', 'articles/set_by_bid/xon/'.$i->id.'/'.(($i->xon+1)%2), $statuses);

                $actions .= '<a class="link" @click="pager(\''.BASE_URL.'articles/history/'.$id_area.'/'.$lang.'/'.$i->bid.'\')" title="'._ARTICLE_HISTORY.'">
                        <i class="fa-solid fa-clock-rotate-left fa-lg"></i>
                    </a>';

				if ($i->level >= 4)
				{
                    $actions .= AdmUtils_helper::link('xlock', 'articles/set_by_bid/xlock/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);

                    $actions .= AdmUtils_helper::link('delete','articles/delete/'.$i->id_area.'/'.$i->lang.'/'.$i->bid);
				}
			}
		}

		$link = (ADVANCED_EDITING)
			? '<a class="link" @click="pager(\''.BASE_URL.'sections/compose/'.$i->id_page.'\')">'.$i->page.'</a>'
			: '<strong>'.$i->page.'</strong>';

		echo '<tr>
				<td class="text-sm">
                    '.date('Y-m-d', $i->date_in).'<br>
                    <span>'.$i->author.'</span>
                </td>
				<td>
                    <strong>'.$i->name.'</strong><br>
                    <span class="text-xs">'.$i->context.' '.$link.'</span>
                </td>
				<td class="space-x-2 text-right">
                    '.$actions.'<br>&nbsp;
				</td>
			</tr>';
	}
	echo '</table>';

	// pagination
	echo '<div id="article_pager" class="pager">'.X4Pagination_helper::tw_admin_pager(BASE_URL.'articles/index/'.$id_area.'/'.$lang, $items[1], 5, false, '?'.http_build_query($qs), '').'</div>';
}
else
{
	echo '<p>'._NO_ITEMS.'</p>';
}
