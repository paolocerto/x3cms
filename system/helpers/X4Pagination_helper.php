<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
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
	 * Controllers for TailWind admin pagination
	 *
	 * @static
	 * @param string	$url page URL
	 * @param array		$info array of info (number of pages, visualized page, amount of items)
	 * @param integer	$section number of items in a group of pages
	 * @param string	$suffix chunk of URL to append after page number
	 * @return string
	 */
	public static function tw_admin_pager($url, $info, $section = 5, $suffix = '')
	{
		$link = '<p class="text-xs mt-4 py-2">'._FOUND.' <span class="font-bold">'.$info[2].'</span> '._ITEMS.' '._IN.' '.$info[0].' '._PAGES.'</p>';

		// if there are more than one page
		if ($info[0] > 1)
		{
			// define window
			$w = intval($info[1]/$section);

			// before
			if ($info[1] > 0)
			{
			    if ($w*$section > 0)
			    {
			        $link .= '<a class="bg2 font-bold px-2 py-1 rounded mr-2" @click="pager(\''.$url.'0'.$suffix.'\')" title="'._FIRST_PAGE.'">1</a>';
			    }
				$link .= '<a class="link font-bold px-2" @click="pager(\''.$url.($info[1]-1).$suffix.'\')" title="'._PREVIOUS.'"><i class="fas fa-chevron-left"></i></a>';
			}

			// visualized section
			$last = min($info[0], ($w+1)*$section);
			for($i = $w*$section; $i < $last; $i++)
			{
				$link .= ($i == $info[1])
					? '<span class="bg course_bg px-2 py-1 rounded mr-1 font-bold">'.($i+1).'</span>'
					: '<a class="link font-bold px-2" @click="pager(\''.$url.$i.$suffix.'\')" title="'._PAGE.' '.($i+1).'">'.($i+1).'</a>';
			}

			// after
			if ($info[1] < ($info[0]-1))
			{
				$link .= '<a class="link font-bold px-2" @click="pager(\''.$url.($info[1]+1).$suffix.'\')" title="'._NEXT.'"><i class="fas fa-chevron-right"></i></a>';
				if (($info[0]-1) > $last)
				{
				    $link .= '<a class="bg2 font-bold px-2 py-1 rounded ml-2" @click="pager(\''.$url.($info[0]-1).$suffix.'\')" title="'._LAST_PAGE.'">'.$info[0].'</a>';
				}
			}
		}
		return $link;
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
		    $left = '<i class="fas fa-arrow-left lg"></i>';
		    $right = '<i class="fas fa-arrow-right lg"></i>';
		}
		else
		{
			$left = '<span class="fas fa-arrow-left lg"></span>';
			$right = '<span class="fas fa-arrow-right lg"></span>';
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
	 * Controllers for TailWind pagination
	 *
	 * @static
	 * @param string	$url page URL
	 * @param array		$info array of info (number of pages, visualized page, amount of items)
	 * @param integer	$section number of items in a group of pages
	 * @param string	$suffix chunk of URL to append after page number
	 * @return string
	 */
	public static function tw_pager($url, $info, $section = 5, $suffix = '')
	{
		$link = '<p class="text-xs mt-4 py-2">'._FOUND.' <span class="font-bold">'.$info[2].'</span> '._ITEMS.' '._IN.' '.$info[0].' '._PAGES.'</p>';

		// if there are more than one page
		if ($info[0] > 1)
		{
			// define window
			$w = intval($info[1]/$section);

			// before
			if ($info[1] > 0)
			{
			    if ($w*$section > 0)
			    {
			        $link .= '<a class="bg2 font-bold px-2 py-1 rounded mr-2" href="'.$url.'0'.$suffix.'" title="'._FIRST_PAGE.'">1</a>';
			    }
				$link .= '<a class="link font-bold px-2" href="'.$url.($info[1]-1).$suffix.'" title="'._PREVIOUS.'"><i class="fas fa-chevron-left"></i></a>';
			}

			// visualized section
			$last = min($info[0], ($w+1)*$section);
			for($i = $w*$section; $i < $last; $i++)
			{
				$link .= ($i == $info[1])
					? '<span class="bg course_bg px-2 py-1 rounded mr-1 font-bold">'.($i+1).'</span>'
					: '<a class="link font-bold px-2" href="'.$url.$i.$suffix.'" title="'._PAGE.' '.($i+1).'">'.($i+1).'</a>';
			}

			// after
			if ($info[1] < ($info[0]-1))
			{
				$link .= '<a class="link font-bold px-2" href="'.$url.($info[1]+1).$suffix.'" title="'._NEXT.'"><i class="fas fa-chevron-right"></i></a>';
				if (($info[0]-1) > $last)
				{
				    $link .= '<a class="bg2 font-bold px-2 py-1 rounded ml-2" href="'.$url.($info[0]-1).$suffix.'" title="'._LAST_PAGE.'">'.$info[0].'</a>';
				}
			}
		}
		return $link;
	}
}
