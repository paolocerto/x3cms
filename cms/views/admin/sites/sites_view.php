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
<h1><?php echo _SITE_MANAGER ?></h1>

<table>
<thead>
	<tr class="first">
		<th class="text-left pl-4"><?php echo _DOMAIN ?></th>
		<th style="width:9em"><?php echo _ACTIONS ?></th>
	</tr>
</thead>
<tbody>
<?php
foreach ($items as $i)
{
    $actions = '';
    if ($_SESSION['level'] > 2)
    {
        $statuses = AdmUtils_helper::statuses($i, ['xon']);

        $actions .= AdmUtils_helper::link('settings', 'sites/config/'.$i->id);

        // if caching
        if (CACHE)
        {
            $actions .= '<a class="link" @click="setter(\''.BASE_URL.'sites/clear_cache\')" title="'._CLEAR_CACHE.'">
                <i class="fa-solid fa-lg fa-eraser"></i>
            </a>';
        }
        if (APC)
        {
            $actions .= ' <a class="link" @click="setter(\''.BASE_URL.'sites/clear_apc\')" title="'._CLEAR_CACHE.' APC">
                <i class="fa-solid fa-lg fa-eraser"></i>
            </a>';
        }
        $actions .= '<a class="link" @click="setter(\''.BASE_URL.'sites/offline/'.$i->id.'/'.(($i->xon+1)%2).'\')" title="'._STATUS.' '.$statuses['xon']['label'].'">
            <i class="fa-solid fa-lg fa-globe '.$statuses['xon']['class'].'"></i>
        </a>';
    }

    // admin user
    if ($_SESSION['level'] >= 4)
    {
        $actions = AdmUtils_helper::link('edit', 'sites/edit/'.$i->id).$actions;
    }

    // bold wau site
    $domain = ($i->id == 1)
        ? '<b>'.$i->domain.'</b>'
        : $i->domain;

    echo '<tr>
            <td>'.$domain.'</td>
            <td class="space-x-2 text-right">'.$actions.'</td>
        </tr>';
}
?>
    </tbody>
</table>
