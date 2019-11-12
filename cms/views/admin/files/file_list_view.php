<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// area switcher
echo '<div class="aright sbox"><ul class="inline-list">';
foreach($areas as $i) 
{
	$on = ($i->id == $id_area) ? 'class="on"' : '';
	echo '<li><a '.$on.' href="'.BASE_URL.'files/index/'.$i->id.'/'.urlencode($category).'/'.urlencode($subcategory).'" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a></li>';
}
echo '</ul></div>';

if (!empty($items[0])) 
{
	// type switcher
	echo '<div class="aright sbox"><ul class="inline-list">';
	foreach($types as $i) 
	{
		$on = ($i->value == $xtype) ? 'class="on"' : '';
		echo '<li><a '.$on.' href="'.BASE_URL.'files/index/'.$id_area.'/'.urlencode($category).'/'.urlencode($subcategory).'/'.$i->value.'/" title="'._SWITCH_TYPE.'">'.ucfirst($i->option).'</a></li>';
	}
	echo '</ul></div>';
}

// category switcher
if (!empty($categories))
{
	echo '<div class="aright sbox"><ul class="inline-list">';
	
	$on = (empty($category)) ? 'class="on"' : '';
	echo '<li><a '.$on.' href="'.BASE_URL.'files/index/'.$id_area.'/" title="'._SWITCH_CATEGORY.'">'.ucfirst(_UNCATEGORIZED).'</a></li>';
	foreach($categories as $i) 
	{
		$on = ($i->ctg == $category) ? 'class="on"' : '';
		echo '<li><a '.$on.' href="'.BASE_URL.'files/index/'.$id_area.'/'.$i->ctg.'" title="'._SWITCH_CATEGORY.'">'.ucfirst($i->ctg).'</a></li>';
	}
	echo '</ul></div>';
	
	if (!empty($category))
	{
		echo '<div class="aright sbox"><ul class="inline-list">';
		foreach($subcategories as $i) 
		{
			$on = ($i->sctg == $subcategory) ? 'class="on"' : '';
			echo '<li><a '.$on.' href="'.BASE_URL.'files/index/'.$id_area.'/'.$category.'/'.$i->sctg.'" title="'._SWITCH_SUBCATEGORY.'">'.ucfirst($i->sctg).'</a></li>';
		}
		echo '</ul></div>';
	}
} 
?>
<h1><?php echo _FILE_LIST ?></h1>
<form name="bulk_action" id="bulk_action" onsubmit="return false;" method="post" action="<?php echo BASE_URL.'files/bulk/'.$id_area.'/'.urlencode($category).'/'.($subcategory).'/'.$xtype ?>">
<input type="hidden" name="action" value="delete" />
<?php
if (!empty($items[0])) 
{
	echo '<div class="band clearfix">';
		
	$what = array('img', 'files', 'media', 'template');
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
				$actions = '<a class="bta" href="'.BASE_URL.'files/edit/'.$i->id.'" title="'._EDIT.'"><i class="fas fa-pencil-alt fa-lg"></i></a>';
				
				// manager or admin user
				if ($i->level > 2)
				{
					$actions .= ' <a class="btl" href="'.BASE_URL.'files/set/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="far fa-lightbulb fa-lg '.$on_status.'"></i></a>';
				}
				// filter
				$actions .= ' <a class="btm" href="'.BASE_URL.'files/index/'.$id_area.'/'.$i->category.'/'.$i->subcategory.'" title="'._FILE_FILTER.'"><i class="fas fa-filter fa-lg"></i></a>';
				
				// admin user
				if ($i->level == 4)
				{
					$delete = '<a class="btl" href="'.BASE_URL.'files/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><i class="fas fa-'.$lock_status.' fa-lg"></i></a> 
					<a class="bta" href="'.BASE_URL.'files/delete/'.$i->id.'" title="'._DELETE.'"><i class="fas fa-trash fa-lg red"></i></a>';
				}
		}
		
		// file info
		if ($i->xtype) 
		{
			// no image
			$thumb = $size = '';
		}
		else
		{
			// cache fix
			$now = '?t='.time();
			
			// image thumbs
			if (file_exists(APATH.'files/thumbs/img/'.$i->name))
			{
				$thumb = '<img src="'.ROOT.'cms/files/thumbs/img/'.$i->name.$now.'" alt="'.$i->alt.'" />';
			}
			else
			{
				$thumb = '<img src="'.FPATH.'img/'.$i->name.$now.'" alt="'.$i->alt.'" />';
			}
			
			// image size
			$size = (file_exists($file_path.'img/'.$i->name)) 
				? @implode('x', @array_slice(@getimagesize($file_path.'img/'.$i->name), 0, 2)).' pixel'._TRAIT_ 
				: '';
		}
		
		// file size
		$kb = @filesize($file_path.$what[$i->xtype].'/'.$i->name);
		$filesize = ($kb) 
			? '('.floor($kb/1024).' KB)' 
			: '';
		
		echo '<div class="one-fifth md-one-fourth sm-one-third xs-one-whole clearfix pad-right pad-bottom">
				<div class="widget dtable">
					<div class="dtable-cell">
						<div class="wbox filebox">
							<a href="'.FPATH.$what[$i->xtype].'/'.$i->name.'" title="'.$i->alt.'">'.$i->name.'</a><br /> 
							<span class="small">'.$size.$filesize.'</span><br />
							<div class="acenter pad-top">'.$thumb.'</div>
						</div>
					</div>
					<div class="dtable-cell sidebar">'.$actions.$delete.'<input type="checkbox" class="bulkable" name="bulk[]" value="'.$i->id.'" /></div>
				</div>
			</div>';
	}
	
	echo '</div></form>';
	
	
	// pagination
	echo '<div id="file_pager" class="pager">'.X4Pagination_helper::pager(BASE_URL.'files/index/'.$id_area.'/'.urlencode($category).'/'.urlencode($subcategory).'/'.$xtype.'/', $items[1], 5, false, '', 'btp').'</div>';
}
else 
{
	echo '<p>'._NO_ITEMS.'</p>';
}
?>
<script>
window.addEvent('domready', function()
{
	X3.content('filters','files/filter/<?php echo $id_area.'/'.urlencode($category).'/'.($subcategory).'/'.$str ?>', '<?php echo X4Utils_helper::navbar($navbar, ' . ', false) ?>');
	buttonize('file_pager', 'btp', 'topic');
	buttonize('topic', 'btm', 'topic');
	buttonize('topic', 'bta', 'modal');
	actionize('topic',  'btl', 'topic', escape('files/index/<?php echo $id_area.'/'.urlencode($category).'/'.urlencode($subcategory).'/'.$xtype ?>'));
	linking('ul.inline-list a');
	blanking();
});
</script>
