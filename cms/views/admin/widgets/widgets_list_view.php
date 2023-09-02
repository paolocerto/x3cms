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
<h1 class="mt-6"><?php echo _WIDGETS_MANAGER ?></h1>
<?php
if ($items)
{
	echo '<table class="mb-0">
        <thead>
	        <tr>
                <th>'._WIDGETS_ITEMS.'</th>
                <th class="w-20">'._ACTIONS.'</th>
            </tr>
        </thead>
    </table>
	<div x-data="xsortable()" x-init="setup(\'sortable\', \'widgets/ordering\')">
        <div id="sortable">';

	$order = array();
	$n = sizeof($items);
	foreach ($items as $i)
	{
		$statuses = AdmUtils_helper::statuses($i);

        $actions = AdmUtils_helper::link('xon', 'widgets/set/xon/'.$i->id.'/'.intval(!$i->xon), $statuses);
        $actions .= AdmUtils_helper::link('delete', 'widgets/delete/'.$i->id);

		echo '<div class="sort-item" id="'.$i->id.'"><table class="my-0"><tr>
                <td>'.$i->area._TRAIT_.$i->description.'</td>
                <td class="w-20 space-x-2 text-right">'.$actions.'</td>
            </tr>
        </table></div>';

		$order[] = $i->id;
	}

    echo '</div></div>';
}
else
{
	echo '<p class="mt-4">'._NO_ITEMS.'</p>';
}