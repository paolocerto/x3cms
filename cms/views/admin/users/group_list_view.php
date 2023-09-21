<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// group list view

echo '<h1 class="mt-6">'._GROUP_LIST.'</h1>';

if (empty($groups))
{
    echo '<p>'._NOT_PERMITTED.'</p>';
}
else
{
    echo '<table>
        <thead>
            <tr>
                <th class="w-20">'._AREA.'</th>
                <th>'._GROUP.'</th>
                <th class="w-56">'._ACTIONS.'</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($groups as $i)
    {
        $statuses = AdmUtils_helper::statuses($i);

        $actions = '';

        // check permission
        if (($i->level > 1 && $i->xlock == 0) || $i->level >= 3)
        {
            // edit group
            $actions = AdmUtils_helper::link('edit', 'groups/edit/'.$i->id);

            // manager group but not admin
            if ($i->level > 2 && $i->id > 1)
            {
                $actions .= AdmUtils_helper::link('xon', 'groups/set/xon/'.$i->id_area.'/'.$i->id.'/'.(($i->xon+1)%2), $statuses);
            }

            // new user
            $actions .= '<a class="link" @click="popup(\''.BASE_URL.'users/edit/0/'.$i->id.'\')" title="'._ADD_USER.'">
                <i class="fa-solid fa-user-plus fa-lg"></i>
            </a>';

            // admin super user
            if ($i->level >= 4)
            {
                if ($i->id > 1)
                {
                    $actions .= AdmUtils_helper::link('settings', 'groups/gperm/'.$i->id, [], _EDIT_GPRIV);
                }
                $actions .= AdmUtils_helper::link('xlock', 'groups/set/xlock/'.$i->id_area.'/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);
                $actions .= AdmUtils_helper::link('delete', 'groups/delete/'.$i->id);
            }
        }

        echo '<tr>
                <td>'.$i->title.'</td>
                <td><a class="link" @click="popup(\''.BASE_URL.'users/users/'.$i->id.'\')" title="">'.$i->name.'</a>'._TRAIT_.$i->description.'</td>
                <td class="space-x-2 text-right">'.$actions.'</td>
            </tr>';
    }
    echo '</tbody>
        </table>';
}
