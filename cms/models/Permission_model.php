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
 * Model for Permissions
 *
 * @package X3CMS
 */
class Permission_model extends X4Model_core 
{
	/**
	 * @var array	admin related privtypes
	 */
	protected $admin_privtypes = array(
		'_area_creation',
		'_group_creation',
		'_language_creation',
		'_menu_creation',
		'_module_install',
		'_template_install',
		'_theme_install',
		'_user_creation',
		'groups',
		'languages',
		'menus',
		'privs',
		'sites',
		'themes',
		'templates',
		'users'
	);
	
	/**
	 * @var array	tables to not set privs
	 */
	protected $no_privs = array(
		'privs',
		'logs',
	);
	
	/**
	 * Constructor: set reference table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// set default table
		parent::__construct('privs');
	}
	
	/**
	 * Get priv id
	 *
	 * @param   integer $id_area Area ID
	 * @param   integer $id_user User ID
	 * @param   string	$what what name
	 * @param   integer $id_what what ID
	 * @return  integer	priv ID
	 */
	private function get_id($id_area, $id_user, $what, $id_what)
	{
		return (int) $this->db->query_var('SELECT id FROM privs WHERE id_who = '.intval($id_user).' AND id_area = '.$id_area.' AND what = '.$this->db->escape($what).' AND id_what = '.$id_what);
	}
	
	/**
	 * Get user priv on an item
	 *
	 * @param   integer	$id_area Area ID
	 * @param   integer	$id_user User ID
	 * @param   string	$what item (Table name)
	 * @param   integer	$id_what Item ID in the table
	 * @return  integer	
	 */
	public function get_priv($id_area, $id_user, $what, $id_what) 
	{
		return $this->db->query_row('SELECT id, level FROM privs WHERE id_area = '.intval($id_area).' AND id_who = '.intval($id_user).' AND what = '.$this->db->escape($what).' AND id_what = '.intval($id_what));
	}
	
	/**
	 * Get user permission's level on an item
	 *
	 * @param   integer	$id_user User ID
	 * @param   string	$what item (Table name)
	 * @param   integer	$id_what Item ID in the table
	 * @return  integer	
	 */
	public function check_priv($id_user, $what, $id_what) 
	{
		return (int) $this->db->query_var('SELECT level FROM privs WHERE id_who = '.intval($id_user).' AND what = '.$this->db->escape($what).' AND id_what = '.intval($id_what));
	}
	
	/**
	 * Get user priv on a table
	 *
	 * @param   integer	$id_area Area ID
	 * @param   integer	$id_user User ID
	 * @param   string	$what item (Table name)
	 * @return  integer	
	 */
	public function get_upriv($id_area, $id_user, $what) 
	{
		return $this->db->query_row('SELECT id, level FROM uprivs WHERE id_area = '.intval($id_area).' AND id_user = '.intval($id_user).' AND privtype = '.$this->db->escape($what));
	}
	
	/**
	 * Get permission level names 
	 * Use levels table
	 *
	 * @return  array	array of objects
	 */
	public function get_levels()
	{
		return $this->db->query('SELECT * FROM levels ORDER BY id ASC');
	}
	
	/**
	 * Get in which areas user can do something
	 * Use aprivs table
	 *
	 * @param   integer $id_user User ID
	 * @return  array	array of objects
	 */
	public function get_aprivs($id_user)
	{
		return $this->db->query('SELECT id_area, area FROM aprivs WHERE id_user = '.intval($id_user).' ORDER BY id_area ASC');
	}
	
	/**
	 * Set in which areas user can do something
	 * Use aprivs table
	 *
	 * @param   integer $id_user User ID
	 * @param   array 	$ids_area array of Area IDs
	 * @return  array	Array(0, boolean)
	 */
	public function set_aprivs($id_user, $ids_area)
	{
		$sql = array();
		
		// delete old aprivs
		$sql[] = 'DELETE FROM aprivs WHERE id_user = '.intval($id_user);
		foreach ($ids_area as $i)
		{
			// get area data
			$a = $this->get_by_id($i, 'areas', 'name');
			
			$sql[] = 'INSERT INTO aprivs (updated, id_user, id_area, area, xon) VALUES (NOW(), '.intval($id_user).', '.intval($i).', '.$this->db->escape($a->name).', 1)';
		}
		return $this->db->multi_exec($sql);
	}
	
	/**
	 * Perform multiple insert permission
	 *
	 * @param   string	$what table name
	 * @param   array 	$array array(id_what, id_user, level)
	 * @param   integer	$id_area Area ID
	 * @return  array	Array(0, boolean)
	 */
	public function pexec($what, $array, $id_area = 0)
	{
		$sql = array();
		foreach($array as $i)
		{
			$id_user = intval($i['id_user']);
			$id_what = intval($i['id_what']);
			
			// check if already exists
			$priv = $this->get_priv($id_area, $id_user, $what, $id_what);
			
			if ($priv)
			{
				if ($priv->level != $i['level'])
				{
					// update
					$sql[] = 'UPDATE privs SET level = '.intval($i['level']).' WHERE id = '.intval($priv->id);
				}
			}
			else
			{
				// insert
				$sql[] = 'INSERT INTO privs (updated, id_area, id_who, what, id_what, level, xon) VALUES (NOW(), '.intval($id_area).', '.$id_user.', '.$this->db->escape($what).', '.$id_what.', '.intval($i['level']).', 1)';
			}
		}
		
		// insert
		if (!empty($sql)) 
			return $this->db->multi_exec($sql);
		else 
			return array(0, 1);
	}
	
	/**
	 * Perform delete permission
	 *
	 * @param   string	$what table name
	 * @param   integer $id_what Record ID
	 * @return  array	Array(0, boolean)
	 */
	public function deleting_by_what($what, $id_what)
	{
		return $this->db->single_exec('DELETE FROM privs WHERE what = '.$this->db->escape($what).' AND id_what = '.intval($id_what));
	}
	
	/**
	 * Reset all permissions of an user
	 * Use aprivs, uprivs and privs tables
	 *
	 * @param   integer $id_user User ID
	 * @return  void
	 */
	public function deleting_by_user($id_user)
	{
		// sanitize
		$id = intval($id_user);
		
		$sql = array();
		
		// delete privileges on areas
		$sql[] = 'DELETE FROM aprivs WHERE id_user = '.$id;
		
		// delete user privileges
		$sql[] = 'DELETE FROM uprivs WHERE id_user = '.$id;
		
		// delete registered privileges
		$sql[] = 'DELETE FROM privs WHERE id_who = '.$id;
		$this->db->multi_exec($sql);
	}
	
	/**
	 * Get group's permissions
	 * Use gprivs table
	 *
	 * @param   integer $id_group Group ID
	 * @param   string	$table Table name
	 * @return  array	array of objects
	 */
	public function get_gprivs($id_group, $table = '')
	{
		return (empty($table))
			? $this->db->query('SELECT * FROM gprivs WHERE id_group = '.intval($id_group))
			: $this->db->query_row('SELECT * FROM gprivs WHERE what = '.$this->db->escape($table).' AND id_group = '.intval($id_group));
	}
	
	/**
	 * Get user's privtype permissions into an area
	 * Use uprivs table
	 *
	 * @param   integer $id_user User ID
	 * @param   integer $id_area Area ID
	 * @param   string	$privtype Privtype name
	 * @return  mixed
	 */
	public function get_uprivs($id_user, $id_area, $privtype = '')
	{
		return (empty($privtype))
			? $this->db->query('SELECT * FROM uprivs WHERE id_user = '.intval($id_user).' AND id_area = '.intval($id_area).' ORDER BY privtype ASC')
			: $this->db->query_var('SELECT level FROM uprivs WHERE privtype = '.$this->db->escape($privtype).' AND id_user = '.intval($id_user).' AND id_area = '.intval($id_area));
	}
	
	/**
	 * Get user's permission on a table into an area
	 *
	 * @param   string	$what Table name
	 * @param   integer $id_user User ID
	 * @param   integer $id_area Area ID
	 * @return  array	array[record ID] => permission level
	 */
	private function get_privs($what, $id_user, $id_area)
	{
		return $this->db->query('SELECT * FROM privs WHERE what = '.$this->db->escape($what).' AND id_who = '.intval($id_user).' AND id_area = '.intval($id_area));
	}
	
	/**
	 * Refresh user permissions
	 *
	 * @param   integer $id_user User ID
	 * @param	mixed	$force if null leaves priv personalizations else (integer) set to default
	 * @return  array	Array(0, boolean)
	 */
	public function refactory($id_user, $force = null)
	{
		// action areas
		$areas = $this->get_aprivs($id_user);
		// refresh user permissions syncronize with group permissions
		$res = $this->sync_upriv($id_user, $areas);
		
		if ($res[1]) 
		{
			// foreach areas and foreach privtype refresh permissions
			$res = $this->sync_priv($id_user, $areas, $force);
		}
		return (isset($res)) ? $res : array(0,1);
	}
	
	/**
	 * Syncronize user privilege types with group privilege types
	 * Add privtypes but not change uprivs levels
	 * Remove privtypes and privs if group hasn't privtype
	 *
	 * @param   integer $id_user User ID
	 * @param	array	$areas array of area objects
	 * @return  array	Array(0, boolean)
	 */
	private function sync_upriv($id_user, $areas)
	{
		// get group's privilege types
		$group = new Group_model();
		$g = $group->get_group_by_user($id_user);
		$gp = X4Utils_helper::obj2array($this->get_gprivs($g->id), 'what', 'level');
		
		$sql = array();
		foreach($areas as $i)
		{
			// get User privilege types on area
			$up = X4Utils_helper::obj2array($this->get_uprivs($id_user, $i->id_area), 'privtype', 'id');
			
			// check group privilege types
			foreach($gp as $k => $v)
			{
				if (isset($up[$k])) 
				{
					// if user have a group's privilege do none
					unset($up[$k]);
				}
				else if ($i->id_area == 1 || !in_array($k, $this->admin_privtypes))
				{
					// if user don't have then add the missing privilege type
					$sql[] = 'INSERT INTO uprivs (updated, id_area, id_user, privtype, level, xon) VALUES (NOW(), '.$i->id_area.', '.$id_user.', \''.$k.'\', '.$v.', 1)';
				}
			}
			
			// in array 'up' now you have only the privileges that the group did not so delete it
			foreach($up as $k => $v)
			{
				$sql[] = 'DELETE u.*, p.* FROM uprivs u 
					JOIN privs p ON u.id_user = p.id_who AND u.privtype = p.what AND u.id_area = p.id_area 
					WHERE u.id = '.$v.' AND p.id_who = '.$id_user.' AND p.what = \''.$k.'\' AND p.id_area = '.$i->id_area;
			}
		}
		
		return (empty($sql)) 
			? array(0,1) 
			: $this->db->multi_exec($sql);
	}
	
	/**
	 * Syncronize user privileges with user permissions
	 * if force is null add priv but not change permission levels
	 * else add, edit and delete privs
	 *
	 * @param   integer $id_user User ID
	 * @param	array	$areas array of area objects
	 * @param	mixed	$force if null leaves privs personalizations (only add missing privs) else (integer) set to default
	 * @return  array	Array(0, boolean)
	 */
	private function sync_priv($id_user, $areas, $force = null)
	{
		$sql = array();
		foreach($areas as $i)
		{
			// get user privilege types on area
			$up = X4Utils_helper::obj2array($this->get_uprivs($id_user, $i->id_area), 'privtype', 'level');
			
			foreach($up as $k => $v)
			{
				// handle all if area is admin and only commons if area isn't admin 
				if ($i->id_area == 1 || !in_array($k, $this->admin_privtypes)) 
				{
					// abstract privilege
					if (substr($k, 0, 1) == '_') 
					{
						// get the Priv ID
						$id = $this->get_id($i->id_area, $id_user, $k, 0);
						
						// if exists create empty array
						if ($id) 
						{
							$items = array();
						}
						else 
						{
							// add empty item to insert
							$item = new Obj_item(0);
							$items = array($item);
						}
					}
					// privilege with table
					else 
					{
						// set case
						$case = (is_null($force)) 
							? null 
							: $v;
							
						// get items
						// if case is null get all items without permissions
						// if not null get all items with permission not equal to case value
						$items = $this->get_all_records($k, $id_user, $i->id_area, $case);
					}
				}
				else 
				{
					$items = array();
				}
				
				// if there are something to handle
				if ($items) 
				{
					if (is_null($force)) 
					{
						// no forcing, only insert missing permissions
						foreach($items as $ii) 
						{
							$sql[] = 'INSERT INTO privs (updated, id_area, id_who, what, id_what, level, xon) 
								VALUES (NOW(), '.$i->id_area.', '.$id_user.', '.$this->db->escape($k).', '.$ii->id.', '.$v.', 1)';
						}
					}
					else 
					{
						// forcing
						foreach($items as $ii) 
						{
							// set all permission to right value (eliminate customizations) if permission is greater than zero
							if ($v) 
							{
							    if ($ii->id && !is_null($ii->pid))
							    {
							        $sql[] = 'UPDATE privs SET level = '.$v.' WHERE id_who = '.$id_user.' AND what = '.$this->db->escape($k).' AND id_what = '.$ii->id;
							    }
							    else
							    {
							        $sql[] = 'INSERT INTO privs (updated, id_area, id_who, what, id_what, level, xon) VALUES (NOW(), '.$i->id_area.', '.$id_user.', '.$this->db->escape($k).', '.$ii->id.', '.$v.', 1)';
								}
							}
							// eliminate if permission is zero
							else
							{
								$sql[] = 'DELETE FROM privs WHERE id_who = '.$id_user.' AND what = '.$this->db->escape($k).' AND id_what = '.$ii->id;
							}
						}
					}
				}
			}
			
			// set privs on admin pages
			if ($i->id_area == 1) 
			{
				// get administration pages without permission
				$pages = $this->get_pages_by_xid('base', $id_user);
				if ($pages) 
				{
					foreach($pages as $ii)
					{
						$sql[] = 'INSERT INTO privs (updated, id_area, id_who, what, id_what, level, xon) 
							VALUES (NOW(), 1, '.$id_user.', \'pages\', '.$ii->id.', 1, 1)';
					}
				}
			}
		}
		
		return (empty($sql)) 
			? array(0,1) 
			: $this->db->multi_exec($sql);
	}
	
	/**
	 * Get id of pages without privs by xid and User ID
	 * Use pages and privs tables
	 *
	 * @param   string	$xid, is a key to group pages
	 * @param   integer $id_user User ID
	 * @return  array	array of integer
	 */
	private function get_pages_by_xid($xid, $id_user)
	{
		return $this->db->query('SELECT pg.id 
			FROM pages pg 
			LEFT JOIN privs p ON p.what = \'pages\' AND p.id_area= pg.id_area AND p.id_what = pg.id AND p.id_who = '.$id_user.' AND pg.xid = '.$this->db->escape($xid).'  
			WHERE p.id_area = 1 AND p.id_what IS NULL');
	}
	
	/**
	 * Get id of table records without user priv or with priv level different from given value
	 *
	 * @param   string	$table Table name
	 * @param   integer $id_user User ID
	 * @param   integer $id_area Area ID
	 * @param	mixed	$case null or integer (if null get id records without priv)
	 * @return  array	array of integer
	 */
	private function get_all_records($table, $id_user, $id_area = 0, $case = null)
	{
		// switch case, force == null
		if (is_null($case)) 
		{
			// without permissions
			$join = 'LEFT';
			$where = ' WHERE p.id_what IS NULL ';
		}
		else 
		{
			// with different level permissions
			$join = 'LEFT';
			$where = ' WHERE (p.id_what IS NULL OR p.level <> '.intval($case).')';
		}
		
		// Some tables require special treatment
		$sql = '';
		
		// excluded tables
        $excluded = array('x4', 'x5');
        $prefix = substr($table, 0, 2);
        
        switch($table) 
		{
		case 'areas':
			$sql = 'SELECT a.id_area AS id, p.id AS pid FROM aprivs a 
			'.$join.' JOIN privs p ON p.what = '.$this->db->escape($table).' AND p.id_who = a.id_user AND p.id_what = a.id_area 
			'.$where.' AND a.id_user = '.intval($id_user).' AND a.id_area = '.intval($id_area) .'
			ORDER BY a.id ASC';
			break;
		case 'dictionary':
			$sql = 'SELECT DISTINCT d.id, p.id AS pid FROM dictionary d
				JOIN aprivs a ON a.area = d.area 
				'.$join.' JOIN privs p ON p.what = '.$this->db->escape($table).' AND p.id_what = d.id AND p.id_who = '.intval($id_user).' AND p.id_area = '.intval($id_area).'
				'.$where.' AND d.updated > '.$this->db->escape($_SESSION['last_in']).'
				ORDER BY d.id ASC';
			break;
		case 'menus':
		case 'templates':
			$sql = 'SELECT DISTINCT t.id, p.id AS pid FROM '.$table.' t 
			JOIN themes th ON th.id = t.id_theme 
			'.$join.' JOIN privs p ON p.what = '.$this->db->escape($table).' AND p.id_what = t.id AND p.id_who = '.intval($id_user).'
			'.$where.'
			ORDER BY t.id ASC';
			break;
		case 'languages':
		case 'sites':
		case 'themes':
		case 'groups':
			$sql = 'SELECT DISTINCT t.id, p.id AS pid FROM '.$table.' t 
			'.$join.' JOIN privs p ON p.what = '.$this->db->escape($table).' AND p.id_what = t.id AND p.id_who = '.intval($id_user).'
			'.$where.'
			ORDER BY t.id ASC';
			break;
		case 'users':
			$sql = 'SELECT DISTINCT u.id, p.id AS pid FROM users u
				JOIN groups g ON g.id = u.id_group 
				'.$join.' JOIN privs p ON p.what = '.$this->db->escape($table).' AND p.id_what = u.id AND p.id_who = '.intval($id_user).'
				'.$where.'
				ORDER BY u.id ASC';
			break;
		default:
			
			// modules and others generic tables
			if (!in_array($table, $this->no_privs)) 
			{
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
					
					// handle reset
					if (is_null($case)) 
                    {
                        $soft = ' AND t.updated > '.$this->db->escape($_SESSION['last_in']);
                    }
                    else 
                    {
                        // hard
                        $soft = '';
                    }
					
					// MySQL table on default DB
					$sql = 'SELECT DISTINCT t.id, p.id AS pid FROM '.$table.' t
						'.$join.' JOIN privs p ON p.what = '.$this->db->escape($table).' AND p.id_what = t.id AND p.id_who = '.intval($id_user).' AND p.id_area = t.id_area
						'.$where.' AND t.id_area = '.intval($id_area).' '.$soft.'
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
	 * Refresh user priv over a table
	 * edit and delete privs
	 *
	 * @param   integer $id_user User ID
	 * @param	array	$id_area array of Area ID
	 * @param	string	$table Table name
	 * @return  array	Array(0, boolean)
	 */
	public function refactory_table($id_user, $id_area, $table)
	{
		$sql = array();
		foreach($id_area as $a) 
		{
			// get user privilege types and levels
			if (!isset($up)) 
				$up = $this->get_uprivs($id_user, $a, $table);
			
			// get id and permission levels on records of the table
			
			// tables to exclude
			$exclude = array('themes', 'templates', 'menus', 'logs');
			
			if (in_array($table, $exclude))
			{
				$items = $this->db->query('SELECT t.id, p.level, p.id AS pid 
					FROM '.$table.' t
					LEFT JOIN privs p ON p.what = '.$this->db->escape($table).' AND p.id_what = t.id AND p.id_who = '.intval($id_user).' AND p.id_area = 1
					ORDER BY t.id ASC');
			}
			else
			{
				$items = $this->db->query('SELECT t.id, p.level, p.id AS pid 
					FROM '.$table.' t
					LEFT JOIN privs p ON p.what = '.$this->db->escape($table).' AND p.id_what = t.id AND p.id_who = '.intval($id_user).' AND p.id_area = t.id_area
					WHERE t.id_area = '.intval($a).'
					ORDER BY t.id ASC');
			}
			// insert, delete and update privs
			foreach($items as $i) 
			{
				// If the permissions of the user are different from those assigned
				if ($i->level != $up) 
				{
					// if have permission
					if ($up) 
					{
						// and the privs is missing
						if (is_null($i->level)) 
							$sql[] = 'INSERT INTO privs (updated, id_area, id_who, what, id_what, level, xon) 
										VALUES (NOW(), '.intval($a).', '.$id_user.', '.$this->db->escape($table).', '.$i->id.', '.$up.', 1)';
						// and the prinvs is different
						else 
							$sql[] = 'UPDATE privs SET level = '.$up.' WHERE id = '.$i->pid;
					}
					// is not allowed
					else 
						$sql[] = 'DELETE FROM privs WHERE id = '.$i->pid;
				}
			}
		}
		
		return (empty($sql)) 
			? array(0,1) : 
			$this->db->multi_exec($sql);
	}
	
	/**
	 * Get privtypes by xrif
	 * xrif should separate different types of privilege private areas (admin and private) and public areas
	 * Util now is always 1 (private)
	 *
	 * @param   integer $xrif
	 * @return  array	array of objects
	 */
	public function get_privtypes($xrif)
	{
		return $this->db->query('SELECT * FROM privtypes WHERE xrif = '.intval($xrif).' AND xon = 1');
	}
	
	/**
	 * Refresh privilege types of the group (insert, update and delete)
	 *
	 * @param   integer $id_group Group ID
	 * @param   array	$insert array to insert
	 * @param   array	$update array to update
	 * @param   array	$delete array to delete
	 * @return  array	Array(0, boolean)
	 */
	public function update_gprivs($id_group, $insert, $update, $delete)
	{
		$sql = array();
		
		// insert
		foreach($insert as $k => $v) 
			$sql[] = 'INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), '.$id_group.', '.$this->db->escape($k).', '.intval($v).', 1)';
		
		// update
		foreach($update as $k => $v) 
			$sql[] = 'UPDATE gprivs SET updated = NOW(), level = '.intval($v).' WHERE id_group = '.intval($id_group).' AND what = '.$this->db->escape($k);
		
		// delete
		foreach($delete as $i) 
			$sql[] = 'DELETE FROM gprivs WHERE id_group = '.intval($id_group).' AND what = '.$this->db->escape($i);
		
		if (empty($sql)) 
			$res = array(0,1);
		else 
		{
			$res = $this->db->multi_exec($sql);
			
			// after the refresh perform a refactory on users of the group
			if ($res[1]) 
				$this->refactory_group($id_group);
		}
		return $res;
	}
	
	/**
	 * Perform a permission's refactory on all users of a group
	 *
	 * @param   integer $id_group group ID
	 * @return  array	Array(0, boolean)
	 */
	private function refactory_group($id_group)
	{
		// get users
		$user = new User_model();
		$u = $user->get_users($id_group);
		
		// refactory user permission
		foreach($u as $i) 
			$res = $this->refactory($i->id);
		
		return (empty($u)) 
			? array(0, 1) 
			: $res;
	}
	
	/**
	 * Refresh user permissions (insert, update and delete)
	 *
	 * @param   integer $id_user User ID
	 * @param   integer $id_area Area ID
	 * @param   array	$insert array to insert
	 * @param   array	$update array to update
	 * @param   array	$delete array to delete
	 * @return  array	Array(0, boolean)
	 */
	public function update_uprivs($id_user, $id_area, $insert, $update, $delete)
	{
		$sql = array();
		
		// delete
		foreach($delete as $k => $v) 
			$sql[] = 'DELETE FROM uprivs WHERE id_area = '.intval($id_area).' AND id_user = '.intval($id_user).' AND privtype = '.$this->db->escape($k);
		
		// insert
		foreach($insert as $k => $v) 
			$sql[] = 'INSERT INTO uprivs (updated, id_area, id_user, privtype, level, xon) VALUES (NOW(), '.$id_area.', '.$id_user.', '.$this->db->escape($k).', '.intval($v).', 1)';
		
		// update
		foreach($update as $k => $v) 
			$sql[] = 'UPDATE uprivs SET updated = NOW(), level = '.intval($v).' WHERE id_area = '.intval($id_area).' AND id_user = '.intval($id_user).' AND privtype = '.$this->db->escape($k);
		
		if (empty($sql)) 
			$res = array(0,1);
		else 
		{
			$res = $this->db->multi_exec($sql);
			
			// refactory user permission
			if ($res[1]) 
				$this->refactory($id_user, 1);
		}
		return $res;
	}
	
	/**
	 * Get user permission on all record in a table by id_area
	 * This method returns all the records in a table
	 * For each element collects: id, name and description (This is the reason why every table must have the following fields: id, name, description)
	 *
	 * @param   integer $id_user User ID
	 * @param   integer	$id_area Area ID
	 * @param   string	$table Table name
	 * @return  array	array of objects
	 */
	public function get_detail($id_user, $id_area, $table)
	{
		// switch table
		switch($table) 
		{
		case 'articles':
			$sql = 'SELECT a.id, a.name, CONCAT(c.name, \' - \', a.lang) AS description, p.level 
				FROM articles a
				JOIN contexts c ON c.code = a.code_context
				LEFT JOIN privs p ON p.what = \'articles\' AND p.id_what = a.id AND p.id_who = '.intval($id_user).'
				WHERE a.id_area = '.intval($id_area).'
				GROUP BY a.bid
				ORDER BY a.id ASC';
			break;
		case 'contexts':
			$sql = 'SELECT c.id, c.name, c.lang AS description, p.level FROM contexts c
				LEFT JOIN privs p ON p.what = \'contexts\' AND p.id_what = c.id AND p.id_who = '.intval($id_user).'
				WHERE c.id_area = '.intval($id_area).'
				ORDER BY c.id ASC';
			break;
		case 'files':
			$sql = 'SELECT c.id, c.name, c.alt AS description, p.level FROM files c
				LEFT JOIN privs p ON p.what = \'files\' AND p.id_what = c.id AND p.id_who = '.intval($id_user).'
				WHERE c.id_area = '.intval($id_area).'
				ORDER BY c.id ASC';
			break;
		case 'dictionary':
			$sql = 'SELECT d.id, d.xkey AS name, d.what AS description, p.level FROM dictionary d
				JOIN aprivs a ON a.id = '.intval($id_area).' AND a.area = d.area 
				LEFT JOIN privs p ON p.what = \'dictionary\' AND p.id_what = d.id AND p.id_who = '.intval($id_user).'
				ORDER BY d.id ASC';
			break;
		case 'languages':
			$sql = 'SELECT l.id, l.code AS name, l.language AS description, p.level 
				FROM languages l 
				LEFT JOIN privs p ON p.what = \'languages\' AND p.id_what = l.id AND p.id_who = '.intval($id_user).'
				ORDER BY l.code ASC';
			break;
		case 'menus':
		case 'templates':
			$sql = 'SELECT t.id, t.name, CONCAT(ar.title, \' - \', th.description, \' - \', t.description) AS description, p.level
				FROM '.$table.' t
				JOIN themes th ON th.id = t.id_theme
				LEFT JOIN areas ar ON ar.id_theme = th.id 
				JOIN aprivs a ON a.id_area = ar.id 
				LEFT JOIN privs p ON p.what = \''.$table.'\' AND p.id_what = t.id AND p.id_who = '.intval($id_user).'
				GROUP BY t.id
				ORDER BY t.id ASC';
			break;
		case 'pages':
			$sql = 'SELECT p.id, p.name, CONCAT(p.lang, \' - \', p.description) AS description, pr.level 
				FROM pages p
				JOIN aprivs a ON a.id_area = p.id_area 
				LEFT JOIN privs pr ON pr.what = \'pages\' AND pr.id_what = p.id AND pr.id_who = '.intval($id_user).'
				WHERE a.id_user = '.intval($id_user).' AND a.xon = 1 AND p.id_area = '.intval($id_area).'
				ORDER BY p.lang ASC, p.ordinal ASC';
			break;
		case 'themes':
			$sql = 'SELECT DISTINCT t.id, t.name, t.description, p.level 
				FROM themes t 
				JOIN areas ar ON ar.id_theme = t.id 
				LEFT JOIN privs p ON p.what = \'themes\' AND p.id_what = t.id AND p.id_who = '.intval($id_user).'
				ORDER BY t.id ASC';
			break;
		case 'users':
			$sql = 'SELECT u.id, u.username AS name, CONCAT(g.description, \' - \', u.description) AS description, p.level 
				FROM users u
				JOIN groups g ON g.id = u.id_group 
				LEFT JOIN privs p ON p.what = \'users\' AND p.id_what = u.id AND p.id_who = '.intval($id_user).'
				ORDER BY u.id ASC';
			break;
		default:
			// for generic tables and modules
			$sql = 'SELECT t.id, t.name, t.description, p.level
				FROM '.$table.' t
				JOIN aprivs a ON a.id_area = t.id_area
				LEFT JOIN privs p ON p.what = \''.$table.'\' AND p.id_what = t.id AND p.id_who = '.intval($id_user).'
				WHERE a.id_user = '.intval($id_user).' AND a.xon = 1  AND a.id_area = '.intval($id_area).' 
				GROUP BY t.id 
				ORDER BY t.id ASC';
			break;
		}
		return $this->db->query($sql);
	}
	
	/**
	 * Refresh user privs on a table
	 *
	 * @param   integer $id_user User ID
	 * @param   integer	$id_area Area ID
	 * @param   string	$table Table name
	 * @param   array	$array Associative array(id, value)
	 * @return  array	Array(0, boolean)
	 */
	public function update_detail_privs($id_user, $id_area, $table, $array)
	{
		$sql = array();
		foreach($array as $i)
		{
			$sql[] = 'UPDATE privs SET updated = NOW(), level = '.intval($i['value']).' WHERE id_what = '.intval($i['id']).' AND id_area = '.intval($id_area).' AND id_who = '.intval($id_user).' AND what = '.$this->db->escape($table);
		}
		$res = $this->db->multi_exec($sql);
		return $res;
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
	 *
	 * @param   integer $id item ID
	 * @return  void
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}
}
