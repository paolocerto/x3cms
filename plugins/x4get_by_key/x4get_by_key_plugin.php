<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

/**
 * x4get_by_key plugin
 *
 * @package		X3CMS
 */
class x4get_by_key_plugin extends X4Plugin_core implements X3plugin
{
	/**
	 * Constructor
	 *
	 * @param	object	$site, site object
	 * @return	void
	 */
	public function __construct($site)
	{
		parent::__construct($site);
	}
	
	/**
	 * Default method
	 * Display paginated articles with specified key
	 *
	 * @param object	$page object
	 * @param array		$args array of args
	 * @param string	$param parameter (the key)
	 * @return string
	 */
	public function get_module($page, $args, $param = '')
	{
		$out = '';
		
		// pagination index
		$pp = (isset($args[0])) 
			? intval($args[0]) 
			: 0;
		
		// tag index
		$tag = (isset($args[1]) && $args[1] == 'tag') 
			? urldecode($args[2]) 
			: false;
		
		if (!empty($param)) 
		{
			if ($tag) 
			{
				$mod = new X4get_by_key_model();
				$items = X4Pagination_helper::paginate($mod->get_articles_by_key_and_tag($page->id_area, $page->lang, $param, $tag), $pp);
				$out .= '<div class="block"><h3>'._TAG.': '.htmlentities($tag).'</h3></div>';
			}
			else 
			{
				$items = X4Pagination_helper::paginate($this->site->get_articles_by_key($page->id_area, $page->lang, $param), $pp);
			}
			
			// use pagination
			if ($items[0]) 
			{
				foreach($items[0] as $i) 
				{
					if (!empty($i->content)) 
					{
						$out .= '<div class="block">'.X4Utils_helper::inline_edit($i, 0);
						// options
						$out .= X4Utils_helper::get_block_options($i);
						
						// check excerpt
						if ($i->excerpt) 
						{
							$text = X4Utils_helper::excerpt($i->content);
							$out .= X4Utils_helper::reset_url(stripslashes($text[0]));
						}
						else 
							$out .= X4Utils_helper::reset_url(stripslashes($i->content));
						
						$out .= '<div class="clear"></div>';
						
						// display tags
						if ($i->show_tags && !empty($i->tags)) 
						{
							$out .= '<p class="tags"><span>'._TAGS.'</span>: ';
							$tt = explode(',', $i->tags);
							foreach($tt as $t)
							{
								$t = trim($t);
								$out .= '<a href="'.BASE_URL.$page->url.'/0/tag/'.urlencode($t).'" title="'._TAG.'">'.$t.'</a> ';
							}
							$out .= '</p>';
						}
						$out .= '</div>';
					}
					
					// module
					if (!empty($i->module)) 
					{
						$out .= X4Utils_helper::module($this->site, $page, $args, $i->module, $i->param);
					}
				}
				
				// pager
				if ($items[1][0] > 1)
				{
				    $out .= '<div id="pager">'.X4Pagination_helper::pager(BASE_URL.$page->url.'/', $items[1]).'</div>';
				}
			}
			else 
			{
				$out .= '<div class="block"><p>'._NO_ITEMS.'</p></div>';
			}
		}
		return $out;
	}
	
	/**
	 * call plugin actions
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$control action name
	 * @param   mixed	$a
	 * @param   mixed	$b
	 * @param   mixed	$c
	 * @param   mixed	$d
	 * @return  void
	 */
	public function call_plugin($id_area, $control, $a, $b, $c, $d) 
	{
		// none
	}
}
