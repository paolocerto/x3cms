<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// x3banners list view

echo '<div class="switcher">';
// lang switcher
if (MULTILANGUAGE)
{
	echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
	foreach ($langs as $i)
	{
		$on = ($i->code == $lang)
			? 'class="link"'
            : 'class="dark"';
		echo '<a '.$on.' @click="pager(\''.BASE_URL.'x3banners/mod/'.$id_area.'/'.$i->code.'\')" title="'._SWITCH_LANGUAGE.'">'.ucfirst($i->language).'</a>';
	}
	echo '</div>';
}

// area switcher
if (MULTIAREA)
{
	echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
	foreach ($areas as $i)
	{
        if ($i->id > 1)
        {
            $on = ($i->id == $id_area)
                ? 'class="link"'
                : 'class="dark"';
            echo '<a '.$on.' @click="pager(\''.BASE_URL.'x3banners/mod/'.$i->id.'/'.$lang.'\')" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a>';
        }
	}
	echo '</div>';
}
echo '</div>';


// filter selector
echo '<form name="xfilter" id="xfilter" action="'.BASE_URL.'x3banners/mod/'.$id_area.'/'.$lang.'" method="GET" onsubmit="return false">
	    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">';

echo '<div>
    <label for="xid_page">'._X3BANNERS_ID_PAGE.'</label>
    <select id="xid_page" name="xid_page" class="w-full" @change="filter()">
        '.X4Form_helper::get_options($pages, 'id', 'name', $qs['xid_page'], [0, '']).'
    </select>
</div>';

echo '<div class="col-span-2">
        <label for="xstr">'._X3BANNERS_SEARCH_MSG.'</label>
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

?>
<h1 class="mt-6"><?php echo $page->icon.' '._X3BANNERS_MANAGE ?></h1>
<?php
$list = $items[0];
if (!empty($list))
{
	echo '<table>
        <thead>
            <tr>
                <th class="w-48">'._X3BANNERS_ID_PAGE.'</th>
                <th>'._X3BANNERS_ITEMS.'</th>
                <th>'._X3BANNERS_ITEM.'</th>
                <th>'._X3BANNERS_START_DATE._TRAIT_._X3BANNERS_END_DATE.'</th>
                <th class="w-48">'._ACTIONS.'</th>
            </tr>
        </thead>';

    echo '<tbody>';

    foreach ($list as $i)
    {
        $statuses = AdminUtils_helper::statuses($i);

        $actions = '';

        if (($i->level > 1 && $i->xlock == 0) || $i->level >= 3)
            {
                $actions = AdminUtils_helper::link('edit', 'x3banners/edit/'.$i->id_area.'/'.$i->lang.'/'.$i->id);

                if ($i->level > 2)
                {
                    $actions .= AdminUtils_helper::link('xon', 'x3banners/set/xon/'.$i->id_area.'/'.$i->id.'/'.intval(!$i->xon), $statuses);

                    if ($i->level >= 4)
                    {
                        $actions .= AdminUtils_helper::link('xlock', 'x3banners/set/xlock/'.$i->id_area.'/'.$i->id.'/'.intval(!$i->xlock), $statuses);
                        $actions .= AdminUtils_helper::link('delete', 'x3banners/delete/'.$i->id);
                    }
                }
            }

        echo '<tr>
            <td>'.$i->page.'</td>
            <td><b>'.$i->title.'</b></td>
            <td>'.$i->description.'</td>
            <td class="text-sm">'.$i->start_date.'<br>'.$i->end_date.'</td>
            <td class="space-x-2 text-right">'.$actions.'</td>
        </tr>';
    }

    echo '</tbody></table>';

    // pagination
    if (!isset($qs) || empty($qs['xstr']))
    {
        echo '<div id="x3banners_pager" class="pager">'.X4Pagination_helper::tw_admin_pager(BASE_URL.'x3banners/mod/'.$id_area.'/'.$lang.'/', $items[1], 5, false, '?'.http_build_query($qs), '').'</div>';
    }
}
else
{
	echo '<p>'._NO_ITEMS.'</p>';
}