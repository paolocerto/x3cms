<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

/**
 * Model for Users Items
 *
 * @package X3CMS
 */
class User_model extends X4Model_core 
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('users');
	}
	
	/**
	 * Get permissions levels
	 * Use levels
	 *
	 * @return  array	Array of objects
	 */
	public function get_levels()
	{
		return $this->db->query('SELECT * FROM levels ORDER BY id ASC');
	}
	
	/**
	 * Get user by ID
	 * Join with privs and groups tables
	 *
	 * @param   integer $id User ID
	 * @return  object
	 */
	public function get_user_by_id($id)
	{
		return $this->db->query_row('SELECT u.*, g.name AS groupname, p.level AS plevel
				FROM users u 
				JOIN privs p ON p.id_who = '.intval($_SESSION['xuid']).' AND p.what = \'users\' AND p.id_what = u.id
				JOIN groups g ON g.id = u.id_group
				WHERE u.id = '.intval($id));
	}
	
	/**
	 * Get users by Group ID
	 * Join with privs table
	 *
	 * @param   integer $id_group Group ID
	 * @return  array	Array of objects
	 */
	public function get_users($id_group)
	{
		return $this->db->query('SELECT u.*, p.level 
				FROM users u 
				JOIN privs p ON p.id_who = '.intval($_SESSION['xuid']).' AND p.what = \'users\' AND p.id_what = u.id
				WHERE u.id_group = '.intval($id_group).' AND (u.hidden = 0 OR '.intval($_SESSION['level']).' = 4) 
				ORDER BY u.username ASC');
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
	 * Check if username and email address are already used by another user
	 *
	 * @param   string	$username
	 * @param   string	$mail Email address
	 * @param   integer	$id User ID
	 * @return  integer
	 */
	public function exists($username, $mail, $id = 0) 
	{
		// condition
		$where = ($id) 
			? ' AND id <> '.intval($id)
			: '';
			
		return $this->db->query_var('SELECT COUNT(id) 
			FROM users 
			WHERE username = '.$this->db->escape($username).' AND mail = '.$this->db->escape($mail).' '.$where);
	}
	
}

/**
 * Empty User object
 * Necessary for the creation form of new user
 *
 * @package X3CMS
 */
class User_obj 
{
	public $lang;
	public $id_group;
	public $username;
	public $password;
	public $description;
	public $mail;
	public $phone;
	public $level = 0;
	
	/**
	 * Constructor
	 *
	 * @param   integer	$id_group Group ID
	 * @return  void
	 */
	public function __construct($id_group)
	{
		$this->id_group = $id_group;
	}
}
