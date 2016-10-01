<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// ul list

if ($items)
{
	echo '<ul>';
	foreach($items as $i)
	{
		if ($i->level)
		{
			$item = (isset($link))
				? '<a '.$class.' href="'.$link.'/'.$i->id.'" title="'.$i->$title.'">'.$i->$value.'</a>'
				: $i->$value;
				
			echo '<li>'.$item.'</li>';
		}
	}
	echo '</ul>';
}
else
	echo '<p>'._NO_ITEMS.'</p>';

