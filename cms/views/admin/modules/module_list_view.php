<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// area switcher

echo '<div class="switcher text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
foreach ($areas as $i)
{
	$on = ($i->id == $id_area) ? 'class="link"' : 'class="dark"';
	echo '<a '.$on.' @click="pager(\''.BASE_URL.'modules/index/'.$i->id.'/'.$i->name.'\')" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a>';
}
echo '</div>';
?>
<h1 class="mt-6"><?php echo $page->icon.' '._MODULE_LIST.': '.$area->title ?></h1>
<?php
if ($plugged || $pluggable)
{
?>
<table>
    <thead>
        <tr>
            <th class="text-left pl-4"><?php echo _MODULE ?></th>
            <th class="w-8"></th>
            <th class="w-48"><?php echo _ACTIONS ?></th>
        </tr>
    </thead>
    <tbody>

<?php
    if ($plugged)
    {
        // installed plugins
        echo '<tr><td colspan="3" class="bg text-center">'._INSTALLED_PLUGINS.'</td></tr>';

        foreach ($plugged as $i)
        {
            $statuses = AdminUtils_helper::statuses($i, ['xon', 'xlock', 'hidden']);

            $actions = '';

            // admin
            $admin = ($i->admin && $i->level > 0)
                ? '<a class="link" @click="pager(\''.BASE_URL.$i->name.'/mod/'.$i->id_area.'\')" title="'.$i->title.'">'.$i->name.'</a>'
                : '<strong>'.$i->name.'</strong>';

            // check permission
            if (($i->level > 2 && $i->xlock == 0) || $i->level >= 3)
            {
                $actions .= AdminUtils_helper::link('xon', 'modules/set/xon/'.$i->id_area.'/'.$i->id.'/'.(($i->xon+1)%2), $statuses);

                // configurable
                if ($i->configurable)
                {
                    $actions .= ' <a class="link" @click="popup(\''.BASE_URL.'modules/config/'.$i->id.'\')" title="'._CONFIG.'">
                        <i class="fa-solid fa-lg fa-sliders"></i>
                    </a>';
                }

                // admin user
                // adminlevel is the specific privs on this item
                if ($i->level >= 4 && $i->adminlevel > 1)
                {
                    if ($i->hidden)
                    {
                        $hidden = _HIDDEN;
                        $icon = '-slash';
                    }
                    else
                    {
                        $hidden = _VISIBLE;
                        $icon = '';
                    }

                    $actions .= '<a class="link" @click="setter(\''.BASE_URL.'modules/set/hidden/'.$i->id_area.'/'.$i->id.'/'.(($i->hidden+1)%2).'\')" title="'._STATUS.' '.$hidden.'">
                            <i class="fa-solid fa-link'.$icon.' fa-lg"></i>
                        </a>';

                    $actions .= AdminUtils_helper::link('xlock', 'modules/set/xlock/'.$i->id_area.'/'.$i->id.'/'.(($i->xlock+1)%2), $statuses);

                    $actions .= '<a class="link" @click="popup(\''.BASE_URL.'modules/uninstall/'.$i->id.'\')" title="'._UNINSTALL.'">
                            <i class="fa-solid fa-download fa-lg warn"></i>
                        </a>';
                }
            }

            // module instructions
            $help = (file_exists(PATH.'plugins/'.$i->name.'/instructions_'.X4Route_core::$lang.'.txt'))
                ? '<a class="link" @click="popup(\''.BASE_URL.'modules/help/'.$i->name.'/'.X4Route_core::$lang.'\')" title="'._INSTRUCTIONS.'">
                        <i class="fa-solid fa-circle-info fa-lg"></i>
                    </a>'
                : '';

            echo '<tr>
                    <td><span class="text-xs hidden md:inline-block">'.$i->version._TRAIT_.'</span> '.$admin.' <span class="text-sm hidden md:inline-block">'._TRAIT_.$i->title.'</span></td>
                    <td>'.$help.'</td>
                    <td class="space-x-2 text-right">'.$actions.'</td>
                    </tr>';
        }
    }

    // installable plugin
    if ($pluggable && $_SESSION['level'] >= 4)
    {
        echo '<tr><td colspan="3" class="bg text-center">'._INSTALLABLE_PLUGINS.'</td></tr>';
        foreach ($pluggable as $i)
        {
            $name = str_replace(PATH.'plugins/', '', $i);
            $actions = '<a class="link" @click="setter(\''.BASE_URL.'modules/install/'.$area->id.'/'.$name.'\')" title="'._INSTALL.'">
                <i class="fa-solid fa-upload fa-lg"></i>
            </a>';

            // module instructions
            $help = (file_exists(PATH.'plugins/'.$name.'/instructions_'.X4Route_core::$lang.'.txt'))
                ? '<a class="link" @click="popup(\''.BASE_URL.'modules/help/'.$name.'/'.X4Route_core::$lang.'\')" title="'._INSTRUCTIONS.'">
                        <i class="fa-solid fa-circle-info fa-lg"></i>
                    </a>'
                : '';

            echo '<tr>
                    <td><strong>'.$name.'</strong></td>
                    <td>'.$help.'</td>
                    <td class="space-x-2 text-right">'.$actions.'</td>
                    </tr>';
        }
    }
?>
    </tbody>
</table>
<?php
}
else
{
    echo '<p class="py-6">'._NO_ITEMS.'</p>';
}
