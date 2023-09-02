<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// ol list

echo '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">';
if ($items)
{
	echo '<ol>';
	foreach ($items as $i)
	{
		if ($i->level)
		{
			$item = (isset($link))
				? '<a '.$class.' @click="$dispatch(\'pager\', \''.$link.'/'.$i->id.'\');modal=false" title="'.$i->$title.'">'.$i->$value.'</a>'
				: $i->$value;

			echo '<li>'.$item.'</li>';
		}
	}
	echo '</ol>';
}
else
{
	echo '<p>'._NO_ITEMS.'</p>';
}
echo '</div>';
