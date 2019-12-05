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
		$on = ($i->code == $lang) ? 'class="on"' : '';
		echo '<li><a '.$on.' href="'.BASE_URL.'dictionary/keys/'.$i->code.'/'.$area.'" title="'._SWITCH_LANGUAGE.'">'.ucfirst($i->language).'</a></li>';
	}
	echo '</ul></div>';
}

// area switcher
echo '<div class="aright sbox"><ul class="inline-list">';
foreach($areas as $i) 
{
	$on = ($i->name == $area) ? 'class="on"' : '';
	echo '<li><a '.$on.' href="'.BASE_URL.'dictionary/keys/'.$lang.'/'.$i->name.'" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a></li>';
}
echo '</ul></div>';
?>
<h2><?php echo _SECTIONS_LIST.': '.$lang.'/'.$area ?></h2>
<table class="zebra">
	<tr>
		<th class="first"><?php echo _SECTION ?></th>
	</tr>
<?php
foreach($keys as $i)
{
	echo '<tr><td><a class="btm" href="'.BASE_URL.'dictionary/words/'.$lang.'/'.$area.'/'.$i->what.'" title="'._SHOW_WORDS.'">'.$i->what.'</a> </td></tr>';
}
?>	
</table>
<script src="<?php echo THEME_URL ?>js/basic.js"></script>
<script>
window.addEvent('domready', function()
{
	X3.content('filters','dictionary/filter/<?php echo $lang.'/'.$area ?>', null);
	buttonize('topic', 'btm', 'tdown');
	zebraTable('zebra');
	linking('ul.inline-list a', 'tdown');
});
</script>
