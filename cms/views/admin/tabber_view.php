<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// tabber view

echo '<div id="'.$tabber_name.'">';

if (isset($title))
{
    echo '<h1>'.$title.'</h1>';
}

if ($tabs)
{
	echo '<div class="tabs"><ul class="clearfix">';
	if (isset($tkeys))
	{
		// array of objects
		foreach ($tabs as $i)
		{
			$url = $tkeys[2];
			$class = ($i->$url == $tkeys[3])
				? ' class="on"'
				: '';

			$id = (isset($tkeys[4]))
				? 'id="'.$tkeys[4].'"'
				: '';

			$url = $tkeys[1];
			$title = $tkeys[0];
			echo '<li '.$id.' '.$class.'><a class="btt" href="'.BASE_URL.$i->$url.'" title="'.$i->$title.'">'.$i->$title.'</a></li>';
		}
	}
	else
	{
		// simple array
		foreach ($tabs as $k => $v)
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
	tabberize("'.$tabber_name.'", "btt", "'.$tabber_container.'");
});
</script>';
}
