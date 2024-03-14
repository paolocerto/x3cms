<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X4WEBAPP
 */

/**
 * Model for Authentication
 *
 * @package X4WEBAPP
 */
class X4Auth_model extends X4Model_core
{
	/*
	 * Here store connection between id_area and related table
	 * In the table we should have a field named "hashkey" VARCHAR(32)
	 */
	protected $areas_tables = array(
		1 => array('table' => 'users', 'session' => 'xuid', 'username' => 'username', 'mail' => 'mail', 'last_in' => 'last_in'),
	);

	/**
	 * Constructor
	 * set the default table
	 */
	public function __construct(string $table_name)
	{
		parent::__construct($table_name);
	}

	/**
	 * Find user
	 */
	public function log_in(array $conditions, array $fields) : stdClass
	{
		// fields values to get from the user record
		$keys = implode(', u.', array_keys($fields));

		// where
		$where = '';
		foreach ($conditions as $k => $v)
		{
			if ($this->table == 'users' && $k == 'id_area')
			{
				$where .= ' AND g.'.$k.' = '.intval($v);
			}
			else
			{
				$where .= ' AND u.'.$k.' = '.$this->db->escape($v);
			}
		}

		// users are joined to groups
		if ($this->table == 'users')
		{
		    return $this->db->query_row('SELECT u.'.$keys.', g.id_area
				FROM users u
				JOIN xgroups g ON g.id = u.id_group
				WHERE u.xon = 1'.$where);
		}
		else
		{
			return $this->db->query_row('SELECT u.'.$keys.'
				FROM '.$this->table.' u
				WHERE u.xon = 1'.$where);
		}
	}

	/**
	 * Update date and time of last log in
	 */
	public function last_in(int $id) : array
	{
		return $this->db->single_exec('UPDATE '.$this->table.' SET last_in = \''.date('Y-m-d H:i:s').'\' WHERE id = '.intval($id));
	}

	/**
	 * Get user by email
	 */
	public function get_user_by_email(int $id_area, string $email) : stdClass
	{
		// users are joined to groups
		if ($this->table == 'users')
		{
			return $this->db->query_row('SELECT u.* FROM users u
				JOIN xgroups g ON g.id = u.id_group
				WHERE
					g.id_area = '.$id_area.' AND
					u.mail = '.$this->db->escape($email));
		}
		else
		{
            // id_area = '.intval($id_area).' AND
			return $this->db->query_row('SELECT * FROM '.$this->table.' WHERE email = '.$this->db->escape($email));
		}
	}

	/**
	 * Reset password
	 */
	public function reset(string $mail, string $new_pwd) : int
	{
		$id = (int) $this->db->query_var('SELECT id FROM '.$this->table.' WHERE mail = '.$this->db->escape(strtolower($mail)));

		if($id)
		{
			$array = array('password' => X4Utils_helper::hashing($new_pwd));
			$result = $this->update($id, $array);
			return intval($result[1]);
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Log in by hash
	 */
	public function rehash(string $hash) : bool
	{
        // get user data
		$u = $this->db->query_row('SELECT * FROM users WHERE id_area = 1 AND xon = 1 AND hashkey = '.$this->db->escape($hash));

		if (!is_object($u) || !isset($u->id))
		{
            return false;
        }

        $_SESSION['site'] = SITE;
        $_SESSION['lang'] = $u->lang;
        $_SESSION['id_area'] = 1;
        $_SESSION['last_in'] = $u->last_in;

        $_SESSION['xuid'] = $u->id;
        $_SESSION['mail'] = $u->mail;
        $_SESSION['username'] = $u->username;
        $_SESSION['level'] = $u->level;
        $_SESSION['id_group'] = $u->id_group;
        return true;
	}
}
