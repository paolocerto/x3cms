<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */


// close button
echo '<div id="close-modal" title="'._CLOSE.'"><i class="fa fa-times fa-lg"></i></div>';

?>
<h2><?php echo _AREA_LANG_MAP ?></h2>
<div class="small">
<?php

$len = 0;
$openul = $openli = 1;
foreach($map as $i)
{
	$ilen = strlen($i->ordinal)/4;
	$class = '';
	if ($ilen > $len) 
	{
		// change subpages
		echo '<ul>';
		$openul++;
	}
	elseif ($ilen < $len) 
	{
		// change subpages
		$n = $len - $ilen;
		for ($l = 0; $l < $n; $l++) 
		{
			echo '</li>';
			$openli--;
			echo '</ul></li>';
			$openul--;
			$openli--;
		}
	}
	else 
	{
		// normal subpage
		if ($i->ordinal == 'A') 
			echo '<ul>';
		else 
		{
			echo '</li>';
			$openli--;
		}
	}
	// menus
	if ($ilen == 2 && $i->id_menu) 
		$class = 'class="map"';
	
	$len = $ilen;
	$description = stripslashes($i->description);
	echo '<li '.$class.'><a class="btm" href="'.BASE_URL.'pages/index/'.$area->id.'/'.$i->lang.'/'.str_replace('/', '$', $i->xfrom).'" title="'.$description.'">'.stripslashes($i->name).'</a>'._TRAIT_.$description;
	$openli++;
}

while ($openli > 0) 
{
	echo '</li>';
	$openli--;
	if ($openul) 
	{
		echo '</ul>';
		$openul--;
	}
}

?>
</div>
<script>
window.addEvent('domready', function()
{
	buttonize('simple-modal', 'btm', 'topic');
});
</script>
