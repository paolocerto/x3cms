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
 * Helper common
 * This class contains many methods commonly used in X4WEBAPP
 *
 * @package X4WEBAPP
 */
class X4Utils_helper
{
	/**
	 * hashing
	 *
	 * @static
	 * @param string	$str String to hash
	 * @param string	$salt optional salt
	 * @return string
	 */
	public static function hashing($str, $salt = '')
	{
		return hash(HASH, $str.$salt);
	}

	/**
	 * Check if user need to be logged
	 *
	 * @static
	 * @param integer	$id_area area ID
	 * @param string	$location area/controller where redirect user for login
	 * @param string	$users name of the table where search users
	 * @return void
	 */
	public static function logged($id_area = 1, $location = 'admin/login', $users = 'users')
	{
		if (!isset($_SESSION['site']) || $_SESSION['site'] != SITE || $_SESSION['id_area'] != $id_area)
		{
			// check for cookie HASH
			$chk = false;
			// check hashkey
			if (isset($_COOKIE[COOKIE.'_hash']) && $_COOKIE[COOKIE.'_hash'] != '')
			{
				$mod = new X4Auth_model($users);
				$private = $mod->get_var($id_area, 'areas', 'private');
				if ($private)
				{
				    $chk = $mod->rehash($id_area, $_COOKIE[COOKIE.'_hash'], $users);
				}
				else
				{
				    $chk = true;
				}
			}

			if (!$chk)
			{

				header('Location: '.ROOT.$location);
				die;
			}
		}
	}

	/**
	 * Check if a call is an AJAX call
	 *
	 * @static
	 * @return boolean
	 */
	public static function is_ajax()
	{
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}

	/**
	 * Check if site is offline
	 *
	 * @static
	 * @param boolean	$xon site status
	 * @param string	$url controller where redirect unlogged user
	 * @return void
	 */
	public static function offline($xon, $url)
	{
		if (!$xon && !isset($_SESSION['xuid']) && X4Route_core::$control != 'offline')
		{
		    if (X4Route_core::$control != 'offline')
		    {
		        header('Location: '.$url);
		        die;
		    }
		    else
		    {
		        header('HTTP/1.1 503 Service Temporarily Unavailable');
		        header('Retry-After: 10800');
		    }
		}
	}

	/**
	 * Define a personal base URL
	 *
	 * @static
	 * @param string	$base_url URL
	 * @return void
	 */
	public static function set_mybase_url($base_url)
	{
		define('MYBASE_URL', $base_url);
	}

	/**
	 * Put the message into a session variable
	 *
	 * @static
	 * @param mixed		$res boolean/array query result
	 * @param string	$ok message if all run fine
	 * @param string	$ko error message
	 * @return void
	 */
	public static function set_msg($res, $ok = _MSG_OK, $ko = _MSG_ERROR)
	{
        switch(gettype($res))
		{
			case 'boolean':
				$_SESSION['msg'] = ($res) ? $ok : $ko;
				break;
			case 'array':
				$_SESSION['msg'] = ($res[1] >= 0) ? $ok : $ko;
				break;
		}
	}

	/**
	 * Get field's error message from a form and put it into a session variable
	 *
	 * @static
	 * @param array		$fields form array
	 * @param string	$title error message title
	 * @return mixed
	 */
	public static function set_error($fields, $title = '_form_not_valid', $set_session = true)
	{
		$dict = new X4Dict_model(X4Route_core::$folder, X4Route_core::$lang);
		$msg = $dict->get_word($title, 'form');
		$fields = self::normalize_form($fields);

		foreach ($fields as $i)
		{
			if (isset($i['error']))
			{
				foreach ($i['error'] as $e)
				{
					// set the available label
					$label = ((is_null($i['label']) && isset($i['alabel'])) || isset($i['alabel']))
						? $i['alabel']
						: $i['label'];

					if (isset($i['rule_msg']))
					{
						// force personalized message
						// to use only when you can have only one type of validation error
						$msg .= '<br /><u>'.$label.'</u> '.$i['rule_msg'];
					}
					elseif (isset($e['related']))
					{
						// for related fields
						$src = array('XXXRELATEDXXX');
						$rpl = array();

						$related = $e['related'];
						if (isset($fields[$related]))
						{
							// if is a related field
							$rpl[] = ((is_null($fields[$related]['label']) && isset($fields[$related]['alabel'])) || isset($fields[$related]['alabel']))
								? $fields[$related]['alabel']
								: $fields[$related]['label'];
						}
						else
						{
							// if is a related value
							$rpl[] = $related;
						}

						if (isset($e['relatedvalue']))
						{
							$src[] = 'XXXVALUEXXX';
							$rpl[] = $e['relatedvalue'];
						}

						$msg .= '<br /><u>'.$label.'</u> '.str_replace($src, $rpl, $dict->get_word($e['msg'], 'form'));
					}
					else
					{
						$msg .= '<br /><u>'.$label.'</u> '.$dict->get_word($e['msg'], 'form');
					}

					// debug
					if (isset($e['debug']))
					{
						$msg .= '<br />'.$e['debug'];
					}
				}
			}
		}

		if ($set_session)
		{
			$_SESSION['msg'] = $msg;
		}
		else
		{
			return $msg;
		}
	}

	/**
	 * Get field's error array to use with JS validation
	 *
	 * @static
	 * @param array		$fields form array
	 * @return array
	 */
	public static function get_error($fields)
	{
		$dict = new X4Dict_model(X4Route_core::$folder, X4Route_core::$lang);
		$fields = self::normalize_form($fields);

		$error = array();
		foreach ($fields as $i)
		{
			if (isset($i['error']))
			{
				foreach ($i['error'] as $ii)
				{
					// for related fields
					if (isset($ii['related']))
					{
						$related = $ii['related'];
						if (isset($fields[$related]))
						{
							// if is a related field
							$rpl = ((is_null($fields[$related]['label']) && isset($fields[$related]['alabel'])) || isset($fields[$related]['alabel']))
								? $fields[$related]['alabel']
								: $fields[$related]['label'];
						}
						else
						{
							// if is a related value
							$rpl = $related;
						}

						$msg = str_replace('XXXRELATEDXXX', $rpl, $dict->get_word($ii['msg'], 'form'));
					}
					else
					{
						$msg = $dict->get_word($ii['msg'], 'form');
					}


					// debug
					if (isset($ii['debug']))
					{
						$msg .= '<br />'.$ii['debug'];
					}

					$error[$i['name']] = addslashes(html_entity_decode($msg));
				}
			}
		}
		return $error;
	}

	/**
	 * Return the form array with as index the name of the field
	 *
	 * @static
	 * @param array		$fields form array
	 * @return array
	 */
	public static function normalize_form($array)
	{
		$a = array();
		foreach ($array as $i)
		{
			if (isset($i['name']))
			{
				$a[$i['name']] = $i;
			}
		}
		return $a;
	}

	/**
	 * Change encoding
	 *
	 * @static
	 * @param string	$text text to convert
	 * @return string
	 */
	public static function to7bit($text)
    {
        $enc = mb_detect_encoding($text);
        if ($enc != 'UTF-8')
        {
            $text = mb_convert_encoding($text, 'UTF-8', $enc);
        }
        $text = htmlspecialchars_decode($text);

        $text = preg_replace_callback(
            array(
                '/&(..)lig;/',
                '/&([aouAOUy])uml;/',
                '/&(.)[^;]*;/'
            ),
            function($m)
            {
                if (isset($m[1]))
                {
                    return $m[1];
                }
            },
            $text);

        return $text;
	}

	/**
	 * Clean a string
	 *
	 * @static
	 * @param string	$str string to clean
	 * @param boolean	$deep If true replace '.' too
	 * @param boolean	$negative If true replace - with _
	 * @return string
	 */
	public static function slugify($str, $deep = false, $negative = false)
	{
		$str = trim($str);
		$str = X4Utils_helper::to7bit($str);
		$str = strtolower(html_entity_decode($str));

        // strip special chars
        $str = preg_replace_callback(
        '/[àèéìòùç]+/is',
        function($m)
        {
            $r = '';
            switch($m[0])
            {
                case 'à':
                    $r = 'a';
                    break;
                case 'è':
                case 'é':
                    $r = 'e';
                    break;
                case 'ì':
                    $r = 'i';
                    break;
                case 'ò':
                    $r = 'o';
                    break;
                case 'ù':
                    $r = 'u';
                    break;
                case 'ç':
                    $r = 'c';
                    break;
            }
            return $r;
        },
        $str);

        // clean
        $regex = ($deep)
            ? '/[^a-z0-9-]+/is'
            : '/[^a-z0-9-\/\.]+/is';

        $res = preg_replace_callback(
            $regex,
            function($m)
            {
                return '-';
            },
            $str);

        // remove duplicates
        $res = preg_replace_callback(
            '/-(-*)/',
            function($m)
            {
                return '-';
            },
            $res);

		return ($negative)
			? str_replace('-', '_', $res)
			: $res;
	}

	/**
	 * Clean a string
	 * remove quote and slashes
	 *
	 * @static
	 * @param string	$str string to clean
	 * @return string
	 */
	public static function clean_str($str)
	{
		return stripslashes(str_replace(
			array('"'),
			array(''),
			$str
		));
	}

	/**
	 * Format money value
	 *
	 * @static
	 * @param float		$num value
	 * @param string	$value currency
	 * @param integer	$decimal number of decimals
	 * @param boolean	$after switch the position of the currency
	 * @return string
	 */
	public static function currency($num, $value = '&euro;', $decimal = 2, $after = true)
	{
        $num = (float) $num;
		$res = number_format($num, $decimal, ',', '.');

		if ($after)
		{
			$res .= ' '.$value;
		}
		else
		{
			$res = $value.' '.$res;
		}

		return $res;
	}

	/**
	 * URL correction
	 *
	 * @static
	 * @param string	$url URL
	 * @return string
	 */
	public static function check_url($url, $protocol = 'http')
	{
		if (substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://')
		{
			return $url;
		}
		else
		{
			return $protocol.'://'.$url;
		}
	}

	/**
	 * Get an integer from an ordinal string
	 * ordinal is a string ok tokens, each token is a 4 char string
	 *
	 * @static
	 * @param string	$ordinal ordinal string
	 * @param integer	$token token index
	 * @return integer
	 */
	public static function get_ordinal_value($ordinal, $token)
	{
		$a = explode('!', chunk_split(substr($ordinal, 1), 3, '!'));
		return base_convert($a[$token], 36, 10);
	}


	/**
	 * Disable caching
	 *
	 * @return void
	 */
	public static function nocache()
	{
		if (!defined('NOCACHE')) define('NOCACHE', true);
	}

	/**
	 * Return Client IP address
	 *
	 * @return string
	 */
	public static function get_ip()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}
