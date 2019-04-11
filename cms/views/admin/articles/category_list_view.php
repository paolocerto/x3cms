<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
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
		echo '<li><a class="btm'.$on.'" href="'.BASE_URL.'categories/index/'.$id_area.'/'.$i->code.'" title="'._SWITCH_LANGUAGE.'">'.ucfirst($i->language).'</a></li>';
	}
	echo '</ul></div>';
}

// area switcher
echo '<div class="aright sbox"><ul class="inline-list">';
foreach($areas as $i) 
{
	$on = ($i->id == $id_area) ? ' on' : '';
	echo '<li><a class="btm'.$on.'" href="'.BASE_URL.'categories/index/'.$i->id.'/'.$lang.'" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a></li>';
}
echo '</ul></div>';

// tag switcher
echo '<div class="aright sbox"><ul class="inline-list">';

if (empty($tags))
{
    echo '<li>'._NO_CATEGORY_TAG.'</li>';
}

foreach($tags as $i) 
{
	$on = ($i->tag == $tag) ? ' on' : '';
	
	$tag_name = (empty($i->tag))
	    ? _NO_CATEGORY_TAG
	    : $i->tag;
	
	echo '<li><a class="btm'.$on.'" href="'.BASE_URL.'categories/index/'.$id_area.'/'.$lang.'/'.$i->tag.'" title="'._CATEGORY_TAG.'">'.$tag_name.'</a></li>';
}
echo '</ul></div>';

?>
<h1><?php echo _CATEGORY_LIST ?></h1>
<?php
if ($items) 
{
	echo '<table class="zebra">
		<tr class="first">
			<th>'._CATEGORIES.'</th>
			<th style="width:6em;">'._ACTIONS.'</th>
			<th style="width:6em;"></th>
		</tr>';
	
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
		
		// check permissions
		if (($i->level > 1 && $i->xlock == 0) || $i->level == 4) 
		{
			$actions = '<a class="bta" href="'.BASE_URL.'categories/edit/'.$i->id_area.'/'.$i->lang.'/'.$i->tag.'/'.$i->id.'" title="'._EDIT.'"><i class="fas fa-pencil-alt fa-lg"></i></a> ';
			if ($i->level > 2) 
			{
				$actions .= ' <a class="btl" href="'.BASE_URL.'categories/set/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="far fa-lightbulb fa-lg '.$on_status.'"></i></a>';
				
				if ($i->level == 4)
				{
					$delete = '<a class="btl" href="'.BASE_URL.'categories/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><i class="fas fa-'.$lock_status.' fa-lg"></i></a>
						 <a class="bta" href="'.BASE_URL.'categories/delete/'.$i->id.'" title="'._DELETE.'"><i class="fas fa-trash fa-lg red"></i></a>';
				}
			}
		}
		
		echo '<tr>
				<td><a class="btm" href="'.BASE_URL.'articles/index/'.$i->id_area.'/'.$i->lang.'/category_order/'.$i->name.'" title="'._VIEW_ARTICLES.'">'.$i->title.'</a></td>
				<td>'.$actions.'</td>
				<td class="aright">'.$delete.'</td>
				</tr>';
	}
	
	echo '</table>';
}
else 
{
	echo '<p>'._NO_ITEMS.'</p>';
}
?>
<script>
window.addEvent('domready', function() 
{
	X3.content('filters', 'categories/filter/<?php echo $id_area.'/'.$lang.'/'.$tag ?>', '<?php echo X4Utils_helper::navbar($navbar, ' . ', false) ?>');
	buttonize('topic', 'btm', 'topic');
	buttonize('topic', 'bta', 'modal');
	actionize('topic', 'btl', 'topic', escape('categories/index/<?php echo $id_area.'/'.$lang.'/'.$tag ?>/0'));
	zebraTable('zebra');
	linking('ul.inline-list a');
});
</script>
