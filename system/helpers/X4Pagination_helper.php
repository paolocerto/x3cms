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
	 */
	public static function paginate(array $array, int $active_page, int $items_per_page = 0) : array
	{
		$i = ($items_per_page) ? $items_per_page : PP;	// items per page
		$n = sizeof($array);	// items
		$p = ceil($n/$i);		// pages

        // (array of items to show, elements needed for pagination)
		return (is_null($array))
			? [[], [$p, $active_page, $n]]
			: [array_slice($array, $active_page*$i, $i), [$p, $active_page, $n]];
	}

    /**
	 * Controllers for TailWind admin pagination
	 */
	public static function tw_admin_pager(string $url, array $info, int $section = 5, string $suffix = '') : string
	{
		$link = '<p class="text-xs mt-4 py-2">
            '._FOUND.' <span class="font-bold">'.$info[2].'</span> '._ITEMS.' '._IN.' '.$info[0].' '._PAGES.'
            </p>';

		// if there are more than one page
        // info = array(number of pages, visualized page, amount of items)
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
				$link .= '<a class="link font-bold px-2" @click="pager(\''.$url.($info[1]-1).$suffix.'\')" title="'._PREVIOUS.'">
                    <i class="fas fa-chevron-left"></i>
                </a>';
			}

			// visualized section
			$last = min($info[0], ($w+1)*$section);
			for($i = $w*$section; $i < $last; $i++)
			{
				$link .= ($i == $info[1])
					? '<span class="bg px-2 py-1 rounded mr-1 font-bold">'.($i+1).'</span>'
					: '<a class="link font-bold px-2" @click="pager(\''.$url.$i.$suffix.'\')" title="'._PAGE.' '.($i+1).'">'.($i+1).'</a>';
			}

			// after
			if ($info[1] < ($info[0]-1))
			{
				$link .= '<a class="link font-bold px-2" @click="pager(\''.$url.($info[1]+1).$suffix.'\')" title="'._NEXT.'">
                    <i class="fas fa-chevron-right"></i>
                </a>';
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
	 */
	public static function pager(
        string $url,
        array $info,
        int $section = 5,
        bool $inline = false,
        string $suffix = '',
        string $class = ''
    ) : string
	{
		$what = ($inline)
		    ? 'span'
		    : 'div';

		$link = '<'.$what.' class="xsmall">
            '._FOUND.' '.$info[2].' '._ITEMS.' '._IN.' '.$info[0].' '._PAGES.'&nbsp;&nbsp;&nbsp;
        </'.$what.'>';

		// query string
		$qs = (empty(X4Route_core::$query_string))
			? ''
			: '?'.X4Route_core::$query_string;

		// define window
		$w = intval($info[1]/$section);

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
			$link .= '<a class="xsmall '.$class.' pager_first" href="'.$url.'0'.$suffix.$qs.'" title="'._FIRST_PAGE.'">1</a>';
			$link .= '<a class="'.$class.' pager_previous" href="'.$url.($info[1]-1).$suffix.$qs.'" title="'._PREVIOUS.'">
                '.$left.'
            </a>';
		}

		// visualized section
		for($i = $w*$section; $i < min($info[0], ($w+1)*$section); $i++)
		{
			$link .= ($i == $info[1])
				? '<span class="n">'.($i+1).'</span>'
				: ' <a class="xsmall '.$class.'" href="'.$url.$i.$suffix.$qs.'" title="'._PAGE.' '.($i+1).'">'.($i+1).'</a>';
		}

		// after
		if ($info[1] < ($info[0]-1))
		{
			$link .= '<a class="'.$class.' pager_next" href="'.$url.($info[1]+1).$suffix.$qs.'" title="'._NEXT.'">
                '.$right.'
            </a>';
			$link .= '<a class="xsmall '.$class.' pager_last" href="'.$url.($info[0]-1).$suffix.$qs.'" title="'._LAST_PAGE.'">
                '.$info[0].'
            </a>';
		}
		return $link;
	}

	/**
	 * Simplified pagination, only previous and next
	 */
	public static function pager_slim(
        string $url,
        array $info,                //  info (number of pages, visualized page, amount of items)
        string $sx = _PREVIOUS,
        string $dx = _NEXT,
        int $section = 5,
        string $suffix = '',
        string $class = '',
        string $first = '',
        string $last = ''
    ) : array
	{
		// query string
		$qs = (empty(X4Route_core::$query_string))
			? ''
			: '?'.X4Route_core::$query_string;

		$link = array();

		// previous
		$link[0] = ($info[1] > 0)
			? '<a class="xsmall '.$class.'" href="'.$url.($info[1]-1).$suffix.$qs.'" title="'._PREVIOUS.'">'.$sx.'</a>'
			: $sx;
		// next
		$link[1] = ($info[1] < ($info[0]-1))
			? '<a class="xsmall '.$class.'" href="'.$url.($info[1]+1).$suffix.$qs.'" title="'._NEXT.'">'.$dx.'</a>'
			: $dx;

		if (!empty($first) && $info[1] > 0)
		{
			$link[0] = '<a class="xsmall '.$class.'" href="'.$url.'0'.$suffix.$qs.'" title="">'.$first.'</a> '.$link[0];
		}

		if (!empty($last) && $info[1] < ($info[0]-1))
		{
			$link[1] = $link[1].' <a class="xsmall '.$class.'" href="'.$url.($info[0]-1).$suffix.$qs.'" title="">'.$last.'</a>';
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
					: ' <a class="xsmall '.$class.'" href="'.$url.$i.$suffix.$qs.'" title="'._PAGE.' '.($i+1).'">'.($i+1).'</a>';
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
	 */
	public static function tw_pager(
        string $url,
        array $info,            // info (number of pages, visualized page, amount of items)
        int $section = 5,
        string $suffix = ''
    ) : string
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
				$link .= '<a class="link font-bold px-2" href="'.$url.($info[1]-1).$suffix.'" title="'._PREVIOUS.'">
                    <i class="fas fa-chevron-left"></i>
                </a>';
			}

			// visualized section
			$last = min($info[0], ($w+1)*$section);
			for($i = $w*$section; $i < $last; $i++)
			{
				$link .= ($i == $info[1])
					? '<span class="bg px-2 py-1 rounded mr-1 font-bold">'.($i+1).'</span>'
					: '<a class="link font-bold px-2" href="'.$url.$i.$suffix.'" title="'._PAGE.' '.($i+1).'">'.($i+1).'</a>';
			}

			// after
			if ($info[1] < ($info[0]-1))
			{
				$link .= '<a class="link font-bold px-2" href="'.$url.($info[1]+1).$suffix.'" title="'._NEXT.'">
                    <i class="fas fa-chevron-right"></i>
                </a>';
				if (($info[0]-1) > $last)
				{
				    $link .= '<a class="bg2 font-bold px-2 py-1 rounded ml-2" href="'.$url.($info[0]-1).$suffix.'" title="'._LAST_PAGE.'">'.$info[0].'</a>';
				}
			}
		}
		return $link;
	}
}
