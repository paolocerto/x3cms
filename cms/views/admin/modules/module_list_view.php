<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// area switcher
echo '<div class="aright sbox"><ul class="inline-list">';
foreach($areas as $i) 
{
	$on = ($i->id == $id_area) ? 'class="on"' : '';
	echo '<li><a '.$on.' href="'.BASE_URL.'modules/index/'.$i->id.'/'.$i->name.'" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a></li>';
}
echo '</ul></div>';
?>
<h1><?php echo _MODULE_LIST.': '.$area->title ?></h1>
<?php
if ($plugged || $pluggable)
{
?>
<table class="zebra">
	<tr>
		<th><?php echo _MODULE ?></th>
		<th style="width:2em;"></th>
		<th style="width:12em;"><?php echo _ACTIONS ?></th>
		<th style="width:8em;"></th>
	</tr>
	
<?php
    if ($plugged) 
    {
        // installed plugins
        echo '<tr><td colspan="4" class="menu">'._INSTALLED_PLUGINS.'</td></tr>';
        
        foreach($plugged as $i)
        {
            if ($i->xon) 
            {
                $status = _ON;
                $on_status = 'orange';
            }
            else 
            {
                $status = _OFF;
                $on_status = 'gray';
            }
            
            if ($i->xlock) 
            {
                $lock = _LOCKED;
                $lock_status = 'lock';
            }
            else 
            {
                $lock = _UNLOCKED;
                $lock_status = 'unlock-alt';
            }
            
            if ($i->hidden) 
            {
                $hidden = _HIDDEN;
                $on_hidden = 'chain-broken';
            }
            else 
            {
                $hidden = _VISIBLE;
                $on_hidden = 'chain';
            }
            
            $actions = $uninstall = '';
            
            // admin
            $admin = ($i->admin && $i->level > 0) 
                ? '<a class="btm" href="'.BASE_URL.$i->name.'/mod/'.$i->id_area.'" title="'.$i->description.'">'.$i->name.'</a>' 
                : '<strong>'.$i->name.'</strong>';
                
            // check permission
            if (($i->level > 2 && $i->xlock == 0) || $i->level == 4) 
            {
                $actions = '<a class="btl" href="'.BASE_URL.'modules/set/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="fa fa-lightbulb-o fa-lg '.$on_status.'"></i></a>';
                
                // admin user
                if ($i->level == 4 && $i->administrator > 1) 
                {
                    $uninstall ='<a class="btl" href="'.BASE_URL.'modules/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><i class="fa fa-'.$lock_status.' fa-lg"></i></a> 
                        <a class="bta" href="'.BASE_URL.'modules/uninstall/'.$i->id.'" title="'._UNINSTALL.'"><i class="fa fa-upload fa-lg"></i></a>';
                    $actions .= '<a class="btl" href="'.BASE_URL.'modules/set/hidden/'.$i->id.'/'.(($i->hidden+1)%2).'" title="'._STATUS.' '.$hidden.'"><i class="fa fa-'.$on_hidden.' fa-lg"></i></a>';
                }
                
                // configurable
                if ($i->configurable) 
                {
                    $actions .= ' <a class="bta" href="'.BASE_URL.'modules/config/'.$i->id.'" title="'._CONFIG.'"><i class="fa fa-cogs fa-lg"></i></a>';
                }
            }
            
            // module instructions
            $help = (file_exists(PATH.'plugins/'.$i->name.'/instructions_'.X4Route_core::$lang.'.txt')) 
                ? '<a class="bta" href="'.BASE_URL.'modules/help/'.$i->name.'/'.X4Route_core::$lang.'" title="'._INSTRUCTIONS.'"><i class="fa fa-info fa-lg"></i></a>'
                : '';
            
            echo '<tr>
                    <td><span class="small xs-hidden">'.$i->version._TRAIT_.'</span> '.$admin.' <span class="small xs-hidden">'._TRAIT_.$i->description.'</span></td>
                    <td>'.$help.'</td>
                    <td>'.$actions.'</td>
                    <td class="aright">'.$uninstall.'</td>
                    </tr>';
        }
    }

    // installable plugin
    if ($pluggable && $_SESSION['level'] == 4) 
    {
        echo '<tr><td colspan="4" class="menu">'._INSTALLABLE_PLUGINS.'</td></tr>';
        foreach($pluggable as $i)
        {
            $name = str_replace(PATH.'plugins/', '', $i);
            $install = '<a class="btl" href="'.BASE_URL.'modules/install/'.$area->id.'/'.$name.'" title="'._INSTALL.'"><i class="fa fa-download fa-lg"></i></a>';
            
            // module instructions
            $help = (file_exists(PATH.'plugins/'.$name.'/instructions_'.X4Route_core::$lang.'.txt')) 
                ? '<a class="bta" href="'.BASE_URL.'modules/help/'.$name.'/'.X4Route_core::$lang.'" title="'._INSTRUCTIONS.'"><i class="fa fa-info fa-lg"></i></a>'
                : '';
                
            echo '<tr>
                    <td><strong>'.$name.'</strong></td>
                    <td>'.$help.'</td>
                    <td></td>
                    <td class="aright">'.$install.'</td>
                    </tr>';
        }
    }
?>
</table>
<?php
}
else
{
    echo '<p>'._NO_ITEMS.'</p>';
}
?>
<script>
window.addEvent('domready', function()
{
	X3.content('filters','modules/filter', '<?php echo X4Utils_helper::navbar($navbar, ' . ', false) ?>');
	buttonize('topic', 'bta', 'modal');
	buttonize('topic', 'btm', 'topic');
	actionize('topic',  'btl', 'topic', escape('modules/index/<?php echo $id_area.'/'.$area->name ?>'));
	zebraTable('zebra');
	linking('ul.inline-list a');
});
</script>

