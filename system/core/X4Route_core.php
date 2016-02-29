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
 * Router class
 * THIS FILE IS DERIVED FROM KOHANA
 * 
 * @package X4WEBAPP
 */
final class X4Route_core 
{
	// this variables hold route data
	public static $protocol  = 'http';
	public static $lang  = '';
	public static $area = 'public';
	public static $folder = 'public';
	public static $control = 'home';
	public static $method = '_default';
	public static $args = array();
	// GET
	public static $query_string = '';
	// POST
	public static $post = false;
	
	// URI
	public static $uri = '';
	
	/*
	 * Default configuration
	 */
	private static $default = array();
	
	/*
	 * Areas for IDs
	 */
	public static $areas = array(
	    'x3cli' => 0,    // for PHP CLI
            'admin' => 1,
            'public' => 2,
            'private' => 3
        );
	
	/*
	 * Localization options
	 * Add what you need if is missing
	 */
	public static $locales = array(
	    'sf' => 'af_ZA',
	    'ar' => 'ar_SA',
	    'bg' => 'bg_BG',
	    'cs' => 'cs_CZ',
        'da' => 'da_DK',
        'de' => 'de_DE',
        'el' => 'el_GR',
        'en' => 'en_US',
        'fi' => 'fi_FI',
        'fr' => 'fr_FR',
        'hi' => 'hi_IN',
        'hr' => 'hr_HR',
        'id' => 'id_ID',
        'it' => 'it_IT',
        'jp' => 'ja_JP',
        'lo' => 'lo_LA',
        'nl' => 'nl_NL',
        'no' => 'no_NO',
        'ph' => 'ph_PH',
        'pt' => 'pt_PT',
        'ro' => 'ro_RO',
        'ru' => 'ru_RU',
        'sp' => 'es_ES',
        'sv' => 'sv_SE',
        'th' => 'th_TH',
        'tr' => 'tr_TR',
        'uk' => 'uk_UA',
        'vi' => 'vi_VN',
        'zh' => 'zh_CN'
    );
	
	/**
	 * Set the route
	 *
	 * @static
	 * @param   string	request_uri
	 * @param   array	default_config (lang, route)
	 * @return  void
	 */
	public static function set_route($request_uri, $default_config = array())
	{
		// set the URI
		self::$uri = $request_uri;
	    
		// set default
		if (empty(self::$default)) 
		{
			self::$default = $default_config;
		}
		
		// set proptocol
		if (isset($_SERVER['HTTPS']))
		{
			self::$protocol = 'https';
		}
		
		// clean uri string
		$uri_str = (ROOT != '/') 
			? trim(str_replace(ROOT, '', $request_uri), '/')
			: trim($request_uri, '/');
		
		// set default route
		if (empty($uri_str))
		{
			$uri_str = $default_config['x3default_route'];
		}
		
		// for querystring
		$us = explode('?', $uri_str);
		// sanitize
		if (isset($us[1]))
		{
			self::$query_string = $us[1];
		}
		
		// check post
		self::$post = (isset($_POST) && !empty($_POST));
		
		// uri segments array
		self::$args = explode('/', $us[0]);
		
		// check alternative languages
		if (strlen(self::$args[0]) == 2)
		{
			self::$lang = array_shift(self::$args);
			self::set_locale(self::$lang);
		}
		
		// area
		if (!empty(self::$args)) 
		{
			if (is_dir(APATH.'controllers/'.self::$args[0])) 
			{
				// the area has a dedicated folder
				if (self::$args[0] == 'x3cli')
				{
					// check for valid cli call
					if (defined('X3CLI') && php_sapi_name() === 'cli')
					{
						self::$area = self::$folder = array_shift(self::$args);
					}
				}
				else
				{
				    self::$area = self::$folder = array_shift(self::$args);
				}
			}
			elseif (isset($default_config[self::$args[0]]))
			{
				// the area not has a dedicated folder

				// set additional area
				self::$area = array_shift(self::$args);

				// set the folder				
				self::$folder = $default_config[self::$area];

				// add additional area to the areas array
				self::$areas[self::$area] = $default_config[self::$area.'_id'];
			}
		}
		
		// controller
		if (!empty(self::$args)) 
		{
			self::$control = array_shift(self::$args);
		}
		
		// method
		if (!empty(self::$args))
		{
			self::$method = array_shift(self::$args);
		}
		
		// home
		if (empty(self::$control)) 
		{
			self::$method = self::$control = 'home';
		}
	}
	
	/**
	 * set the lang
	 *
	 * @static
	 * @param   string	code lang
	 * @return  void
	 */ 
	public static function set_lang($code)
	{
		if (empty(self::$lang)) 
		{
			self::$lang = $code;
			self::set_locale(self::$lang);
		}
	}
	
	/**
	 * set the localization
	 *
	 * @static
	 * @param   string	code lang
	 * @return  void
	 */ 
	public static function set_locale($code)
	{
		setlocale(LC_ALL, self::$locales[$code].'.UTF-8');
	}
	
	/**
	 * get id_area
	 *
	 * @static
	 * @return  integer
	 */ 
	public static function get_id_area()
	{
		return self::$areas[X4Route_core::$area];
	}
	
	/**
	 * get query string
	 *
	 * @static
	 * @return  array
	 */ 
	public static function get_query_string()
	{
		if (empty(self::$query_string))
			return array();
		else
		{
			$a = array();
			$items = explode('&amp;', htmlentities(strip_tags(urldecode(self::$query_string)), ENT_QUOTES, 'UTF-8', false));
			
			foreach($items as $i)
			{
				if (!empty($i))
				{
					$tok = explode('=', $i);
					// is a multiselect?
					$ms = substr($tok[0], 0, -2);
					if ($tok[0] == $ms.'[]')
					{
						// is a multiselect, get an array
						$a[$ms] = $_GET[$ms];
					}
					else
					{
						$a[$tok[0]] = trim($tok[1]);
					}
				}
			}
			return $a;
		}
	}
	
	/**
	 * get the route
	 *
	 * @static
	 * @return  string
	 */ 
	public static function get_route()
	{
	    $param = (self::$args[0] == '_default') 
			? '' 
			: '/'.implode('/', self::$args);
			
		$area = (self::$area == 'public') 
			? '' 
			: self::$area.'/';
			
		return self::$lang.'/'.$area.self::$control.'/'.self::$method.$param;
	}
	
	/**
	 * redirect to new route
	 *
	 * @static
	 * @param   array	URL Route 
	 * @return  void
	 */ 
	public static function redirect($route)
	{
		$old_route = self::get_route();
		// replace route items
		foreach($route as $k => $v)
		{
			switch ($k)
			{
			case 'args':
				self::$args = explode('/', $v);
				break;
			default:
				//$what = $$k;
				self::$$k = $v;
				break;
			}
		}
		// avoids loop
		$new_route = self::get_route();
		if ($new_route != $old_route)
		{
			// redirect
			header('Location: '.self::get_route());
		}
		else
		{
			header('Location: '.BASE_URL.'msg/message/_page_not_found');
		}
		die;
	}
	
	/**
	 * get the URI
	 *
	 * @static
	 * @param   boolean $query_string 
	 * @return  string
	 */ 
	public static function get_uri($query_string = true)
	{
	    $uri = ($query_string)
	        ? self::$uri
	        : str_replace('?'.self::$query_string, '', self::$uri);
	        
		return self::$protocol.'://'.$_SERVER['SERVER_NAME'].$uri;
	}
	
	/**
	 * get controller path
	 *
	 * @static
	 * @return  string
	 */ 
	public static function controller_path()
	{
		$folder = str_replace('-', '_', self::$folder);
		$control = str_replace('-', '_', self::$control);
		
		if (file_exists(APATH.'controllers/'.$folder.'/'.$control.'_controller'.EXT)) 
		{
			// app controller
			return APATH.'controllers/'.$folder.'/'.$control.'_controller'.EXT;
		}
		elseif (file_exists(PATH.'plugins/'.$control.'/controllers/'.$control.'_controller'.EXT)) 
		{
			// plugin controller
			return PATH.'plugins/'.$control.'/controllers/'.$control.'_controller'.EXT;
		}
		else 
		{
			// x4page generic controller
			array_unshift(self::$args, self::$method);
			self::$method = self::$control;
			return SPATH.'controllers/X4Page_controller'.EXT;
		}
	}

}	// End X4Route class
