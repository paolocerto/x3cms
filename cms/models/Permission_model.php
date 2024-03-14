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
 * Model for Permissions
 *
 * @package X3CMS
 */
class Permission_model extends X4Model_core
{
	/**
	 * admin related privtypes
	 */
	protected $admin_privtypes = array(
		'_group_creation',
		'_user_creation',
		'xgroups',
		'areas',
        'languages',
		'menus',
		'privs',
		'sites',
		'themes',
		'templates',
		'users',
	);

    /**
	 * super admin related privtypes
     * Set restricted access for creation and deletion
	 */
	protected $superadmin_privtypes = array(
		'_area_creation',
		'_language_creation',
		'_menu_creation',
		'_module_install',
        '_site_creation',
		'_template_install',
        '_theme_install',
        'areas',
        'languages',
        'menus',
        'module',
        'sites',
        'templates',
        'themes',
	);

	/**
	 * tables to not set privs
	 */
	protected $no_privs = array(
        'debug',
		'logs',
        'privs',
	);

	/**
	 * Constructor: set reference table
	 */
	public function __construct(string $db = 'default')
	{
		// set default table
		parent::__construct('privs', $db);
	}

	/**
	 * Get priv id
	 */
	private function get_id(int $id_area, int $id_user, string $what, int $id_what) : int
	{
		return (int) $this->db->query_var('SELECT IF (p.id IS NULL, 0, p.id) AS id
			FROM uprivs u
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.id_area = u.id_area AND p.what = u.privtype
			WHERE u.id_user = '.$id_user.' AND u.id_area = '.$id_area.' AND u.privtype = '.$this->db->escape($what).' AND p.id_what = '.$id_what);
	}

	/**
	 * Get user priv on an item
	 */
	public function get_priv(int $id_area, int $id_user, string $what, int $id_what) : stdClass
	{
		return $this->db->query_row('SELECT IF(p.id IS NULL, 0, p.id) AS id, IF(p.id IS NULL, u.level, p.level) AS level
			FROM uprivs u
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.id_area = u.id_area AND p.what = u.privtype
			WHERE u.id_user = '.$id_user.' AND u.id_area = '.$id_area.' AND u.privtype = '.$this->db->escape($what).' AND p.id_what = '.$id_what);
	}

	/**
	 * Get user permission's level on an item
	 */
	public function check_priv(int $id_user, string $what, int $id_what, int $id_area = 0) : int
	{
        $where = $id_area
            ? 'u.id_area = '.$id_area.' AND'
            : '';

        if ($id_what)
        {
            return (int) $this->db->query_var('SELECT IF(p.id IS NULL, u.level, p.level) AS level
                FROM uprivs u
                LEFT JOIN privs p ON p.id_who = u.id_user AND p.id_area = u.id_area AND p.what = u.privtype AND p.id_what = '.$id_what.'
                WHERE '.$where.' u.id_user = '.$id_user.' AND u.privtype = '.$this->db->escape($what));
        }
        else
        {
            // for new items we check only on user privs
            return (int) $this->db->query_var('SELECT level
                FROM uprivs
                WHERE id_area = '.$id_area.' AND id_user = '.$id_user.' AND privtype = '.$this->db->escape($what));
        }
	}

	/**
	 * Get user priv on a table
	 */
	public function get_upriv(int $id_area, int $id_user, string $what) : stdClass
	{
		return $this->db->query_row('SELECT id, level FROM uprivs WHERE id_area = '.$id_area.' AND id_user = '.$id_user.' AND privtype = '.$this->db->escape($what));
	}

	/**
	 * Get permission level names
	 */
	public function get_levels() : array
	{
        $max_level = isset($_SESSION['level'])
            ? $_SESSION['level']
            : 4;

		return $this->db->query('SELECT * FROM levels WHERE id <= '.intval($max_level).' ORDER BY id ASC');
	}

	/**
	 * Get in which areas user can do something
	 */
	public function get_aprivs(int $id_user) : array
	{
		return $this->db->query('SELECT id_area, area FROM aprivs WHERE id_user = '.$id_user.' ORDER BY id_area ASC');
	}

	/**
	 * Set in which areas user can do something
	 */
	public function set_aprivs(int $id_user, array $ids_area) : array
	{
		$sql = array();

		// delete old aprivs
		$sql[] = 'DELETE FROM aprivs WHERE id_user = '.$id_user;
		foreach ($ids_area as $i)
		{
			// get area data
			$a = $this->get_by_id($i, 'areas', 'name');

			$sql[] = 'INSERT INTO aprivs
                    (updated, id_user, id_area, area, xon)
                VALUES
                    (NOW(), '.$id_user.', '.intval($i).', '.$this->db->escape($a->name).', 1)';
		}
		return $this->db->multi_exec($sql);
	}

	/**
	 * Perform multiple insert permission
	 */
	public function pexec(
        string $what,
        array $array,       // array(id_what, id_user, level)
        int $id_area = 0
    ) : array
	{
		$sql = array();
		foreach ($array as $i)
		{
			$id_user = intval($i['id_user']);
			$id_what = intval($i['id_what']);

			// get upriv
			$upriv = $this->get_upriv($id_area, $id_user, $what);
			// check if already exists
			$priv = $this->get_priv($id_area, $id_user, $what, $id_what);

			if ($priv)
			{
				if ($upriv->level != $i['level'])
				{
					if ($priv->level != $i['level'])
					{
						// update
						$sql[] = 'UPDATE privs SET level = '.intval($i['level']).' WHERE id = '.$priv->id;
					}
				}
				else
				{
					// delete
					$sql[] = 'DELETE FROM privs WHERE id = '.$priv->id;
				}
			}
			else
			{
				if ($upriv->level != $i['level'])
				{
					// insert
					$sql[] = 'INSERT INTO privs
                            (updated, id_area, id_who, what, id_what, level, xon)
                        VALUES (
                            NOW(),
                            '.$id_area.',
                            '.$id_user.',
                            '.$this->db->escape($what).',
                            '.$id_what.',
                            '.intval($i['level']).',
                            1
                        )';
				}
			}
		}

		// insert/update/delete
		return (empty($sql))
			? array(0, 1)
			: $this->db->multi_exec($sql);
	}

	/**
	 * Perform delete permission for all on deleted item
	 */
	public function deleting_by_what(string $what, int $id_what) : array
	{
		return $this->db->single_exec('DELETE FROM privs WHERE what = '.$this->db->escape($what).' AND id_what = '.$id_what);
	}

	/**
	 * Reset all permissions of an user
	 */
	public function deleting_by_user(int $id_user) : array
	{
		$sql = array();

		// delete privileges on areas
		$sql[] = 'DELETE FROM aprivs WHERE id_user = '.$id_user;

		// delete user privileges
		$sql[] = 'DELETE FROM uprivs WHERE id_user = '.$id_user;

		// delete registered privileges
		$sql[] = 'DELETE FROM privs WHERE id_who = '.$id_user;
		$this->db->multi_exec($sql);
	}

	/**
	 * Get group's permissions
	 */
	public function get_gprivs(int $id_group, string $table = '') : mixed
	{
		return (empty($table))
			? $this->db->query('SELECT * FROM gprivs WHERE id_group = '.$id_group)
			: $this->db->query_row('SELECT * FROM gprivs WHERE what = '.$this->db->escape($table).' AND id_group = '.$id_group);
	}

	/**
	 * Get user's privtype permissions into an area
	 */
	public function get_uprivs(int $id_user, int $id_area, string $privtype = '') : mixed
	{
		return (empty($privtype))
			? $this->db->query('SELECT *
                FROM uprivs
                WHERE id_user = '.$id_user.' AND id_area = '.$id_area.'
                ORDER BY privtype ASC')
			: $this->db->query_var('SELECT level
                FROM uprivs
                WHERE privtype = '.$this->db->escape($privtype).' AND id_user = '.$id_user.' AND id_area = '.$id_area);
	}

	/**
	 * Set user's privtype permissions into an area
	 */
	public function set_uprivs(int $id_user, int $id_area, string $privtype, int $level) : array
    {
		return $this->db->single_exec($sql);
	}

	/**
	 * Get user's permission on a table into an area
	 */
	private function get_privs(string $what, int $id_user, int $id_area) : array
	{
		return $this->db->query('SELECT IF(p.id IS NULL, 0, p.id) AS id, IF(p.id IS NULL, u.level, p.level) AS level
			FROM uprivs u
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.id_area = u.id_area AND p.what = u.privtype
			WHERE u.id_user = '.$id_user.' AND u.id_area = '.$id_area.' AND u.privtype = '.$this->db->escape($what));
	}

	/**
	 * Refresh user permissions
	 */
	public function refactory(
        int $id_user,
        bool $force = false         // if false leaves priv personalizations else (integer) set to default
    ) : array
	{
		// action areas
		$areas = $this->get_aprivs($id_user);

		// refresh user permissions syncronize with group permissions
		$result = $this->sync_upriv($id_user, $areas);

		if ($result[1])
		{
			// foreach areas and foreach privtype refresh permissions
			$res = $this->sync_priv($id_user, $areas, $force);
		}

		return (isset($res) && $res[1])
			? $res
			: array(0,1);
	}

	/**
	 * Syncronize user privilege types with group privilege types
	 * Add privtypes but not change uprivs levels
	 * Remove privtypes and privs if group hasn't privtype
	 */
	private function sync_upriv(int $id_user, array $areas) : array
	{
		// get group's privilege types
		$group = new Group_model();
		$g = $group->get_group_by_user($id_user);
		$gp = X4Array_helper::obj2array($this->get_gprivs($g->id), 'what', 'level');

		$sql = array();
		foreach ($areas as $i)
		{
			// get User privilege types on area
			$up = X4Array_helper::obj2array($this->get_uprivs($id_user, $i->id_area), 'privtype', 'id');

			// check group privilege types
			foreach ($gp as $k => $v)
			{
				if (isset($up[$k]))
				{
					// if user have a group's privilege do none
					unset($up[$k]);
				}
				elseif ($i->id_area == 1 || !in_array($k, $this->admin_privtypes))
				{
					// if user don't have then add the missing privilege type
					$sql[] = 'INSERT INTO uprivs
                            (updated, id_area, id_user, privtype, level, xon)
                        VALUES
                            (NOW(), '.$i->id_area.', '.$id_user.', \''.$k.'\', '.$v.', 1)';
				}
			}

			// in array 'up' now you have only the privileges that the group did not so delete it
			foreach ($up as $k => $v)
			{
				$sql[] = 'DELETE u, p FROM uprivs u
					LEFT JOIN privs p ON u.id_user = p.id_who AND u.privtype = p.what AND u.id_area = p.id_area
					WHERE u.id = '.$v.' AND u.id_user = '.$id_user.' AND u.privtype = \''.$k.'\' AND u.id_area = '.$i->id_area;
			}
		}
		return (empty($sql))
			? array(0,1)
			: $this->db->multi_exec($sql);
	}

	/**
	 * Syncronize user privileges with user permissions
	 * if force is false remove privs equal to default value
	 * else remove all privs
	 */
	private function sync_priv(
        int $id_user,
        array $areas,
        bool $force = false     // if false leaves privs personalizations (only add missing privs) else set to default
    ) : array
	{
		$sql = array();
		foreach ($areas as $i)
		{
			// get user privilege types on area
			$up = X4Array_helper::obj2array($this->get_uprivs($id_user, $i->id_area), 'privtype', 'level');

			// k => privtype and v => level
			foreach ($up as $k => $v)
			{
				$install = false;
				$items = array();
				// handle all if area is admin and only commons if area isn't admin
				if ($i->id_area == 1 || !in_array($k, $this->admin_privtypes))
				{
					// not handle abstract privilege
					if (substr($k, 0, 1) != '_')
					{
						// get items
						// if force get all items with permissions different to default so we can delete them
						// if force is false get all items with permission equal to default (v) so we can delete them
						$items = $this->get_all_records($k, $id_user, $i->id_area, $v, $force);
					}
					else
					{
                        			// creation permissions
						$install = true;
						$items = array(1);
					}
				}

				// if there are something to handle
				if ($items)
				{
					if (!$force && !$install)
					{
						// we remove items with priv equal to upriv
                        foreach ($items as $ii)
						{
							$sql[] = 'DELETE FROM privs WHERE id = '.$ii->pid;
						}
					}
					else
					{
						// forcing we delete all personalizations on permissions
						$sql[] = 'DELETE FROM privs WHERE id_who = '.$id_user.' AND what = '.$this->db->escape($k);
					}
				}
			}
		}

		return (empty($sql))
			? array(0,1)
			: $this->db->multi_exec($sql);
	}

	/**
	 * Get id of table records with user priv equal to default or with priv level different from default
	 * if equal to default we delete them
	 * if different we delete them in case of force set to true
	 */
	private function get_all_records(string $table, int $id_user, int $id_area, int $level, bool $force) : array
	{
		$where = ($force)
			? ' WHERE p.level <> '.$level   // we will delete items with different level permissions
			: ' WHERE p.level = '.$level;   // we will delete items with the same level permissions

		// Some tables require special treatment
		$sql = '';

		// excluded tables
        $excluded = array('x4', 'x5');

        switch($table)
		{
		case 'areas':
			$sql = 'SELECT a.id_area AS id, p.id AS pid
				FROM aprivs a
				JOIN privs p ON p.what = '.$this->db->escape($table).' AND p.id_who = a.id_user AND p.id_what = a.id_area
				'.$where.' AND a.id_user = '.$id_user.' AND a.id_area = '.$id_area.'
				ORDER BY a.id ASC';
			break;
		case 'dictionary':
			$sql = 'SELECT DISTINCT d.id, p.id AS pid
				FROM dictionary d
				JOIN aprivs a ON a.area = d.area
				JOIN privs p ON p.what = '.$this->db->escape($table).' AND p.id_what = d.id AND p.id_who = '.$id_user.' AND p.id_area = '.$id_area.'
				'.$where.'
				ORDER BY d.id ASC';
			break;
		case 'menus':
		case 'templates':
			$sql = 'SELECT DISTINCT t.id, p.id AS pid
				FROM '.$table.' t
				JOIN themes th ON th.id = t.id_theme
				JOIN privs p ON p.what = '.$this->db->escape($table).' AND p.id_what = t.id AND p.id_who = '.$id_user.'
				'.$where.'
				ORDER BY t.id ASC';
			break;
		case 'languages':
		case 'sites':
		case 'themes':
		case 'xgroups':
		case 'widgets':
			$sql = 'SELECT DISTINCT t.id, p.id AS pid
				FROM '.$table.' t
				JOIN privs p ON p.what = '.$this->db->escape($table).' AND p.id_what = t.id AND p.id_who = '.$id_user.'
				'.$where.'
				ORDER BY t.id ASC';
			break;
		case 'users':
			$sql = 'SELECT DISTINCT u.id, p.id AS pid
				FROM users u
				JOIN xgroups g ON g.id = u.id_group
				JOIN privs p ON p.what = '.$this->db->escape($table).' AND p.id_what = u.id AND p.id_who = '.$id_user.'
				'.$where.'
				ORDER BY u.id ASC';
			break;
		default:

			// modules and others generic tables
			if (!in_array($table, $this->no_privs))
			{
				// no mysql tables
				$prefix = substr($table, 0, 2);
				if (in_array($prefix, $excluded))
				{
				    // Modules without table and
					// Mongo DB collections are not connected with privs table
					return array();
				}
				else
				{
					if (substr($table, 0, 3) == 'x3_')
					{
						// is a plugin table, could be in another DB
						$chk = $this->db->query('SHOW TABLES LIKE '.$this->db->escape($table));

						if (!$chk)
						{
							// table is in another DB
							return array();
						}
					}

					// MySQL table on default DB
					$sql = 'SELECT DISTINCT t.id, p.id AS pid
						FROM '.$table.' t
						JOIN privs p ON p.what = '.$this->db->escape($table).' AND p.id_what = t.id AND p.id_who = '.$id_user.' AND p.id_area = t.id_area
						'.$where.' AND t.id_area = '.$id_area.'
						ORDER BY t.id ASC';
				}
			}
			else
			{
				return array();
			}
			break;
		}
		return $this->db->query($sql);
	}

	/**
	 * Get privtypes by xrif
	 * xrif should separate different types of privilege private areas (admin and private) and public areas
	 * Util now xrif is always 1 (private)
	 */
	public function get_privtypes(int $xrif) : array
	{
		return $this->db->query('SELECT * FROM privtypes WHERE xrif = '.$xrif.' AND xon = 1');
	}

	/**
	 * Refresh privilege types of the group (insert, update and delete)
	 */
	public function update_gprivs(int $id_group, array $insert, array $update, array $delete) : array
	{
		$sql = array();

		// insert
		foreach ($insert as $k => $v)
		{
			$sql[] = 'INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), '.$id_group.', '.$this->db->escape($k).', '.intval($v).', 1)';
		}

		// update
		foreach ($update as $k => $v)
		{
			$sql[] = 'UPDATE gprivs SET updated = NOW(), level = '.intval($v).' WHERE id_group = '.$id_group.' AND what = '.$this->db->escape($k);
		}

		// delete
		foreach ($delete as $i)
		{
			$sql[] = 'DELETE FROM gprivs WHERE id_group = '.$id_group.' AND what = '.$this->db->escape($i);
		}

		if (empty($sql))
		{
			$res = array(0,1);
		}
		else
		{
			$res = $this->db->multi_exec($sql);

			// after the refresh perform a refactory on users of the group
			if ($res[1])
			{
				$this->refactory_group($id_group);
			}
		}
		return $res;
	}

	/**
	 * Perform a permission's refactory on all users of a group
	 */
	private function refactory_group(int $id_group) : array
	{
		// get users
		$user = new User_model();
		$u = $user->get_users($id_group);

		// refactory user permission
		foreach ($u as $i)
		{
			$res = $this->refactory($i->id);
		}

		return (empty($u))
			? array(0, 1)
			: $res;
	}

	/**
	 * Refresh user permissions (insert, update and delete)
	 */
	public function update_uprivs(int $id_user, int $id_area, array $insert, array $update, array $delete) : array
	{
		$sql = array();

		// delete
		foreach ($delete as $k => $v)
		{
			$sql[] = 'DELETE FROM uprivs WHERE id_area = '.$id_area.' AND id_user = '.$id_user.' AND privtype = '.$this->db->escape($k);
		}

		// insert
		foreach ($insert as $k => $v)
		{
			$sql[] = 'INSERT INTO uprivs (updated, id_area, id_user, privtype, level, xon) VALUES (NOW(), '.$id_area.', '.$id_user.', '.$this->db->escape($k).', '.intval($v).', 1)';
		}

		// update
		foreach ($update as $k => $v)
		{
			$sql[] = 'UPDATE uprivs SET updated = NOW(), level = '.intval($v).' WHERE id_area = '.$id_area.' AND id_user = '.$id_user.' AND privtype = '.$this->db->escape($k);
		}

		if (empty($sql))
		{
			$res = array(0,1);
		}
		else
		{
			$res = $this->db->multi_exec($sql);

			// refactory user permission
			if ($res[1])
			{
				$this->refactory($id_user, true);
			}
		}
		return $res;
	}

	/**
	 * Get user permission on all record in a table by id_area
	 * This method returns all the records in a table
	 * For each element collects: id, name and description (This is the reason why every table must have the following fields: id, name, description)
	 */
	public function get_detail(int $id_user, int $id_area, string $table) : array
	{
		// switch table
		switch($table)
		{
		case 'articles':
			$sql = 'SELECT a.id, a.name, CONCAT(c.name, \' - \', a.lang) AS description, IF (p.id IS NULL, u.level, p.level) AS level
				FROM articles a
				JOIN contexts c ON c.code = a.code_context
				JOIN uprivs u ON u.id_area = a.id_area AND u.privtype = \'articles\' AND u.id_user = '.$id_user.'
				LEFT JOIN privs p ON p.what = u.privtype AND p.id_who = u.id_user AND p.id_what = a.id
				WHERE a.id_area = '.$id_area.'
				GROUP BY a.bid
				ORDER BY a.id ASC';
			break;
		case 'contexts':
			$sql = 'SELECT c.id, c.name, c.lang AS description, IF (p.id IS NULL, u.level, p.level) AS level
				FROM contexts c
				JOIN uprivs u ON u.id_area = c.id_area AND u.privtype = \'contexts\' AND u.id_user = '.$id_user.'
				LEFT JOIN privs p ON p.what = u.privtype AND p.id_who = u.id_user AND p.id_what = c.id
				WHERE c.id_area = '.$id_area.'
				GROUP BY c.id
				ORDER BY c.id ASC';
			break;
		case 'files':
			$sql = 'SELECT c.id, c.name, c.alt AS description, IF (p.id IS NULL, u.level, p.level) AS level
				FROM files c
				JOIN uprivs u ON u.id_area = c.id_area AND u.privtype = \'files\' AND u.id_user = '.$id_user.'
				LEFT JOIN privs p ON p.what = u.privtype AND p.id_who = u.id_user AND p.id_what = c.id
				WHERE c.id_area = '.$id_area.'
				GROUP BY c.id
				ORDER BY c.id ASC';
			break;
		case 'dictionary':
			$sql = 'SELECT d.id, d.xkey AS name, d.what AS description, IF (p.id IS NULL, u.level, p.level) AS level
				FROM dictionary d
				JOIN aprivs a ON a.id = '.$id_area.' AND a.area = d.area
				JOIN uprivs u ON u.id_area = a.id_area AND u.privtype = \'dictionary\' AND u.id_user = '.$id_user.'
				LEFT JOIN privs p ON p.what = u.privtype AND p.id_who = u.id_user AND p.id_what = d.id
				GROUP BY d.id
				ORDER BY d.id ASC';
			break;
		case 'languages':
			if ($id_area == 1)
			{
				$sql = 'SELECT l.id, l.code AS name, l.language AS description, IF (p.id IS NULL, u.level, p.level) AS level
					FROM languages l
					JOIN uprivs u ON u.id_area = 1 AND u.privtype = \'languages\' AND u.id_user = '.$id_user.'
					LEFT JOIN privs p ON p.what = u.privtype AND p.id_who = u.id_user AND p.id_what = l.id
					GROUP BY l.id
					ORDER BY l.code ASC';
			}
			break;
		case 'menus':
		case 'templates':
			$sql = 'SELECT t.id, t.name, CONCAT(ar.title, \' - \', th.description, \' - \', t.description) AS description, IF (p.id IS NULL, u.level, p.level) AS level
				FROM '.$table.' t
				JOIN themes th ON th.id = t.id_theme
				LEFT JOIN areas ar ON ar.id_theme = th.id
				JOIN aprivs a ON a.id_area = ar.id
				JOIN uprivs u ON u.id_area = ar.id AND u.privtype = \''.$table.'\' AND u.id_user = '.$id_user.'
				LEFT JOIN privs p ON p.what = u.privtype AND p.id_who = u.id_user AND p.id_what = t.id
				GROUP BY t.id
				ORDER BY t.id ASC';
			break;
		case 'pages':
			$sql = 'SELECT p.id, p.name, CONCAT(p.lang, \' - \', p.description) AS description, IF (pr.id IS NULL, u.level, pr.level) AS level
				FROM pages p
				JOIN uprivs u ON u.id_area = p.id_area AND u.privtype = \'pages\' AND u.id_user = '.$id_user.'
				LEFT JOIN privs pr ON pr.what = u.privtype AND pr.id_who = u.id_user AND pr.id_what = p.id
				WHERE p.id_area = '.$id_area.'
				GROUP BY p.id
				ORDER BY p.lang ASC, p.ordinal ASC';
			break;
		case 'themes':
			$sql = 'SELECT DISTINCT t.id, t.name, t.description, IF (p.id IS NULL, u.level, p.level) AS level
				FROM themes t
				JOIN areas ar ON ar.id_theme = t.id
				JOIN uprivs u ON u.id_area = ar.id AND u.privtype = \'themes\' AND u.id_user = '.$id_user.'
				LEFT JOIN privs p ON p.what = u.privtype AND p.id_who = u.id_user AND p.id_what = t.id
				GROUP BY t.id
				ORDER BY t.id ASC';
			break;
		case 'users':
			$sql = 'SELECT u.id, u.username AS name, CONCAT(g.description, \' - \', u.description) AS description, IF (p.id IS NULL, up.level, p.level) AS level
				FROM users u
				JOIN xgroups g ON g.id = u.id_group
				JOIN uprivs up ON up.id_area = g.id_area AND up.privtype = \'users\' AND up.id_user = '.$id_user.'
				LEFT JOIN privs p ON p.what = up.privtype AND p.id_who = up.id_user AND p.id_what = u.id
				GROUP BY u.id
				ORDER BY u.id ASC';
			break;
		default:
			// for generic tables and modules
			$sql = 'SELECT t.id, t.name, t.description, IF (p.id IS NULL, u.level, p.level) AS level
				FROM '.$table.' t
				JOIN uprivs u ON u.id_area = t.id_area AND u.privtype = \''.$table.'\' AND u.id_user = '.$id_user.'
				LEFT JOIN privs p ON p.what = u.privtype AND p.id_who = u.id_user AND p.id_what = t.id
				WHERE t.id_area = '.$id_area.'
				GROUP BY t.id
				ORDER BY t.id ASC';
			break;
		}
		return (empty($sql))
			? array()
			: $this->db->query($sql);
	}

	/**
	 * Refresh user privs on a table
	 */
	public function update_detail_privs(int $id_user, int $id_area, string $table, array $array) : array
	{
		// get upriv
		$upriv = $this->get_upriv($id_area, $id_user, $table);

		$sql = array();
		foreach ($array as $i)
		{
			$new_level = intval($i['value']);
			if ($upriv->level == $new_level)
			{
				// delete priv
				$sql[] = 'DELETE FROM privs WHERE what = '.$this->db->escape($table).' AND id_what = '.intval($i['id']).' AND id_who = '.$id_user;
			}
			else
			{
				// get priv id
				$id_priv = $this->get_id($id_area, $id_user, $table, $i['id']);
				if ($id_priv)
				{
					$sql[] = 'UPDATE privs SET updated = NOW(), level = '.$new_level.' WHERE id_what = '.intval($i['id']).' AND id_area = '.$id_area.' AND id_who = '.$id_user.' AND what = '.$this->db->escape($table);
				}
				else
				{
					$sql[] = 'INSERT INTO privs (updated, id_area, id_who, what, id_what, level, xon)
						VALUES (NOW(), '.$id_area.', '.$id_user.', '.$this->db->escape($table).', '.intval($i['id']).', '.$new_level.', 1)';
				}
			}
		}
		return $this->db->multi_exec($sql);
	}

}

/**
 * Class needed by sync_priv method
 * emulates a query result
 * @package		X3CMS
 */
class Obj_item
{
	public $id;

	/**
	 * Constructor
	 */
	public function __construct(int $id)
	{
		$this->id = $id;
	}
}
