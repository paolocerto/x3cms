<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */

/**
 * Helper for pagination
 * 
 * @package X4WEBAPP
 */
class X4Pagination_helper 
{
	
	/**
	 * Get an array of records and slice a page of items
	 *
	 * @static
	 * @param array		array of records 
	 * @param integer	page of items to show
	 * @param integer	number of items per page
	 * @return array	(array of items to show, elements needed for pagination)
	 */
	public static function paginate($array, $start, $pp = 0)
	{
		$i = ($pp) ? $pp : PP;	// items per page
		$n = sizeof($array);	// items
		$p = ceil($n/$i);		// pages
		return (is_null($array))
			? array(array(), array($p, $start, $n))
			: array(array_slice($array, $start*$i, $i), array($p, $start, $n));
	}
	
	/**
	 * Controllers for pagination
	 *
	 * @static
	 * @param string	$url page URL
	 * @param array		$info array of info (number of pages, visualized page, amount of items)
	 * @param integer	$section number of items in a group of pages
	 * @param boolean	$inline inline visualization
	 * @param string	$suffix chunk of URL to append after page number
	 * @param string	$class Link class
	 * @return string
	 */
	public static function pager($url, $info, $section = 5, $inline = false, $suffix = '', $class = '')
	{
		$what = ($inline) 
		    ? 'span' 
		    : 'div';
		    
		$link = '<'.$what.' class="xsmall">'._FOUND.' '.$info[2].' '._ITEMS.' '._IN.' '.$info[0].' '._PAGES.'&nbsp;&nbsp;&nbsp;</'.$what.'>';
		
		// query string
		$qs = (empty(X4Route_core::$query_string)) 
			? ''
			: '?'.X4Route_core::$query_string;
		
		// define window
		$w = intval($info[1]/$section);
		
		$class = ' '.$class;
		
		// icons
		if (isset($_SESSION['xuid']))
		{
		    $left = '<i class="fa fa-arrow-left lg"></i>';
		    $right = '<i class="fa fa-arrow-right lg"></i>';
		}
		else
		{
		    $left = '<img src="'.THEME_URL.'img/left.png" alt="'._PREVIOUS.'" />';
		    $right = '<img src="'.THEME_URL.'img/right.png" alt="'._NEXT.'" />';
		}
		
		// before
		if ($info[1] > 0) 
		{
			$link .= '<a class="xsmall'.$class.' pager_first" href="'.$url.'0'.$suffix.$qs.'" title="'._FIRST_PAGE.'">1</a>';
			$link .= '<a class="'.$class.' pager_previous" href="'.$url.($info[1]-1).$suffix.$qs.'" title="'._PREVIOUS.'">'.$left.'</a>';
		}
		
		// visualized section
		for($i = $w*$section; $i < min($info[0], ($w+1)*$section); $i++)
		{
			$link .= ($i == $info[1]) 
				? '<span class="n">'.($i+1).'</span>' 
				: ' <a class="xsmall'.$class.'" href="'.$url.$i.$suffix.$qs.'" title="'._PAGE.' '.($i+1).'">'.($i+1).'</a>';
		}
		
		// after
		if ($info[1] < ($info[0]-1)) 
		{
			$link .= '<a class="'.$class.' pager_next" href="'.$url.($info[1]+1).$suffix.$qs.'" title="'._NEXT.'">'.$right.'</a>';
			$link .= '<a class="xsmall'.$class.' pager_last" href="'.$url.($info[0]-1).$suffix.$qs.'" title="'._LAST_PAGE.'">'.$info[0].'</a>';
		}
		return $link;
	}
	
	/**
	 * Simplified pagination, only previous and next
	 *
	 * @static
	 * @param string	$url page URL
	 * @param array		$info array of info (number of pages, visualized page, amount of items)
	 * @param string	$sx previous link
	 * @param string	$dx next link
	 * @param integer	$section number of visible links
	 * @param string	$suffix chunk of URL to append after page number
	 * @param string	$class Link class
 	 * @param string	$first First item link
 	 * @param string	$last Last item link
	 * @return array
	 */
	public static function pager_slim($url, $info, $sx = _PREVIOUS, $dx = _NEXT, $section = 5, $suffix = '', $class = '', $first = '', $last = '')
	{
		// query string
		$qs = (empty(X4Route_core::$query_string)) 
			? ''
			: '?'.X4Route_core::$query_string;
		
		$link = array();
		
		$class = ' '.$class;
		
		// previous
		$link[0] = ($info[1] > 0) 
			? '<a class="xsmall'.$class.'" href="'.$url.($info[1]-1).$suffix.$qs.'" title="'._PREVIOUS.'">'.$sx.'</a>' 
			: $sx;
		// next
		$link[1] = ($info[1] < ($info[0]-1)) 
			? '<a class="xsmall'.$class.'" href="'.$url.($info[1]+1).$suffix.$qs.'" title="'._NEXT.'">'.$dx.'</a>' 
			: $dx;
			
		if (!empty($first) && $info[1] > 0)
		{
			$link[0] = '<a class="xsmall'.$class.'" href="'.$url.'0'.$suffix.$qs.'" title="">'.$first.'</a> '.$link[0];
		}
		
		if (!empty($last) && $info[1] < ($info[0]-1))
		{
			$link[1] = $link[1].' <a class="xsmall'.$class.'" href="'.$url.($info[0]-1).$suffix.$qs.'" title="">'.$last.'</a>';
		}
		
		// section
		$tmp = '';
		if ($section)
		{
			$start = $info[1]%$section;
			$end = min($info[0], $start + $section);
			for ($i = $start; $i < $end; $i++)
			{
				$tmp .= ($i == $info[1]) 
					? ' <span class="n">'.($i+1).'</span>' 
					: ' <a class="xsmall'.$class.'" href="'.$url.$i.$suffix.$qs.'" title="'._PAGE.' '.($i+1).'">'.($i+1).'</a>';
			}
		}
		// links to each page
		$link[2] = $tmp;
		// current page
		$link[3] = $info[1]+1;
		// number of pages
		$link[4] = $info[0];
		return $link;
	}
	
	/**
	 * Controllers for Bootstrap pagination
	 *
	 * @static
	 * @param string	$url page URL
	 * @param array		$info array of info (number of pages, visualized page, amount of items)
	 * @param integer	$section number of items in a group of pages
	 * @param boolean	$inline inline visualization
	 * @param string	$suffix chunk of URL to append after page number
	 * @param string	$class Link class
	 * @return string
	 */
	public static function bs_pager($url, $info, $section = 5, $suffix = '')
	{
		$link = '<p class="small">'._FOUND.' '.$info[2].' '._ITEMS.' '._IN.' '.$info[0].' '._PAGES.'</p>';
		
		// if there are more than one page
		if ($info[2] > 1)
		{
			// define window
			$w = intval($info[1]/$section);
			
			// before
			if ($info[1] > 0) 
			{
				$link .= '<a class="pager_item" href="'.$url.'0'.$suffix.'" title="'._FIRST_PAGE.'">1</a>';
				$link .= '<a class="pager_arrow" href="'.$url.($info[1]-1).$suffix.'" title="'._PREVIOUS.'"><i class="glyphicon glyphicon-chevron-left"></i></a>';
			}
			
			// visualized section
			for($i = $w*$section; $i < min($info[0], ($w+1)*$section); $i++)
			{
				$link .= ($i == $info[1]) 
					? '<span class="pager_active">'.($i+1).'</span>' 
					: '<a class="pager_item" href="'.$url.$i.$suffix.'" title="'._PAGE.' '.($i+1).'">'.($i+1).'</a>';
			}
			
			// after
			if ($info[1] < ($info[0]-1)) 
			{
				$link .= '<a class="pager_arrow" href="'.$url.($info[1]+1).$suffix.'" title="'._NEXT.'"><i class="glyphicon glyphicon-chevron-right"></i></a>';
				$link .= '<a class="pager_item" href="'.$url.($info[0]-1).$suffix.'" title="'._LAST_PAGE.'">'.$info[0].'</a>';
			}
		}
		return $link;
	}
}
