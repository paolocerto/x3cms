<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

?>
<div id="tabber">
	<h1><?php echo $title ?></h1>
<?php 
if ($tabs)
{
	echo '<div class="tabs"><ul class="clearfix">';
	if (isset($tkeys))
	{
		// array of objects
		foreach($tabs as $i)
		{
			$class = ($i->$tkeys[2] == $tkeys[3])
				? ' class="on"'
				: '';
			
			$id = (isset($tkeys[4]))
				? 'id="'.$tkeys[4].'"'
				: '';
			
			echo '<li '.$id.' '.$class.'><a class="btt" href="'.BASE_URL.$i->$tkeys[1].'" title="'.$i->$tkeys[0].'">'.$i->$tkeys[0].'</a></li>';
		}
	}
	else
	{
		// simple array
		foreach($tabs as $k => $v)
		{
			$class = ($k == $on)
				? ' class="on"'
				: '';
			
			$id = (isset($v[2]))
				? 'id="'.$v[2].'"'
				: '';
			
			echo '<li '.$id.' '.$class.'><a class="btt" href="'.$v[1].'" title="'.$v[0].'">'.$v[0].'</a></li>';
		}
	}
	echo '</ul></div>';
}
?>
	<div id="tdown">
		<?php echo $down ?>
	</div>
</div>
<?php
if (isset($tabber_container))
{
	echo '
<script>
window.addEvent("domready", function()
{
	tabberize("btt", "'.$tabber_container.'");
});
</script>';
}
