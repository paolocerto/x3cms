<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// form results view

$bulk_url = 'x3form_builder/bulk/'.$id_area.'/'.$lang;
?>
<h1 class="mt-6">
    <?php echo $page->icon ?>
    <a class="link" @click="pager('<?php echo BASE_URL.'x3form_builder/mod/'.$id_area.'/'.$lang ?>')" title="<?php echo _X3FB_MANAGE ?>"><?php echo _X3FB_MANAGE ?></a>
    <?php echo _TRAIT_._X3FB_RESULTS.': '.$form->name ?>
</h1>

<div x-data="bulkable()" x-init='setup("<?php echo $bulk_url ?>")' >

<div x-show="bulk.length > 0" class="buttons">
    <input type="hidden" id="bulk_action" x-model="xaction" value="delete" />
    <button type="button" @click="execute()" class="link"><?php echo _DELETE_BULK ?></button>
</div>

<?php
if (!empty($items[0]))
{
    echo '<table>
        <thead>
            <tr>
                <th class="w-48"></th>
                <th>'._X3FB_RESULTS.'</th>
                <th class="w-40">'._ACTIONS.'</th>
                <th class="w-8 text-center"><input type="checkbox" @click="toggle()" /></th>
            </tr>
        </thead>
        </tbody>';

	foreach ($items[0] as $i)
	{
		$statuses = AdmUtils_helper::statuses($i);

		$actions = '';
		if (($i->level > 2 && $i->xlock == 0) || $i->level >= 3)
		{
            $actions .= AdmUtils_helper::link('xon', 'x3form_builder/set/results/xon/'.$i->id_area.'/'.$i->id.'/'.intval(!$i->xon), $statuses);
			if ($i->level >= 4)
			{
                $actions .= AdmUtils_helper::link('xlock', 'x3form_builder/set/results/xlock/'.$i->id_area.'/'.$i->id.'/'.intval(!$i->xlock), $statuses);
                $actions .= AdmUtils_helper::link('delete', 'x3form_builder/delete_result/'.$i->id_area.'/'.$i->id);
			}
		}

		echo '<tr>
				<td class="text-sm">'.$i->updated.'</td>
                <td>'.$mod->show_message($i->result).'</td>
				<td class="space-x-2 text-right">'.$actions.'</td>
                <td class="text-center"><input type="checkbox" class="bulkable" x-model="bulk" value="'.$i->id.'" /></td>
			</tr>';
	}
	echo '</tbody></table>
    </div>';

    echo '<div id="form_pager" class="pager">'.X4Pagination_helper::tw_admin_pager(BASE_URL.'x3form_builder/results/'.$id_area.'/'.$lang.'/'.$id_form.'/', $items[1], 5, false, '', '').'</div>';
}
else
{
	echo '<p>'._NO_ITEMS.'</p>';
}