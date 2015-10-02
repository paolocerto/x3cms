<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
?>
<h2><?php echo $theme._TRAIT_._MENU_LIST ?></h2>
<table class="zebra">
	<tr class="first">
		<th><?php echo _MENUS ?></th>
		<th style="width:6em"><?php echo _ACTIONS ?></th>
		<th style="width:6em;"></th>
	</tr>
<?php
foreach($menus as $i)
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
	$actions = $delete = '';
	
	// check permission
	if (($i->level > 2 && $i->xlock == 0) || $i->level == 4) 
	{
		$actions = '<a class="bta" href="'.BASE_URL.'menus/edit/'.$i->id_theme.'/'.$i->id.'" title="'._EDIT.'"><i class="fa fa-pencil fa-lg"></i></a>
			<a class="btl" href="'.BASE_URL.'menus/set/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="fa fa-lightbulb-o fa-lg '.$on_status.'"></i>';
		
		// admin user
		if ($i->level == 4) 
			$delete ='<a class="btl" href="'.BASE_URL.'menus/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><i class="fa fa-'.$lock_status.' fa-lg"></i></a>
				<a class="bta" href="'.BASE_URL.'menus/delete/'.$i->id.'" title="'._DELETE.'"><i class="fa fa-trash fa-lg red"></i></a>';
	}
	
	echo '<tr>
			<td><strong>'.$i->name.'</strong> <span class="xs-hidden">'._TRAIT_.$i->description.'</span></td>
			<td>'.$actions.'</td>
			<td class="aright">'.$delete.'</td>
			</tr>';
}
?>	
</table>
<script>
window.addEvent('domready', function()
{
	X3.content('filters','menus/filter/<?php echo $id_theme ?>', null);
	buttonize('topic', 'btm', 'tdown');
	buttonize('topic', 'bta', 'modal');
	actionize('topic',  'btl', 'tdown', escape('menus/xlist/<?php echo $id_theme.'/'.$theme ?>'));
	zebraTable('zebra');
});
</script>
