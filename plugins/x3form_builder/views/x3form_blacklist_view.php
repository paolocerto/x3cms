<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// form blacklist view

?>
<h1 class="mt-6">
    <?php echo $page->icon ?>
    <a class="link" @click="pager('<?php echo BASE_URL.'x3form_builder/mod/'.$id_area.'/'.$lang ?>')" title="<?php echo _X3FB_MANAGE ?>"><?php echo _X3FB_MANAGE ?></a>
    <?php echo _TRAIT_._X3FB_BLACKLIST_MANAGE ?>
</h1>

<?php
if (!empty($items[0]))
{
    echo '<table>
        <thead>
            <tr>
                <th>'._X3FB_BLACKLIST_ITEMS.'</th>
                <th class="w-40">'._ACTIONS.'</th>
            </tr>
        </thead>
        </tbody>';

	foreach ($items[0] as $i)
	{
		$statuses = AdminUtils_helper::statuses($i);

		$actions = '';
		if (($i->level > 2 && $i->xlock == 0) || $i->level >= 3)
		{
            $actions .= AdminUtils_helper::link('xon', 'x3form_builder/set/blacklist/xon/'.$i->id_area.'/'.$i->id.'/'.intval(!$i->xon), $statuses);
			if ($i->level >= 4)
			{
                $actions .= AdminUtils_helper::link('xlock', 'x3form_builder/set/blacklist/xlock/'.$i->id_area.'/'.$i->id.'/'.intval(!$i->xlock), $statuses);
                $actions .= AdminUtils_helper::link('delete', 'x3form_builder/delete_blacklist/'.$i->id_area.'/'.$i->id);
			}
		}

		echo '<tr>
				<td>'.$i->name.'</td>
				<td class="text-right">'.$actions.'</td>
			</tr>';
	}
	echo '</tbody></table>';

    echo '<div id="form_pager" class="pager">'.X4Pagination_helper::tw_admin_pager(BASE_URL.'x3form_builder/blacklist/'.$id_area.'/'.$lang.'/', $items[1], 5, '').'</div>';
}
else
{
	echo '<p>'._NO_ITEMS.'</p>';
}
