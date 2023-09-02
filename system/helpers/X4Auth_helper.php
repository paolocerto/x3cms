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
			foreach ($fields as $k => $v)
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

    /**
     * Get header Authorization
     * */
    public static function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization']))
        {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION']))
        {
            //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        }
        elseif (function_exists('apache_request_headers'))
        {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization']))
            {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /**
    * get access token from header
    * */
    public static function getBearerToken()
    {
        $headers = self::getAuthorizationHeader();

        // HEADER: Get the access token from the header
        if (!empty($headers))
        {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches))
            {
                return $matches[1];
            }
        }
        return null;
    }
}
