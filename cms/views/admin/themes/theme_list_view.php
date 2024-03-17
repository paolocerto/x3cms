<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// theme manager

echo '<h1 class="mt-6">'.$page->icon.' '.$page->title.'</h1>';

if (empty($theme_in))
{
    // user cannot see this contents
    echo '<p>'._NOT_PERMITTED.'</p>';
}
else
{

    echo '<table>
        <thead>
            <tr>
                <th class="w-4">'._AREA.'</th>
                <th class="text-left pl-4">'._THEME.'</th>
                <th class="w-60">'._ACTIONS.'</th>
            </tr>
        </thead>
        <tbody>';

    if ($theme_in)
    {
        echo '<tr><td colspan="3" class="bg text-center">'._INSTALLED_THEMES.'</td></tr>';

        $tmp = 0;
        foreach ($theme_in as $i)
        {
            $statuses = AdminUtils_helper::statuses($i, ['xon', 'xlock']);

            $actions = $area = '';

            if (($i->level > 2 && $i->xlock == 0) || $i->level >= 3)
            {
                if ($i->id > 1)
                {
                    // not for admin theme
                    $actions .= AdminUtils_helper::link('edit', 'themes/edit/'.$i->id);
                }

                $actions .= AdminUtils_helper::link('xon', 'themes/set/xon/'.$i->id.'/'.(($i->xon+1)%2), $statuses);

                if ($i->level >= 4)
                {
                    $actions .= AdminUtils_helper::link('xlock', 'themes/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);

                    // templates
                    $actions .= '<a class="link" @click="pager(\''.BASE_URL.'templates/index/'.$i->id.'/'.$i->name.'\')" title="'._TEMPLATES.'">
                        <i class="fa-solid fa-lg fa-object-group"></i>
                    </a>';
                    // menus
                    $actions .= '<a class="link" @click="pager(\''.BASE_URL.'menus/index/'.$i->id.'/'.$i->name.'\')" title="'._MENUS.'">
                        <i class="fa-solid fa-bars fa-lg"></i>
                    </a>';

                    if (empty($i->area))
                    {
                        $uninstall = '<a class="link" @click="popup(\''.BASE_URL.'themes/uninstall/'.$i->id.'\')" title="'._UNINSTALL.'">
                            <i class="fa-solid fa-download fa-lg warn"></i>
                        </a>';
                    }
                    else
                    {
                        $uninstall = '<a><i class="fa-solid fa-download fa-lg off"></i></a>';
                        $area = '['.$i->area.']';
                    }
                }
            }
            if ($tmp != $i->id)
            {
                $tmp = $i->id;

                $actions .= ($i->level >= 4)
                    ? AdminUtils_helper::link('refresh', 'themes/set/minimize/'.$i->id, [], _MINIMIZE)
                    : '';

                echo '<tr>
                        <td class="w-6">'.$area.'</td>
                        <td><strong>'.$i->name.'</strong> <span class="xs-hidden"> - '.$i->description.'</span></td>
                        <td class="space-x-2 text-right">'.$actions.$uninstall.'</td>
                        </tr>';
            }
            else
            {
                echo '<tr>
                        <td>'.$area.'</td>
                        <td></td>
                        <td></td>
                        </tr>';
            }
        }
    }

    if ($theme_out && $_SESSION['level'] >= 4)
    {
        echo '<tr><td colspan="3" class="bg text-center">'._INSTALLABLE_THEMES.'</td></tr>';
        foreach ($theme_out as $i)
        {
            $name = preg_replace('/(.*)\/(.*)/is', '$2', $i, 1);

            $install = '<a class="link" @click="setter(\''.BASE_URL.'themes/install/'.$name.'\')" title="'._INSTALL.'">
                <i class="fa-solid fa-upload fa-lg"></i>
            </a>';
            echo '<tr>
                    <td></td>
                    <td>'.$name.'</td>
                    <td class="space-x-2 text-right">'.$install.'</td>
                </tr>';
        }
    }

    echo '</tbody>
        </table>';
}
