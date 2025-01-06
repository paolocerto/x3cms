<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// contextx list view

?>
<h1 class="mt-6"><?php echo $page->icon.' '._CONTEXT_LIST ?></h1>
<p><?php echo _CONTEXT_MSG ?></p>


<?php
if ($items)
{
    echo '<table>
    <thead>
	<tr>
		<th>'._CONTEXTS.'</th>
		<th class="w-40">'._ACTIONS.'</th>
	</tr>
    </thead>
    <tbody>';

	foreach ($items as $i)
	{
        $statuses = AdminUtils_helper::statuses($i);

		$actions = '';

        // check permissions
        if (($i->level > 1 && $i->xlock == 0) || $i->level >= 3)
        {
            // only user contexts are editables
            if ($i->code > 100)
            {
                $actions = AdminUtils_helper::link('edit', 'contexts/edit/'.$i->id_area.'/'.$i->lang.'/'.$i->id);
                if ($i->level > 2)
			    {
                    $actions .= AdminUtils_helper::link('xon', 'contexts/set/xon/'.$i->id_area.'/'.$i->id.'/'.(($i->xon+1)%2), $statuses);

                    if ($i->level >= 4)
                    {
                        $actions .= AdminUtils_helper::link('xlock', 'contexts/set/xlock/'.$i->id_area.'/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);

                        $actions .= AdminUtils_helper::link('delete', 'contexts/delete/'.$i->id);
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
                <td class="text-right">'.$actions.'</td>
            </tr>';
	}

    echo '</tbody>
        </table>
        <p class="text-sm"><b>*</b> '._DEFAULT_CONTEXTS_MSG.'</p>';
}
else
{
	echo '<p>'._NO_ITEMS.'</p>';
}
