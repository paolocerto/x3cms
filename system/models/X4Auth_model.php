<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
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
        //2 => array('table' => 'x3_students', 'session' => 'uid', 'username' => 'email', 'mail' => 'email', 'last_in' => 'last_in'),
        4 => array('table' => 'x3_students', 'session' => 'uid', 'username' => 'nickname', 'mail' => 'email', 'last_in' => 'last_in', )
	);

	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct($table_name)
	{
		parent::__construct($table_name);

		// update areas tables
		$areas = $this->db->query('SELECT id FROM areas WHERE id > 1 AND private = 1');
		foreach ($areas as $i)
		{
		    $this->areas_tables[$i->id] = array('table' => $table_name, 'session' => 'uid', 'username' => 'title', 'mail' => 'mail', 'last_in' => 'last_in');
		}
	}

	/**
	 * Find user
	 *
	 * @param   array	$conditions Login conditions
	 * @param   array	$fields Fields to get
	 * @return  object
	 */
	public function log_in($conditions, $fields)
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
	 *
	 * @param   integer	$id User ID
	 * @return  array
	 */
	public function last_in($id)
	{
		return $this->db->single_exec('UPDATE '.$this->table.' SET last_in = \''.date('Y-m-d H:i:s').'\' WHERE id = '.intval($id));
	}

	/**
	 * Get user by email
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string	$email Email address
	 * @return  object
	 */
	public function get_user_by_email($id_area, $email)
	{
		// users are joined to groups
		if ($this->table == 'users')
		{
			return $this->db->query_row('SELECT u.* FROM users u
				JOIN xgroups g ON g.id = u.id_group
				WHERE
					g.id_area = '.intval($id_area).' AND
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
	 *
	 * @param   string	$mail Subscriber mail
	 * @param   string	$new_pwd Subscriber password
	 * @return  integer
	 */
	public function reset($mail, $new_pwd)
	{
		$id = $this->db->query_var('SELECT id FROM '.$this->table.' WHERE mail = '.$this->db->escape(strtolower($mail)));

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
	 * Get user by hash
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string	$hash Member hashkey
	 * @param	string	$usertable
	 * @return  object
	 */
	public function log_in_by_hash(int $id_area, string $hash)
	{
		if ($id_area > 1)
		{
			return $this->db->query_row('SELECT * FROM x3_users WHERE id_area = '.$id_area.' AND xon = 1 AND hashkey = '.$this->db->escape($hash));
            
		}
		else
		{
			return $this->db->query_row('SELECT * FROM '.$this->areas_tables[$id_area]['table'].' WHERE xon = 1 AND hashkey = '.$this->db->escape($hash));
		}
	}

	/**
	 * Log in by hash
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string	$hash Member hashkey
	 * @return  void
	 */
	public function rehash($id_area, $hash)
	{
        // get user data
		$u = $this->log_in_by_hash($id_area, $hash);

		if (is_object($u) && isset($u->id))
		{
            $_SESSION['site'] = SITE;
			$_SESSION['lang'] = $u->lang;
            $_SESSION['id_area'] = $id_area;
			$_SESSION['last_in'] = $u->last_in;

            // level
            if ($id_area == 1)
            {
                $_SESSION['xuid'] = $u->id;
                $_SESSION['mail'] = $u->mail;
                $_SESSION['username'] = $u->username;
                $_SESSION['level'] = $u->level;
                $_SESSION['id_group'] = $u->id_group;
			}
			else
			{
                $_SESSION['uid'] = $u->id;
                $_SESSION['nickname'] = $u->nickname;
                $_SESSION['email'] = $u->email;
                
			}
			return true;
		}
		else
		{
			return false;
		}
	}
}
