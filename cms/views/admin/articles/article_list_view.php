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
if (MULTILANGUAGE) {
	echo '<div class="aright sbox"><ul class="inline-list">';
	foreach($langs as $i) 
	{
		$on = ($i->code == $lang) ? ' on' : '';
		echo '<li><a class="btm'.$on.'" href="'.BASE_URL.'articles/index/'.$id_area.'/'.$i->code.'/'.$xcase.'/'.$id_what.'" title="'._SWITCH_LANGUAGE.'">'.ucfirst($i->language).'</a></li>';
	}
	echo '</ul></div>';
}

// area switcher
echo '<div class="aright sbox"><ul class="inline-list">';
foreach($areas as $i) 
{
	$on = ($i->id == $id_area) ? ' on' : '';
	echo '<li><a class="btm'.$on.'" href="'.BASE_URL.'articles/index/'.$i->id.'/'.$lang.'/'.$xcase.'/'.$id_what.'" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a></li>';
}
echo '</ul></div>';

// category switcher
if (isset($categories) && $categories)
{
	echo '<div class="aright sbox"><ul class="inline-list">';
	foreach($categories as $i) 
	{
		$on = ($i->name == $id_what) ? ' on' : '';
		echo '<li><a class="btm'.$on.'" href="'.BASE_URL.'articles/index/'.$id_area.'/'.$lang.'/'.$xcase.'/'.$i->name.'" title="'._SWITCH_CATEGORY.'">'.ucfirst($i->description).'</a></li>';
	}
	echo '</ul></div>';
}

// context switcher
if (isset($contexts) && $contexts)
{
	echo '<div class="aright sbox"><ul class="inline-list">';
	foreach($contexts as $i) 
	{
		$on = ($i->code == $id_what) ? ' on' : '';
		echo '<li><a class="btm'.$on.'" href="'.BASE_URL.'articles/index/'.$id_area.'/'.$lang.'/'.$xcase.'/'.$i->code.'" title="'._SWITCH_CONTEXT.'">'.ucfirst($i->name).'</a></li>';
	}
	echo '</ul></div>';
}

// author switcher
if (isset($authors) && $authors) 
{
	echo '<div class="aright sbox"><ul class="inline-list">';
	foreach($authors as $i) 
	{
		$on = ($i->id_editor == $id_what) ? ' on' : '';
		echo '<li><a class="btm'.$on.'" href="'.BASE_URL.'articles/index/'.$id_area.'/'.$lang.'/'.$xcase.'/'.$i->id_editor.'" title="'._SWITCH_AUTHOR.'">'.ucfirst($i->author).'</a></li>';
	}
	echo '</ul></div>';
}

// key switcher
if (isset($keys) && $keys) 
{
	echo '<div class="aright sbox"><ul class="inline-list">';
	foreach($keys as $i) 
	{
		$on = ($i->xkeys == $id_what) ? ' on' : '';
		echo '<li><a class="btm'.$on.'" href="'.BASE_URL.'articles/index/'.$id_area.'/'.$lang.'/'.$xcase.'/'.$i->xkeys.'" title="'._SWITCH_KEY.'">'.ucfirst($i->xkeys).'</a></li>';
	}
	echo '</ul></div>';
}

// page name
$title = (isset($page))
	? _TRAIT_.stripslashes($page->name)
	: '';
?>
<h1><?php echo _ARTICLE_LIST.$title ?></h1>
<div class="tabs">
	<ul class="clearfix">
<?php
// articles tabs
foreach ($cases as $k => $v)
{
	// if active
	$on = ($k == $xcase) 
		? 'class="on"' 
		: '';
	$label = constant('_'.strtoupper($k));
	echo '<li '.$on.'><a class="'.$v[1].'" href="'.BASE_URL.$v[0].'/'.$id_area.'/'.$lang.'/'.$k.'" title="'.$label.'">'.$label.'</a></li>';
}
?>
	</ul>
</div>

<?php 
// use pagination
if ($items[0]) {
?>
<table class="zebra">
	<tr class="first">
		<th style="width:8em;"></th>
		<th><?php echo _ARTICLES ?></th>
		<th style="width:10em;"><?php echo _ACTIONS ?></th>
		<th style="width:6em;"></th>
	</tr>
<?php
	foreach($items[0] as $i)
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
			// edit
			$actions = '<a class="btm" href="'.BASE_URL.'articles/edit/'.$i->id_area.'/'.$i->lang.'/'.$i->code_context.'/'.$i->bid.'" title="'._EDIT.'"><i class="fas fa-pencil-alt fa-lg"></i></a> ';
			// duplicate
			$actions .= '<a class="btm" href="'.BASE_URL.'articles/edit/'.$i->id_area.'/'.$i->lang.'/'.$i->code_context.'/'.$i->bid.'/0/1" title="'._DUPLICATE.'"><i class="fas fa-files-o fa-lg"></i></a> ';
			
			if ($i->level > 2) 
			{
				$actions .= ' <a class="btl" href="'.BASE_URL.'articles/set_by_bid/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="far fa-lightbulb fa-lg '.$on_status.'"></i></a>';
				
				if ($i->level == 4)
				{
					$delete = '<a class="btl" href="'.BASE_URL.'articles/set_by_bid/xlock/'.$i->id.'/'.(($i->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><i class="fas fa-'.$lock_status.' fa-lg"></i></a>
							 <a class="bta" href="'.BASE_URL.'articles/delete/'.$i->bid.'" title="'._DELETE.'"><i class="fas fa-trash fa-lg red"></i></a>';
				}
			}
		}
		
		$link = (ADVANCED_EDITING) 
			? '<a class="btm" href="'.BASE_URL.'sections/compose/'.$i->id_page.'">'.$i->page.'</a>' 
			: '<strong>'.$i->page.'</strong>';
		
		echo '<tr>
				<td>'.date('Y-m-d', $i->date_in).'<span class="dblock xsmall">'.$i->author.'</span></td>
				<td><strong>'.$i->name.'</strong><span class="dblock small">'.$i->context.' '.$link.'</span></td>
				<td>'.$actions.' <a class="btm" href="'.BASE_URL.'articles/history/'.$id_area.'/'.$lang.'/'.$i->bid.'" title="'._ARTICLE_HISTORY.'"><i class="fas fa-history fa-lg"></i></a></td>
				<td class="aright">'.$delete.'</td>
				</tr>';
	}
	echo '</table>';
	
	// pagination
	echo '<div id="article_pager" class="pager">'.X4Pagination_helper::pager(BASE_URL.'articles/index/'.$id_area.'/'.$lang.'/'.$xcase.'/'.$id_what.'/', $items[1], 5, false, '/'.$str, 'btp').'</div>'; 
}
else 
{
	echo '<p>'._NO_ITEMS.'</p>';
}
?>
<script>
window.addEvent('domready', function() 
{
	X3.content('filters', 'articles/filter/<?php echo $id_area.'/'.$lang.'/'.$xcase.'/'.$id_what.'/'.$str ?>', '<?php echo X4Utils_helper::navbar($navbar, ' . ', false) ?>');
	buttonize('article_pager', 'btp', 'topic');
	buttonize('topic', 'btm', 'topic');
	buttonize('topic', 'bta', 'modal');
	actionize('topic', 'btl', 'topic', escape('articles/index/<?php echo $id_area.'/'.$lang.'/'.$xcase.'/0/'.$pp ?>/0'));
	zebraTable('zebra');
});
</script>

