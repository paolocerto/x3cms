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
		echo '<a '.$on.' @click="pager(\''.BASE_URL.'categories/index/'.$id_area.'/'.$i->code.'\')" title="'._SWITCH_LANGUAGE.'">'.ucfirst($i->language).'</a>';
	}
	echo '</div>';
}

// area switcher
echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
foreach ($areas as $i)
{
	$on = ($i->id == $id_area) ? 'class="link"' : 'class="dark"';
	echo '<a '.$on.' @click="pager(\''.BASE_URL.'categories/index/'.$i->id.'/'.$lang.'\')" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a>';
}
echo '</div>';

// tag switcher
echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
// all tags
$on = ($tag == 'xxxall') ? 'class="link"' : 'class="dark"';
echo '<a '.$on.' @click="pager(\''.BASE_URL.'categories/index/'.$id_area.'/'.$lang.'/xxxall\')" title="'._CATEGORY_TAG.'">'._ALL_TAGS.'</a>';
// no tags
$on = ($tag == '') ? 'class="link"' : 'class="dark"';
echo '<a '.$on.' @click="pager(\''.BASE_URL.'categories/index/'.$id_area.'/'.$lang.'\')" title="'._CATEGORY_TAG.'">'._NO_CATEGORY_TAG.'</a>';


foreach ($tags as $i)
{
	$on = ($i->tag == $tag) ? 'class="link"' : 'class="dark"';
	echo '<a '.$on.' @click="pager(\''.BASE_URL.'categories/index/'.$id_area.'/'.$lang.'/'.$i->tag.'\')" title="'._CATEGORY_TAG.'">'.$i->tag.'</a>';
}
echo '</div></div>';

?>
<h1 class="mt-6"><?php echo $page->icon.' '._CATEGORY_LIST ?></h1>
<?php
if ($items)
{
	echo '<table>
        <thead>
            <tr>
                <th>'._CATEGORIES.'</th>
                <th>'._CATEGORY_TAG.'</th>
                <th class="w-40">'._ACTIONS.'</th>
            </tr>
        </thead>
        <tbody>';

	foreach ($items as $i)
	{
		$statuses = AdminUtils_helper::statuses($i);
		$actions = '';

		// check permissions
		if (($i->level > 1 && $i->xlock == 0) || $i->level >= 3)
		{
            $actions = AdminUtils_helper::link('edit', 'categories/edit/'.$i->id_area.'/'.$i->lang.'/'.$i->tag.'/'.$i->id);
			if ($i->level > 2)
			{
                $actions .= AdminUtils_helper::link('xon', 'categories/set/xon/'.$id_area.'/'.$i->id.'/'.(($i->xon+1)%2), $statuses);

				if ($i->level >= 4)
				{
                    $actions .= AdminUtils_helper::link('xlock', 'categories/set/xlock/'.$id_area.'/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);

                    $actions .= AdminUtils_helper::link('delete', 'categories/delete/'.$i->id);
				}
			}
		}

		echo '<tr>
				<td><a class="link" @click="pager(\''.BASE_URL.'articles/index/'.$i->id_area.'/'.$i->lang.'?&xctg='.$i->name.'\')" title="'._VIEW_ARTICLES.'">'.$i->title.'</a></td>
                <td>'.$i->tag.'</td>
				<td class="space-x-2 text-right">'.$actions.'</td>
			</tr>';
	}

	echo '</tbody>
    </table>';
}
else
{
	echo '<p>'._NO_ITEMS.'</p>';
}