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
	 *
	 * @static
	 * @param string	$str chunk of html code
	 * @return string
	 */
	public static function recontent($str)
	{
        if (isset($_SESSION['nickname']))
        {
            $src = array(
                '@NOME@'
            );
            $rpl = array(
                $_SESSION['nickname']
            );
            return str_replace($src, $rpl, self::reset_url(stripslashes($str)));
        }
        else
        {
            return $str;
        }
	}

	/**
	 * Clean HTML string fixing incomplete URLs with ROOT
	 *
	 * @static
	 * @param string	$str chunk of html code
	 * @return string
	 */
	public static function reset_url($str)
	{
		$str = str_replace('href="./', 'href="/', $str);

		if (ROOT == '/')
		{
			if(function_exists('preg_replace_callback'))
			{
				// add missing ROOT
				$src = "#href=\"(?!(mailto\:|tel\:|http\://|https\://|ftp\://|/)).*?#i";

				return preg_replace_callback(
					$src,
					function($m)
					{
						return 'href="/';
					},
					$str);
			}
			else
			{
				$src = "#href=\"(mailto\:|tel\:|((ht|f)tp(s?)\://)){0,1}.*?#i";
				$rpl = "href=\"$1";

				return preg_replace($src, $rpl, $str);
			}
		}
		else
		{
			// this is not very elegant but works
			$root = substr(ROOT, 1, -1);
			$oot = substr($root, 1);

			if(function_exists('preg_replace_callback'))
			{
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
					$str);
			}
			else
			{
				$src = array(
					"#href=\"(/(?!$root)).*?#i",				// slash without root
					"#href=\"(/)*$root(/)*\"#i",				// root without slashes
					"#href=\"(/".$root[0]."(?!$oot)).*?#i",		// /first_root_letter but not root
					"#href=\"(".$root[0]."(?!$oot)).*?#i",		// first_root_letter but not root
					"#href=\"^(mailto|tel|http|/$root/).*?#i"	// different from http and root
					);

				$rpl = array(
					"href=\"/$root$1",
					"href=\"/$root/",
					"href=\"/$root$1",
					"href=\"/$root/$1",
					"href=\"/$root/$1"
					);

				return preg_replace($src, $rpl, $str);
			}
		}
	}

	/**
	 * Create online editing link
	 * Each section can be edited in a separated page
	 *
	 * @static
	 * @param object	$obj article object
	 * @param integer	$n section number
	 * @return string
	 */
	public static function online_edit($obj, $n)
	{
		$section = ($n)
			? _TRAIT_.'<span class="xbig">Section '.$n.'</span>'
			: '';

		return (ONLINE && isset($obj->xlock) && isset($_SESSION['xuid']) && $_SESSION['site'] == SITE && $obj->xlock == 0)
			? '<div class="edit"><a href="'.ROOT.X4Route_core::$lang.'/admin" title="'._GOTO_ADMIN.'">'._ADMIN.'</a>'._TRAIT_.'<a href="'.BASE_URL.'x3admin/edit/'.$obj->id.'" title="'._EDIT_ARTICLE.'">'._EDIT.'</a>'.$section.'</div>'
			: '';
	}

	/**
	 * Create inline editing link
	 * Each section can be edit in place (less powerful and complete than online edit)
	 *
	 * @static
	 * @param integer	$id_area
	 * @param string	$domain
	 * @return string
	 */
	public static function inline_edit($id_area, $domain)
	{
		if (isset($_SESSION['xuid']) && INLINE)
        {
            $lang = X4Route_core::$lang;

            $base_url = (ROOT == '/')
                ? $domain
                : str_replace(ROOT, '', $domain.'/');
/*
            echo '<script src="'.ROOT.'files/js/tinymce/tinymce.min.js"></script>
<script>
var abid = null;
$(document).ready(function() {
    // remove edit boxes if present
    $("div.edit").remove();

    // add borders
    $(".block").addClass("inline_editing");

    ie_instruction("'._INLINE_EDITING_MODE_MSG.'", 1);

    // enable inline
    tinymce_inline();

    $(document).on("click", "#x3_inline_editor", function(e) {
        $(this).fadeOut();
    });
});

function ie_instruction(msg, delay) {
    $("#x3_inline_editor").fadeOut();
    setTimeout(function(){
        $("#x3_inline_editor").html("<b>'._INLINE_EDITING_MODE.':</b>" + msg).fadeIn();
    }, delay*1000);
}

function tinymce_inline() {
    tinymce.init({
        selector: ".block",
        theme: "inlite",
        plugins: "image table advlist lists link paste contextmenu textpattern autolink media youtube visualblocks",
        insert_toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent blockquote | link image | media youtube",
        selection_toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent blockquote | link image | media youtube",
        inline: true,
        //paste_data_images: true,
        relative_urls: false,
        remove_script_host: true,
        document_base_url: "'.$base_url.'",

        style_formats: [
		    {title: "Headers", items: [
		        {title: "h1", block: "h1"},
		        {title: "h2", block: "h2"},
		        {title: "h3", block: "h3"},
		        {title: "h4", block: "h4"},
		        {title: "h5", block: "h5"},
		        {title: "h6", block: "h6"}
		    ]},

		    {title: "Blocks", items: [
		        {title: "p", block: "p"},
		        {title: "div", block: "div"},
		        {title: "pre", block: "pre"}
		    ]},

		    {title: "Containers", items: [
		        {title: "section", block: "section", wrapper: true, merge_siblings: false},
		        {title: "article", block: "article", wrapper: true, merge_siblings: false},
		        {title: "blockquote", block: "blockquote", wrapper: true},
		        {title: "hgroup", block: "hgroup", wrapper: true},
		        {title: "aside", block: "aside", wrapper: true},
		        {title: "figure", block: "figure", wrapper: true}
		    ]}
		],

		importcss_append: true,
		content_css : "'.ROOT.'themes/admin/css/tinymce.css",

        //templates: "'.BASE_URL.'admin/files/js/'.$id_area.'/template",
        link_list: "'.BASE_URL.'admin/files/js/'.$id_area.'/files",
        image_list: "'.BASE_URL.'admin/files/js/'.$id_area.'/img",
        media_list: "'.BASE_URL.'admin/files/js/'.$id_area.'/media",

        setup: function(ed) {
            ed.on("focus", function (e) {
              abid = ed.id;
            });
            ed.on("blur", function(e){
                save_article(abid, ed.getContent());
            });
        }
    });
}

function save_article(bid, content) {
    $.ajax({
        type:"POST",
        url: root+"/x3admin/update/'.$id_area.'/'.$lang.'/"+bid,
        data: {"content": content},
        success: function(msg) {
            if (msg.length > 0) {
                ie_instruction("<p>"+msg+"</p>", 1);
                ie_instruction("'._INLINE_EDITING_MODE_MSG.'", 4);
            }
        },
        error: function(xhr, textStatus, errorThrown){
            ie_instruction("<p>"+textStatus+"</p>");
            ie_instruction("'._INLINE_EDITING_MODE_MSG.'", 4);
        }
    });
}
</script>';
*/
        }
        else
        {
            return '';
        }
	}

	/**
	 * Set an existent template
	 *
	 * @static
	 * @param string	$template template name
	 * @return string
	 */
	public static function set_tpl($template, $alternative_theme_url = '')
	{
        if (empty($alternative_theme_url))
        {
            return (!empty($template) && file_exists($_SERVER['DOCUMENT_ROOT'].THEME_URL.'templates/'.$template.'.php'))
                ? THEME_URL.'templates/'.$template
                : THEME_URL.'templates/base';
        }
        else
        {
            return (!empty($template) && file_exists($_SERVER['DOCUMENT_ROOT'].$alternative_theme_url.'templates/'.$template.'.php'))
                ? $alternative_theme_url.'templates/'.$template
                : '';
        }
    }

	/**
	 * Load a plugin and execute default method
	 *
	 * @static
	 * @param object	$site site obj
	 * @param object	$page page obj
	 * @param array		$args page's arguments
	 * @param string	$module plugin name
	 * @param string	$param parameter
	 * @param boolean	$force force load plugin even if not usable
	 * @return string
	 */
	public static function module(X4Site_model $site, stdClass $page, array $args, string $module, string $param = '', bool $force = false)
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
	 * Check if a plugin is installed
	 *
	 * @static
	 * @param string	$id_are Area ID
	 * @param string	$module Plugin name
	 * @return boolean
	 */
	public static function module_exists(int $id_area, string $module)
	{
		$plug = new X4Plugin_model();
		return $plug->exists($module, $id_area, false, 1);
	}

    /**
	 * Get page URL by plugin name and parameter
	 *
	 * @static
	 * @param string	$id_are Area ID
     * @param string    $lang
	 * @param string	$module Plugin name
     * @param string    $apram
	 * @return boolean
	 */
	public static function get_page_to(int $id_area, string $lang, string $module, string $param = '')
	{
		$plug = new X4Plugin_model();
		return $plug->get_page_to($id_area, $lang, $modname, $param);
	}

	/**
	 * Check if sections are empty
	 *
	 * @static
	 * @param string	$sections Array of sections
	 * @return boolean
	 */
	public static function empty_sections($sections)
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
	 * Split HTML content
	 *
	 * @static
	 * @param string	$str HTML code
	 * @param string	$sep separator
	 * @return array	(before, after)
	 */
	public static function excerpt($str, $sep = '<!--pagebreak-->')
	{
		$s = str_replace(array('<p>'.$sep.'</p>', '<p>'.$sep, $sep.'</p>'), array($sep, $sep.'<p>', '</p>'.$sep), $str, $count);
		if ($count == 0)
		{
			$s = str_replace($sep, '</p>'.$sep.'<p>', $s);
		}
		$s = explode($sep, $s);
		return $s;
	}

	/**
	 * Build a simple breadcrumb
	 *
	 * @static
	 * @param array		$array array of pages
	 * @param string	$sep separator
	 * @param boolean	$home show the home page link in navbar
	 * @return string
	 */
	public static function navbar($array, $sep = ' > ', $home = true)
	{
		$str = '';
		if (!empty($array))
		{
			// chain of pages
			$item = array_pop($array[0]);

			// additional URL params
			$url_params = (isset($array[1]))
				? $array[1]
				: array();

			foreach ($array[0] as $i)
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
				if ($home || $i->url != 'home')
				{
					$str .= '<a href="'.BASE_URL.$url.$param.'" title="'.stripslashes($i->description).'">'.stripslashes($i->name).'</a><span>'.$sep.'</span>';
				}
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
	 *
	 * @static
	 * @param string	$ordinal ordinal of visualized page
	 * @param array		$items menu array
	 * @param integer	$start_deep depth of the origin of the first item
	 * @param integer	$levels number of levels to display
	 * @param string	$ul_attribute first ul attribute
	 * @param boolean	$arrow display an arrow if submenu
	 * @param boolean	$home display the home link
	 * @param boolean	$vertical display menù open if related to active
	 * @return string
	 */
	public static function build_menu($ordinal, $items, $start_deep = 1, $levels = 1, $ul_attribute = '', $arrow = false,  $home = false, $vertical = false)
	{
		$out = '';
		$open_li = false;
		if (is_array($items) && !empty($items))
		{
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
						else if ($tmp_deep > $i->deep)
						{
							// close opened inner uls
							while($tmp_deep > $i->deep)
							{
								$out .= '</li></ul>';
								$tmp_deep--;
							}
							$out .= '</li>';
						}
						else if ($open_li)
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
		}
		return $out;
	}

    /**
	 * Check relation
	 * Check if two ordinals are related (one is a substr of the other)
	 *
	 * @static
	 * @param string	$ordinal_active_page ordinal of visualized page
	 * @param string	$ordinal_menu_item ordinal of another page
	 * @return boolean
	 */
	public static function check_relation($ordinal_active_page, $ordinal_menu_item)
	{
		$oap_len = strlen($ordinal_active_page);
		$omi_len = strlen($ordinal_menu_item);
		if ($oap_len != $omi_len)
		{
			// not at the same level
			if ($oap_len > $omi_len)
			{
				// active page is a subpage
				$ordinals = array($ordinal_active_page, $ordinal_menu_item);
				sort($ordinals);
				return (strstr($ordinals[1], $ordinals[0]) != '');
			}
			else
			{
				// active page is parent
				return false;
			}
		}
		else
		{
			// same level
			return (substr($ordinal_active_page, 0, -4) == substr($ordinal_menu_item, 0, -4));
		}
	}

	/**
	 * Build a botstrap nested menu
	 *
	 * @static
	 * @param string	$ordinal ordinal of visualized page
	 * @param array		$items menu array
	 * @param integer	$levels number of levels to display
	 * @return string
	 */
	public static function build_bootstrap_menu($ordinal, $items, $levels = 1, $home = false)
	{
		$menu = '';
		$sub = '';
		$deep = 1;
		$r = 1;

		if (is_array($items))
		{
		    $c = 0;
		    $n = sizeof($items);;
		    $min = 4 + $deep*4;
			$tmp_deep = $deep - 1;

			foreach ($items as $i)
			{
				// check related
				if ($tmp_deep == $deep)
				{
				    // if sub ordinal is equal is a subpage
					$r = substr($i->ordinal, 0, $min) == substr($ordinal, 0, $min);
				}

				// check if item is in the right range of deep
				if ($i->deep == $deep || ($r && $i->deep <= $levels))
				{
					// check if you need open an ul
					if ($tmp_deep < $i->deep)
					{
						$tmp_deep++;
						if (!empty($menu) && $tmp_deep > $deep)
						{
						    // is a sub
							$menu .= '<ul class="dropdown-menu">';
						}
						else
						{
							// First ul
							$menu .= '<ul class="nav navbar-nav navbar-right">';

							// home
							if ($tmp_deep == 1 && $home)
							{
								$active = ($ordinal == 'A')
									? ' class="active" '
									: ' ';

								$menu .= '<li '.$active.'><a href="'.BASE_URL.'" title="Home page">'._HOME_PAGE.'</a></li>';
							}
						}
					}
					// close opened ul
					else if ($tmp_deep > $i->deep)
					{
						for($ii = $tmp_deep; $ii > $i->deep; $ii--)
						{
							$menu .= '</li></ul>';
							$tmp_deep--;
						}
						$menu .= '</li>';
					}
					// close li
					else
					{
						$menu .= '</li>';
					}
				}
				else
				{
				    // sub levels
				    if ($tmp_deep < $i->deep)
					{
						$tmp_deep++;
						if (!empty($menu) && $tmp_deep > $deep)
						{
						    // is a sub
							$menu .= '<ul class="dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">';
						}
					}
					// close opened ul
					else if ($tmp_deep > $i->deep)
					{
						for($ii = $tmp_deep; $ii > $i->deep; $ii--)
						{
							$menu .= '</li></ul>';
							$tmp_deep--;
						}
						$menu .= '</li>';
					}
					// close li
					else
					{
						$menu .= '</li>';
					}
				}

				// detect active pages
                $active = (
                    $i->ordinal == $ordinal || // is the active page
                    (
                        $i->deep == $deep &&	// you are in a nested level
                        substr($i->ordinal, 0, $min) == substr($ordinal, 0, $min)) // with the same ordinal prefix
                    )
                        ? ' class="active" '
                        : '';

                // handle inside and outside
                if (!is_numeric($i->hidden))
                {
                    if (is_numeric($i->url))
                    {
                        $url = '#" onclick="$(\'#sn'.$i->url.'\').animatescroll();';
                    }
                    else
                    {
                        $url = (substr($i->url, 0, 4) == 'http')
                            ? $i->url
                            : BASE_URL.$i->url;
                    }
                }
                else
                {
                    $url = BASE_URL.$i->url;
                }

                // check for dropdown
                if ($c < $n && isset($items[$c+1]) && $items[$c+1]->deep > $i->deep)
                {
                    $active = (!empty($active))
                        ? 'active'
                        : '';

                    $menu .= '<li class="dropdown dropdown-submenu '.$active.'"><a  href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'.stripslashes($i->name).' <span class="caret"></span></a>';
                }
                else
                {
                    $menu .= '<li'.$active.'><a href="'.$url.'" title="'.stripslashes($i->title).'">'.stripslashes($i->name).'</a>';
                }
                $c++;
            }

			// close onpened ul
			for($ii = $tmp_deep; $ii >= $deep; $ii--)
			{
				// handle inside and outside
				$menu .= '</li></ul>';
				$tmp_deep--;
			}
		}
		return $menu;
	}

    /**
	 * Build a tailwind menu with dropdowns
     * Here we build the content of nav tag
     * NOTE: we handle only ONE level (deep <= 2)
	 *
	 * @static
	 * @param string	$ordinal ordinal of visualized page
	 * @param array		$items  menu array
     * @param boolean   $home   display or not the home link
	 * @return array    array($menu, $sub)
	 */
	public static function build_tailwind_menu($ordinal, $items, $home = false)
	{
		$menu = array();
		$sub = array();

        if (is_array($items))
		{
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

					// is a menu item or a subitem?
                    if ($i->deep == 1)
                    {
                        // is a menù

                        // home
                        if (!$c && $home)
                        {
                            $menu[] = array(
                                'url' => BASE_URL,
                                'title' => 'Home page',
                                'name' => HOME_PAGE,
                                'active' => ($ordinal == 'A'),
                                'fake' => false
                            );
                        }

                        // detect active pages
                        $active = (
                            $i->ordinal == $ordinal || // is the active page
                            substr($i->ordinal, 0, $min) == substr($ordinal, 0, $min) // with the same ordinal prefix
                        );

                        $menu[] = array(
                            'key' => $i->url,   // used to link submenus
                            'url' => $url,
                            'title' => $i->title,
                            'name' => $i->name,
                            'active' => $active,
                            'fake' => $i->fake
                        );
                    }
                    else
                    {
                        // is a submenù

                        // init
                        if (!isset($sub[$i->xfrom]))
                        {
                            $sub[$i->xfrom] = array();
                        }

                        // detect active pages
                        $active = (
                            $i->ordinal == $ordinal || // is the active page
                            substr($i->ordinal, 0, $min) == substr($ordinal, 0, $min) // with the same ordinal prefix
                        );

                        $sub[$i->xfrom][] = array(
                            'url' => $url,
                            'title' => $i->title,
                            'name' => $i->name,
                            'active' => $active,
                        );
                    }
                    $c++;
                }
            }
        }
		return array($menu, $sub);
	}

	/**
	 * Create options for article
	 *
	 * @static
	 * @param object	$block the article object
	 * @param string	$date_format date format
	 * @return string
	 */
	public static function get_block_options($block, $date_format = DATE_FORMAT)
	{
		if (isset($block->date_in))
		{
			$a = array();
			// show_author, show_date, show_tags, show_actions
			if ($block->show_date)
			{
				$a[] = @date($date_format, $block->date_in);
			}
			if ($block->show_author && !empty($block->author))
			{
				$a[] = $block->author;
			}
			if ($block->show_actions)
			{
				// TODO
			}
			return (!empty($a))
				? '<div class="block_options">'.implode(' - ', $a).'</div>'
				: '';
		}
		return '';
	}

    /**
	 * Sectionize build section container
	 *
	 * @static
	 * @param array     $css    array of extra CSS rules
     * @param object    $site   $site object
     * @param object    $page   $page object
     * @param array     $args   Page paramenters
     * @param integer   $index  Section index
     * @param array     $section    Array of articles
	 * @param string	$grid a key to switch between different grid systems: none|tailwind
	 * @return string
	 */
	public static function sectionize(&$css, $site, $page, $args, $index, $section, $grid = '')
	{
        // handle advanced sections and basic sections
		$articles = (is_array($section['a']))
            ? $section['a'] // articles in section
            : [$section['a']]; // settings in sections

        // check section style
		$class = array();
		$style = array();
        $data_img = '';
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
                $css[] = '#sn'.($index).'{background-image: url('.ROOT.'cms/files/'.SPREFIX.'/filemanager/img/'.$section['s']['img_h'].')}';
            }

            // vertical background
            if (isset($section['s']['img_v']) && !empty($section['s']['img_v']))
            {
                $css[] = '@media (max-width: 1024px) {#sn'.($index).'{background-image: url('.ROOT.'cms/files/'.SPREFIX.'/filemanager/img/'.$section['s']['img_v'].')}}';
            }
        }
        else
        {
            // default section[s]
            $section['s']['columns'] = 1;
            $section['s']['width'] = 'fullwidth';
        }

        // get section content
        $content = self::add_articles($site, $page, $args, $grid, $articles, $section['s']);

        $tmp = '';
        if (!empty($content))
        {
            // anchor + section
            $tmp = '<a name="a'.($index).'"></a>
                <div class="section '.implode(' ',  $class).'" id="sn'.($index).'">
                '.$content.'
                </div>';
        }
		return $tmp;
	}

	/**
	 * add articles
	 *
	 * @static
     *
     * @param object    $site   $site object
     * @param object    $page   $page object
     * @param array     $args   Page paramenters    // for plugins
	 * @param string	$grid
	 * @param array     $articles
	 * @param array 	$section settings
	 * @return string
	 */
	public static function add_articles($site, $page, $args, $grid, $articles, $section)
	{
		switch($grid)
		{
			case 'bootstrap':
                // bootstrap 3.37
                return self::bootstrap($site, $page, $args, $articles, $section);
				break;
            case 'tailwind':
                return self::tailwind($site, $page, $args, $articles, $section);
                break;
		}
    }

	/**
	 * bootstrap row
	 *
	 * @static
     * @param object    $site   $site object
     * @param object    $page   $page object
     * @param array     $args   Page paramenters    // for plugins
	 * @param array     $articles
	 * @param array     $section    Settings
	 * @return string
	 */
	public static function bootstrap($site, $page, $args, $articles, $section)
	{
        // get number of columns
        $columns = $section['columns'];

		// available suffixes for widths
		$cols = array('', '12', '6', '4', '3', '2');

		// number of articles
		$n = sizeof($articles);

		// set columns
		if ($columns > 1)
		{
			// adjust columns to number of articles in the row
			$columns = min($n, $columns);
		}

		$txt = '';

		// article counter
		$c = 0;
		foreach ($articles as $i)
		{
			// define class suffix for lg view
			if ($columns > 1 && $c > $columns && $columns > ($n - $c))
			{
				// here we redefine $col if the last articles are less than columns
				$col = $cols[$n - $c];
			}
			else
			{
				$col = $cols[$columns];
			}


			// define subclasses
			if ($columns%3 == 0)
			{
				$middle = 'col-md-4';
				$small = 'col-sm-6';	// to check
			}
			elseif ($columns%2 == 0)
			{
				$middle = 'col-md-3';
				$small = 'col-sm-6';
			}
			else
			{
				$middle = '';
				$small = '';
			}

			// open/reopen the row
			if ($c == 0)
			{
				$txt .= '<div class="row">';
			}
			elseif ($c%$columns == 0)
			{
				$txt .= '</div>'.NL.'<div class="row">';
			}

			$txt .= '<div class="col-xs-12 '.$small.' '.$middle.' col-lg-'.$col.'">';

			// handle contents
			if (!empty($i->content))
			{
				$txt .= self::recontent($i->content);
			}

			// module
			if (!empty($i->module))
			{
				$txt .= X4Theme_helper::module($site, $page, $args, $i->module, $i->param);
			}

			// scripts
			if (EDITOR_SCRIPTS && !empty($i->js) && strstr($i->js, '<script') != '')
			{
				$txt .= $i->js;
			}

			$txt .= '</div>';
			$c++;
		}
		return $txt.'</div>';
	}

    /**
	 * tailwind grid
	 *
	 * @static
     * @param integer   $nc
	 * @return string
	 */
    private static function tw_grid($nc)
    {
        $grid = array(
            1 => '',
            2 => 'grid lg:grid-cols-2 sm:grid-cols-1 grid-cols-1',
            3 => 'grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1',
            4 => 'grid lg:grid-cols-4 md:grid-cols-2 sm:grid-cols-1 grid-cols-1',
            5 => 'grid lg:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 grid-cols-1',
            6 => 'grid lg:grid-cols-6 md:grid-cols-3 sm:grid-cols-2 grid-cols-1',
        );
        return $grid[$nc];
    }

    /**
	 * tailwind span
	 *
	 * @static
     * @param integer   $col
	 * @return string
	 */
    private static function tw_span($col)
    {
        $span = array(
            11 => '',
            12 => '',
            21 => '',
            31 => '',
            32 => 'lg:col-span-2',
            41 => '',
            42 => 'lg:col-span-2 md:col-span-2',
            43 => 'lg:col-span-3 md:col-span-2',
            51 => '',
            52 => 'lg:col-span-2 md:col-span-2 sm:col-span-2',
            53 => 'lg:col-span-3 md:col-span-2 sm:col-span-2',
            54 => 'lg:col-span-4 md:col-span-3 sm:col-span-2',
            61 => '',
            62 => 'lg:col-span-2 md:col-span-2 sm:col-span-2',
            63 => 'lg:col-span-3 md:col-span-3 sm:col-span-2',
            64 => 'lg:col-span-4 md:col-span-3 sm:col-span-2',
            65 => 'lg:col-span-5 md:col-span-3 sm:col-span-2',
        );
        return $span[$col];
    }

    /**
	 * tailwind section row
	 *
	 * @static
     * @param object    $site   $site object
     * @param object    $page   $page object
     * @param array     $args   Page paramenters    // for plugins
	 * @param string	$grid
	 * @param array     $articles
	 * @param array     $section    Settings
	 * @return string
	 */
	public static function tailwind($site, $page, $args, $articles, $section)
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
        $csizes = isset($section['col_sizes'])
            ? explode('+', $section['col_sizes'])
            : array_fill(0, $section['columns'], 1);
        // this is the real number of columns with subdivion
        $nc = sizeof($csizes);

        // get fake number of columns
        $columns = $section['columns'];

		// number of articles
		$na = sizeof($articles);

		// if we have less articles than columns we reset columns to number of articles
		if ($columns > 1 && $na < $nc)
		{
			// adjust fake columns to number of articles in the row
			$columns = sizeof($articles);
		}

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
                // simple case: fake and real number of columns are equal
                if ($columns == $nc)
                {
                    // we define col size equal for all
                    $col = $nc.'1';
                }
                else
                {
                    // in most complex case we have subdivisions
                    // we need index for csizes
                    $ci = ($c < ($nc - 1))
                        ? $c            // we are in article less than real columns
                        : $c % $nc;     // we are in article equal or greater than real columns

                    // we build the subs index to get the CSS class for width
                    $col = $columns.''.$csizes[$ci];
                }

                // define grid by fake columns
                $grid = self::tw_grid($columns);

                // define span with subdivision
                $class = self::tw_span($col);

                // open/reopen the grid
                $tmp = '';
                if ($c == 0)
                {
                    $tmp = '<div class="'.$grid.' gap-6 px-4 pb-2">';

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
                elseif ($c%$nc == 0)
                {
                    $tmp = '</div>'.NL.'<div class="'.$grid.' pt-4 pb-2 gap-6 px-4">';
                }

                // handle settings
                $style = '';
                if (isset($section['col_settings']))
                {
                    if (isset($section['col_settings']['bg'.$c]) && $section['col_settings']['bg'.$c] != '')
                    {
                        $style .= 'background:'.$section['col_settings']['bg'.$c].';';
                    }

                    if (isset($section['col_settings']['fg'.$c]) && $section['col_settings']['fg'.$c] != '')
                    {
                        $style .= 'color:'.$section['col_settings']['fg'.$c];
                    }

                    if (isset($section['col_settings']['class'.$c]))
                    {
                        $class .= $section['col_settings']['class'.$c];
                    }
                }

                // open the columns
                // we add padding to mantain contents not too near the end of the screen
                $tmp .= '<div class="'.$class.'" style="'.$style.'">';

                // scripts
                $js = '';
                if (EDITOR_SCRIPTS && !empty($i->js) && strstr($i->js, '<script') != '')
                {
                    $js = $i->js;
                }

                // add the column and close it
                $txt .= $tmp.$content.$module.$js.'</div>';
                $c++;
            }
		}
		return $txt.$end_grid;
	}

    /**
	 * tailwind navbar
     * build menù items and dropdowns
	 *
	 * @static
     * @param array     $menu
     * @param array     $dropdowns
     * @param string    $style CSS classes for menu items
     * @param string    $active_style CSS classes for active item
     * @param string    $inactive_style CSS classes for inactive items
	 * @return string
	 */
	public static function tailwind_navbar($menu, $dropdowns, $style, $active_style, $inactive_style)
	{
        $transition = '
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 w-full mt-2 origin-top-right rounded-md shadow-lg md:w-48"
        x-cloak';

        $menu_items = '';
        if (!empty($menu))
        {
            // builde the menu items
            foreach ($menu as $i)
            {
                $active = ($i['active'])
                    ? $active_style
                    : $inactive_style;

                if (isset($dropdowns[$i['key']]))
                {
                    $key = str_replace('-', '_', $i['key']);

                    // build sub
                    $sub = '';
                    foreach ($dropdowns[$i['key']] as $ii)
                    {
                        $sub .= '<a
                                    class="block px-4 py-2 mt-2 focus:outline-none focus:shadow-outline '.$style.'"
                                    href="'.$ii['url'].'">'.$ii['name'].'
                                </a>';
                    }

                    // is a dropdown
                    if ($i['fake'])
                    {
                        $menu_items .= ' <div @click.away="'.$key.' = false" class="relative" x-data="{ '.$key.': false }">
                                            <button
                                                @click="'.$key.' = !'.$key.'"
                                                class="focus:outline-none '.$style.'"
                                            >
                                                <span>'.stripslashes($i['name']).'</span>
                                                <svg fill="currentColor" viewBox="0 0 20 20" :class="{\'rotate-180\': '.$key.', \'rotate-0\': !'.$key.'}" class="inline w-4 h-4 mt-1 ml-1 transition-transform duration-200 transform md:-mt-1"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                            </button>

                                            <div
                                                x-show="'.$key.'"
                                                '.$transition.'
                                            >
                                                <div class="px-2 py-2 bg-white shadow">
                                                    '.$sub.'
                                                </div>
                                            </div>
                                        </div>';
                    }
                    else
                    {
                        $menu_items .= ' <div @click.away="'.$key.' = false" class="relative" x-data="{ '.$key.': false }">
                                            <button
                                                x-on:mouseover="'.$key.' = !'.$key.'"
                                                onclick="window.location.href=\''.$i['url'].'\';"
                                                class="focus:outline-none '.$style.'"
                                            >
                                                <span>'.str_replace(' ', '&nbsp;', stripslashes($i['name'])).'</span>
                                                <svg
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                    :class="{\'rotate-180\': '.$key.', \'rotate-0\': !'.$key.'}"
                                                    class="inline w-4 h-4 mt-1 ml-1 transition-transform duration-200 transform md:-mt-1"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd">
                                                    </path>
                                                </svg>
                                            </button>

                                            <div
                                                x-show="'.$key.'"
                                                '.$transition.'
                                            >
                                                <div class="px-2 py-2 bg-white shadow">
                                                    '.$sub.'
                                                </div>
                                            </div>
                                        </div>';
                    }

                }
                else
                {
                    // normal item
                    $menu_items .= '<a
                                        href="'.$i['url'].'"
                                        title="'.stripslashes($i['title']).'"
                                        class="focus:outline-none px-2 pt-2 '.$style.' '.$active.'"
                                    >
                                        '.str_replace(' ', '&nbsp;', stripslashes($i['name'])).'
                                    </a>';
                }

            }
        }
        return $menu_items;
    }

    /**
	 * handle redirect
	 *
	 * @static
	 * @return void
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
