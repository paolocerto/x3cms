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
<table class="zebra">
	<tr class="first">
		<th style="width:24em;"><?php echo _THEME ?></th>
		<th><?php echo _AREA ?></th>
		<th></th>
		<th style="width:12em;"><?php echo _ACTIONS ?></th>
		<th style="width:6em;"></th>
	</tr>
	
<?php
if ($theme_in) 
{
	echo '<tr><td colspan="5" class="menu">'._INSTALLED_THEMES.'</td></tr>';
	$tmp = 0;
	foreach($theme_in as $i)
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
		
		if ($i->xlock) {
			$lock = _LOCKED;
			$lock_status = 'lock';
		}
		else 
		{
			$lock = _UNLOCKED;
			$lock_status = 'unlock-alt';
		}
		$actions = $uninstall = $area = '';
		
		if (($i->level > 2 && $i->xlock == 0) || $i->level == 4) 
		{
			$actions = '<a class="btl" href="'.BASE_URL.'themes/set/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="fa fa-lightbulb-o fa-lg '.$on_status.'"></i></a> ';
			
			if ($i->level == 4) 
			{
				$uninstall = '<a class="btl" href="'.BASE_URL.'themes/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><i class="fa fa-'.$lock_status.' fa-lg"></i></a>';
				if (empty($i->area)) 
					$uninstall .= '<a class="bta" href="'.BASE_URL.'themes/uninstall/'.$i->id.'" title="'._UNINSTALL.'"><i class="fa fa-upload fa-lg"></i></a>';
				else 
				{
					$uninstall .= '<a><i class="fa faupload invisible fa-lg"></i></a>';
					$area = '['.$i->area.']';
				}
			}
		}
		if ($tmp != $i->id) 
		{
			$tmp = $i->id;
			
			$minify = ($i->level == 4)
			    ? '<a class="btl" href="'.BASE_URL.'themes/minimize/'.$i->id.'/'.$i->name.'" title="'._MINIMIZE.'"><i class="fa fa-recycle fa-lg"></i></a></td>'
			    : '';
			
			echo '<tr>
					<td><strong>'.$i->name.'</strong> <span class="xs-hidden"> - '.$i->description.'</span></td>
					<td>'.$area.'</td>
					<td></td>
					<td>'.$actions.'
						<a class="btm" href="'.BASE_URL.'templates/index/'.$i->id.'/'.$i->name.'" title="'._TEMPLATES.'"><i class="fa fa-desktop fa-lg"></i></a> 
						<a class="btm" href="'.BASE_URL.'menus/index/'.$i->id.'/'.$i->name.'" title="'._MENUS.'"><i class="fa fa-navicon fa-lg"></i></a>
						'.$minify.'
					<td>'.$uninstall.'</td>
					</tr>';
		}
		else 
		{
			echo '<tr>
					<td></td>
					<td></td>
					<td>'.$area.'</td>
					<td></td>
					<td></td>
					</tr>';
		}
	}
}

if ($theme_out && $_SESSION['level'] == 4) 
{
	echo '<tr><td colspan="5" class="menu">'._INSTALLABLE_THEMES.'</td></tr>';
	foreach($theme_out as $i)
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
		
		$install = '<a class="btl" href="'.BASE_URL.'themes/install/'.$name.'" title="'._INSTALL.'"><i class="fa fa-download fa-lg"></i></a>';
		echo '<tr>
				<td colspan="2">'.$name.'</td>
				<td></td>
				<td></td>
				<td class="aright">'.$install.'</td>
				</tr>';
	}
}
?>
</table>
<script>
window.addEvent('domready', function()
{
	X3.content('filters','themes/filter', null);
	buttonize('topic', 'btm', 'tdown');
	buttonize('topic', 'bta', 'modal');
	actionize('topic',  'btl', 'tdown', escape('themes'));
	zebraTable('zebra');
});
</script>
