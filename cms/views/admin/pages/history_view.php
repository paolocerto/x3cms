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
<h2><?php echo _HISTORY_LIST.' '.$page->name ?></h2>
<div id="popper"></div><div id="unique_pop"></div>
<?php
if ($page->url != 'home') 
{
	echo '<p><a href="'.BASE_URL.'pages/index/'.$page->id_area.'/'.$page->lang.'/'.str_replace('/', '-', $page->xfrom).'" title="'._GO_BACK.'"><<<</a> <strong>Pagina '.$page->name.'</strong></p>';
}
?>	
<table class="zebra">
	<tr>
		<th style="width:80%;"><?php echo _PREVIEW ?></th>
		<th style="width:10%;"><?php echo _ACTIONS ?></th>
		<th style="width:10%;"></th>
	</tr>
<?php
foreach($contents as $i)
{
	if ($i->xon) 
	{
		$status = _ON;
		$on_status = 'on';
	}
	else 
	{
		$status = _OFF;
		$on_status = 'off';
	}
	
	if ($i->xlock) 
	{
		$lock = _LOCKED;
		$lock_status = 'locked';
	}
	else 
	{
		$lock = _UNLOCKED;
		$lock_status = 'unlocked';
	}
	
	// define end of visibility time window
	$out = ($i->date_out > 0) 
		? date('Y-m-d', $i->date_out) 
		: _UNDEFINED;
	
	$actions = $delete = $date_in = $date_out = '';
	
	// check permission
	if (($i->level > 2 && $i->xlock == 0) || $i->level == 4) {
		$actions .= '<a href="'.BASE_URL.'contents/set/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><img src="'.THEME_URL.'img/'.$on_status.'.png" alt="'.$status.'" /></a>';
		
		// admin user
		if ($i->level == 4) {
			$delete = '<a href="'.BASE_URL.'contents/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><img src="'.THEME_URL.'img/'.$lock_status.'.png" alt="'.$lock.'" /></a> 
				<a class="u_pop" href="'.BASE_URL.'contents/delete/'.$i->id.'" title="'._DELETE.'"><img src="'.THEME_URL.'img/delete.png" alt="'._DELETE.'" /></a>';
		}
		
		$date_in = '<a class="u_pop" href="'.BASE_URL.'contents/setdate/'.$i->id.'" title="'._EDIT_DATE.'">'.date('Y-m-d', $i->date_in).'</a>';
		$date_out = '<a class="u_pop" href="'.BASE_URL.'contents/setdate/'.$i->id.'" title="'._EDIT_DATE.'">'.$out.'</a>';
	}
	else 
	{
		$date_in = date('Y-m-d', $i->date_in);
		$date_out = date('Y-m-d', $i->date_out);
	}
	
	echo '<tr>
			<td><h3>'._LAST_UPGRADE.': '.$i->updated.'</h3>'.stripslashes($i->content).'<h3>'._MODULE.': '.$i->module.'/'.$i->param.'</h3></td>
			<td>
				'._START_DATE.' '.$date_in.'<br />
				'._END_DATE.' '.$date_out.'<br />
				'.$actions.'
			</td>
			<td class="aright">'.$delete.'</td>
			</tr>';
}
?>	
</table>
