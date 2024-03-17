<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// dictionary view

echo '<div class="switcher">';
// language switcher
if (MULTILANGUAGE)
{
	echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
	foreach ($langs as $i)
	{
		$on = ($i->code == $lang) ? 'class="link"' : 'class="dark"';
		echo '<a '.$on.' @click="pager(\''.BASE_URL.'dictionary/keys/'.$i->code.'/'.$area.'?xwhat='.$what.'\')" title="'._SWITCH_LANGUAGE.'">'.ucfirst($i->language).'</a></li>';
	}
	echo '</div>';
}

// area switcher
echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
foreach ($areas as $i)
{
	$on = ($i->name == $area) ? 'class="link"' : 'class="dark"';
	echo '<a '.$on.' @click="pager(\''.BASE_URL.'dictionary/keys/'.$lang.'/'.$i->name.'?xwhat='.$what.'\')" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a></li>';
}
echo '</div>';

// filter selector
echo '<form name="xfilter" id="xfilter" action="'.BASE_URL.'dictionary/keys/'.$lang.'/'.$area.'" method="GET" onsubmit="return false">

	<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="xwhat">'._SECTION.'</label>
            <select id="xwhat" name="xwhat" class="w-full" @change="filter()">
                '.X4Form_helper::get_options($keys, 'what', 'what', $what, '').'
            </select>
        </div>';

echo '<div class="col-span-2">
        <label for="xstr">'._DICTIONARY_SEARCH_MSG.'</label>
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

echo '<h1 class="mt-6">'.$page->icon.' '._WORDS_LIST.'</h1>';

if ($items)
{
	echo '<table>
        <thead>
			<tr>
                <th class="w-4"></th>
				<th class="md:w-60 text-left pl-4">'._KEY.'</th>
				<th class="text-left pl-4">'._WORD.'</th>
				<th class="w-36">'._ACTIONS.'</th>
			</tr>
        </thead>
        <tbody>';

	foreach ($items as $i)
	{
		$statuses = AdminUtils_helper::statuses($i);
		$actions = '';

		// check permission
		if (($i->level > 2 && $i->xlock == 0) || $i->level >= 3)
		{
            $actions = AdminUtils_helper::link('edit', 'dictionary/edit/'.$i->lang.'/'.$i->area.'/'.$i->id);

			// manager or admin user
			if ($i->level > 2 || $i->level == 4)
			{
				$actions .= AdminUtils_helper::link('xon', 'dictionary/set/xon/'.$i->area.'/'.$i->id.'/'.(($i->xon+1)%2), $statuses);
			}
			// admin user
			if ($i->level >= 4)
			{
				$actions .= AdminUtils_helper::link('xlock', 'dictionary/set/xlock/'.$i->area.'/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);
                $actions .= AdminUtils_helper::link('delete', 'dictionary/delete/'.$i->id);
			}
		}

		echo '<tr>
            <td>'.$i->lang.'</td>
			<td>'.$i->xkey.'</td>
			<td>'.$i->xval.'</td>
			<td class="space-x-2 text-right">'.$actions.'</td>
		</tr>';
	}
	echo '</tbody></table>';
}
else
{
	echo '<p>'._NO_ITEMS.'</p>';
}