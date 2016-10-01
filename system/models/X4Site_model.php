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
 * Model for Site
 *
 * @package X3CMS
 */
class X4Site_model extends X4Model_core 
{
	/**
	 * @var object	Site object
	 */
	public $site;
	
	/**
	 * @var object	Area object
	 */
	public $area;
	
	/**
	 * @var string	Language code
	 */
	public $lang;
	
	/**
	 * @var int	Time
	 */
	public $now;
	
	/**
	 * Constructor
	 * Initialize site model
	 * 
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct('sites');
		
		// get area
		$this->area = $this->set_data();
		
		// if no language is set by route set area predefined language
		$this->lang = (empty(X4Route_core::$lang)) 
			? $this->area->lang 
			: X4Route_core::$lang;
		
		// set language
		X4Route_core::set_lang($this->lang);
		
		// get site
		$this->site = $this->get_site($this->area->id);
		
		if (!$this->area || !$this->site) 
		{
			header('Location: '.ROOT.'public/msg/message/_page_not_found');
			die;
		}
		
		// set now
		$this->now = time();
		
		// Load site parameters
		$this->to_define();
	}
	
	/**
	 * Get area related object
	 *
	 * @return array
	 */
	private function set_data()
	{
		$sql = 'SELECT SQL_CACHE t.name AS theme, a.id, a.id_theme, a.folder, a.private, l.code AS lang 
			FROM themes t
			JOIN areas a ON a.id_theme = t.id 
			JOIN alang l ON l.id_area = a.id AND l.predefined = 1
			WHERE a.name = '.$this->db->escape(X4Route_core::$area).' AND a.xon = 1'; 
		return $this->db->query_row($sql);
	}
	
	/**
	 * Get site related object
	 *
	 * @param integer	area ID
	 * @return array
	 */
	public function get_site($id_area)
	{
		// check APC
		$c = (APC)
			? apc_fetch(SITE.'site'.$id_area)
			: array();
			
		if (empty($c))
		{
			$c = $this->db->query_row('SELECT SQL_CACHE s.*, l.code, l.title, l.description, l.keywords, l.rtl 
				FROM sites s
				JOIN alang l ON l.code = '.$this->db->escape($this->lang).'
				WHERE s.id = 1 AND l.id_area = '.intval($id_area));
			
			if (APC)
				apc_store(SITE.'site'.$id_area, $c);
		}
		return $c;
	}
	
	/**
	 * Define site parameters
	 *
	 * @return void
	 */
	private function to_define()
	{
		$items = $this->db->query('SELECT SQL_CACHE UPPER(name) AS xkey, xvalue FROM param WHERE xrif = \'site\'');
		foreach($items as $i)
		{
			if (!defined($i->xkey)) define($i->xkey, $i->xvalue);
		}
	}
	
	/**
	 * Build area related object
	 *
	 * @param integer	site ID
	 * @return array	array of objects
	 */
	public function get_param($id_site)
	{
		// check APC
		$c = (APC)
			? apc_fetch(SITE.'param'.$id_site)
			: array();
		
		if (empty($c))
		{
			$c = $this->db->query('SELECT SQL_CACHE pa.*, p.level 
				FROM param pa
				JOIN sites s ON s.id = '.intval($id_site).'
				JOIN privs p ON p.id_who = '.intval($_SESSION['xuid']).' AND p.what = \'sites\' AND p.id_what = s.id
				WHERE pa.xrif = \'site\' AND pa.id_area = 0 ORDER BY pa.id ASC');
			
			if (APC)
				apc_store(SITE.'param'.$id_site, $c);
		}
		return $c;
	}
	
	// CONTENTS
	
	/**
	 * Get page object
	 *
	 * @param string	page URL
	 * @return object
	 */
	public function get_page($method = 'home')
	{
		return $this->db->query_row('SELECT p.*, a.name AS area, a.private
			FROM pages p
			JOIN areas a ON a.id = p.id_area
			WHERE a.id = '.$this->db->escape($this->area->id).' AND 
				p.lang = '.$this->db->escape($this->lang).' AND 
				p.url = '.$this->db->escape($method).' AND
				p.xon = 1');
	}
	
	/**
	 * Get sections
	 *
	 * @param integer	page ID
	 * @return array	array of section objects
	 */
	public function get_sections($id_page)
	{
		// check APC
		$sections = (APC)
			? apc_fetch(SITE.'sections'.$id_page)
			: array();
		
		if (empty($sections))
		{
			if (ADVANCED_EDITING) 
			{
				$sect = $this->db->query('SELECT * FROM sections WHERE id_page = '.intval($id_page).' AND xon = 1 ORDER BY progressive ASC');
				foreach($sect as $i) 
				{
					$s = array();
					// get bids
					$a = explode('|', $i->articles);
					foreach($a as $bid) 
					{
						// get articles
						$t = $this->db->query_row('SELECT * FROM articles WHERE id_area = '.intval($i->id_area).' AND bid = '.$this->db->escape($bid).' AND xon = 1 AND date_in <= '.$this->now.' AND (date_out = 0 OR date_out >= '.$this->now.') ORDER BY id DESC');
						if ($t) 
							$s[] = $t;
					}
					$sections[$i->progressive] = $s;
				}
			}
			else 
			{
				// simple editing
				$sect = $this->db->query_row('SELECT * FROM articles WHERE id_page = '.intval($id_page).' AND xon = 1 AND date_in <= '.$this->now.' AND (date_out = 0 OR date_out >= '.$this->now.') ORDER BY id DESC');
				$sections[1] = ($sect) 
					? array($sect) 
					: array();
			}
			
			if (APC)
				apc_store(SITE.'sections'.$id_page, $sections);
		}
		return $sections;
	}
	
	/**
	 * Get an article by bid
	 *
	 * @param integer	area ID
	 * @param string	lang
	 * @param string	article bid
	 * @return array	array of objects
	 */
	public function get_article_by_bid($id_area, $lang, $bid)
	{
		// check APC
		$c = (APC)
			? apc_fetch(SITE.'abid'.$id_area.$lang.$bid)
			: array();
			
		if (empty($c))
		{
		    $c = $this->db->query_row('SELECT a.* FROM 
					(
					SELECT * 
					FROM articles 
					WHERE 
					    id_area = '.intval($id_area).' AND 
					    lang = '.$this->db->escape($lang).' AND 
					    xon = 1 AND 
					    date_in <= '.$this->now.' AND 
					    (date_out = 0 OR date_out >= '.$this->now.') 
					    ORDER BY updated DESC, id DESC
					) a
				WHERE a.bid = '.$this->db->escape($bid).' 
				ORDER BY a.date_in DESC, a.updated DESC, a.id DESC');
			
			if (APC)
				apc_store(SITE.'abid'.$id_area.$lang.$bid, $c);
		}
		return $c;
	}
	
	/**
	 * Get articles by key
	 *
	 * @param integer	area ID
	 * @param string	lang
	 * @param string	article key
	 * @return array	array of objects
	 */
	public function get_articles_by_key($id_area, $lang, $key)
	{
		return $this->db->query('SELECT a.* FROM 
				(
				SELECT * 
				FROM articles 
				WHERE id_area = '.intval($id_area).' AND lang = '.$this->db->escape($lang).' AND xon = 1 AND date_in <= '.$this->now.' AND (date_out = 0 OR date_out >= '.$this->now.') ORDER BY date_in DESC, updated DESC
				) a
			WHERE a.xkeys = '.$this->db->escape($key).' 
			GROUP BY a.bid
			ORDER BY a.date_in DESC, a.id DESC');
	}
	
	/**
	 * Get articles by contexts
	 *
	 * @param integer	area ID
	 * @param string	lang
	 * @param string	article context
	 * @return array	array of objects
	 */
	public function get_articles_by_context($id_area, $lang, $context)
	{
		return $this->db->query('SELECT a.* FROM 
				(
				SELECT * 
				FROM articles 
				WHERE 
				    id_area = '.intval($id_area).' AND 
				    lang = '.$this->db->escape($lang).' AND 
				    xon = 1 AND 
				    date_in <= '.$this->now.' AND 
				    (date_out = 0 OR date_out >= '.$this->now.') 
				    ORDER BY date_in DESC, updated DESC, id DESC
				) a
			JOIN contexts c ON c.code = a.code_context
			WHERE c.xkey = '.$this->db->escape($context).' 
			GROUP BY a.bid
			ORDER BY a.date_in DESC, a.id DESC');
	}
	
	/**
	 * Get categories by key
	 *
	 * @param integer	area ID
	 * @param string	lang
	 * @param string	article key
	 * @return array	array of objects
	 */
	public function get_categories_by_key($id_area, $lang, $key)
	{
		return $this->db->query('SELECT a.*, c.description AS ctg FROM 
				(
				SELECT * 
				FROM articles 
				WHERE id_area = '.intval($id_area).' AND lang = '.$this->db->escape($lang).' AND xon = 1 AND date_in <= '.$this->now.' AND (date_out = 0 OR date_out >= '.$this->now.') ORDER BY date_in DESC, updated DESC
				) a
			JOIN categories c ON c.id_area = a.id_area AND c.lang = a.lang AND c.xon = 1 AND c.name = a.category
			WHERE a.xkeys = '.$this->db->escape($key).' 
			GROUP BY c.id
			ORDER BY c.name ASC');
	}
	
	/**
	 * Get categories by context
	 *
	 * @param integer	area ID
	 * @param string	lang
	 * @param string	article context
	 * @return array	array of objects
	 */
	public function get_categories_by_context($id_area, $lang, $context)
	{
		return $this->db->query('SELECT a.*, c.description AS ctg FROM 
				(
				SELECT * 
				FROM articles 
				WHERE id_area = '.intval($id_area).' AND lang = '.$this->db->escape($lang).' AND xon = 1 AND date_in <= '.$this->now.' AND (date_out = 0 OR date_out >= '.$this->now.') ORDER BY date_in DESC, updated DESC
				) a
			JOIN categories c ON c.id_area = a.id_area AND c.lang = a.lang AND c.xon = 1 AND c.name = a.category
			JOIN contexts o ON o.xkey = '.$this->db->escape($context).' AND o.id_area = a.id_area AND o.lang = a.lang AND o.xon = 1 AND o.code = a.code_context
			GROUP BY c.id
			ORDER BY c.name ASC');
	}
	
	/**
	 * Get menus by area ID
	 *
	 * @param integer	area ID
	 * @return array	associative array of array of objects
	 */
	public function get_menus($id_area, $maxdeep = MAX_MENU_DEEP) 
	{
		// check APC
		$c = (APC)
			? apc_fetch(SITE.'menu'.$id_area)
			: array();
		
		if (empty($c))
		{
			// privs
			if ($id_area == 1) 
			{
				$level = ', p.level';
				$page_privs = 'INNER JOIN privs p ON p.id_who = '.intval($_SESSION['xuid']).' AND p.what = \'pages\' AND p.id_what = pa.id AND p.level > 0';
			}
			else 
			{
				$level = $page_privs = '';
			}
			
			// get menus
			$sql = 'SELECT m.* 
				FROM menus m 
				WHERE m.id_theme = '.$this->area->id_theme.' AND m.xon = 1';
			$menus = $this->db->query($sql);
			
			// get pages foreach menu
			$c = array();
			foreach($menus as $i)
			{
				$c[$i->name] = $this->db->query('SELECT pa.url, pa.name, pa.title, pa.xfrom, pa.hidden, pa.deep, pa.ordinal '.$level.' 
					FROM pages pa
					'.$page_privs.'
					WHERE pa.id_area = '.intval($id_area).' AND pa.lang = '.$this->db->escape($this->lang).' AND pa.id_menu = '.intval($i->id).' AND pa.xpos > 0 AND pa.xon = 1 AND pa.deep < '.$maxdeep.'
					ORDER BY pa.ordinal ASC');
			}
			
			if (APC)
				apc_store(SITE.'menu'.$id_area, $c);
		}
		return $c;
	}
	
	/**
	 * Get breadcrumb
	 *
	 * @param object	page object
	 * @return array	array of objects
	 */
	public function get_bredcrumb($page)
	{
		// check APC
		$c = (APC)
			? apc_fetch(SITE.'breadcrumb'.$page->id)
			: array();
			
		if (empty($c))
		{
			$c = $this->db->query('SELECT xfrom, url, name, description 
				FROM pages 
				WHERE id_area = '.intval($page->id_area).' AND
					lang = '.$this->db->escape($page->lang).' AND 
					(ordinal = '.$this->db->escape($page->ordinal).' OR
					INSTR('.$this->db->escape(substr($page->ordinal, 0, -4)).', ordinal) > 0) AND
					(LENGTH(ordinal) < LENGTH('.$this->db->escape($page->ordinal).') OR id = '.intval($page->id).')
				ORDER BY ordinal ASC');
			
			if (APC)
				apc_store(SITE.'breadcrumb'.$page->id, $c);
		}
		return $c;
	}
	
	/**
	 * Get area map
	 *
	 * @param object	page object
	 * @param boolean	if true only active pages
	 * @param boolean	if true dont show subpages of disabled pages
	 * @param string	ordinal origin
	 * @return array	array of objects
	 */
	public function get_map($page, $xon = false, $public = true, $ordinal = 'A')
	{
		// check APC
		$c = (APC)
			? apc_fetch(SITE.'map'.$page->lang.$ordinal)
			: array();
		
		if (empty($c))
		{
			if ($xon) 
			{
				$where = ' AND a.xon = 1';
				$pwhere = ' AND p.xon = 1';
			}
			else $where = $pwhere = '';
			
			if ($page->id_area == 1) 
				$hidden = $phidden = $disabled = '';
			else 
			{
				$hidden = ' AND a.hidden = 0';
				$phidden = ' AND p.hidden = 0';
			}
			
			$disabled = ($public) 
				? ' AND (SELECT SUM(p.xon) FROM pages p WHERE p.id_area = a.id_area AND p.lang = a.lang '.$phidden.$pwhere.' AND INSTR(a.ordinal, p.ordinal) > 0) = (a.deep + 1)' 
				: '';
			
			$c = $this->db->query('SELECT a.xfrom, a.url, a.name, a.title, a.description, a.ordinal, a.lang, a.id_menu, a.deep 
				FROM pages a
				WHERE a.id_area = '.intval($page->id_area).' AND 
					a.ordinal LIKE '.$this->db->escape($ordinal.'%').' AND 
					a.lang = '.$this->db->escape($page->lang).' '.$hidden.$where.$disabled.'
				ORDER BY a.ordinal ASC');
			
			if (APC)
				apc_store(SITE.'map'.$page->lang.$ordinal, $c);
		}
		return $c;
	}
	
	/**
	 * Search into pages
	 *
	 * @param integer	area ID
	 * @param array		array of strings
	 * @return array	array of objects
	 */
	public function search($id_area, $array)
	{
		$w_p = $w_c = array();
		foreach ($array as $a) {
			$i = htmlentities($a);
			$w_c[] = ' LOWER(a.content) LIKE '.$this->db->escape('%'.$i.'%');
			$w_p[] = ' (
				LOWER(p.name) LIKE '.$this->db->escape('%'.$i.'%').' OR 
				LOWER(p.title) LIKE '.$this->db->escape('%'.$i.'%').' OR 
				LOWER(p.description) LIKE '.$this->db->escape('%'.$i.'%').'
				) ';
		}
		$where_c = implode(' AND ', $w_c);
		$where_p = implode(' AND ', $w_p);
		
		$sql = 'SELECT p.url, p.name, p.description FROM pages p 
				WHERE p.xon = 1 AND 
					p.id_area = '.$id_area.' AND
					p.lang = '.$this->db->escape($this->lang).' AND 
					(
						'.$where_p.' OR 
						(
							(
								SELECT COUNT(a.id) 
								FROM articles a 
								WHERE p.id = a.id_page AND 
									a.date_in <= '.$this->now.' AND 
									(a.date_out = 0 OR a.date_out >= '.$this->now.') AND 
									a.xon = 1 AND 
									('.$where_c.') 
								GROUP BY a.id_page
								ORDER BY a.id DESC 
							) > 0
						)
					)
				ORDER BY p.url ASC';
		
		return $this->db->query($sql);
	}
	
	/**
	 * Get page URL by plugin name and parameter
	 *
	 * @param integer	area ID
	 * @param string	lang
	 * @param string	plugin name
	 * @param string	parameter value, accepts * wildcard
	 * @return string	page URL
	 */
	public function get_page_to($id_area, $lang, $modname, $param = '')
	{
		// check APC
		$c = (APC)
			? apc_fetch(SITE.'pageto'.$id_area.$lang.$modname.$param)
			: array();
		
		if (empty($c))
		{
			$where = (strstr($param, '*') != '') 
				? '	AND a.param LIKE '.$this->db->escape(str_replace('*', '%', $param))
				: ' AND a.param = '.$this->db->escape($param);
			
			$sql = 'SELECT p.url FROM pages p 
					JOIN articles a ON a.id_page = p.id
					WHERE p.xon = 1 AND 
						p.id_area = '.$id_area.' AND
						p.lang = '.$this->db->escape($lang).' AND
						a.xon = 1 AND 
						a.date_in <= '.$this->now.' AND 
						(a.date_out = 0 OR a.date_out >= '.$this->now.') AND
						a.module = '.$this->db->escape($modname).$where.'
					GROUP BY a.bid
					ORDER BY a.id DESC';
			
			$c = $this->db->query_var($sql);
			
			if (APC)
			{
				apc_store(SITE.'pageto'.$id_area.$lang.$modname.$param, $c);
			}
		}
		return $c;
	}
	
	/**
	 * Get plugin's parameters
	 *
	 * @param string	plugin name
	 * @param integer	area ID
	 * @return array	associative array (parameter name => value)
	 */
	public function get_module_param($plugin_name, $id_area)
	{
		// check APC
		$conf = (APC)
			? apc_fetch(SITE.'mod_param'.$plugin_name.$id_area)
			: array();
			
		if (empty($conf))
		{
		    $res = $this->db->query('SELECT p.name, p.xvalue
				FROM param p
				JOIN modules m ON m.name = p.xrif AND m.id_area = p.id_area
				WHERE p.xrif = '.$this->db->escape($plugin_name).' AND p.id_area = '.intval($id_area).'
				ORDER BY p.id ASC');
			
			foreach($res as $i) 
			{
				$conf[$i->name] = $i->xvalue;
			}
			
			if (APC)
			{
				apc_store(SITE.'mod_param'.$plugin_name.$id_area, $conf);
			}
		}
		return $conf;
	}
	
	/**
	 * Get plugin's single parameter
	 *
	 * @param string	plugin name
	 * @param integer	area ID
	 * @param string	parameter name
	 * @return array	associative array (parameter name => value)
	 */
	public function get_module_param_value($plugin_name, $id_area, $param)
	{
		// check APC
		$value = (APC)
			? apc_fetch(SITE.'mod_param'.$plugin_name.$id_area.$param)
			: '';
			
		if (empty($value))
		{
			$value = $this->db->query_var('SELECT p.xvalue
				FROM param p
				JOIN modules m ON m.name = p.xrif AND m.id_area = p.id_area
				WHERE p.xrif = '.$this->db->escape($plugin_name).' AND p.id_area = '.intval($id_area).' AND p.name = '.$this->db->escape($param));
			
			if (APC)
			{
				apc_store(SITE.'mod_param'.$plugin_name.$id_area.$param, $value);
			}
		}
		return $value;
	}
	
	/**
	 * Get languages related to active area
	 *
	 * @return array	array of objects
	 */
	public function get_alang()
	{
		return $this->db->query('SELECT * FROM alang WHERE id_area = '.intval($this->area->id).' AND xon = 1 ORDER BY language ASC');
	}
}
