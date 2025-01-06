<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

 // form builder list view

?>
<h1 class="mt-6"><?php echo $page->icon.' '._X3FB_MANAGE ?></h1>
<?php

if (!empty($items[0]))
{
	echo '<table>
            <thead>
                <tr>
                    <th>'._X3FB_FORMS.'</th>
                    <th>'._X3FB_RESULTS.'</th>
                    <th class="w-44">'._ACTIONS.'</th>
                </tr>
            </thead>
            <tbody>';

	foreach ($items[0] as $i)
	{
		$statuses = AdminUtils_helper::statuses($i);

		$actions = '';
		if (($i->level > 1 && $i->xlock == 0) || $i->level >= 3)
		{
			$actions = AdminUtils_helper::link('edit', 'x3form_builder/edit/'.$i->id_area.'/'.$i->lang.'/'.$i->id);

			if ($i->level > 2)
			{
                $actions .= AdminUtils_helper::link('xon', 'x3form_builder/set/forms/xon/'.$i->id_area.'/'.$i->id.'/'.intval(!$i->xon), $statuses);

                $actions .= AdminUtils_helper::link('duplicate', 'x3form_builder/duplicate/'.$i->id_area.'/'.$i->lang.'/'.$i->id);

				if ($i->level >= 4)
				{
                    $actions .= AdminUtils_helper::link('xlock', 'x3form_builder/set/forms/xlock/'.$i->id_area.'/'.$i->id.'/'.intval(!$i->xlock), $statuses);
                    $actions .= AdminUtils_helper::link('delete', 'x3form_builder/delete/'.$i->id_area.'/'.$i->id);
				}
			}
		}

		// export results
		$export = ($i->n)
			? '<a target="_blank" href="'.BASE_URL.'x3form_builder/export/'.$i->id_area.'/'.$i->lang.'/'.$i->id.'" title="'._X3FB_EXPORT.'"><i class="fas fa-download fa-lg"></i></a>'
			: '';

		echo '<tr>
				<td>
                    <a class="link" @click="pager(\''.BASE_URL.'x3form_builder/fields/'.$i->id_area.'/'.$i->lang.'/'.$i->id.'\')" title="'._X3FB_FIELDS.'">
                        '.stripslashes($i->name).'
                    </a><br />
                    '.stripslashes($i->description).'
                </td>
				<td>
                    <a class="link" @click="pager(\''.BASE_URL.'x3form_builder/results/'.$i->id_area.'/'.$i->lang.'/'.$i->id.'\')" title="'._X3FB_RESULTS.'">
                        '._X3FB_RESULTS.'
                    </a>
                    <span class="xsmall">[ '.$i->n.' ]</span> '.$export.'
                </td>
                <td class="text-right">'.$actions.'</td>
            </tr>';
	}

	echo '</tbody>
    </table>';
	echo '<div id="form_pager" class="pager">'.X4Pagination_helper::tw_admin_pager(BASE_URL.'x3form_builder/mod/'.$id_area.'/'.$lang.'/',$items[1], 5, false, '', '').'</div>';
}
else
{
	echo '<p>'._NO_ITEMS.'</p>';
}
