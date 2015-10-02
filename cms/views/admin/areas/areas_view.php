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
<h1><?php echo _AREA_LIST ?></h1>
<table class="zebra">
	<tr class="first">
		<th><?php echo _AREA ?></th>
		<th style="width:8em;"><?php echo _ACTIONS ?></th>
		<th style="width:6em;"></th>
	</tr>
<?php
foreach($areas as $i)
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
		$actions = '<a class="bta" href="'.BASE_URL.'areas/edit/'.$i->id.'" title="'._EDIT.'"><i class="fa fa-pencil fa-lg"></i></a> 
			<a class="bta" href="'.BASE_URL.'areas/seo/'.$i->id.'" title="'._SEO_DATA.'"><i class="fa fa-cogs fa-lg"></i></a>'; 
			
		// manager user
		if ($i->id > 2) 
		{
			$actions .= ' <a class="btl" href="'.BASE_URL.'areas/set/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="fa fa-lightbulb-o fa-lg '.$on_status.'"></i></a>';
		
			// admin user
			if ($i->level == 4) 
			{
				$delete ='<a class="btl" href="'.BASE_URL.'areas/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><i class="fa fa-'.$lock_status.' fa-lg"></i></a>';
			
				// not default areas
				$delete .= ' <a class="bta" href="'.BASE_URL.'areas/delete/'.$i->id.'" title="'._DELETE.'"><i class="fa fa-trash fa-lg red"></i></a>';
			}
		}
	}
	echo '<tr>
			<td class="hide-x"><a class="btt" href="'.BASE_URL.'pages/xlist/'.$i->id.'/'.X4Route_core::$lang.'/home/1" title="">'.$i->name.'</a> <span class="xs-hidden">'._TRAIT_.$i->description.'</span></td>
			<td>'.$actions.'</td>
			<td class="aright">'.$delete.'</td>
			</tr>';
}
?>	
</table>
<script>
window.addEvent('domready', function() {
	X3.content('filters','areas/filter', '<?php echo X4Utils_helper::navbar($navbar, ' . ') ?>');
	buttonize('topic', 'bta', 'modal');
	actionize('topic',  'btl', 'topic', escape('areas/xlist'));
	zebraTable('zebra');
	linking('table.zebra a.btt');
});
</script>

