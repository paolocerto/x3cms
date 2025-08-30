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
 * Theme Helper
 * This class contains many methods commonly used in X4WEBAPP
 *
 * @package X4WEBAPP
 */
class X4Theme_helper
{
	/**
	 * Replace markers in a string
	 */
	public static function recontent(string $html) : string
	{
        if (!isset($_SESSION['nickname']))
        {
            return $html;
        }

        $src = array(
            '@NOME@'
        );
        $rpl = array(
            $_SESSION['nickname']
        );
        return str_replace($src, $rpl, self::reset_url(stripslashes($html)));
	}

	/**
	 * Clean HTML string fixing incomplete URLs with ROOT
	 */
	public static function reset_url(string $html) : string
	{
		$html = str_replace('href="./', 'href="/', $html);

		if (ROOT == '/')
		{
            // add missing ROOT
            $src = "#href=\"(?!(mailto\:|tel\:|http\://|https\://|ftp\://|/)).*?#i";

            return preg_replace_callback(
                $src,
                function($m)
                {
                    return 'href="/';
                },
                $html);
		}
		else
		{
			// this is not very elegant but works
			$root = substr(ROOT, 1, -1);
			$oot = substr($root, 1);

            $src = array(
                "#href=\"/(?!$root).*?#i",					// slash without root
                "#href=\"(/)*$root(/)*#i",					// root without slashes
                "#href=\"(/".$root[0]."(?!$oot)).*?#i",		// /first_root_letter but not root
                "#href=\"(".$root[0]."(?!$oot)).*?#i",		// first_root_letter but not root
                "#href=\"^(mailto|tel|http|/$root/).*?#i"		// different from http and root
            );

            return preg_replace_callback(
                $src,
                function($m)
                {
                    $root = substr(ROOT, 1, -1);

                    if (isset($m[1]) && $m[1] == $root[0])
                    {
                        return 'href="/'.$root.'/'.$root[0];
                    }
                    else
                    {
                        return 'href="/'.$root.'/';
                    }
                },
                $html);
		}
	}

	/**
	 * Set an existent template
	 */
	public static function set_tpl(string $template, string $alternative_theme = '') : string
	{
        if (empty($alternative_theme_url))
        {
            return (!empty($template) && file_exists(TPATH.THEME.'templates/'.$template.'.php'))
                ? TPATH.THEME.'templates/'.$template
                : TPATH.THEME.'templates/base';
        }
        else
        {
            return (!empty($template) && file_exists(TPATH.$alternative_theme.'templates/'.$template.'.php'))
                ? TPATH.$alternative_theme.'templates/'.$template
                : '';
        }
    }

	/**
	 * Load a plugin and execute default method
	 */
	public static function module(
        X4Site_model $site,
        stdClass $page,
        array $args,
        string $module,
        string $param = '',
        bool $force = false // load plugin even if not installed in the area
    ) : mixed
	{
		$plug = new X4Plugin_model();
		if ($force || $plug->usable($module, $page->id_area) && file_exists(PATH.'plugins/'.$module.'/'.$module.'_plugin.php'))
		{
			X4Core_core::auto_load($module.'/'.$module.'_plugin');
			$plugin = ucfirst($module.'_plugin');
			$m = new $plugin($site);
			return $m->get_module($page, $args, $param);
		}
		else
		{
			return '';
		}
	}

	/**
	 * Check if a plugin is installed in the area
	 */
	public static function module_exists(int $id_area, string $module) : bool
	{
		$plug = new X4Plugin_model();
		return $plug->exists($module, $id_area, false, 1);
	}

    /**
	 * Get page URL by plugin name and parameter
	 */
	public static function get_page_to(int $id_area, string $lang, string $module, string $param = '') : string
	{
		$plug = new X4Plugin_model();
		return $plug->get_page_to($id_area, $lang, $modname, $param);
	}

	/**
	 * Check if sections are empty
	 */
	public static function empty_sections(array $sections) : bool
	{
		foreach ($sections as $i)
		{
			if (!empty($i))
			{
				return false;
			}
		}
		return true;
    }

    /**
	 * Split HTML content with excerpt separator
	 */
	public static function excerpt(string $str, string $sep = '<!--pagebreak-->') : array
	{
		$s = str_replace(
            array('<p>'.$sep.'</p>', '<p>'.$sep, $sep.'</p>'),
            array($sep, $sep.'<p>', '</p>'.$sep),
            $str,
            $count
        );

		if ($count == 0)
		{
			$s = str_replace($sep, '</p>'.$sep.'<p>', $s);
		}
		return explode($sep, $s);
	}

	/**
	 * Build a simple breadcrumb
	 */
	public static function navbar(array $pages, string $sep = ' > ', bool $home = true, string $css = '') : string
	{
		$str = '';
		if (sizeof($pages[0]) > 1)
		{
			// chain of pages
			$item = array_pop($pages[0]);

			// additional URL params
			$url_params = (isset($pages[1]))
				? $pages[1]
				: [];

			foreach ($pages[0] as $i)
			{
				// handle params
				$param = (isset($url_params[$i->url]))
					? '/'.$url_params[$i->url]
					: '';

                // is the URL the home page?
				$url = ($i->url == 'home')
					? ''
					: $i->url;

				// add a crumb
				$str .= (($home || $i->url != 'home') && !$i->fake)
                    ? '<a class="'.$css.'" href="'.BASE_URL.$url.$param.'" title="'.stripslashes($i->description).'">'.stripslashes($i->name).'</a><span>'.$sep.'</span>'
                    : stripslashes($i->name).'<span>'.$sep.'</span>';
			}
			// do we have to show home?
			if ($home || $item->url != 'home')
			{
				$str .= '<span>'.stripslashes($item->name).'</span>';
			}
		}
		return $str.'&nbsp;';
	}

	/**
	 * Build a nested menu
	 */
	public static function build_menu(
        string $ordinal,
        array $items,
        int $start_deep = 1,
        int $levels = 1,
        string $ul_attribute = '',
        bool $arrow = false,    // display an arrow if submenu
        bool $home = false,
        bool $vertical = false // display menù open if related to active
    ) : string
	{
        if (!is_array($items) || empty($items))
		{
            return '';
        }

		$out = '';
		$open_li = false;

        // first ul
        $out .= '<ul '.$ul_attribute.'>';

        $tmp_deep = $items[0]->deep;
        foreach ($items as $i)
        {
            // check if item is in the right range of deep
            if ($i->deep >= $start_deep && $i->deep < $levels)
            {
                // reset ul_attribute
                $ul_attribute = '';

                // home in menù?
                if ($home && $start_deep == 1)
                {
                    $active = ($ordinal == 'A')
                        ? 'class="active"'
                        : '';

                    $out .= '<li '.$active.'><a href="'.BASE_URL.'" title="Home page">'._HOME_PAGE.'</a>';
                    $open_li = true;
                    $home = false;
                }

                // inner levels
                if ($i->deep >= $start_deep)
                {
                    // relation to active used for vertical
                    $related = false;

                    // check if related
                    if ($vertical)
                    {
                        $related = self::check_relation($ordinal, $i->ordinal);
                    }

                    // check if you need open an ul
                    if ($tmp_deep < $i->deep && $open_li)
                    {
                        $tmp_deep++;

                        // ul attributes
                        if ($tmp_deep >= $start_deep && $vertical && !$related)
                        {
                            // overwrite for inner uls
                            $ul_attribute = 'style="display:none"';
                        }

                        // inner ul
                        if ($tmp_deep > $start_deep)
                        {
                            if ($arrow)
                            {
                                // keep open or closed submenu
                                $out .= ($related)
                                    ? '<div class="submenu pointer"><span class="fa fa-2x fa-chevron-up"></span></div>'
                                    : '<div class="submenu pointer"><span class="fa fa-2x fa-chevron-down"></span></div>';
                            }

                            $out .= '<ul '.$ul_attribute.'>';
                        }
                    }
                    elseif ($tmp_deep > $i->deep)
                    {
                        // close opened inner uls
                        while($tmp_deep > $i->deep)
                        {
                            $out .= '</li></ul>';
                            $tmp_deep--;
                        }
                        $out .= '</li>';
                    }
                    elseif ($open_li)
                    {
                        $out .= '</li>';
                    }

                    // detect active pages
                    $active = ($i->ordinal == $ordinal)
                        ? 'class="active"'
                        : '';

                    // add the item (open)
                    if ($i->fake)
                    {
                        $out .= '<li '.$active.'><a href="#" class="submenu" title="'.stripslashes($i->title).'">'.stripslashes($i->name).'</a>';
                    }
                    else
                    {
                        $out .= '<li '.$active.'><a href="'.BASE_URL.$i->url.'" title="'.stripslashes($i->title).'">'.stripslashes($i->name).'</a>';
                    }
                    $open_li = true;
                }
            }
        }

        // close onpened uls
        while($tmp_deep >= $start_deep)
        {
            $out .= '</li></ul>';
            $tmp_deep--;
        }
		return $out;
	}

    /**
	 * Check relation
	 * Check if two ordinals are related (one is a substr of the other)
	 */
	public static function check_relation(string $ordinal_active_page, string $ordinal_menu_item) : bool
	{
		$oap_len = strlen($ordinal_active_page);
		$omi_len = strlen($ordinal_menu_item);
		if ($oap_len == $omi_len)
		{
            // same level
			return substr($ordinal_active_page, 0, -4) == substr($ordinal_menu_item, 0, -4);
        }

        // not at the same level
        if ($oap_len > $omi_len)
        {
            // active page is a subpage
            $ordinals = array($ordinal_active_page, $ordinal_menu_item);
            sort($ordinals);
            return strstr($ordinals[1], $ordinals[0]) != '';
        }
        else
        {
            // active page is parent
            return false;
        }
	}

    /**
	 * Build a tailwind menu with dropdowns
     * Here we build the content of nav tag
     * NOTE: we handle only ONE level (deep <= 2)
	 */
	public static function build_tailwind_menu(string $ordinal, array $items, bool $home = false) : array
	{
        if (!is_array($items) || empty($items))
		{
            return [[], []];
        }

        $menu = $sub = [];
        $c = 0;
        foreach ($items as $i)
        {
            // check if item is in the right range of deep
            if ($i->deep <= 2)
            {
                // length ordinal
                $min = 4 + $i->deep*4;

                // handle inside and outside
                if (empty($i->redirect))
                {
                    $url = (substr($i->url, 0, 4) == 'http')
                        ? $i->url
                        : BASE_URL.$i->url;
                }
                else
                {
                    $url = $i->redirect;
                }

                if (!$c && $home)
                {
                    list($name, $mobile) = self::mode($i);

                    $menu[] = array(
                        'key' => 'home',
                        'url' => BASE_URL,
                        'title' => 'Home page',
                        'name' => $name,
                        'mobile' => $mobile,
                        'active' => ($ordinal == 'A'),
                        'fake' => false,
                        'action' => ''
                    );
                }

                // detect active pages
                $active = (
                    $i->ordinal == $ordinal || // is the active page
                    substr($i->ordinal, 0, $min) == substr($ordinal, 0, $min) // with the same ordinal prefix
                );

                list($name, $mobile) = self::mode($i);

                if ($i->deep == 1)
                {
                    $menu[] = array(
                        'key' => $i->url,   // used to link submenus
                        'url' => $url,
                        'title' => $i->title,
                        'name' => $name,
                        'mobile' => $mobile,
                        'mode' => $i->mode,
                        'active' => $active,
                        'fake' => $i->fake,
                        'action' => $i->action,
                        'class' => $i->xclass
                    );
                }
                else
                {
                    // is a submenù?
                    if (!isset($sub[$i->xfrom]))
                    {
                        $sub[$i->xfrom] = [];
                    }

                    $sub[$i->xfrom][] = array(
                        'url' => $url,
                        'title' => $i->title,
                        'name' => $name,
                        'mobile' => $mobile,
                        'mode' => $i->mode,
                        'active' => $active,
                        'fake' => $i->fake,
                        'action' => $i->action
                    );
                }
                $c++;
            }
        }
        return array($menu, $sub);
	}

    /**
     * Menù mode
     * returned array [mobile, screen]
     */
    public static function mode(stdClass $item) : array
    {
        if (!$item->mode || empty($item->icon))
        {
            // only text
            $tmp = str_replace(' ', '&nbsp;', stripslashes($item->name));
            return [$tmp, $tmp];
        }

        $icon = substr($item->icon, 0, 1) == '<'
            ? $item->icon
            : '<img src="'.THEME_URL.'img/'.$item->icon.'" />';

        if ($item->mode == 1)
        {
            // only icon
            // we have two to handle cases like submenu in navbar where menù is only icons and submenù not
            return [$icon, $icon.'&nbsp;&nbsp;'.str_replace(' ', '&nbsp;', stripslashes($item->name))];
        }
        else
        {
            // icon + text
            // requires CSS
            $tmp = $icon.'<span>&nbsp;&nbsp;'.str_replace(' ', '&nbsp;', stripslashes($item->name)).'</span>';
            return [$tmp, $tmp];
        }
    }

	/**
	 * Create options for article
	 */
	public static function get_block_options(stdClass $block, string $date_format = DATE_FORMAT) : string
	{
		if (!isset($block->date_in))
		{
            return '';
        }

        $a = [];
        // show_author, show_date
        if ($block->show_date)
        {
            $a[] = @date($date_format, $block->date_in);
        }
        if ($block->show_author && !empty($block->author))
        {
            $a[] = $block->author;
        }

        return (!empty($a))
            ? '<div class="block_options">'.implode(' - ', $a).'</div>'
            : '';
	}

    /**
	 * Sectionize build section container
	 */
	public static function sectionize(
        array &$css,        // array of extra CSS rules
        X4Site_model $site,
        stdClass $page,
        array $args,
        int $index,
        array $section,
        string $grid = ''   // a key to switch between different grid systems: none|tailwind
    ) : string
	{
        // handle advanced sections and basic sections
		$articles = (is_array($section['a']))
            ? $section['a']         // articles in section
            : [$section['a']];      // settings in sections

        // check section style
		$class = [];
        if (isset($section['s']))
        {
			// background
			if (isset($section['s']['bgcolor']) && $section['s']['bgcolor'] != 'default')
			{
				$css[] = '#sn'.($index).'{background-color:'.$section['s']['bgcolor'].';}';
			}

			// foreground
			if (isset($section['s']['fgcolor']) && $section['s']['fgcolor'] != 'default')
			{
				$css[] = '#sn'.($index).'{color:'.$section['s']['fgcolor'].';}';
			}

            // theme style
			if (isset($section['s']['style']) && !empty($section['s']['style']))
			{
				$class[] = $section['s']['style'];
			}

            // class
			if (isset($section['s']['class']) && !empty($section['s']['class']))
			{
				$class[] = $section['s']['class'];
			}

            // we handle widths in articles

			// height
			if (isset($section['s']['height']) && $section['s']['height'] == 'fullscreen')
			{
				$class[] = ($grid == 'tailwind')
                    ? 'h-screen'
                    : 'full';   // full is a CSS rule
			}

            // horizontal background
            if (isset($section['s']['img_h']) && !empty($section['s']['img_h']))
            {
                $css[] = '#sn'.($index).'{background-image: url('.FMPATH.'img/'.$section['s']['img_h'].');background-size:cover;}';
            }

            // vertical background
            if (isset($section['s']['img_v']) && !empty($section['s']['img_v']))
            {
                $css[] = '@media (max-width: 1024px) {#sn'.($index).'{background-image: url('.FMPATH.'img/'.$section['s']['img_v'].');background-size:cover;}}';
            }
        }
        else
        {
            // default section[s]
            $section['s']['columns'] = 1;
            $section['s']['width'] = 'container mx-auto';
        }

        // get section content
        $content = self::add_articles($site, $page, $args, $grid, $articles, $section['s']);

        $tmp = '';
        if (!empty($content))
        {
            // anchor + section
            $tmp = '<a name="a'.($index).'"></a>
<section class="'.implode(' ',  $class).'" id="sn'.($index).'">
'.AdminUtils_helper::flmngr($content).'
</section>
<style>
'.implode(NL, $css).'
</style>';
        }
		return $tmp;
	}

	/**
	 * add articles to section
	 */
	public static function add_articles(
        X4Site_model $site,
        stdClass $page,
        array $args,        // for plugins
        string $grid,
        array $articles,
        array $section
    ) : string
	{
		switch ($grid)
		{
			case 'tailwind':
                return self::tailwind($site, $page, $args, $articles, $section);
                break;
		}
    }

    /**
	 * tailwind grid
     * Using the number of columns you get Tailwind classis for the grid
	 */
    public static function tw_grid(int $nc) : string
    {
        $grid = array(
            1 => '',
            2 => 'gap-x-6 gap-y-8 grid lg:grid-cols-2 sm:grid-cols-1 grid-cols-1',
            3 => 'gap-x-6 gap-y-8 grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1',
            4 => 'gap-x-6 gap-y-8 grid xl:grid-cols-4 lg:grid-cols-2 md:grid-cols-2 sm:grid-cols-1 grid-cols-1',
            5 => 'gap-x-6 gap-y-8 grid xl:grid-cols-5 lg:grid-cols-3 md:grid-cols-3 sm:grid-cols-2 grid-cols-1',
            6 => 'gap-x-6 gap-y-8 grid xl:grid-cols-6 lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2 grid-cols-1',
        );
        return $grid[$nc];
    }

    /**
	 * tailwind span
     * we use a two digit input
     * - the first digit is the num of columns
     * - the second digit is the number of colums for the article
     * then you get the Tailwind span classes you need for the article
	 */
    private static function tw_span(
        int $grid_columns,
        int $real_columns,
        int $article_index,
        array $columns_sizes
    ) : string
    {
        $span = array(
            11 => '',
            12 => '',
            21 => '',
            31 => '',
            32 => 'lg:col-span-2',
            41 => '',
            42 => 'xl:col-span-2 lg:col-span-2 md:col-span-2',
            43 => 'xl:col-span-3 lg:col-span-3 md:col-span-2',
            51 => '',
            52 => 'xl:col-span-2 lg:col-span-2 md:col-span-2 sm:col-span-2',
            53 => 'xl:col-span-3 lg:col-span-3 md:col-span-2 sm:col-span-2',
            54 => 'xl:col-span-4 lg:col-span-4 md:col-span-3 sm:col-span-2',
            61 => '',
            62 => 'lg:col-span-2 md:col-span-2 sm:col-span-2',
            63 => 'lg:col-span-3 md:col-span-3 sm:col-span-2',
            64 => 'lg:col-span-4 md:col-span-3 sm:col-span-2',
            65 => 'lg:col-span-5 md:col-span-3 sm:col-span-2',
        );

        // simple case: fake and real number of columns are equal
        if ($grid_columns == $real_columns)
        {
            // we define col size equal for all
            $col = $real_columns.'1';
        }
        else
        {
            // in most complex case we have subdivisions
            // we need index for csizes
            $ci = ($article_index < ($real_columns - 1))
                ? $article_index            // we are in article less than real columns
                : $article_index % $real_columns;     // we are in article equal or greater than real columns

            // we build the subs index to get the CSS class for width
            $col = $grid_columns.''.$columns_sizes[$ci];
        }
        return $span[$col];
    }

    /**
	 * tailwind section row
	 */
	public static function tailwind(
        X4Site_model $site,
        stdClass $page,
        array $args,
        array $articles,
        array $section
    ) : string
	{
        // handle columns
        list($columns, $nc, $csizes) = self::getColumns($section, sizeof($articles));

		$txt = $end_grid = '';

		// article counter
		$c = 0;
		foreach ($articles as $i)
		{
            // handle contents
            $content = '';
			if (!empty($i->content))
			{
				$content = self::recontent($i->content);
			}

			// module
            $module = '';
			if (!empty($i->module))
			{
				$module = X4Theme_helper::module($site, $page, $args, $i->module, $i->param);
			}

            if (!empty($content) || !empty($module))
            {
                // define grid by fake columns
                $grid = self::tw_grid($columns);

                // define span with subdivision
                $class = [self::tw_span($columns, $nc, $c, $csizes)];

                // open/reopen the grid
                $tmp = self::openGrid($grid, $section, $nc, $c);

                // handle settings
                $colors = self::getClassAndColor($class, $section, $c);

                // open the columns
                // we add padding to mantain contents not too near the end of the screen
                $tmp .= '<article class="'.implode(' ', $class).'" style="'.$colors.'">';

                // scripts
                $js = '';
                if (EDITOR_SCRIPTS && !empty($i->js) && strstr($i->js, '<script') != '')
                {
                    $js = $i->js;
                }

                // add the column and close it
                $txt .= $tmp.$content.$module.$js.'</article>';
                $c++;
            }
		}
		return $txt.$end_grid;
	}

    /**
     * Get columns
     */
    private static function getColumns(array &$section, int $num_of_articles) : array
    {
        // fix for missing columns
        if (!isset($section['columns']))
        {
            $section['columns'] = 1;
        }
        // fix for missing width
        if (!isset($section['width']))
        {
            $section['width'] = 'container mx-auto';
        }

        // get real number of columns
        $columns_sizes = isset($section['col_sizes'])
            ? explode('+', $section['col_sizes'])
            : array_fill(0, $section['columns'], 1);
        // this is the real number of columns with subdivion
        $real_columns = sizeof($columns_sizes);
        // get fake number of columns
        $grid_columns = $section['columns'];

		// if we have less articles than columns we reset columns to the number of articles
		if ($grid_columns > 1 && $num_of_articles < $real_columns)
		{
			$grid_columns = $num_of_articles;
		}
        return [$grid_columns, $real_columns, $columns_sizes];
    }

    /**
     * Get article class and color
     */
    private static function getClassAndColor(array &$class, array $section, int $article_index) : string
    {
        $colors = '';
        if (isset($section['col_settings']))
        {
            if (isset($section['col_settings']['bg'.$article_index]) && $section['col_settings']['bg'.$article_index] != '')
            {
                $colors .= 'background:'.$section['col_settings']['bg'.$article_index].';';
            }

            if (isset($section['col_settings']['fg'.$article_index]) && $section['col_settings']['fg'.$article_index] != '')
            {
                $colors .= 'color:'.$section['col_settings']['fg'.$article_index];
            }

            if (isset($section['col_settings']['style'.$article_index]))
            {
                $class[] = $section['col_settings']['style'.$article_index];
            }

            if (isset($section['col_settings']['class'.$article_index]))
            {
                $class[] = $section['col_settings']['class'.$article_index];
            }
        }
        return $colors;
    }

    /**
     * Handle grid
     */
    private static function openGrid(
        string $grid,
        array $section,
        int $real_columns,
        int $article_index
    ) : string
    {
        $tmp = '';
        if ($article_index == 0)
        {
            $tmp = '<div class="'.$grid.' px-2 md:px-4 pb-2">';

            // handle width
            if ($section['width'] != 'fullwidth')
            {
                $tmp = '<div class="'.$section['width'].'">'.$tmp;
                $end_grid = '</div></div>';
            }
            else
            {
                $end_grid = '</div>';
            }
        }
        elseif ($article_index % $real_columns == 0)
        {
            // close the grid for each row
            // is this a good idea???
            $tmp = '</div>'.NL.'<div class="'.$grid.' pt-4 pb-2 px-2 md:px-4">';
        }
        return $tmp;
    }

    /**
	 * tailwind navbar
     * build menù items and dropdowns
	 */
	public static function tailwind_navbar(
        array $menu,
        array $dropdowns,
        string $style,
        string $active_style,
        string $inactive_style
    ) : string
	{
        $transition = '
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute w-full -ml-14 mt-2 -translate-x-2/3 z-20"
        x-cloak';

        if (empty($menu))
        {
            return '';
        }

        $menu_items = '';
        // build the menu items
        foreach ($menu as $i)
        {
            $active = ($i['active'])
                ? $active_style
                : $inactive_style;

            // is a dropdown
            if (isset($dropdowns[$i['key']]))
            {
                $key = str_replace('-', '_', $i['key']);

                $svg = '<svg
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            :class="{\'rotate-180\': '.$key.', \'rotate-0\': !'.$key.'}"
                            class="inline w-4 h-4 mt-1 ml-1 transition-transform duration-200 transform md:-mt-1"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd">
                            </path>
                        </svg>';

                // build sub
                $sub_screen = $sub_mobile = '';
                foreach ($dropdowns[$i['key']] as $ii)
                {
                    $action = empty($ii['action'])
                        ? 'href="'.$ii['url'].'"'
                        : '@click="'.$ii['action'].'"';

                    $sub_screen .= '<a
                                class="block px-4 py-2 '.$style.' cursor-pointer"
                                '.$action.'
                            >
                                '.$ii['mobile'].'
                            </a>';

                    $sub_mobile .= '<div class="w-full inline-flex md:hidden pb-1 lg:py-0 border-t border-b-gray-200">
                                    <a
                                        class="px-2 pt-2 '.$style.' cursor-pointer"
                                        '.$action.'
                                    >
                                        '.$ii['mobile'].'
                                    </a>
                            </div>';
                }


                if ($i['fake'])
                {
                    list($link, $mobile) = self::link($i, $style, $active, $svg, $key);

                    // with no link
                    $menu_items .= ' <div @click.away="'.$key.' = false" class="relative hidden md:inline-flex" x-data="{ '.$key.': false }">
                                        '.$link.'
                                        <div
                                            x-show="'.$key.'"
                                            '.$transition.'
                                        >
                                            <div class="sub rounded-md shadow-lg w-72 mt-10 pb-4">
                                                '.$sub_screen.'
                                            </div>
                                        </div>
                                    </div>
                                    '.$sub_mobile;
                }
                else
                {
                    // with link
                    if ($i['mode'] == 0)
                    {
                        $i['name'] = str_replace(' ', '&nbsp;', stripslashes($i['name']));
                    }
                    list($link, $mobile) = self::link($i, $style, $active, $svg, '');

                    $menu_items .= ' <div @click.away="'.$key.' = false" class="relative hidden md:inline-flex" x-data="{ '.$key.': false }">
                                        <button
                                            x-on:mouseover="'.$key.' = true"
                                            x-on:ontouchstart="'.$key.' = true"
                                            onclick="window.location.href=\''.$i['url'].'\';"
                                            class="'.$style.'"
                                        >
                                            <span>'.$i['name'].'</span>
                                            '.$svg.'
                                        </button>

                                        <div
                                            x-show="'.$key.'"
                                            x-on:mouseleave="'.$key.' = false"
                                            x-on:ontouchend="'.$key.' = false"
                                            '.$transition.'
                                            class="z-50"
                                        >
                                            <div class="sub rounded-md shadow-lg w-72 mt-10 pb-4">
                                                '.$sub_screen.'
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full inline-flex md:hidden pb-1 lg:py-0 border-t border-b-gray-200">
                                        '.$mobile.'
                                    </div>
                                    '.$sub_mobile;
                }

            }
            else
            {
                list($link, $mobile) = self::link($i, $style, $active, '');

                // for screens and mobile
                $menu_items .= '<div class="hidden md:inline-flex">
                                '.$link.'
                            </div>
                            <div class="w-full inline-flex md:hidden pb-1 lg:py-0 border-t border-b-gray-200">
                                '.$mobile.'
                            </div>';
            }
        }
        return $menu_items;
    }

    /**
	 * tailwind sidebar
     * build menù items WITHOUT dropdowns
	 */
	public static function tailwind_sidebar(
        array $menu,
        array $dropdowns,
        string $style,
        string $active_style,
        string $inactive_style
    ) : string
	{

        // TODO
        $transition = '
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute w-full -ml-14 mt-2 -translate-x-2/3 z-20"
        x-cloak';

        if (empty($menu))
        {
            return '';
        }

        $menu_items = '';
        // build the menu items
        foreach ($menu as $i)
        {
            $active = ($i['active'])
                ? $active_style
                : $inactive_style;

            // is a dropdown
            if (isset($dropdowns[$i['key']]))
            {
                $key = str_replace('-', '_', $i['key']);

                $svg = '<svg
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            :class="{\'rotate-180\': '.$key.', \'rotate-0\': !'.$key.'}"
                            class="inline w-4 h-4 mt-1 ml-1 transition-transform duration-200 transform md:-mt-1"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd">
                            </path>
                        </svg>';

                if ($i['fake'])
                {
                    list($link, $mobile) = self::link($i, $style, $active, $svg, $key);

                    // with no link
                    $menu_items .= ' <div @click.away="'.$key.' = false" class="relative hidden md:inline-flex" x-data="{ '.$key.': false }">
                                        '.$link.'
                                        <div
                                            x-show="'.$key.'"
                                            '.$transition.'
                                        >
                                            <div class="sub rounded-md shadow-lg w-72 mt-10 pb-4">
                                                '.$sub_screen.'
                                            </div>
                                        </div>
                                    </div>
                                    '.$sub_mobile;
                }
                else
                {
                    // with link
                    $menu_items .= ' <div @click.away="'.$key.' = false" class="relative hidden md:inline-flex" x-data="{ '.$key.': false }">
                                        <button
                                            x-on:mouseover="'.$key.' = true"
                                            x-on:ontouchstart="'.$key.' = true"
                                            onclick="window.location.href=\''.$i['url'].'\';"
                                            class="focus:outline-none pt-2 '.$style.'"
                                        >
                                            <span>'.str_replace(' ', '&nbsp;', stripslashes($i['name'])).'</span>
                                            '.$svg.'
                                        </button>

                                        <div
                                            x-show="'.$key.'"
                                            x-on:mouseleave="'.$key.' = false"
                                            x-on:ontouchend="'.$key.' = false"
                                            '.$transition.'
                                            class="z-50"
                                        >
                                            <div class="px-2 py-2 bg-white shadow">
                                                '.$sub_screen.'
                                            </div>
                                        </div>
                                    </div>
                                    '.$sub_mobile;
                }

            }
            else
            {
                list($link, $mobile) = self::link($i, $style, $active, '');

                // for screens and mobile
                $menu_items .= '<div class="hidden md:inline-flex">
                                '.$link.'
                            </div>
                            <div class="w-full inline-flex md:hidden pb-1 lg:py-0 border-t border-b-gray-200">
                                '.$mobile.'
                            </div>';
            }
        }
        return $menu_items;
    }

    /**
     * Navbar link
     */
    public static function link(array $item, string $style, string $active, string $svg, string $key = '') : array
    {
        if (empty($key))
        {
            if (empty($item['action']))
            {
                // normal menu item
                $btn_click = 'onclick="window.location.href=\''.$item['url'].'\'"';
                $mobile_click = 'href="'.$item['url'].'"';
                $btn_label = $item['name'];
                $mobile_label = $item['mobile'];
            }
            else
            {
                // menu item with action
                $btn_click = $mobile_click = '@click="'.$item['action'].'"';
                $btn_label = $item['name'];
                $mobile_label = $item['mobile'];
            }
        }
        else
        {
            // submenu button
            $btn_click = $mobile_click = '@click="'.$key.' = !'.$key.'"';
            $btn_label = '<span>'.stripslashes($item['name']).'</span><span class="btn_info">'.$svg.'</span>';
            $mobile_label = '<span>'.stripslashes($item['mobile']).'</span><span class="btn_info">'.$svg.'</span>';
        }

        // personalized style for menu item
        $btn_style = isset($item['class']) && !empty($item['class'])
            ? $item['class']
            : $style;

        $btn = '<button
                type="button"
                '.$btn_click.'
                title="'.stripslashes($item['title']).'"
                class="'.$btn_style.' '.$active.'"
            >
                '.$btn_label.'
            </button>';

        $mobile = '<a
            '.$mobile_click.'
            title="'.stripslashes($item['title']).'"
            class="px-2 '.$btn_style.' '.$active.'"
        >
            '.$btn_label.'
        </a>';

        $link = ($item['mode'] == 1)
            ? $btn
            : $mobile;

        $mobile = '<a
            '.$mobile_click.'
            title="'.stripslashes($item['title']).'"
            class="px-2 pt-2 '.$style.' '.$active.'"
        >
            '.$mobile_label.'
        </a>';

        return [$link, $mobile];
    }

    /**
	 * handle redirect
	 */
	public static function redirect()
	{
		if (isset($_SESSION['redirect']))
        {
            $location = $_SESSION['redirect'];
            unset($_SESSION['redirect']);
            header('Location: '.$location);
            die;
        }
    }

}
