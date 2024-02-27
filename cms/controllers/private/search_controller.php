<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
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
	 * check if user is logged
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::logged(3, 'public/home');
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
		$view = new X4View_core(X4Theme_helper::set_tpl($page->tpl));
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
			$searched = ($is_post)
			    ? X4Validation_helper::sanitize(strtolower($_POST['search']), 'string')
                : $qs['search'];

			// handle data
			$str = explode(' ', addslashes($searched));

			// search in area's articles
			$found = $this->site->search($page->id_area, $str);

			// build links to items found
			if ($found)
			{
				// update counter
				$tot += sizeof($found);

				// set message
				$tmp .= '<strong>'._SEARCH_PAGES.'</strong></p><ul class="search_result">';

				// build links to items found
				foreach ($found as $i)
				{
					$tmp .= '<li><a href="'.BASE_URL.$i->url.'" title="'.stripslashes($i->description).'">'.stripslashes($i->name).'</a>'._TRAIT_.nl2br(stripslashes($i->description)).'</li>';
				}
				$tmp .= '</ul>';
			}

			// modules
			$plug = new X4Plugin_model();

			// get searchable plugins
			$searchable = $plug->get_searchable($page->id_area);
			if ($searchable)
			{
				foreach ($searchable as $i)
				{
					// model to load
					$model = ucfirst($i->name).'_model';
					$mod = new $model;

					// get page URL to use as link
					$to_page = (isset($mod->search_param))
						? $this->site->get_page_to($page->id_area, $page->lang, $i->name, $mod->search_param)
                        : $this->site->get_page_to($page->id_area, $page->lang, $i->name, '*');

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
                        {
							$tmp .= '<strong>'.constant('_SEARCH_'.$plugin).'</strong></p>';
                        }
						// build links to items found
						$tmp .= '<ul class="search_result">';
                        // create list to pages where found articles with matched contents
						foreach ($found as $ii)
						{
							// create url
							$url = (isset($mod->personalized_url) && $mod->personalized_url)
								? $mod->get_url($ii, $to_page)
								: $to_page.'/'.$ii->id.'/detail';

							// item name
							$item = stripslashes($ii->name);

							$description = (empty($ii->description))
								? ''
								: _TRAIT_.stripslashes($ii->description);

							// link to item
							$tmp .= '<li><a href="'.BASE_URL.$url.'" title="'.$item.'">'.$item.'</a>'.$description.'</li>';
						}
						$tmp .= '</ul>';
					}
				}
			}

			// if found
			$tmp = ($tot)
                ? '<p>'._SEARCH_FOUND.' '.$tot.' '._SEARCH_ITEMS.'</p>'.$tmp
                : '<p>'._SEARCH_ZERO_RESULT.'</p>';

			$msg = new Obj_msg(_SEARCH_RESULT, _SEARCH_OF.' <strong>'.addslashes($_POST['search']).'</strong>'.$tmp);
		}
		else
		{
			// empty request
			$msg = new Obj_msg(_SEARCH_RESULT, '<p>'._SEARCH_MSG_SEARCH_EMPTY.'</p>');
		}

		// get menus
		$view->menus = $this->site->get_menus();
		$view->navbar = array($this->site->get_bredcrumb($page));

		// popolate section
		$sections = $this->site->get_sections($page->id);
		$sections[1]['a'] = array($msg);
		$view->sections = $sections;
		$view->args = array('_default');

		$view->render(true);
	}
}
