<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
 
/**
 * Controller for Search results
 * 
 * @package X3CMS
 */
class Search_controller extends X4Cms_controller
{
	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Display search results
	 *
	 * @return  void
	 */
	public function _default()
	{
		// load dictionary
		$this->dict->get_wordarray(array('search'));
		
		// get page data
		$page = $this->get_page('search');
		$view = new X4View_core(X4Utils_helper::set_tpl($page->tpl));
		$view->page = $page;
		
		// build the message
		$tmp = '';
		
		// check post
		$is_post = (X4Route_core::$post && trim($_POST['search']) != '');
		// check query string
		$is_get = false;
		if (X4Route_core::$query_string)
		{
			$qs = X4Route_core::get_query_string();
			$is_get = (isset($qs['search']) && !empty($qs['search']));
		}
		
		// search
		// if submitted
		if ($is_post || $is_get)
		{
			// found counter
			$tot = 0;
			
			// sanitize
			if ($is_post)
			{
				$searched = X4Validation_helper::sanitize(strtolower($_POST['search']), 'string');
			}
			else
			{
				$searched = $qs['search'];
			}
			
			// handle _POST
			$str = explode(' ', addslashes($searched));
			
			// search in area's articles
			$found = $this->site->search($page->id_area, $str);
			
			// build links to items found
			if ($found) 
			{
				// update counter
				$tot += sizeof($found);
				
				// set message
				$tmp .= '<h3>'._SEARCH_PAGES.'</h3><ul class="search_result">';
				
				// build links to items found
				foreach($found as $i) 
				{
					$tmp .= '<li><a href="'.$i->url.'" title="'.stripslashes($i->description).'">'.stripslashes($i->name).'</a>'._TRAIT_.nl2br(stripslashes($i->description)).'</li>';
				}
				$tmp .= '</ul>';
			}
			
			// modules
			$plug = new X4Plugin_model();
			
			// get searchable plugins
			$searchable = $plug->get_searchable($page->id_area);
			if ($searchable) 
			{
				foreach($searchable as $i)
				{
					// model to load
					$model = ucfirst($i->name).'_model';
					$mod = new $model;
					
					// get page URL to use as link
					if (isset($mod->search_param))
					{
						$to_page = $this->site->get_page_to($page->id_area, $page->lang, $i->name, $mod->search_param);
					}
					else
					{
						$to_page = $this->site->get_page_to($page->id_area, $page->lang, $i->name, '*');
					}
					
					// perform plugin search
					$found = $mod->search($page->id_area, $page->lang, $str);
					
					// build links to items found
					if ($found) 
					{
						// plugin name
						$plugin = strtoupper($i->name);
						
						// update counter
						$tot += sizeof($found);
						
						// set message
						if (defined('_SEARCH_'.$plugin)) 
							$tmp .= '<h3>'.constant('_SEARCH_'.$plugin).'</h3>';
						
						// build links to items found
						$tmp .= '<ul class="search_result">';
						foreach($found as $ii) 
						{
							// create url
							$url = (isset($mod->personalized_url) && $mod->personalized_url) 
								? $mod->get_url($ii, $to_page) 
								: $to_page.'/'.$ii->id.'/detail';
							
							// item name
							$item = stripslashes($ii->name);
							
							$descr = (empty($ii->description))
								? ''
								: _TRAIT_.nl2br(stripslashes($ii->description));
							
							// link to item
							$tmp .= '<li><a href="'.$url.'" title="'.$item.'">'.$item.'</a>'.$descr.'</li>';
						}
						$tmp .= '</ul>';
					}
				}
			}
			
			// if found
			if ($tot)
			{
				$tmp = '<p>'._SEARCH_FOUND.' '.$tot.' '._SEARCH_ITEMS.'</p>'.$tmp;
			}
			else
			{
				$tmp .= '<p>'._SEARCH_ZERO_RESULT.'</p>';
			}	
			$msg = new Obj_msg(_SEARCH_RESULT, _SEARCH_OF.' <strong>'.addslashes($searched).'</strong>'.$tmp, false);
		}
		else 
		{
			// empty request
			$msg = new Obj_msg(_SEARCH_RESULT, '<p>'._SEARCH_MSG_SEARCH_EMPTY.'</p>');
		}
		
		// get menus
		$view->menus = $this->site->get_menus($page->id_area);
		$view->navbar = array($this->site->get_bredcrumb($page));
		
		// popolate section
		$sections = $this->site->get_sections($page->id);
		$sections[1] = array($msg);
		$view->sections = $sections;
		
		$view->render(TRUE);
	}
}
