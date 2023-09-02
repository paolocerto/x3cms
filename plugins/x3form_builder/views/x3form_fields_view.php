<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

?>
<h1 class="mt-6">
    <a class="link" @click="pager('<?php echo BASE_URL.'x3form_builder/mod/'.$id_area.'/'.$lang ?>')" title="<?php echo _X3FB_MANAGE ?>"><?php echo _X3FB_MANAGE ?></a>
    <?php echo _TRAIT_.$form->name.': '._X3FB_FIELDS ?>
</h1>

<?php
if (!empty($items))
{
	echo '<p>'._X3FB_DETAIL_MSG.'</p>';
	echo '<table class="mb-0">
        <thead>
            <tr>
                <th class="w-40">'._X3FB_XTYPE.'</th>
                <th>'._X3FB_FIELDS.'</th>
                <th class="w-40">'._ACTIONS.'</th>
            </tr>
        </thead>
        </table>
        <div x-data="xsortable()" x-init="setup(\'sortable\', \'x3form_builder/ordering/'.$id_area.'/'.$lang.'/'.$id_form.'\')">
            <div id="sortable">';

    $js_url = $domain.'/admin/x3form_builder/encoded_rules';

	foreach ($items as $i)
	{
		$statuses = AdmUtils_helper::statuses($i);
		$actions = '';

		if (($i->level > 1 && $i->xlock == 0) || $i->level >= 3)
		{
            $actions = '<a
                class="link"
                @click="popup({url: \''.BASE_URL.'x3form_builder/edit_field/'.$i->id_area.'/'.$i->lang.'/'.$i->id_form.'/'.$i->id.'\', js: \''.$js_url.'\'})"
                title="'._X3FB_NEW_FIELD.'"
            >
                <i class="fa-solid fa-lg fa-pen-to-square"></i>
            </a>';

            if ($i->level > 2)
			{
                $actions .= AdmUtils_helper::link('xon', 'x3form_builder/set/fields/xon/'.$i->id.'/'.intval(!$i->xon), $statuses);
				if ($i->level >= 4)
				{
                    $actions .= AdmUtils_helper::link('xlock', 'x3form_builder/set/fields/xlock/'.$i->id.'/'.intval(!$i->xlock), $statuses);
                    $actions .= AdmUtils_helper::link('delete', 'x3form_builder/delete_field/'.$i->id_area.'/'.$i->id);
				}
			}
		}

		echo '<div class="sort-item" id="'.$i->id.'"><table class="my-0"><tr>
                <td class="w-40"><span class="font-sm">'.$i->xtype.'</span></td>
                <td><strong>'.$i->label.'</strong> ['.$i->name.']</td>
				<td class="w-40 space-x-2 text-right">'.$actions.'</td>
			</tr></table></div>';
	}

    echo '</div></div>';
}
else
{
	echo '<p>'._NO_ITEMS.'</p>';
}