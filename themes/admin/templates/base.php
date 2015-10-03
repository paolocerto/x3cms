<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// section 1
if (!empty($sections[1])) 
{
	foreach($sections[1] as $i) 
	{
		if (!empty($i->content))
		{
			// options
			echo X4Utils_helper::get_block_options($i);
			echo X4Utils_helper::reset_url(stripslashes($i->content.NL.html_entity_decode($i->js)));
		}
		if (!empty($i->module)) 
			echo stripslashes(X4Utils_helper::module($this->site, $page, $args, $i->module, $i->param));
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
