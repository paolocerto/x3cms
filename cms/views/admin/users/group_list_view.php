<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
?>
<h2><?php echo _GROUP_LIST ?></h2>

<table class="zebra">
	<tr class="first">
		<th><?php echo _AREA ?></th>
		<th><?php echo _GROUP ?></th>
		<th style="width:12em;"><?php echo _ACTIONS ?></th>
		<th style="width:6em;"></th>
	</tr>
<?php
foreach($groups as $i)
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
	if (($i->level > 1 && $i->xlock == 0) || $i->level == 4) 
	{
		$actions = '<a class="bta" href="'.BASE_URL.'groups/edit/'.$i->id.'" title="'._EDIT.'"><i class="fas fa-pencil-alt fa-lg"></i></a> 
			<a class="bta" href="'.BASE_URL.'users/edit/0/'.$i->id.'" title="'._ADD_USER.'"><i class="fas fa-user-plus fa-lg"></i></a>';
		
		// manager user
		if ($i->level > 2 && $i->id > 1) 
			$actions .= ' <a class="btl" href="'.BASE_URL.'groups/set/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="far fa-lightbulb fa-lg '.$on_status.'"></i></a>';
		
		// admin user
		if ($i->level == 4 && $i->id > 1) 
		{
			$delete = '<a class="btl" href="'.BASE_URL.'groups/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><i class="fas fa-'.$lock_status.' fa-lg"></i></a> 
			 <a class="bta" href="'.BASE_URL.'groups/delete/'.$i->id.'" title="'._DELETE.'"><i class="fas fa-trash fa-lg red"></i></a>';
			
			if ($i->id > 1) 
				$actions .= ' <a class="bta" href="'.BASE_URL.'groups/gperm/'.$i->id.'" title="'._EDIT_GPRIV.'"><i class="fas fa-cogs fa-lg"></i></a>';
		}
		
	}
	
	//$items = $umod->get_users($i->id);
	
	echo '<tr>
			<td>'.$i->title.'</td>
			<td><a class="bta" href="'.BASE_URL.'users/users/'.$i->id.'" title="">'.$i->name.'</a>'._TRAIT_.$i->description.'</td>
			<td>'.$actions.'</td>
			<td class="aright">'.$delete.'</td>
			</tr>';
}
?>
</table>
<script src="<?php echo THEME_URL ?>js/basic.js"></script>
<script>
window.addEvent('domready', function()
{
	X3.content('filters','groups/filter', null);
	buttonize('topic', 'btm', 'tdown');
	buttonize('topic', 'bta', 'modal');
	actionize('topic',  'btl', 'tdown', escape('users'));
	zebraTable('zebra');
});
</script>
