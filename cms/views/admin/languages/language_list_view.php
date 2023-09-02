<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// Language list

echo '<h1 class="mt-6">'._LANGUAGES_MANAGER.'</h1>';

if (empty($langs))
{
    // user cannot see this contents
    echo '<p>'._NOT_PERMITTED.'</p>';
}
else
{
    echo '<table>
	<thead>
        <tr>
            <th class="w-4"></th>
            <th class="text-left pl-4">'.ucfirst(_LANGUAGE).'</th>
            <th class="w-36">'._ACTIONS.'</th>
        </tr>
    </thead>
    <tbody>';

	foreach ($langs as $i)
	{
        $statuses = AdmUtils_helper::statuses($i);
		$actions = '';

		// check permission
		if (($i->level > 2 && $i->xlock == 0) || $i->level >= 3)
		{
			$actions = AdmUtils_helper::link('edit', 'languages/edit/'.$i->id);

            // status
            $actions .= ($i->code != $this->site->area->lang)
				? AdmUtils_helper::link('xon', 'languages/set/xon/'.$i->id.'/'.(($i->xon+1)%2), $statuses)
				: '<a><i class="far fa-lightbulb fa-lg '.$statuses['xon']['class'].'"></i></a>';

			// admin user
			if ($i->level >= 4)
			{
				$actions .= AdmUtils_helper::link('xlock', 'languages/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);

				$actions .= ($i->code == X4Route_core::$lang)
					? '<a><i class="fa-solid fa-lg fa-trash off"></i></a>'
					: AdmUtils_helper::link('delete', 'languages/delete/'.$i->id);
			}
		}

		echo '<tr>
				<td><strong>['.$i->code.']</strong></td>
				<td><a class="link" @click="pager(\''.BASE_URL.'dictionary/keys/'.$i->code.'\')" title="'._SHOW_LANG_KEYS.'">'.$i->language.'</a></td>
                <td class="space-x-2 text-right">'.$actions.'</td>
            </tr>';
    }
    echo '</tbody>
    </table>';
}
