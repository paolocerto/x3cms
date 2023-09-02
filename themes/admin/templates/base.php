<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// section 1
if (!empty($sections[1]))
{
	// handle advanced sections and basic sections
	$articles = (isset($sections[1]['a']))
		? $sections[1]['a']
		: $sections[1];

	foreach ($articles as $i)
	{
		if (!empty($i->content))
		{
			// options
			echo X4Theme_helper::get_block_options($i);
			echo X4Theme_helper::reset_url(stripslashes($i->content.NL.html_entity_decode($i->js)));
		}
		if (!empty($i->module))
		{
			echo stripslashes(X4Theme_helper::module($this->site, $page, $args, $i->module, $i->param));
		}
	}
}
// content
elseif (isset($content))
{
	echo $content;
}
else
{
	echo '<h1>'._WARNING.'</h1><p>'._GLOBAL_PAGE_NOT_FOUND.'</p>';
}
