<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

/**
 * Model for Menu Items
 *
 * @package X3CMS
 */
class Menu_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('menus');
	}
	
	/**
	 * Get menus
	 * Join with themes, areas and privs tables
	 * Each area have an associate theme
	 * Each theme have a set of menus
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$area Area name
	 * @param   string	$order sort order
	 * @return  array	array of objects
	 */
	public function get_menus($id_area = 0, $area = '', $order = 'name') 
	{
		// condition
		$where = '';
		if ($id_area) $where .= ' AND a.id = '.intval($id_area);
		if (!empty($area)) $where .= ' AND a.name = '.$this->db->escape($area);
		
		$sql = 'SELECT DISTINCT m.*, t.name AS theme, p.level
				FROM menus m 
				JOIN themes t ON t.id = m.id_theme
				JOIN areas a ON a.id_theme = t.id
				JOIN privs p ON p.id_who = '.intval($_SESSION['xuid']).' AND p.what = \'menus\' AND p.id_what = m.id
				WHERE m.id > 0 '.$where.' 
				ORDER BY m.'.$order.' ASC';
		return $this->db->query($sql);
	}
	
	/**
	 * Get menus by themes
	 * Join with themes and privs tables
	 *
	 * @param   integer $id_theme Theme ID
	 * @return  array	array of objects
	 */
	public function get_menus_by_theme($id_theme) {
		$sql = 'SELECT DISTINCT m.*, t.name AS theme, p.level
				FROM menus m 
				JOIN themes t ON t.id = m.id_theme
				JOIN privs p ON p.id_who = '.intval($_SESSION['xuid']).' AND p.what = \'menus\' AND p.id_what = m.id
				WHERE m.id > 0 AND t.id = '.intval($id_theme).' 
				ORDER BY m.name ASC';
		return $this->db->query($sql);
	}
	
	/**
	 * Update references to the menu in the table pages
	 * Use pages table
	 *
	 * @param   integer $id Page ID
	 * @param   integer $holder Menu ID
	 * @param   string	$orders Encoded string, for each menu you have a section, each section contains the list of Page ID in menu
	 * @return  array	array of objects
	 */
	public function menu($id, $holder, $orders)
	{
		// get page data 
		$page = $this->get_by_id($id, 'pages', 'id_area, lang, id_menu');
		
		$sql = array();
		
		// check  holder
		if ($page->id_menu != $holder) 
			$sql[] = 'UPDATE pages SET id_menu = '.$holder.' WHERE id = '.intval($id);
		
		// refresh order
		$menus = explode('_', $orders);
		foreach ($menus as $i)
		{
			$c = 1;
			if (!empty($i)) 
			{
				$el = explode('-', $i);
				// get array of Page ID in the menu
				$items = explode(',', $el[1]);
				if (!empty($items)) 
				{
					foreach ($items as $ii)
					{
						$sql[] = 'UPDATE pages SET xpos = '.$c.' WHERE id = '.intval($ii);
						$c++;
					}
				}
			}
		}
		
		// perform the update
		$result = $this->db->multi_exec($sql);
		APC && apc_delete(SITE.'menu'.$page->id_area);
		
		// refresh ordinals
		$this->ordinal($page->id_area, $page->lang, 'home', 'A');
		
		return $result;
	}
	
	/**
	 * Reset pages data: tpl, css, ordinal, id_menu
	 * Use pages table
	 * Called when you change the theme of an area
	 *
	 * @param   integer $id_area Area ID
	 * @return  array
	 */
	public function reset($id_area)
	{
		$sql = 'UPDATE pages 
			SET tpl = \'base\', css = \'base\', ordinal = \'A\', id_menu = 0 
			WHERE id_area = '.intval($id_area);
			
		return $this->db->single_exec($sql);
	}
	
	/**
	 * Ordinalize pages (recursive)
	 * Use pages table
	 * to accelerate recovery when data needs to build structured items (like menu, breadcrumbs, site map)
	 * X3 use the ordinal field that consists of a string that identifies the location of a page in a given area and language
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   string	$xfrom Parent URL
	 * @param   string	$base The ordinal of the parent URL
	 * @return  array
	 */
	public function ordinal($id_area, $lang, $xfrom, $base)
	{
		// get page children of xfrom
		$pages = $this->db->query('SELECT * FROM pages 
			WHERE 
				id_area = '.$this->db->escape($id_area).' AND 
				lang = '.$this->db->escape($lang).' AND
				xfrom = '.$this->db->escape($xfrom).' AND url <> \'home\'
			ORDER BY id_menu ASC');
		
		$sql = array();
		if ($pages) 
		{
			// get deep
			$deep = ($xfrom == 'home') 
				? 0 
				: $this->db->query_var('SELECT deep FROM pages WHERE id_area = '.intval($id_area).' AND lang = '.$this->db->escape($lang).' AND url = '.$this->db->escape($xfrom));
			
			// refresh xpos and deep on the pages concerned
			$this->deeper($id_area, $lang, 'home', $deep);
			
			foreach($pages as $i)
			{
				// exclude home page
				if ($i->url != $xfrom) 
				{
					$menu = ($i->deep == 1) ? str_pad(base_convert(strval($i->id_menu), 10, 36), 3, '0', STR_PAD_LEFT) : ''; 
					
					// build base token
					// is a four chars value: the first is a marker (no menu/in menu), other store position up to 46656
					$pos = intval($i->id_menu > 0).str_pad(base_convert(strval($i->xpos), 10, 36), 3, '0', STR_PAD_LEFT);
					
					// update query
					$sql[] = 'UPDATE pages SET ordinal = '.$this->db->escape($base.$menu.$pos).' WHERE id = '.$i->id;
					
					// recursive call
					$this->ordinal($id_area, $lang, $i->url, $base.$menu.$pos);
				}
			}
			$this->db->multi_exec($sql);
		}
	}
	
	/**
	 * Refresh recursively deep and xpos value
	 * Use pages table
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   string	$xfrom Page URL
	 * @param   integer $deep Page depth in the area tree
	 * @return  array
	 */
	private function deeper($id_area, $lang, $xfrom, $deep)
	{
		// get pages children of xfrom with the same Area ID and Language code
		$pages = $this->db->query('SELECT id, url, id_menu, xpos 
				FROM pages 
				WHERE 
					id_area = '.intval($id_area).' AND 
					lang = '.$this->db->escape($lang).' AND
					xfrom = '.$this->db->escape($xfrom).'
				ORDER BY xpos ASC');
		
		// set deep
		$deep = ($xfrom == 'home') 
			? 1 
			: $deep++;
		
		$sql = $pos = array();
		foreach($pages as $i) 
		{
			// initialize pos counter
			if (!isset($pos[$i->id_menu])) 
				$pos[$i->id_menu] = 1;
			
			// only if page is not the home page
			if ($i->url != 'home') 
			{
				if($i->xpos) 
				{
					// a page already ordinalized
					$xpos = $pos[$i->id_menu];
					$pos[$i->id_menu]++;
				}
				else {
					// a new page
					$max = $this->db->query_var('SELECT MAX(xpos) FROM pages WHERE id_menu = '.intval($i->id_menu).' AND id_area = '.intval($id_area).' AND lang = '.$this->db->escape($lang).' AND xfrom = '.$this->db->escape($xfrom));
					$max++;
					$xpos = $max;
				}
				
				$sql[] = 'UPDATE pages SET deep = '.intval($deep).', xpos = '.$xpos.' WHERE id = '.$i->id;
			}
		}
		
		$this->db->multi_exec($sql);
	}
	
	/**
	 * Get subpages as a menu
	 *
	 * @param integer	area ID
	 * @param string	ordinal
	 * @return array	associative array of array of objects
	 */
	public function get_subpages($id_area, $ordinal, $maxdeep = MAX_MENU_DEEP) 
	{
	    return $this->db->query('SELECT url, name, title, xfrom, hidden, deep, ordinal 
            FROM pages
            WHERE 
                id_area = '.intval($id_area).' AND 
                ordinal != '.$this->db->escape($ordinal).' AND
                ordinal LIKE '.$this->db->escape($ordinal.'%').' AND
                hidden = 0 AND
                xon = 1 AND 
                deep < '.$maxdeep.'
            ORDER BY ordinal ASC');
	}

}

/**
 * Empty Menu object
 * Necessary for the creation form of new menu
 *
 * @package X3CMS
 */
class Menu_obj 
{
	// object vars
	public $id_theme;
	public $name;
	public $description;
	
	/**
	 * Constructor
	 *
	 * @param   integer	$id_theme Theme ID
	 * @return  void
	 */
	public function __construct($id_theme) 
	{
		$this->id_theme = $id_theme;
	}
}
