<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */
?>
<h2><?php echo _FILE_TREE ?></h2>
<?php
if ($items)
{
	echo '<div id="tree">';
	$aopen = $copen = false;
	$a = $cat = '';

	foreach ($items as $i)
	{
		if ($a != $i->id)
		{
			$a = $i->id;
			$cat = '';

			if ($copen)
			{
				echo '</div>';
				$copen = false;
			}

			if ($aopen)
			{
				echo '</div>';
				$aopen = false;
			}
			echo '<span class="rtoggle">'.$i->title._TRAIT_.$i->description.'</span><div class="relement">';
			$aopen = true;

		}

		if ($cat != $i->category) {
			$cat = $i->category;
			if ($copen) {
				echo '</div>';
				$copen = false;
			}
			echo '<span class="rtoggle">'.$i->category.'</span><div class="relement">';
			$copen = true;
		}
		echo '<p><a href="'.BASE_URL.'files/index/'.$i->id.'/'.$i->category.'/'.$i->subcategory.'" title="">'.$i->subcategory.'</a></p>';
	}

	if ($copen) echo '</div>';
	if ($aopen) echo '</div>';
	echo '</div>';
}
else
{
	echo '<p>'._NO_ITEMS.'</p>';
}

echo '<script src="'.THEME_URL.'js/basic.js"></script>';
