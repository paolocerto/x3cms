<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// local help view

echo '<p>'._HELP_MSG.'</p>';

if ($items)
{

	echo '<ul>';
	foreach ($items as $i)
	{
		echo '<li><a class="link" href="'.BASE_URL.$i->url.'" title="">'.$i->title.'</a>'._TRAIT_.$i->description.'</li>';
	}
	echo '</ul>';
}
