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

echo '<div class="switcher">';
// lang switcher
if (MULTILANGUAGE)
{
	echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
	foreach ($langs as $i)
	{
		$on = ($i->code == $lang)
			? 'class="link"'
            : 'class="dark"';
		echo '<a '.$on.' @click="pager(\''.BASE_URL.'x3form_builder/blacklist/'.$id_area.'/'.$i->code.'\')" title="'._SWITCH_LANGUAGE.'">'.ucfirst($i->language).'</a>';
	}
	echo '</div>';
}

// area switcher
if (MULTIAREA)
{
	echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
	foreach ($areas as $i)
	{
        if ($i->id > 1)
        {
            $on = ($i->id == $id_area)
                ? 'class="link"'
                : 'class="dark"';
            echo '<a '.$on.' @click="pager(\''.BASE_URL.'x3form_builder/blacklist/'.$i->id.'/'.$lang.'\')" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a></li>';
        }
	}
	echo '</div>';
}
echo '</div>';

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
		$statuses = AdmUtils_helper::statuses($i);

		$actions = '';
		if (($i->level > 2 && $i->xlock == 0) || $i->level >= 3)
		{
            $actions .= AdmUtils_helper::link('xon', 'x3form_builder/set/blacklist/xon/'.$i->id_area.'/'.$i->id.'/'.intval(!$i->xon), $statuses);
			if ($i->level >= 4)
			{
                $actions .= AdmUtils_helper::link('xlock', 'x3form_builder/set/blacklist/xlock/'.$i->id_area.'/'.$i->id.'/'.intval(!$i->xlock), $statuses);
                $actions .= AdmUtils_helper::link('delete', 'x3form_builder/delete_blacklist/'.$i->id_area.'/'.$i->id);
			}
		}

		echo '<tr>
				<td>'.$i->name.'</td>
				<td class="space-x-2 text-right">'.$actions.'</td>
			</tr>';
	}
	echo '</tbody></table>';

    echo '<div id="form_pager" class="pager">'.X4Pagination_helper::tw_admin_pager(BASE_URL.'x3form_builder/blacklist/'.$id_area.'/'.$lang.'/', $items[1], 5, false, '', '').'</div>';
}
else
{
	echo '<p>'._NO_ITEMS.'</p>';
}