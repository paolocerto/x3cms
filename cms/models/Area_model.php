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
 * Model for Area Items
 *
 * @package X3CMS
 */
class Area_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 */
	public function __construct($db = 'default')
	{
		parent::__construct('areas', $db);
	}

	/**
	 * Get area data and default language code
	 * Join with alang
	 */
	public function get_area_data(int $id_area) : stdClass
	{
		return $this->db->query_row('SELECT a.*, l.code
			FROM areas a
			JOIN alang l ON a.id = l.id_area AND l.xdefault = 1
			WHERE a.id = '.$id_area);
	}

	/**
	 * Get area ID by area name
	 */
	public function get_area_id(string $area_name) : int
	{
		return (int) $this->db->query_var('SELECT id FROM areas WHERE name = '.$this->db->escape($area_name));
	}

	/**
	 * Get areas data as an array
	 * Join with privs table
	 */
	public function get_areas(int $id_site = 1, int $id_area = 1, string $which = '') : array
	{
        // condition to get all areas or only one
		$where = ($id_site)
            ? ' AND (a.id_site = 0 OR a.id_site = '.$id_site.')'
            : '';

		// condition to get all areas or only one
		$where .= (empty($which) || $id_area == 1)
				? ''
				: ' AND a.id = '.$id_area;

		switch($which)
		{
			case 'public':
				$where .= ' AND a.private = 0';
				break;
			case 'private':
				$where .= ' AND a.private = 1';
				break;
		}

        $sql = 'SELECT a.*, u.level
            FROM areas a
			JOIN aprivs p ON p.id_area = a.id
            JOIN uprivs u ON u.id_area = a.id AND u.id_user = p.id_user AND u.privtype = '.$this->db->escape('areas').'
			WHERE p.id_user = '.intval($_SESSION['xuid']).' '.$where.'
			GROUP BY a.id
            ORDER BY a.id ASC';

		return $this->db->query($sql);
	}

    /**
	 * Get domains
	 */
	public function get_domains() : array
	{
		return $this->db->query('SELECT id, domain FROM sites ORDER BY domain ASC');
	}

    /**
	 * Reset xdefault
	 */
	public function reset_xdefault(int $id_site, int $id_area) : array
	{
		$sql = 'UPDATE areas
            SET xdefault = 0
            WHERE id_site = '.$id_site.' AND id != '.$id_area;

		return $this->db->single_exec($sql);
	}

	/**
	 * Get areas data as an array
	 * Join with privs table
	 */
	public function get_areas_for_users(int $id_area = 1) : array
	{
		$sql = 'SELECT a.*, 4 AS level
            FROM areas a
            ORDER BY a.id ASC';

		return $this->db->query($sql);
	}

    /**
	 * Get sites
	 * Join with privs table
	 */
	public function get_my_sites() : array
	{
	    $sql = 'SELECT s.*, IF(p.id IS NULL, u.level, p.level) AS plevel
            FROM sites s
			JOIN uprivs u ON u.privtype = '.$this->db->escape('sites').'
            LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = u.id
			WHERE u.id_user = '.intval($_SESSION['xuid']).'
			GROUP BY s.id
            ORDER BY s.id ASC';

		return $this->db->query($sql);
	}

	/**
	 * Get areas data as an array
	 */
	public function get_my_areas(
        int $id_site,
        int $id_area = 0,
        string $which = ''  // empty is the default, other options are public|private
    ) : array
	{
	    $items = X4Array_helper::indicize($this->get_areas($id_site, 1, $which, 1), 'id');

	    // check user group
	    $id_group = $this->get_var($_SESSION['xuid'], 'users', 'id_group');

	    if ($id_group > 1 && isset($items[1]))
	    {
		    // remove admin area
	        unset($items[1]);
	    }

	    // reset id_area if user doesn't have permissions
	    if ($id_area && !isset($items[$id_area]))
	    {
	        reset($items);
	        $id_area = key($items);
	    }
	    return array($id_area, $items);
	}

	/**
	 * Create a tmp file where store extra areas data
	 */
	public function extra_areas() : void
	{
		$items = $this->db->query('SELECT id, name, private FROM areas WHERE id > 3 AND xon = 1 ORDER BY id ASC');

		$a = array();
		foreach ($items as $i)
		{
		    $a[$i->name] = ($i->private)
		        ? 'private'
		        : 'public';

		    $a[$i->name.'_id'] = $i->id;
		}

		$file = json_encode($a);
        // create dir if not exists
        if (!is_dir(APATH.'files/'.SECRET))
        {
            mkdir(APATH.'files/'.SECRET, 0777);
        }
		file_put_contents(APATH.'files/'.SECRET.'/'.SECRET.'.txt', $file);
	}

	/**
	 * Get user's permissions over areas
	 */
	public function get_aprivs_id() : array
	{
		$sql = 'SELECT id_area, area
			FROM aprivs
			WHERE id_user = '.intval($_SESSION['xuid']).'
			ORDER BY id ASC';

		return $this->db->query($sql);
	}

	/**
	 * Get the default template of the area
	 */
	public function get_default_template(int $id_area) : int
	{
		$sql = 'SELECT t.id
			FROM templates t
			JOIN themes th ON th.id = t.id_theme
			JOIN areas a ON a.id_theme = th.id AND a.id = '.$id_area.'
			WHERE t.xon = 1
			ORDER BY t.id ASC';

		return (int) $this->db->query_var($sql);
	}

	/**
	 * Get controller's folders
	 * This information is needed when create a new area
	 */
	public function get_folders() : array
	{
		// get all controller's folders
		$folders = glob(APATH.'/controllers/*', GLOB_ONLYDIR);

		$a = array();
		foreach ($folders as $i)
		{
			$tmp = preg_replace_callback('/(.*)\/(.*)/is',
					function($m)
					{
						return $m[2];
					},
					$i);

			// exclude admin folder and x3cli
			if ($tmp != 'admin' && $tmp != 'x3cli')
			{
				$a[] = new Obj_folder($tmp);
			}
		}
		return $a;
	}

	/**
	 * Check if an area name already exists
	 */
	public function exists(string $area, int $id = 0) : int
	{
		// condition
		$where = ($id)
			? ' AND id <> '.$id		// if is an update
			: '';

		$sql = 'SELECT COUNT(*)
			FROM areas
			WHERE name = '.$this->db->escape($area).' '.$where;

		return (int) $this->db->query_var($sql);
	}

	/**
	 * Delete area
	 *
	 */
	public function delete_area(int $id, string $area_name) : array
	{
		$sql = array();

		// alang
		$sql[] = 'DELETE FROM alang WHERE id_area = '.$id;
		// aprivs
		$sql[] = 'DELETE FROM aprivs WHERE id_area = '.$id;
		// articles
		$sql[] = 'DELETE FROM articles WHERE id_area = '.$id;
		// categories
		$sql[] = 'DELETE FROM categories WHERE id_area = '.$id;
		// contexts
		$sql[] = 'DELETE FROM contexts WHERE id_area = '.$id;
		// dictionary
		$sql[] = 'DELETE FROM dictionary WHERE area = '.$this->db->escape($area_name);
		// files
		$sql[] = 'DELETE FROM files WHERE id_area = '.$id;
		// groups
		$sql[] = 'DELETE FROM xgroups WHERE id_area = '.$id;
		// modules
		$sql[] = 'DELETE FROM modules WHERE id_area = '.$id;
		// pages
		$sql[] = 'DELETE FROM pages WHERE id_area = '.$id;
		// param
		$sql[] = 'DELETE FROM param WHERE id_area = '.$id;
		// privs
		$sql[] = 'DELETE FROM privs WHERE id_area = '.$id;
		// sections
		$sql[] = 'DELETE FROM sections WHERE id_area = '.$id;
		// uprivs
		$sql[] = 'DELETE FROM uprivs WHERE id_area = '.$id;
		// users (with groups)
		// TO DO
		// widgets
		$sql[] = 'DELETE FROM widgets WHERE id_area = '.$id;

		// areas
		$sql[] = 'DELETE FROM areas WHERE id = '.$id;

		return $this->db->multi_exec($sql);
	}

	/**
	 * Rename area
     * SECRET method not linked in admin
	 */
	public function rename_area(int $id_area, string $old, string $new) : array
	{
		$sql = array();

		// aprivs
		$sql[] = 'UPDATE aprivs SET area = '.$this->db->escape($new).' WHERE id_area = '.$id_area;

		// areas
		$sql[] = 'UPDATE areas SET name = '.$this->db->escape($new).' WHERE id = '.$id_area;

		// dictionary
		$sql[] = 'UPDATE dictionary SET area = '.$this->db->escape($new).' WHERE area = '.$this->db->escape($old);

		return $this->db->multi_exec($sql);
	}

}

/**
 * Empty Area object
 * Necessary for the creation form of new area
 *
 * @package X3CMS
 */
class Area_obj
{
	// object vars
	public $name = '';
	public $title = '';
	public $description = '';
	public $lang = '';
	public $code = '';
	public $old_id_theme = 0;
    public $id_site = 0;
	public $id_theme = 0;
	public $private = 0;
    public $xdefault = 0;
	public $folder = '';
    public $xlock = 0;

    public function __construct(string $folder = '')
	{
		$this->folder = $folder;
	}
}

/**
 * Folder object
 * Necessary for the creation form of new area
 *
 * @package X3CMS
 */
class Obj_folder
{
	// folder name
	public $folder;

	/**
	 * Constructor
	 */
	public function __construct(string $folder)
	{
		$this->folder = $folder;
	}
}
