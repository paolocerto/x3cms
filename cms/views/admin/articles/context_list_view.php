<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// language switcher
if (MULTILANGUAGE) 
{
	echo '<div class="aright sbox"><ul class="inline-list">';
	foreach($langs as $i) 
	{
		$on = ($i->code == $lang) ? ' on' : '';
		echo '<li><a class="btm'.$on.'" href="'.BASE_URL.'contexts/index/'.$id_area.'/'.$i->code.'" title="'._SWITCH_LANGUAGE.'">'.ucfirst($i->language).'</a></li>';
	}
	echo '</ul></div>';
}

// area switcher
echo '<div class="aright sbox"><ul class="inline-list">';
foreach($areas as $i) 
{
	$on = ($i->id == $id_area) ? ' on' : '';
	echo '<li><a class="btm '.$on.'" href="'.BASE_URL.'contexts/index/'.$i->id.'/'.$lang.'" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a></li>';
}
echo '</ul></div>';

?>
<h1><?php echo _CONTEXT_LIST ?></h1>

<table class="zebra">
	<tr class="first">
		<th><?php echo _CONTEXTS ?></th>
		<th style="width:6em;"><?php echo _ACTIONS ?></th>
		<th style="width:6em;"></th>
	</tr>
		
<?php
if ($items) 
{
	foreach($items as $i)
	{
		if ($i->level) 
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
			else {
				$lock = _UNLOCKED;
				$lock_status = 'unlock-alt';
			}
			$actions = $delete = '';
		
			// check permissions
			if (($i->level > 1 && $i->xlock == 0) || $i->level == 4) 
			{
				if ($i->code > 100) 
				{
					$actions = '<a class="bta" href="'.BASE_URL.'contexts/edit/'.$i->id_area.'/'.$i->lang.'/'.$i->id.'" title="'._EDIT.'"><i class="fa fa-pencil fa-lg"></i></a> ';
			
					if ($i->level > 2)
					{
						$actions .= ' <a class="btl" href="'.BASE_URL.'contexts/set/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="fa fa-lightbulb-o fa-lg '.$on_status.'"></i></a>';
			
						if ($i->level == 4)
						{
							$delete = '<a class="btl" href="'.BASE_URL.'contexts/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><i class="fa fa-'.$lock_status.' fa-lg"></i></a>
							 <a class="bta" href="'.BASE_URL.'contexts/delete/'.$i->id.'" title="'._DELETE.'"><i class="fa fa-trash fa-lg red"></i></a>';
						}
					}
				}
			}
		
			echo '<tr>
					<td><a class="btm" href="'.BASE_URL.'articles/index/'.$i->id_area.'/'.$i->lang.'/context_order/'.$i->code.'" title="'._VIEW_ARTICLES.'">'.$i->name.'</a></td>
					<td class="aright">'.$actions.'</td>
					<td class="aright">'.$delete.'</td>
					</tr>';
		}
	}
}
?>
</table>
<script>
window.addEvent('domready', function() 
{
	X3.content('filters', 'contexts/filter/<?php echo $id_area.'/'.$lang ?>', '<?php echo X4Utils_helper::navbar($navbar, ' . ', false) ?>');
	buttonize('topic', 'btm', 'topic');
	buttonize('topic', 'bta', 'modal');
	actionize('topic', 'btl', 'topic', escape('contexts/index/<?php echo $id_area.'/'.$lang ?>/0'));
	zebraTable('zebra');
});
</script>
