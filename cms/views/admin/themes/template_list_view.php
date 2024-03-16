<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// templates list

echo '<h1 class="mt-6">'.$page->icon.' '.$theme._TRAIT_._TEMPLATE_LIST.'</h1>';

echo '<table>
    <thead>
        <tr>
            <th class="w-40">'._TEMPLATE.'</th>
            <th></th>
            <th class="w-44">'._ACTIONS.'</th>
        </tr>
    </thead>
    <tbody>';

if ($tpl_in)
{
	echo '<tr><td colspan="3" class="bg text-center">'._INSTALLED_TEMPLATES.'</td></tr>';

	foreach ($tpl_in as $i)
	{
        $statuses = AdmUtils_helper::statuses($i, ['xon', 'xlock']);

		$actions = $uninstall = '';

		// check permission
		if (($i->level > 2 && $i->xlock == 0) || $i->level >= 3)
		{
            $actions = AdmUtils_helper::link('edit', 'templates/edit/template/'.$theme.'/'.$i->id);

			$actions .= '<a class="link" @click="pager(\''.BASE_URL.'templates/edit/css/'.$theme.'/'.$i->id.'\')" title="'._EDIT.' css">
                <i class="fa-solid fa-paintbrush fa -lg"></i>
            </a>';

            $actions .= AdmUtils_helper::link('xon', 'templates/set/xon/'.$i->id.'/'.(($i->xon+1)%2), $statuses);

			// admin user
			if ($i->level >= 4)
			{
                $actions .= AdmUtils_helper::link('xlock', 'templates/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);

				$actions .= ($i->name != 'base')
					? '<a class="link" @click="popup(\''.BASE_URL.'templates/uninstall/'.$i->id.'\')" title="'._UNINSTALL_TEMPLATE.'">
                            <i class="fa-solid fa-download fa-lg warn"></i>
                        </a>'
					: '<a><i class="fa-solid fa-download fa-lg off"></i></a>';;
			}
		}
		echo '<tr>
				<td><strong>'.$i->name.'</strong></td>
				<td>'.$i->description.' ['.$i->sections.']</td>
				<td class="space-x-2 text-right">'.$actions.'</td>
			</tr>';
	}
}

// only for admin users
if (!empty($tpl_out) && $_SESSION['level'] >= 4)
{

    $tmp = '';
	foreach ($tpl_out as $i)
	{
		$name = preg_replace('/(.*)\/(.*)/is', '$2', $i, 1);
		$install = '<a class="link" @click="setter(\''.BASE_URL.'templates/install/'.$id_theme.'/'.$name.'\')" title="'._INSTALL.'">
            <i class="fa-solid fa-upload fa-lg"></i>
        </a>';

		if ($name != 'x3ui' && $name != 'mail' && $name != 'login')
		{
			$tmp .= '<tr>
					<td>'.$name.'</td>
					<td></td>
					<td class="aright">'.$install.'</td>
					</tr>';
		}
	}

    if (!empty($tmp))
    {
        echo '<tr><td colspan="3" class="bg text-center">'._INSTALLABLE_TEMPLATES.'</td></tr>'.$tmp;
    }
}

echo '</tbody>
</table>';
