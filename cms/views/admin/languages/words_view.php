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
		$on = ($i->code == $lang) ? 'on' : '';
		echo '<li><a class="btm '.$on.'" href="'.BASE_URL.'dictionary/keys/'.$i->code.'/'.$area.'/'.$what.'" title="'._SWITCH_LANGUAGE.'">'.ucfirst($i->language).'</a></li>';
	}
	echo '</ul></div>';
}

// area switcher
echo '<div class="aright sbox"><ul class="inline-list">';
foreach($areas as $i) 
{
	$on = ($i->name == $area) ? 'on' : '';
	echo '<li><a class="btm '.$on.'" href="'.BASE_URL.'dictionary/keys/'.$lang.'/'.$i->name.'/'.$what.'" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a></li>';
}
echo '</ul></div>';

// keys switcher
if ($keys)
{
	echo '<div class="aright sbox"><ul class="inline-list">';
	foreach($keys as $i) 
	{
		$on = ($i->what == $what) ? 'on' : '';
		echo '<li><a class="btm '.$on.'" href="'.BASE_URL.'dictionary/keys/'.$lang.'/'.$area.'/'.$i->what.'" title="'._SWITCH_AREA.'">'.ucfirst($i->what).'</a></li>';
	}
	echo '</ul></div>';
}
?>
<h2><?php echo _WORDS_LIST.': '.$lang.'/'.$area.'/'.$what ?></h2>
<?php
if ($items) 
{
	echo '<table class="zebra">
			<tr class="first">
				<th style="width:20em;">'._KEY.'/'._WORD.'</th>
				<th></th>
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
		
		// check permission
		if (($i->level > 1 && $i->xlock == 0) || $i->level == 4) 
		{
			$actions = '<a class="bta" href="'.BASE_URL.'dictionary/edit/'.$i->id.'" title="'._EDIT.'"><i class="fa fa-pencil fa-lg"></i></a>';
			
			// manager or admin user
			if ($i->level > 2 || $i->level == 4) 
			{
				$actions .= ' <a class="btl" href="'.BASE_URL.'dictionary/set/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="fa fa-lightbulb-o fa-lg '.$on_status.'"></i></a>';
			}
			// admin user
			if ($i->level == 4)
			{
				$delete = '<a class="btl" href="'.BASE_URL.'dictionary/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><i class="fa fa-'.$lock_status.' fa-lg"></i></a> 
					<a class="bta" href="'.BASE_URL.'dictionary/delete/'.$i->id.'" title="'._STATUS.'"><i class="fa fa-trash fa-lg red"></i></a>';
			}
		}
		
		echo '<tr>
			<td>'.$i->xkey.'</td>
			<td>'.$i->xval.'</td>
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
	X3.content('filters','dictionary/filter/<?php echo $lang.'/'.$area.'/'.$what ?>', null);
	buttonize('topic', 'btm', 'tdown');
	buttonize('topic', 'bta', 'modal');
	actionize('topic',  'btl', 'tdown', escape('dictionary/keys/<?php echo $lang.'/'.$area.'/'.$what ?>'));
	zebraTable('zebra');
	linking('ul.inline-list a', 'tdown');
});
</script>
