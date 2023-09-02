<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
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
	public function get_user_by_id(int $id)
	{
		return $this->db->query_row('SELECT u.*, g.name AS groupname, IF(p.id IS NULL, up.level, p.level) AS plevel
				FROM users u
				JOIN uprivs up ON up.id_user = '.intval($_SESSION['xuid']).' AND up.privtype = '.$this->db->escape('users').'
				LEFT JOIN privs p ON p.id_who = up.id_user AND p.what = up.privtype AND p.id_what = u.id
				JOIN xgroups g ON g.id = u.id_group
				WHERE u.id = '.$id);
	}

	/**
	 * Get users by Group ID
	 * Join with privs table
	 *
	 * @param   integer $id_group Group ID
	 * @return  array	Array of objects
	 */
	public function get_users(int $id_group)
	{
        	return $this->db->query('SELECT u.*, IF(p.id IS NULL, up.level, p.level) AS level
				FROM users u
				JOIN uprivs up ON up.id_user = '.intval($_SESSION['xuid']).' AND up.privtype = '.$this->db->escape('users').'
				LEFT JOIN privs p ON p.id_who = up.id_user AND p.what = up.privtype AND p.id_what = u.id
				JOIN aprivs ap ON ap.id_user = '.intval($_SESSION['xuid']).' AND ap.id_area != 1
				JOIN aprivs ap2 ON ap2.id_user = u.id AND ap2.id_area = ap.id_area
				WHERE u.id_group = '.$id_group.' AND (u.hidden = 0 OR '.intval($_SESSION['level']).' = 4)
				GROUP BY u.id
				ORDER BY u.username ASC');
	}

	/**
	 * Check if username and email address are already used by another user
	 *
	 * @param   string	$username
	 * @param   string	$mail Email address
	 * @param   integer	$id User ID
	 * @return  integer
	 */
	public function exists(string $username, string $mail, int $id = 0)
	{
		// condition
		$where = ($id)
			? ' AND id <> '.$id
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
	public function __construct(int $id_group, string $lang)
	{
		$this->id_group = $id_group;
		$this->lang = $lang;
	}
}
