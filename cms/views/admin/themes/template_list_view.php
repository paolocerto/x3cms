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
<h2><?php echo $theme._TRAIT_._TEMPLATE_LIST ?></h2>
<table class="zebra">
	<tr class="first">
		<th><?php echo _TEMPLATE ?></th>
		<th></th>
		<th style="width:120px;"><?php echo _ACTIONS ?></th>
		<th style="width:90px;"></th>
	</tr>
	
<?php
if ($tpl_in) 
{
	echo '<tr><td colspan="4" class="menu">'._INSTALLED_TEMPLATES.'</td></tr>';
	foreach($tpl_in as $i)
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
		$actions = $uninstall = '';
		
		// check permission
		if (($i->level > 2 && $i->xlock == 0) || $i->level == 4) 
		{
			$actions = '<a class="bta" href="'.BASE_URL.'templates/edit/template/'.$theme.'/'.$i->id.'/" title="'._EDIT.'"><i class="fas fa-pencil-alt fa-lg"></i></a> 
				<a class="bta" href="'.BASE_URL.'templates/edit/css/'.$theme.'/'.$i->id.'/" title="'._EDIT.' css"><i class="fas fa-paint-brush fa-lg"></i></a> 
				<a class="btl" href="'.BASE_URL.'templates/set/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="far fa-lightbulb fa-lg '.$on_status.'"></i></a>';
			
			// admin user
			if ($i->level == 4) 
			{
				$uninstall ='<a class="btl" href="'.BASE_URL.'templates/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><i class="fas fa-'.$lock_status.' fa-lg"></i></a>';
				$uninstall .= ($i->name != 'base') 
					? '<a class="bta" href="'.BASE_URL.'templates/uninstall/'.$i->id.'" title="'._UNINSTALL_TEMPLATE.'"><i class="fas fa-upload fa-lg"></i></a>' 
					: '<a><i class="fas fa-upload invisible fa-lg"></i></a>';
			}
		}
		echo '<tr>
				<td><strong>'.$i->name.'</strong></td>
				<td>'.$i->description.' ['.$i->sections.']</td>
				<td>'.$actions.'</td>
				<td class="aright">'.$uninstall.'</td>
				</tr>';
	}
}

// only for admin users
if ($tpl_out && $_SESSION['level'] == 4) 
{
	echo '<tr><td colspan="4" class="menu">'._INSTALLABLE_TEMPLATES.'</td></tr>';
	foreach($tpl_out as $i)
	{
		if(function_exists('preg_replace_callback'))
		{
			$name = preg_replace_callback(
				'/(.*)\/(.*)/is', 
				function($m)
				{
					return $m[2];
				}, 
				$i);
		}
		else
		{
			$name = preg_replace('/(.*)\/(.*)/is', '$2', $i, 1);
		}
		$install = '<a class="bta" href="'.BASE_URL.'templates/install/'.$id_theme.'/'.urlencode($name).'" title="'._INSTALL.'"><i class="fas fa-download fa-lg"></i></a>';
			
		if ($name != 'x3ui' && $name != 'mail' && $name != 'login')
		{
			echo '<tr>
					<td>'.$name.'</td>
					<td></td>
					<td></td>
					<td class="aright">'.$install.'</td>
					</tr>';
		}
	}
}
?>
</table>
<script src="/themes/admin/js/basic.js"></script>
<script>
window.addEvent('domready', function()
{
	X3.content('filters','templates/filter', null);
	buttonize('topic', 'btm', 'tdown');
	buttonize('topic', 'bta', 'modal');
	actionize('topic',  'btl', 'tdown', escape('templates/index/<?php echo $id_theme.'/'.$theme ?>'));
	zebraTable('zebra');
});
</script>
