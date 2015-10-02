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
<h1><?php echo _WIDGETS_MANAGER ?></h1>
<?php
if ($items)
{
	echo '<form id="sort_updater" name="sort_updater" action="'.BASE_URL.'widgets/ordering" method="post">
	<table class="zerom">
	<tr class="first">
		<th>'._WIDGETS_ITEMS.'</th>
		<th style="width:6em;">'._ACTIONS.'</th>
		<th style="width:4em;"></th>
	</tr></table>
	<ul id="sortable" class="nomargin zebra">';
	
	$order = array();
	$n = sizeof($items);
	foreach($items as $i)
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
		
		$actions = ' <a class="btl" href="'.BASE_URL.'widgets/set/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="fa fa-lightbulb-o fa-lg '.$on_status.'"></i></a> ';
		$delete = ' <a class="bta" href="'.BASE_URL.'widgets/delete/'.$i->id.'" title="'._DELETE.'"><i class="fa fa-trash fa-lg red"></i></a>';
		
		echo '<li id="'.$i->id.'">
				<table><tr>
				<td style="width:80%">'.$i->area._TRAIT_.$i->description.'</td>
				<td style="width:10%">'.$actions.'</td>
				<td class="aright" style="width:10%">'.$delete.'</td>
				</tr></table></li>';
		$order[] = $i->id;
	}
	
	$o = implode(',', $order);
	
	echo '</ul>
		<input type="hidden" name="sort_order" id="sort_order" value="'.$o.'" />
		</form>';
}
else
	echo '<p>'._NO_ITEMS.'</p>';
?>
<script>
window.addEvent('domready', function()
{
	X3.content('filters','widgets/filter', null);
	buttonize('topic', 'btt', 'topic');
	buttonize('topic', 'bta', 'modal');
	actionize('topic',  'btl', 'topic', escape('widgets'));
	zebraUl('zebra');
	sortize('sort_updater', 'sortable', 'sort_order');
});
</script>
