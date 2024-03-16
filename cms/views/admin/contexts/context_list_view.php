<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

echo '<div class="switcher">';
// language switcher
if (MULTILANGUAGE)
{
	echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
	foreach ($langs as $i)
	{
		$on = ($i->code == $lang) ? 'class="link"' : 'class="dark"';
		echo '<a '.$on.' @click="pager(\''.BASE_URL.'contexts/index/'.$id_area.'/'.$i->code.'\')" title="'._SWITCH_LANGUAGE.'">'.ucfirst($i->language).'</a>';
	}
	echo '</div>';
}

// area switcher
echo '<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
foreach ($areas as $i)
{
	$on = ($i->id == $id_area) ? 'class="link"' : 'class="dark"';
	echo '<a '.$on.' @click="pager(\''.BASE_URL.'contexts/index/'.$i->id.'/'.$lang.'\')" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a>';
}
echo '</div></div>';

?>
<h1 class="mt-6"><?php echo $page->icon.' '._CONTEXT_LIST ?></h1>
<p><?php echo _CONTEXT_MSG ?></p>

<table>
    <thead>
	<tr>
		<th><?php echo _CONTEXTS ?></th>
		<th class="w-40"><?php echo _ACTIONS ?></th>
	</tr>
    </thead>
    <tbody>

<?php
if ($items)
{
	foreach ($items as $i)
	{
        $statuses = AdmUtils_helper::statuses($i);

		$actions = '';

        // check permissions
        if (($i->level > 1 && $i->xlock == 0) || $i->level >= 3)
        {
            // only user contexts are editables
            if ($i->code > 100)
            {
                $actions = AdmUtils_helper::link('edit', 'contexts/edit/'.$i->id_area.'/'.$i->lang.'/'.$i->id);
                if ($i->level > 2)
			    {
                    $actions .= AdmUtils_helper::link('xon', 'contexts/set/xon/'.$i->id_area.'/'.$i->id.'/'.(($i->xon+1)%2), $statuses);

                    if ($i->level >= 4)
                    {
                        $actions .= AdmUtils_helper::link('xlock', 'contexts/set/xlock/'.$i->id_area.'/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);

                        $actions .= AdmUtils_helper::link('delete', 'contexts/delete/'.$i->id);
                    }
                }
            }
            else
            {
                $actions = '*';
            }
        }

        echo '<tr>
                <td><a class="link" @click="pager(\''.BASE_URL.'articles/index/'.$i->id_area.'/'.$i->lang.'?&xcnt='.$i->code.'\')" title="'._VIEW_ARTICLES.'">'.$i->name.'</a></td>
                <td class="space-x-2 text-right">'.$actions.'</td>
            </tr>';
	}
}
?>
</tbody>
</table>

<p class="text-sm"><b>*</b> <?php echo _DEFAULT_CONTEXTS_MSG ?></p>