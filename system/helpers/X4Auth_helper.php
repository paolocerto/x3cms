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
 * Helper for Authentication
 * 
 * @package X4WEBAPP
 */
class X4Auth_helper 
{
	/**
	 * Authenticate user
	 *
	 * @static
	 * @param string	$table User table name
	 * @param array		$conditions array('field_name' => 'field_value')
	 * @param array		$fields array needed to set session  eg. array('field_name' => 'session_name')
	 * @param boolean	$last_in if true update last_in field in the user record
	 * @param boolean	$hash if true it uses hashkey to extend sessions life
	 * @return boolean
	 */
	public static function log_in($table, $conditions, $fields, $last_in = true, $hash = false)
	{
		$mod = new X4Auth_model($table);
		$user = $mod->log_in($conditions, $fields);
		
		// user exists!
		if (!empty($user)) 
		{
			// set session values
			foreach($fields as $k => $v)
			{
				$_SESSION[$v] = $user->$k;
			}
			
			// update last login field
			if ($last_in)
			{
			    $mod->last_in($user->id);
			}
			
			if ($hash)
			{
				$new_hash = md5($conditions['username'].$conditions['password'].time().SALT);
				$res = $mod->update($user->id, array('hashkey' => $new_hash), 'users');
				
				if ($res[1])
				{
					setcookie(COOKIE.'_hash', $new_hash, time() + 2592000, '/', $_SERVER['HTTP_HOST']);
				}
			}
			
			return true;
		}
		return false;
	}
	
	/**
	 * Perform logout
	 *
	 * @return  void
	 */
	public static function log_out()
	{
		// destroy session
		session_unset();
		session_destroy();
	}
	
}
