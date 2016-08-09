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
 * Helper common
 * This class contains many methods commonly used in X4WEBAPP
 * 
 * @package X4WEBAPP
 */
class X4Utils_helper 
{
	/**
	 * hashing
	 *
	 * @static
	 * @param string	$str String to hash
	 * @param string	$salt optional salt
	 * @return string
	 */
	public static function hashing($str, $salt = '')
	{
		return hash(HASH, $str.$salt);
	}
	
	/**
	 * Check if user need to be logged
	 *
	 * @static
	 * @param integer	$id_area area ID
	 * @param string	$location area/controller where redirect user for login
	 * @return void
	 */
	public static function logged($id_area = 1, $location = 'admin/login')
	{
		if (!isset($_SESSION['site']) || $_SESSION['site'] != SITE || $_SESSION['id_area'] != $id_area) 
		{
			// check for cookie HASH
			$chk = false;
			// check hashkey
			if (isset($_COOKIE[COOKIE.'_hash']) && $_COOKIE[COOKIE.'_hash'] != '')
			{
				$mod = new X4Auth_model('users');
				$chk = $mod->rehash($id_area, $_COOKIE[COOKIE.'_hash']);
			}
			
			if (!$chk)
			{
				header('Location: '.ROOT.$location);
				die;
			}
		}
	}
	
	/**
	 * Check if a call is an AJAX call
	 *
	 * @static
	 * @return boolean
	 */
	public static function is_ajax()
	{
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}
	
	/**
	 * Check permissions over a record
	 * use X3privs_model
	 *
	 * @static
	 * @param integer	$id_who ID user
	 * @param string	$what table
	 * @param integer	$id_what ID record
	 * @param integer	$value permission level
	 * @return void
	 */
	public static function chklevel($id_who, $what, $id_what, $value)
	{
		$priv = new X3privs_model();
		$level = $priv->check_priv($id_who, $what, $id_what);
		if ($level < $value) 
		{
			header('Location: '.BASE_URL.'msg/message/_not_permitted');
			die;
		}
	}
	
	/**
	 * Check if site is offline
	 *
	 * @static
	 * @param boolean	$xon site status
	 * @param string	$url controller where redirect unlogged user
	 * @return void
	 */
	public static function offline($xon, $url)
	{
		if (!$xon && !isset($_SESSION['site']) && X4Route_core::$control != 'offline') 
		{
		    if (X4Route_core::$control != 'offline')
		    {
		        header('Location: '.$url);
		        die;
		    }
		    else
		    {
		        header('HTTP/1.1 503 Service Temporarily Unavailable');
		        header('Retry-After: 10800');
		    }
		}
	}
	
	/**
	 * Define a personal base URL
	 *
	 * @static
	 * @param string	$base_url URL
	 * @return void
	 */
	public static function set_mybase_url($base_url)
	{
		define('MYBASE_URL', $base_url);
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
	 * Create inline editing link
	 *
	 * @static
	 * @param object	$obj article object
	 * @param integer	$n section number
	 * @return string
	 */
	public static function inline_edit($obj, $n)
	{
		$section = ($n) 
			? _TRAIT_.'<span class="xbig">Section '.$n.'</span>' 
			: '';
			
		return (INLINE && isset($obj->xlock) && isset($_SESSION['xuid']) && $_SESSION['site'] == SITE && $obj->xlock == 0) 
			? '<div class="edit"><a href="'.ROOT.X4Route_core::$lang.'/admin" title="'._GOTO_ADMIN.'">'._ADMIN.'</a>'._TRAIT_.'<a href="'.BASE_URL.'x3admin/edit/'.$obj->id.'" title="'._EDIT_ARTICLE.'">'._EDIT.'</a>'.$section.'</div>' 
			: '';
	}
	
	/**
	 * Set an existent template
	 *
	 * @static
	 * @param string	$template template name
	 * @return string
	 */
	public static function set_tpl($template)
	{
		return (!empty($template) && file_exists($_SERVER['DOCUMENT_ROOT'].THEME_URL.'templates/'.$template.'.php')) 
			? THEME_URL.'templates/'.$template 
			: THEME_URL.'templates/base';
	}
	
	/**
	 * Put the message into a session variable
	 *
	 * @static
	 * @param mixed		$res boolean/array query result
	 * @param string	$ok message if all run fine
	 * @param string	$ko error message
	 * @return void
	 */
	public static function set_msg($res, $ok = _MSG_OK, $ko = _MSG_ERROR)
	{
		switch(gettype($res))
		{
			case 'boolean':
				$_SESSION['msg'] = ($res) ? $ok : $ko;
				break;
			case 'array':
				$_SESSION['msg'] = ($res[1] >= 0) ? $ok : $ko;
				break;
		}
	}
	
	/**
	 * Get field's error message from a form and put it into a session variable
	 *
	 * @static
	 * @param array		$fields form array
	 * @param string	$title error message title
	 * @return mixed
	 */
	public static function set_error($fields, $title = '_form_not_valid', $set_session = true)
	{
		$dict = new X4Dict_model(X4Route_core::$folder, X4Route_core::$lang);
		$msg = $dict->get_word($title, 'form');
		$fields = self::normalize_form($fields);
		
		foreach($fields as $i)
		{
			if (isset($i['error']))
			{
				foreach($i['error'] as $ii)
				{
					// set the available label
					$label = ((is_null($i['label']) && isset($i['alabel'])) || isset($i['alabel']))
						? $i['alabel']
						: $i['label'];
						
					// for related fields
					if (isset($i['related'][$i['name']]))
					{
						$src = array('XXXRELATEDXXX');
						$rpl = array();
						
						$related = $i['related'][$i['name']];
						if (isset($fields[$related]))
						{
							// if is a related field
							$rpl[] = ((is_null($fields[$related]['label']) && isset($fields[$related]['alabel'])) || isset($fields[$related]['alabel']))
								? $fields[$related]['alabel']
								: $fields[$related]['label'];
						}
						else
						{
							// if is a related value
							$rpl[] = $related;
						}
						
						if (isset($i['relatedvalue'][$i['name']]))
						{
							$src[] = 'XXXVALUEXXX';
							$rpl[] = $i['relatedvalue'][$i['name']];
						}
									
						$msg .= '<br /><u>'.$label.'</u> '.str_replace($src, $rpl, $dict->get_word($ii, 'form'));
					}
					else
					{
						$msg .= '<br /><u>'.$label.'</u> '.$dict->get_word($ii, 'form');
					}
				}
			}
		}
		
		if ($set_session)
		{
			$_SESSION['msg'] = $msg;
		}
		else
		{
			return $msg;
		}
	}
	
	/**
	 * Get field's error array to use with JS validation
	 *
	 * @static
	 * @param array		$fields form array
	 * @return array
	 */
	public static function get_error($fields)
	{
		$dict = new X4Dict_model(X4Route_core::$folder, X4Route_core::$lang);
		$fields = self::normalize_form($fields);
		
		$error = array();
		foreach($fields as $i)
		{
			if (isset($i['error']))
			{
				foreach($i['error'] as $ii)
				{
					// for related fields
					if (isset($i['related'][$i['name']]))
					{
						$related = $i['related'][$i['name']];
						if (isset($fields[$related]))
						{
							// if is a related field
							$rpl = ((is_null($fields[$related]['label']) && isset($fields[$related]['alabel'])) || isset($fields[$related]['alabel']))
								? $fields[$related]['alabel']
								: $fields[$related]['label'];
						}
						else
						{
							// if is a related value
							$rpl = $related;
						}
									
						$msg = str_replace('XXXRELATEDXXX', $rpl, $dict->get_word($ii, 'form'));
					}
					else
					{
						$msg = $dict->get_word($ii, 'form');
					}
					
					$error[$i['name']] = addslashes(html_entity_decode($msg));
				}
			}
		}
		
		return $error;
	}
	
	
	/**
	 * Return the form array with as index the name of the field
	 *
	 * @static
	 * @param array		$fields form array
	 * @return array
	 */
	public static function normalize_form($array)
	{
		$a = array();
		foreach($array as $i)
		{
			if (isset($i['name']))
			{
				$a[$i['name']] = $i;
			}
		}
		return $a;
	}
	
	/**
	 * Change encoding
	 *
	 * @static
	 * @param string	$text text to convert
	 * @param string	$from_enc original encoding
	 * @return string
	 */
	private static function to7bit($text, $from_enc) {
		$text = mb_convert_encoding($text,'HTML-ENTITIES',$from_enc);
		if (function_exists('preg_replace_callback'))
		{
			$text = preg_replace_callback(
				array(
					'/&(..)lig;/',
					'/&([aouAOUy])uml;/',
					'/&(.)[^;]*;/'
				),
				function($m) 
				{
					if (isset($m[1]))
					{
						return $m[1];
					}
				},
				$text);
		}
		else
		{
			$text = preg_replace(
				array('/&szlig;/','/&(..)lig;/',
					 '/&([aouAOU])uml;/','/&(.)[^;]*;/'),
				array('ss',"$1","$1".'e',"$1"),
				$text);
		}
		return $text;
	} 
	
	/**
	 * Clean a string
	 *
	 * @static
	 * @param string	$str string to clean
	 * @param boolean	$deep If true replace '.' too
	 * @param boolean	$negative If true replace - with _
	 * @return string
	 */
	public static function unspace($str, $deep = false, $negative = false)
	{
		$str = trim($str);
		$str = X4Utils_helper::to7bit($str, 'UTF-8');
		$str = strtolower(html_entity_decode($str));
		
		if (function_exists('preg_replace_callback'))
		{
			// strip special chars
			$str = preg_replace_callback(
			'/[àèéìòùç]+/is',
			function($m) 
			{
				$r = '';
				switch($m[0])
				{
					case 'à':
						$r = 'a';
						break;
					case 'è':
					case 'é':
						$r = 'e';
						break;
					case 'ì':
						$r = 'i';
						break;
					case 'ò':
						$r = 'o';
						break;
					case 'ù':
						$r = 'u';
						break;
					case 'ç':
						$r = 'c';
						break;
				}
				return $r;
			},
			$str);
		
			// clean 
			$regex = ($deep)
				? '/[^a-z0-9-]+/is'
				: '/[^a-z0-9-\/\.]+/is';
		
			$res = preg_replace_callback(
				$regex,
				function($m) 
				{
					return '-';
				},
				$str);
			
			// remove duplicates
			$res = preg_replace_callback(
				'/-(-*)/',
				function($m) 
				{
					return '-';
				},
				$res);
		}
		else
		{
			$str = preg_replace('/[àèéìòùç]+/e', '-', $str);
			// clean 
			$res = ($deep)
			    ? preg_replace('/[^a-z0-9-\.]+/', '-', $str)
			    : preg_replace('/[^a-z0-9-\/\.]+/', '-', $str);
			
			// remove duplicates
			$res = preg_replace('/-(-*)/', '-', $res);
		}
		
		return ($negative)
			? str_replace('-', '_', $res)
			: $res;
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
	public static function module($site, $page, $args, $module, $param = '', $force = false)
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
			return '';
	}
	
	/**
	 * Check if a plugin is installed
	 *
	 * @static
	 * @param string	$id_are Area ID
	 * @param string	$module Plugin name
	 * @return boolean
	 */
	public static function module_exists($id_area, $module)
	{
		$plug = new X4Plugin_model();
		return $plug->exists($module, $id_area, false, 1);
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
		foreach($sections as $i)
		{
			if (!empty($i))
				return false;
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
			$item = array_pop($array[0]);
			$ids = (isset($array[1])) 
				? $array[1] 
				: array();
				
			foreach($array[0] as $i)
			{
				$id = (isset($ids[$i->url])) 
					? '/'.$ids[$i->url] 
					: '';
				$url = ($i->url == 'home') 
					? '' 
					: $i->url;
				if ($home || $i->url != 'home')
				{
					$str .= '<a href="'.BASE_URL.$url.$id.'" title="'.stripslashes($i->description).'">'.stripslashes($i->name).'</a>'.$sep;
				}
			}
			if ($home || $item->url != 'home')
			{
				$str .= stripslashes($item->name);
			}
		}
		return $str;
	}
	
	/**
	 * Format money value
	 *
	 * @static
	 * @param float		$num value
	 * @param string	$value currency
	 * @param integer	$decimal number of decimals
	 * @param boolean	$after switch the position of the currency
	 * @return string
	 */
	public static function currency($num, $value = '&euro;', $decimal = 2, $after = true)
	{
		$res = number_format($num, $decimal, ',', '.');
		
		if ($after) 
		{
			$res .= ' '.$value;
		}
		else
		{
			$res = $value.' '.$res;	
		}
		
		return $res;
	}
	
	/**
	 * URL correction
	 *
	 * @static
	 * @param string	$url URL
	 * @return string
	 */
	public static function check_url($url, $protocol = 'http')
	{
		if (substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://')
		{
			return $url;
		}
		else
		{
			return $protocol.'://'.$url;
		}
	}
	
	/**
	 * Get an integer from an ordinal string
	 * ordinal is a string ok tokens, each token is a 4 char string
	 *
	 * @static
	 * @param string	$ordinal ordinal string
	 * @param integer	$token token index
	 * @return integer
	 */
	public static function get_ordinal_value($ordinal, $token)
	{
		$a = explode('!', chunk_split(substr($ordinal, 1), 3, '!'));
		return base_convert($a[$token], 36, 10);
	}
	
	/**
	 * Convert an associative array to an array of objects
	 *
	 * @static
	 * @param array		$array associative array
	 * @param string	$val key field name
	 * @param string	$opt value field name
	 * @param string	$disabled field to set disabled items
	 * @return array
	 */
	public static function array2obj($array, $val, $opt, $disabled = null)
	{
		$o = array();
		foreach($array as $i) 
		{
			$d = (is_null($disabled))
				? false
				: $i[$disabled];
			
			$o[] = new Obj_opt($i[$val], $i[$opt], $d);
		}
		return $o;
	}
	
	/**
	 * Convert an array of objects to an associative array
	 *
	 * @static
	 * @param array		$array array of objects
	 * @param string	$key key field name
	 * @param string	$value value field name
	 * @return array
	 */
	public static function obj2array($array, $key, $value)
	{
		$a = array();
		if (is_null($key)) 
			foreach($array as $i) 
			{
				$a[] = $i->$value;
			}
		else 
			foreach($array as $i) {
				$a[$i->$key] = $i->$value;
			}
		return $a;
	}
	
	/**
	 * Build a nested menu
	 *
	 * @static
	 * @param string	$ordinal ordinal of visualized page
	 * @param array		$items menu array
	 * @param integer	$deep depth of the origin of the first item
	 * @param string	$key [ALL|RELATED_INSIDE|RELATED_OUTSIDE|NONE]
	 * @param integer	$levels number of levels to display
	 * @param boolean	$arrow display an arrow if submenu
	 * @param string	$ul_attribute first ul attribute
	 * @param boolean	$home display the home link
	 * @return string
	 */
	public static function build_menu($ordinal, $items, $deep = 1, $key = 'NONE', $levels = 1, $arrow = false, $ul_attribute = '', $home = false)
	{
		$menu = '';
		$sub = '';
		$io = 0;
		
		if (is_array($items)) 
		{
			// preset related
			switch($key) 
			{
			case 'RELATED_INSIDE':
			case 'RELATED_OUTSIDE':
				$min = 4 + $deep*4;
				$r = true;
				
				// inside or outside
				$io = 1 + intval($key == 'RELATED_OUTSIDE');
				break;
			case 'ALL':
				$r = true;
				break;
			default:
				$r = false;
				break;
			}
			
			$tmp_deep = $deep - 1;
			foreach($items as $i)
			{
				// check related
				if ($io && $tmp_deep == $deep)
					$r = substr($i->ordinal, 0, $min) == substr($ordinal, 0, $min);
				
				// check if item is in the right range of deep
				if ($i->deep == $deep || ($r && $i->deep < ($deep + $levels))) 
				{
					// check if you need open an ul
					if ($tmp_deep < $i->deep)
					{
						$tmp_deep++;
						// handle inside and outside
						if ($io > 1 && $tmp_deep > $deep)
						{
							$sub .= '<div class="sub"><ul>';
						}
						else
						{
							if ($arrow && $tmp_deep > $deep)
							{
								$menu .= '<span class="arrow"></span>';
							}
							
							// First ul
							$ul_attr = ($tmp_deep == 1 && !empty($ul_attribute))
								? ' '.$ul_attribute
								: '';
								
							// First ul
							$menu .= '<ul '.$ul_attr.'>';
							
							// home
							if ($tmp_deep == 1 && $home)
							{
								$active = ($ordinal == 'A')
									? ' class="active" ' 
									: ' ';
								
								$menu .= '<li '.$active.'><a href="'.BASE_URL.'" title="Home page">Home</a></li>';
							}
						}
					}
					// close opened ul
					else if ($tmp_deep > $i->deep) 
					{
						for($n = $tmp_deep; $n > $i->deep; $n--)
						{
							// handle inside and outside
							if ($io > 1 && $tmp_deep > $deep)
							{
								$sub .= '</li></ul></div>';
							}
							else
							{
								$menu .= '</li></ul>';
							}
							$tmp_deep--;
						}
						$menu .= '</li>';
					}
					// close li
					else 
					{
						// handle inside and outside
						if ($io > 1 && $tmp_deep > $deep)
						{
							$sub .= '</li>';
						}
						else
						{
							$menu .= '</li>';
						}
					}
					
					// detect active pages
					$active = (
						$i->ordinal == $ordinal || // is the active page
						(
							$io && // there are related
							(
								$i->deep == $deep &&	// you are in a nested level
								substr($i->ordinal, 0, $min) == substr($ordinal, 0, $min)) // with the same ordinal prefix
							)
						) 
							? ' class="active" ' 
							: ' ';
					
					// handle inside and outside
					if ($io > 1 && $tmp_deep > $deep)
					{
						$sub .= '<li'.$active.'><a href="'.BASE_URL.$i->url.'" title="'.stripslashes($i->title).'">'.stripslashes($i->name).'</a>';
					}
					else
					{
						$menu .= '<li'.$active.'><a href="'.BASE_URL.$i->url.'" title="'.stripslashes($i->title).'">'.stripslashes($i->name).'</a>';
					}
				}
			}
			
			// close onpened ul
			for($n = $tmp_deep; $n >= $deep; $n--)
			{
				// handle inside and outside
				if ($io > 1 && $tmp_deep > $deep)
				{
					$sub .= '</li></ul></div>';
				}
				else
				{
					$menu .= '</li></ul>';
				}
				$tmp_deep--;
			}
		}
		return $menu.$sub;
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
	public static function build_bootstrap_menu($ordinal, $items, $levels = 1)
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
			
			foreach($items as $i)
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
							$menu .= '<ul class="nav navbar-nav">';
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
                        : ' ';
                
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
                    $menu .= '<li class="dropdown dropdown-submenu"><a  href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'.stripslashes($i->name).' <span class="caret"></span></a>';
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
	 * Change date format (European to MySQL  and vice versa)
	 *
	 * @static
	 * @param string	$date date or datetime string
	 * @param string	$from_sep date separator  
	 * @param string	$to_sep date separator  
	 * @return string
	 */
	public static function change_date($date, $from_sep = '-', $to_sep = '-')
	{
		$dt = explode(' ', $date);
		$t = (isset($dt[1])) ? ' '.$dt[1] : '';
		$d = array_reverse(explode($from_sep, $dt[0]));
		return implode($to_sep, $d).$t;
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
	 * push array
	 *
	 * @static
	 * @param	array	$a First array
	 * @param	array	$to_add Array of array to add
	 * @return	void
	 */
	public static function push_array(&$a, $to_add)
	{
		// add
		foreach($to_add as $i)
		{
			if (is_array($i))
			{
				foreach($i as $ii)
					$a[] = $ii;
			}
			else
			{
				$a[] = $i;
			}
		}
	}
	
	/**
	 * Disable caching
	 *
	 * @return void
	 */
	public static function nocache()
	{
		if (!defined('NOCACHE')) define('NOCACHE', true);
	}
	
	/**
	 * Indicize array, replace index with IDs
	 *
	 * @param	array	$array Array to indicize
	 * @param	string	$index Field to use as index
	 * @return array
	 */
	public static function indicize($array, $index)
	{
		$a = array();
		foreach($array as $i)
		{
			$a[$i->$index] = $i;
		}
		return $a;
	}
	
	/**
	 * Return Client IP address
	 *
	 * @return string
	 */
	public static function get_ip()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} 
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} 
		else 
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}

/**
 * Option object
 *
 * @package		X4WEBAPP
 */
class Obj_opt 
{
	public $value;
	public $option;
	public $disabled;
	
	/**
	 * Constructor
	 * set the folder name
	 *
	 * @param   string $v option value
	 * @param   string $o option name
	 * @return  void
	 */
	public function __construct($v, $o, $d = false)
	{
		$this->value = $v;
		$this->option = $o;
		$this->disabled = $d;
	}
}
