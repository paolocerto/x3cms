<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// menu list view

echo '<h1 class="mt-6">'.$page->icon.' '.$theme._TRAIT_._MENU_LIST.'</h1>';

echo '<table>
	<thead>
        <tr>
            <th class="w-40">'._MENUS.'</th>
            <th></th>
            <th class="w-40">'._ACTIONS.'</th>
        </tr>
    </thead>
    <tbody>';

foreach ($menus as $i)
{
    $statuses = AdmUtils_helper::statuses($i);
	$actions = '';

	// check permission
	if (($i->level > 2 && $i->xlock == 0) || $i->level >= 3)
	{
        $actions = AdmUtils_helper::link('edit', 'menus/edit/'.$i->id_theme.'/'.$i->id);

        $actions .= AdmUtils_helper::link('xon', 'menus/set/xon/'.$i->id.'/'.(($i->xon+1)%2), $statuses);

		// admin user
		if ($i->level >= 4)
        {
            $actions .= AdmUtils_helper::link('xlock', 'menus/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);

            $actions .= AdmUtils_helper::link('delete', 'menus/delete/'.$i->id);
        }
	}

	echo '<tr>
			<td><strong>'.$i->name.'</strong></td>
            <td><span class="hidden md:inline-block">'.$i->description.'</span></td>
			<td class="space-x-2 text-right">'.$actions.'</td>
		</tr>';
}

echo '</tbody>
</table>';