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

<h1><?php echo _ARTICLE_HISTORY.': '.$art->name ?></h1>
<form name="bulk_action" id="bulk_action" onsubmit="return false;" method="post" action="<?php echo BASE_URL.'articles/bulk/'.$id_area.'/'.$lang.'/'.$bid ?>">

<input type="hidden" name="action" value="delete" />
<table class="zebra">
	<tr class="first">
		<th><?php echo _PREVIEW ?></th>
		<th style="width:14em;"><?php echo _ACTIONS ?></th>
		<th style="width:7em;"></th>
		<th style="width:4em;"><input type="checkbox" class="bulker" name="bulk_selector" id="bulk_selector" /></th>
	</tr>
<?php
foreach($history as $i)
{
	if ($i->xon) 
	{
		$status = _ON;
		$on_status = 'orange';
	}
	else {
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
	
	// define the end of the visibility window
	$out = ($i->date_out) 
		? date('Y-m-d', $i->date_out) 
		: _UNDEFINED;
	
	$actions = $delete = $date_in = $date_out = '';
	// if user have write permission and object is unlocked or user is an administrator
	if (($i->level > 2 && $i->xlock == 0) || $i->level == 4) 
	{
		$actions .= '<a class="btl" href="'.BASE_URL.'articles/set/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="far fa-lightbulb fa-lg '.$on_status.'"></i></a>';
		
		// administrator
		if ($i->level == 4) 
		{
			$delete = '<a class="btl" href="'.BASE_URL.'articles/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><i class="fas fa-'.$lock_status.' fa-lg"></i></a> 
				<a class="bta" href="'.BASE_URL.'articles/delete_version/'.$i->id.'" title="'._DELETE.'"><i class="fas fa-trash fa-lg red"></i></a>';
		}
		$date_in = '<a class="bta" href="'.BASE_URL.'articles/setdate/'.$i->id.'" title="'._EDIT_DATE.'">'.date('Y-m-d', $i->date_in).'</a>';
		$date_out = '<a class="bta" href="'.BASE_URL.'articles/setdate/'.$i->id.'" title="'._EDIT_DATE.'">'.$out.'</a>';
	}
	else 
	{
		$date_in = date('Y-m-d', $i->date_in);
		$date_out = date('Y-m-d', $i->date_out);
	}
	
	echo '<tr>
			<td class="small line-height1"><h3>'._LAST_UPGRADE.': '.$i->updated.'</h3>'.stripslashes($i->content).'<h3>'._MODULE.': '.$i->module.'/'.$i->param.'</h3></td>
			<td>
				'._START_DATE.' '.$date_in.'<br />
				'._END_DATE.' '.$date_out.'
			</td>
			<td class="aright">'.$actions.$delete.'</td>
			<td class="acenter"><input type="checkbox" class="bulkable vmiddle" name="bulk[]" value="'.$i->id.'" /></td>
			</tr>';
}
?>
</table>
</form>
<script>
window.addEvent('domready', function()
{
	X3.content('filters','articles/filter/<?php echo $id_area.'/'.$lang ?>/bulk', '<?php echo X4Utils_helper::navbar($navbar, ' . ', false) ?>');
	buttonize('topic', 'btm', 'topic');
	buttonize('topic', 'bta', 'modal');
	actionize('topic',  'btl', 'topic', escape('articles/history/<?php echo $id_area.'/'.$lang.'/'.$bid ?>/0'));
	zebraTable('zebra');
});
</script>
