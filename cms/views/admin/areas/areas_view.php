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
<h1><?php echo _AREA_LIST ?></h1>

<table>
    <thead>
        <tr>
            <th class="w-4">#</th>
            <th><?php echo _AREA ?></th>
            <th class="w-44"><?php echo _ACTIONS ?></th>
        </tr>
    </thead>
    <tbody>

<?php
foreach ($areas as $i)
{
    $statuses = AdmUtils_helper::statuses($i);

	$actions = '';

	// check permission
	if (($i->level > 2 && $i->xlock == 0) || $i->level >= 3)
	{
		$actions = AdmUtils_helper::link('edit', 'areas/edit/'.$i->id);

        $actions .= AdmUtils_helper::link('settings','areas/seo/'.$i->id);

		// manager user
		if ($i->id > 2)
		{
			$actions .= AdmUtils_helper::link('xon', 'areas/set/xon/'.$i->id.'/'.(($i->xon+1)%2), $statuses);

			// admin user
			if ($i->level >= 4)
			{
                $actions .= AdmUtils_helper::link('xlock', 'areas/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);

				// not default areas
                $actions .= AdmUtils_helper::link('delete','areas/delete/'.$i->id);
			}
		}
	}

	$private = ($i->private)
	    ? ' - ['._PRIVATE.']'
	    : '';

	echo '<tr>
			<td>#'.$i->id.'</td>
			<td><a class="link" href="'.BASE_URL.'pages/index/'.$i->id.'/'.X4Route_core::$lang.'/home/1" title="">'.$i->name.'</a> <span class="hidden md:inline-block">'._TRAIT_.$i->description.$private.'</span></td>
			<td class="space-x-2 text-right">'.$actions.'</td>
			</tr>';
}
?>
    </tbody>
</table>