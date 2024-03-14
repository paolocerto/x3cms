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
 * Model for Menu Items
 *
 * @package X3CMS
 */
class Menu_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 */
	public function __construct()
	{
		parent::__construct('menus');
	}

	/**
	 * Get menus
	 * Each area have an associate theme
	 * Each theme have a set of menus
	 */
	public function get_menus(int $id_area = 0, string $area = '', string $order = 'name') : array
	{
		// condition
		$where = '';
		if ($id_area) $where .= ' AND a.id = '.$id_area;
		if (!empty($area)) $where .= ' AND a.name = '.$this->db->escape($area);

		$sql = 'SELECT DISTINCT m.*, t.name AS theme, IF(p.id IS NULL, u.level, p.level) AS level
				FROM menus m
				JOIN themes t ON t.id = m.id_theme
				JOIN areas a ON a.id_theme = t.id
				JOIN uprivs u ON u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('menus').'
				LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = m.id
				WHERE m.id > 0 '.$where.'
				GROUP BY m.id
				ORDER BY m.'.$order.' ASC';
		return $this->db->query($sql);
	}

	/**
	 * Get menus by themes
	 */
	public function get_menus_by_theme(int $id_theme) : array
    {
		$sql = 'SELECT DISTINCT m.*, t.name AS theme, IF(p.id IS NULL, u.level, p.level) AS level
				FROM menus m
				JOIN themes t ON t.id = m.id_theme
				JOIN uprivs u ON u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('menus').'
				LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = m.id
				WHERE m.id > 0 AND t.id = '.$id_theme.'
				ORDER BY m.name ASC';
		return $this->db->query($sql);
	}

	/**
	 * Reset pages data: tpl, css, ordinal, id_menu
	 * Called when you change the theme of an area
	 */
	public function reset(int $id_area) : array
	{
		$sql = 'UPDATE pages
			SET tpl = \'base\', css = \'base\', ordinal = \'A\', id_menu = 0
			WHERE id_area = '.$id_area;

		return $this->db->single_exec($sql);
	}

	/**
	 * Ordinalize pages (recursive)
	 * to accelerate recovery when data needs to build structured items (like menu, breadcrumbs, site map)
	 * X3 use the ordinal field that consists of a string that identifies the location of a page in a given area and language
	 */
	public function ordinal(
        int $id_area,
        string $lang,
        string $xfrom,      // Parent URL
        string $base        // The ordinal of the parent URL
    ) : void
	{
		// get page children of xfrom
		$pages = $this->db->query('SELECT * FROM pages
			WHERE
				id_area = '.$id_area.' AND
				lang = '.$this->db->escape($lang).' AND
				xfrom = '.$this->db->escape($xfrom).' AND url <> \'home\'
			ORDER BY id_menu ASC');

		$sql = array();
		if ($pages)
		{
			// get deep
			$deep = ($xfrom == 'home')
				? 0
				: $this->db->query_var('SELECT deep FROM pages WHERE id_area = '.$id_area.' AND lang = '.$this->db->escape($lang).' AND url = '.$this->db->escape($xfrom));

			// refresh xpos and deep on the pages concerned
			$this->deeper($id_area, $lang, 'home', $deep);

			foreach ($pages as $i)
			{
				// exclude home page
				if ($i->url != $xfrom)
				{
					// build base token
					// is a four chars value: the first is a marker (no menu/in menu), other store position up to 46656
					$pos = intval($i->id_menu > 0).str_pad(base_convert(strval($i->xpos), 10, 36), 3, '0', STR_PAD_LEFT);

                    // menu token
					$menu = ($i->deep == 1)
                    ? str_pad(base_convert(strval($i->id_menu), 10, 36), 3, '0', STR_PAD_LEFT)
                    : '';

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
	 */
	private function deeper(int $id_area, string $lang, string $xfrom, int $deep) : void
	{
		// get pages children of xfrom with the same Area ID and Language code
		$pages = $this->db->query('SELECT id, url, id_menu, xpos
				FROM pages
				WHERE
					id_area = '.$id_area.' AND
					lang = '.$this->db->escape($lang).' AND
					xfrom = '.$this->db->escape($xfrom).'
				ORDER BY xpos ASC');

		// set deep
		$deep = ($xfrom == 'home')
			? 1
			: $deep++;

		$sql = $pos = array();
		foreach ($pages as $i)
		{
			// initialize pos counter
			if (!isset($pos[$i->id_menu]))
            {
				$pos[$i->id_menu] = 1;
            }
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
					$max = $this->db->query_var('SELECT MAX(xpos) FROM pages WHERE id_menu = '.$i->id_menu.' AND id_area = '.$id_area.' AND lang = '.$this->db->escape($lang).' AND xfrom = '.$this->db->escape($xfrom));
					$max++;
					$xpos = $max;
				}

				$sql[] = 'UPDATE pages SET deep = '.$deep.', xpos = '.$xpos.' WHERE id = '.$i->id;
			}
		}
		$this->db->multi_exec($sql);
	}

	/**
	 * Get subpages as a menu
	 */
	public function get_subpages(int $id_area, string $ordinal, int $maxdeep = MAX_MENU_DEEP, int $home = 0) : array
	{
		$where = ($home)
			? ''
			: ' ordinal != '.$this->db->escape($ordinal).' AND ';

	    return $this->db->query('SELECT url, name, title, xfrom, hidden, deep, ordinal
            FROM pages
            WHERE
                id_area = '.$id_area.' AND
		'.$where.'
                ordinal LIKE '.$this->db->escape($ordinal.'%').' AND
                hidden = 0 AND
                xon = 1 AND
                deep < '.$maxdeep.'
            ORDER BY ordinal ASC');
	}

    /**
	 * Get languages in area
	 */
	public function get_languages(int $id_area) : array
	{
		return $this->db->query('SELECT code
            FROM alang
            WHERE id_area = '.$id_area);
	}

	/**
	 * Get ordinal of a page by url
	 */
	public function get_ordinal_by_url(int $id_area, string $lang, string $url) : string
	{
		return (string) $this->db->query_var('SELECT ordinal
            FROM pages
            WHERE
                id_area = '.$id_area.' AND
                lang = '.$this->db->escape($lang).' AND
                url = '.$this->db->escape($url));
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
    public $xlock = 0;

	/**
	 * Constructor
	 */
	public function __construct(int $id_theme)
	{
		$this->id_theme = $id_theme;
	}
}
