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
		$on = ($i->code == $page->lang) ? 'class="on"' : '';
		echo '<li><a '.$on.' href="'.BASE_URL.'pages/index/'.$page->id_area.'/'.$i->code.'/home/1" title="'._SWITCH_LANGUAGE.'">'.ucfirst($i->language).'</a></li>';
	}
	echo '</ul></div>';
}

// area switcher
echo '<div class="aright sbox"><ul class="inline-list">';
foreach($areas as $i) 
{
	$on = ($i->id == $id_area) ? 'class="on"' : '';
	echo '<li><a '.$on.' href="'.BASE_URL.'pages/index/'.$i->id.'/'.$lang.'/home/1" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a></li>';
}
echo '</ul></div>';

// to avoid slash problems
$xfrom = str_replace('/', '§', $page->url);

?>
<h1><?php echo _PAGE_LIST.' \''.$area.'\''._TRAIT_._LANGUAGE.' \''.$lang ?>'</h1>
<p><?php echo _MENU_AND_ORDER ?></p>
<?php

// do not delete pages
$no_del = array('home', 'msg', 'search', 'logout', 'offline');

// do not move pages
$no_menu = array('home', 'msg', 'search', 'offline', 'x3admin');

if (isset($page->url) && $page->url != 'home') 
{
	// parent page
	$parent = str_replace('/', '§', $page->xfrom);
	echo '<p><a class="btm" href="'.BASE_URL.'pages/index/'.$page->id_area.'/'.$page->lang.'/'.$parent.'/1" title="'._GO_BACK.'"><i class="fas fa-arrow-left lg"></i> '.stripslashes($page->name).'</a></p>';
}

// menu arrangement
$m = array();
if ($page->url == 'home') 
{
	foreach($menus as $i) 
	{
		$m[$i->id] = $i->description;
	}
}
else 
{
	foreach($menus as $i) 
	{
		if ($i->id == $page->id_menu) $m[$i->id] = $i->description;
	}
}

if (!empty($pages)) 
{
	
	// ids item
	$ids = array();
	// sortable item
	$sort = array();
	
	echo '<form id="menu_manager">
			<input type="hidden" name="id_area" id="id_area" value="'.$page->id_area.'" />
			<input type="hidden" name="lang" id="lang" value="'.$page->lang.'" />
			<input type="hidden" name="pagefrom" id="pagefrom" value="'.$page->url.'" />
		<div class="titlebar">'.ucfirst(_PAGES).'</div><ul class="nomargin zebra min-height" id="m0">';
	$sort[0] = '#m0';
	$memo = $c = 0;
	foreach($pages as $i)
	{
		if ($i->url != 'x3admin') {
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
			if (($i->level > 1 && $i->xlock == 0) || $i->level == 4) {
				$actions = (ADVANCED_EDITING) ?
					'<a class="btm" href="'.BASE_URL.'sections/compose/'.$i->id.'" title="'._EDIT.'"><i class="fas fa-pencil-alt fa-lg"></i></a>' :
					'<a class="btm2" href="'.BASE_URL.'articles/edit/'.$i->id_area.'/'.$i->lang.'/1/0/'.$i->id.'" title="'._EDIT.'"><i class="fas fa-pencil-alt fa-lg"></i></a>';
				
				// manager user
				if ($i->level > 2) 
				{
					$actions .= (in_array($i->url, $no_del)) 
						? '<a><i class="far fa-lightbulb fa-lg invisible"></i></a>' 
						: '<a class="btl" href="'.BASE_URL.'pages/set/xon/'.$i->id.'/'.(($i->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="far fa-lightbulb fa-lg '.$on_status.'"></i></a>';
						
					$actions .= ' <a class="bta" href="'.BASE_URL.'pages/seo/'.$i->id.'" title="'._SEO_TOOLS.'"><i class="fas fa-cogs fa-lg"></i></a>' ;
					
					// admin user
					if ($i->level == 4) 
					{
						$delete = '<a class="btl" href="'.BASE_URL.'pages/set/xlock/'.$i->id.'/'.(($i->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><i class="fas fa-'.$lock_status.' fa-lg"></i></a>';
						
						if (!in_array($i->url, $no_del)) 
						{
							$delete .= ' <a class="bta" href="'.BASE_URL.'pages/delete/'.$i->id.'" title="'._DELETE.'"><i class="fas fa-trash fa-lg red"></i></a>';
						}
						else
						{
							$delete .= ' <a><i class="fas fa-trash fa-lg invisible"></i></a>';
						}
					}
				}
			}
			
			// menus
			if ($memo != $i->id_menu && isset($m[$i->id_menu])) 
			{
				// check if is the next menu
				foreach($m as $k => $v)
				{
					echo '</ul>
						<input type="hidden" name="sort'.$c.'" id="sort'.$c.'" value="'.implode(', ', $ids).'" />
						<div class="titlebar">'._MENU.': '.stripslashes($v).'</div><ul class="nomargin zebra min-height" id="m'.$k.'">';
					$c++;
					$ids = array();
					$sort[$c] = '#m'.$k;
					unset($m[$k]);
					if ($k == $i->id_menu) 
					{
						break;
					}
				}
			}
			// add the id
			$ids[] = $i->id;
			
			$inmenu = (!in_array($i->url, $no_menu)) 
				? 'class="items" id="'.$i->id.'"' 
				: '';
				
			echo '<li '.$inmenu.'><table><tr>
					<td><a class="btm" href="'.BASE_URL.'pages/index/'.$i->id_area.'/'.$i->lang.'/'.str_replace('/', '§', $i->url).'/1" title="'._SUBPAGES.'">'.stripslashes($i->name).'</a></td>
					<td class="aright" style="width:12em;">'.$actions.$delete.'</td>
					</tr></table></li>';
		}
	}
	
	// empty menus
	foreach($m as $k => $v)
	{
		echo '</ul>
			<input type="hidden" name="sort'.$c.'" id="sort'.$c.'" value="'.implode(', ', $ids).'" />
			<div class="titlebar">'._MENU.': '.stripslashes($v).'</div><ul class="nomargin min-height" id="m'.$k.'">';
		$c++;
		$ids = array();
		$sort[$c] = '#m'.$k;
		unset($m[$k]);
	}
	
	// close
	echo '</ul><input type="hidden" name="sort'.$c.'" id="sort'.$c.'" value="'.implode(', ', $ids).'" />';
	
?>
<p>&nbsp;</p>
<script src="<?php echo THEME_URL ?>js/basic.js"></script>
<script>
window.addEvent('domready', function() {
	X3.content('filters', 'pages/filter/<?php echo $id_area.'/'.$lang.'/'.$xfrom ?>', '<?php echo X4Utils_helper::navbar($navbar, ' . ', false) ?>');
	buttonize('topic', 'btm', 'topic');
	buttonize('topic', 'btm2', 'topic', '<?php echo $referer ?>');
	buttonize('topic', 'bta', 'modal');
	buttonize('topic', 'btal', 'topic');
	actionize('topic',  'btl', 'topic', escape('pages/index/<?php echo $id_area.'/'.$lang.'/'.$xfrom ?>'));
	linking('ul.inline-list a');
	zebraUl('zebra');
	
//get data
var id_area = $('id_area').get('value');
var lang = $('lang').get('value');
var xfrom = $('pagefrom').get('value');

<?php
echo 'var sortableListsArray = $$("'.implode(', ', $sort).'"),
		sortableLists = new Sortables(sortableListsArray, {
		//creates a clone to follow my cursor when i drag
		clone: true,
		//defines the class of the drag handle
		handle: ".handle",
		//will let you create an effect for the
		//item returning to list after drag
		revert: {
			//accepts Fx options
			duration: 50
		},
		//determines opacity of list element, not drag clone
		opacity: .5,
	 
		onStart: function(el){
			//passes element you are dragging
			el.highlight("#aacc00");
		},
		onComplete: function(el) {
			var id = el.get("id");
			// menu has changed
			var holder = el.getParent("ul").get("id");
			var tmp = "";
			';
		
foreach($sort as $k => $v) 
{
	echo 'var sect'.$k.' = sortableLists.serialize('.($k).');
		if ($("sort'.$k.'") != null) {
			$("sort'.$k.'").set("value", sect'.$k.');
		}
		tmp = tmp + "'.substr($v, 2).'-" + sect'.$k.' + \'_\';';
}
	echo 'var req = new Request.HTML({
			url: root+"menus/menu/"+id+"/"+holder+"/"+tmp,
					method:"get"
			}).send();
		}
	});';
?>
});
</script>

<?php
}
else 
{
	if (!isset($page->url) || $page->url == 'home') 
		echo '<p>'._NO_SUBPAGES._TRAIT_.'<a class="btl" href="'.BASE_URL.'pages/init/'.$id_area.'/'.$lang.'" title="'._INIZIALIZE_AREA.'">'._INIZIALIZE_AREA.'</a></p>';
	else
		echo '<p>'._NO_SUBPAGES.'</p>';
	
	echo '
<script>
window.addEvent("domready", function() {
	X3.content("filters", "pages/filter/'.$id_area.'/'.$lang.'/'.$page->url.'", "'.addslashes(X4Utils_helper::navbar($navbar, ' . ')).'");
	linking("ul.inline-list a");
	buttonize("topic", "btm", "topic");
	actionize("topic",  "btl", "topic", escape("pages/index/'.$id_area.'/'.$lang.'"));
});
</script>';
}
?>
</div>

