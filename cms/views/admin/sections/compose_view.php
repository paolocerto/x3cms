<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// draggable item
$drag = array();
// sortable item
$sort = array();

// SECTIONS
$ltmp = $rtmp = '';
$published = array();
$artts = array();

foreach($sections as $k => $v)
{
	$sorter = array();
	$ltmp .= '<h4>'._SECTION.' '.$k.'</h4>';
	$sort[$k] = '#section'.$k;
	$ltmp .= '<ul id="section'.$k.'" class="cdroppable">';
	$artts[$k] = '';
	
	if (isset($v->articles) && !empty($v->articles)) 
	{
		$artt = $mod->get_articles($pagetoedit->id_area, $pagetoedit->lang, $v->articles);
		
		// if there are articles
		if ($artt) 
		{
			foreach($artt as $ii)
			{
				$published[] = $ii->bid;
				$m = (empty($ii->module)) 
					? _TRAIT_ 
					: $ii->module;
				
				$p = (empty($ii->param)) 
					? _TRAIT_ 
					: $ii->param;
					
				$ltmp .= '<li class="listitem" id="'.$ii->bid.'">
					<div class="sbox"><b>'.stripslashes($ii->name).'</b>'._TRAIT_.'<a class="bta" href="'.BASE_URL.'articles/edit/'.$pagetoedit->id_area.'/'.$pagetoedit->lang.'/'.$ii->code_context.'/'.$ii->bid.'" title="'._EDIT.'">'._EDIT.'</a></div>
					'.stripslashes($ii->content).'
					<div class="tbox">'._MODULE.': '.$m.'&nbsp;&nbsp;|&nbsp;&nbsp;'._PARAM.': '.$p.'</div>
					</li>';
			}
		}
		$artts[$k] = $v->articles;
	}
	$ltmp .= '</ul>
		<input type="hidden" name="sort'.$k.'" id="sort'.$k.'" value="'.$artts[$k].'"  />';
}

// ARTICLES
foreach($codes as $i)
{
	$const = '_CONTEXT_'.strtoupper($i->xkey);
	
	$context = (defined($const)) 
		? constant($const) 
		: $dict->get_word($const, 'articles', $pagetoedit->lang);
		
	$rtmp .= '<h4 class="context">'.$context.'</h4><ul id="'.$i->xkey.'" class="section cartts">';
	$drag[] = '#'.$i->xkey;
	foreach($articles as $ii) 
	{
		if($ii->code_context == $i->code) 
		{
			$a = array_shift($articles);
			// only if no published
			if (!in_array($a->bid, $published)) 
				$rtmp .= '<li class="listitem" id="'.$a->bid.'"><strong>'.stripslashes($a->name).'</strong></li>';
		}
	}
	$rtmp .= '<li class="listdrop">'._DROP_HERE.'</li></ul>';
}
?>

<div class="band inner-pad clearfix">

	<div class="two-third sm-one-half">
		<h1><span class="sm-hidden"><?php echo _COMPOSE_EDITOR.' </span><a class="bta" href="'.BASE_URL.'pages/index/'.$pagetoedit->id_area.'/'.$pagetoedit->lang.'/'.$pagetoedit->xfrom.'/1" title="">'.$pagetoedit->name.'</a>'._TRAIT_.$pagetoedit->area.'/'.$pagetoedit->lang ?></h1>
		<h2><?php echo _SECTIONS ?>&nbsp;<span id="alert_box" class="error"></span></h2>
		<p><?php echo _SECTIONS_MSG ?></p>
		
		<div id="droppable">
<?php 

	echo '<form id="compose" action="'.BASE_URL.'sections/compositing" method="post" onsubmit="return false">
			<input type="hidden" name="id_area" id="id_area" value="'.$pagetoedit->id_area.'" />
			<input type="hidden" name="lang" id="lang" value="'.$pagetoedit->lang.'" />
			<input type="hidden" name="id_page" id="id_page" value="'.$pagetoedit->id.'" />
			<input type="hidden" name="snum" id="snum" value="'.sizeof($sections).'" />';
		
	echo $ltmp.'<div class="buttons"><button id="esopmoc" type="button" onclick="setForm(\'compose\');">'._SUBMIT.'</button></div>
			</form>';
?>
		</div>
	</div>
	
	<div class="one-third sm-one-half fixed">
		<h2><?php echo _ARTICLES_LIST ?></h2>
		<p><?php echo _ARTICLES_MSG ?></p>
		<div id="accordion">
<?php 
	echo $rtmp;

if (!empty($layout))
{
	echo '<h4 class="context">'._SECTIONS.'</h4><div class="section acenter pad-top"><img src="'.$layout.'" alt="layout" /></div>';
}
?>
		</div>
	</div>
</div>

<script src="<?php echo THEME_URL ?>js/basic.js"></script>
<script>
window.addEvent('domready', function() {
	buttonize('topic', 'bta', 'topic', '<?php echo $referer ?>');
//get data
var id_area = $('id_area').get('value');
var lang = $('lang').get('value');
var id_page = $('id_page').get('value');
var modified = false;

X3.content('filters','sections/filter/<?php echo $pagetoedit->id_area.'/'.$pagetoedit->lang.'/'.$pagetoedit->id ?>', '<?php echo X4Utils_helper::navbar($navbar, ' . ', false) ?>');
new Fx.Accordion($('accordion'), '#accordion .context', '#accordion .section', {
			onActive: function(toggler) { toggler.addClass("active-accordion"); },
			onBackground: function(toggler) { toggler.removeClass("active-accordion");},
			display:1
		});

<?php

// create javascript for composition
echo 'var sortableListsArray = ["'.implode('", "', $sort).'", "'.implode('", "', $drag).'"];

var sortableLists = new Sortables(sortableListsArray, {
	clone: true,
	handle: ".handle",
	revert: {duration: 50},
	opacity: .5,
	
	onStart: function(el){
		//passes element you are dragging
		//el.highlight("#aacc00");
		var bid = el.get("id"),
			holder = el.getParent("ul").get("id"),
			req = new Request.HTML({
				url: root+"sections/get_title/"+id_area+"/"+lang+"/"+bid,
				method:"get",
				update: el
			}).send();
		el.setStyle("background", "none");
	},
	onComplete: function(el){
		var bid = el.get("id"),
			holder = el.getParent("ul").get("id");
			
		// replace content
		if (holder.contains("section")){
			var req = new Request.HTML({
				url: root+"sections/get_article/"+id_area+"/"+lang+"/"+id_page+"/"+bid,
				method:"get",
				async:false,
				update: el,
				onComplete: function(ele){
					buttonize(holder, "bta", "topic", "'.$referer.'");
				}
			}).send();
			el.setStyle("background", "white");
		} else {
			var req = new Request({
				url: root+"sections/recode_article/"+id_area+"/"+lang+"/"+holder+"/"+bid+"/"+id_page,
				method:"get"
			}).send();
		}
		
		if (!modified){
			modified = true;
			$("alert_box").set("html", "'._TRAIT_._UNSAVED_CHANGES.'");
		}
		
';
// on complete
foreach($sort as $k => $v) 
{
	echo 'var sect'.$k.' = sortableLists.serialize('.($k-1).');
	    $("sort'.$k.'").set("value", sect'.$k.');'.NL;
}
	echo '}
	});';
?>
});
</script>
