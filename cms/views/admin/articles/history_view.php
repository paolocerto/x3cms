<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// article history
$bulk_url = 'articles/bulk/'.$id_area.'/'.$lang.'/'.$bid;
?>

<h1 class="mt-6"><?php echo _ARTICLE_HISTORY.': '.$art->name ?></h1>
<div x-data="bulkable()" x-init='setup("<?php echo $bulk_url ?>")' >

    <div x-show="bulk.length > 0" class="buttons">
        <input type="hidden" id="bulk_action" x-model="xaction" value="delete" />
        <button type="button" @click="execute()" class="link"><?php echo _DELETE_BULK ?></button>
    </div>

    <table>
        <thead>
            <tr>
                <th><?php echo _PREVIEW ?></th>
                <th></th>
                <th class="w-40"><?php echo _ACTIONS ?></th>
                <th class="w-8 text-center"><input type="checkbox" @click="toggle()" /></th>
            </tr>
        </thead>
        <tbody>

<?php
foreach ($history as $i)
{
    $statuses = AdmUtils_helper::statuses($i);

	// define the end of the visibility window
	$out = ($i->date_out)
		? date('Y-m-d', $i->date_out)
		: _UNDEFINED;

    $date_in = date('Y-m-d', $i->date_in);
	$date_out = date('Y-m-d', $i->date_out);

    $actions = '';
    if ($i->level > 1)
    {
        // edit in full page
        $actions = '<a class="link" @click="pager(\''.BASE_URL.'articles/edit/'.$i->id_area.'/'.$i->lang.'/'.$i->code_context.'/'.$i->bid.'\')" title="'._EDIT.'">
            <i class="fa-solid fa-lg fa-pen-to-square"></i>
        </a>';

        // if user have write permission and object is unlocked or user is an administrator
        if (($i->level > 2 && $i->xlock == 0) || $i->level >= 3)
        {
            $actions .= AdmUtils_helper::link('xon', 'articles/set_by_bid/xon/'.$i->id.'/'.(($i->xon+1)%2), $statuses);

            // administrator
            if ($i->level >= 4)
            {
                $actions .= AdmUtils_helper::link('xlock', 'articles/set_by_bid/xlock/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);

                $actions .= AdmUtils_helper::link('delete','articles/delete_version/'.$i->id);
            }
            $date_in = '<a class="link" @click="popup(\''.BASE_URL.'articles/setdate/'.$i->id.'\')" title="'._EDIT_DATE.'">'.date('Y-m-d', $i->date_in).'</a>';
            $date_out = '<a class="link" @click="popup(\''.BASE_URL.'articles/setdate/'.$i->id.'\')" title="'._EDIT_DATE.'">'.$out.'</a>';
        }
    }

	echo '<tr>
			<td class="text-sm">
                <p class="font-bold mt-4">'._LAST_UPGRADE.': '.$i->updated.'</p>
                '.stripslashes($i->content).'
                <p class="font-bold mb-4">'._MODULE.': '.$i->module.'/'.$i->param.'</p>
            </td>
			<td>
				'._START_DATE.' '.$date_in.'<br />
				'._END_DATE.' '.$date_out.'
			</td>
			<td class="space-x-2 text-right">'.$actions.'</td>
			<td class="text-center"><input type="checkbox" class="bulkable" x-model="bulk" value="'.$i->id.'" /></td>
		</tr>';
}
?>
    </tbody>
</table>
</div>
