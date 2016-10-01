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

<p><?php echo _HELP_MSG ?></p>

<?php

if ($items)
{
	
	echo '<ul class="zebra">';
	foreach($items as $i)
	{
		echo '<li><a class="btm" href="'.BASE_URL.$i->url.'" title="">'.$i->title.'</a>'._TRAIT_.$i->description.'</li>';
	}
	echo '</ul>';
}
?>
<script>
window.addEvent('domready', function()
{
	X3.content('filters','help/filter/<?php echo $page->lang ?>', null);
	buttonize('topic', 'btm', 'tdown');
	zebraUl('zebra');
});
</script>

