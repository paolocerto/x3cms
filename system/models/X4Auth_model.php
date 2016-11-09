<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
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
		1 => array('table' => 'users', 'session' => 'xuid', 'username' => 'username', 'mail' => 'mail', 'last_in' => 'last_in')
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
		foreach($areas as $i)
		{
		    $this->areas_tables[$i->id] = array('table' => 'x3_members', 'session' => 'uid', 'username' => 'title', 'mail' => 'mail', 'last_in' => 'last_in');
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
		foreach($conditions as $k => $v)
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
				JOIN groups g ON g.id = u.id_group
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
				JOIN groups g ON g.id = u.id_group
				WHERE 
					g.id_area = '.intval($id_area).' AND 
					u.mail = '.$this->db->escape($email));
		}
		else
		{
			return $this->db->query_row('SELECT * FROM '.$this->table.' WHERE id_area = '.intval($id_area).' AND mail = '.$this->db->escape($email));
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
	 * @return  object
	 */
	public function log_in_by_hash($id_area, $hash)
	{
		return $this->db->query_row('SELECT * FROM '.$this->areas_tables[$id_area]['table'].' WHERE xon = 1 AND hashkey = '.$this->db->escape($hash));
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
	    // to handle generic private area
	    $tmp_id_area = ($id_area > 3)
	        ? 3
	        : $id_area;
	    
		$u = $this->log_in_by_hash($id_area, $hash);
		
		if ($u)
		{
			$_SESSION['site'] = SITE;
			$_SESSION['lang'] = $u->lang;
			
			$username = $this->areas_tables[$tmp_id_area]['username'];
			$_SESSION['nickname'] = $u->$username;
			
			$mail = $this->areas_tables[$tmp_id_area]['mail'];
			$_SESSION['mail'] = $u->$mail;
			
			$_SESSION[$this->areas_tables[$tmp_id_area]['session']] = $u->id;
			$_SESSION['id_area'] = $id_area;
			//$_SESSION['timer'] = time();
			$last_in = $this->areas_tables[$tmp_id_area]['last_in'];
			$_SESSION['last_in'] = $u->$last_in;
			
$this->logger($_SESSION[$this->areas_tables[$tmp_id_area]['session']], 1, 'users', 're-log in');
			
            // level
            if ($id_area == 1)
            {
                $_SESSION['username'] = $u->$username;
                $_SESSION['level'] = $u->level;
            }
            
			return true;
		}
		else
		{
			return false;
		}
	}
}
