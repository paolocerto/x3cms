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
	 */
	public static function hashing(string $str, string $salt = '') : string
	{
        // HASH is defined in config
		return hash(HASH, $str.$salt);
	}

	/**
	 * Check if user need to be logged
	 */
	public static function logged(int $id_area = 1, string $login_location = 'admin/login', string $users_table = 'users')
	{
		if (!isset($_SESSION['site']) || $_SESSION['site'] != SITE || $_SESSION['id_area'] != $id_area)
		{
			// check for cookie HASH
			$chk = false;
			// check hashkey
			if (isset($_COOKIE[COOKIE.'_hash']) && $_COOKIE[COOKIE.'_hash'] != '')
			{
                switch ($users_table)
                {
                    case 'users':
                        $mod = new X4Auth_model($users);
                        $chk = $mod->rehash($_COOKIE[COOKIE.'_hash']);
                        break;
                    default:
                        $model = ucfirst($users.'_model');
                        $mod = new $model();
                        $chk = $mod->rehash($_COOKIE[COOKIE.'_hash']);
                        break;
                }
			}

			if (!$chk)
			{
                header('Location: '.ROOT.$login_location);
                die;
			}
		}
	}

	/**
	 * Check if a call is an AJAX call
	 */
	public static function is_ajax() : bool
	{
		return (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        );
	}

	/**
	 * Check if site is offline
	 */
	public static function offline(int $site_status, string $offline_url)
	{
		if (!$site_status && !isset($_SESSION['xuid']) && X4Route_core::$control != 'offline')
		{
		    if (X4Route_core::$control != 'offline')
		    {
		        header('Location: '.$offline_url);
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
	 */
	public static function set_mybase_url(string $base_url)
	{
		define('MYBASE_URL', $base_url);
	}

	/**
	 * Put the message into a session variable
	 */
	public static function set_msg(mixed $res, string $ok = _MSG_OK, string $ko = _MSG_ERROR)
	{
        switch (gettype($res))
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
	 * Get field's error message from a form and put it into a session variable if set_session is true
	 */
	public static function set_error(array $fields, string $title = '_form_not_valid', string $return = 'session')
	{
		$dict = new X4Dict_model(X4Route_core::$folder, X4Route_core::$lang);
		$msg = $dict->get_word($title, 'form');
		$fields = self::normalize_form($fields);

        $errors = array();
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
							$rpl[] = (
                                (is_null($fields[$related]['label']) && isset($fields[$related]['alabel'])) ||
                                isset($fields[$related]['alabel'])
                            )
								? $fields[$related]['alabel']
								: $fields[$related]['label'];
						}
						else
						{
							$rpl[] = $related;  // if is a related value
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

					if (isset($e['debug']))
					{
						$msg .= '<br />'.$e['debug'];
					}

                    $errors[$i['name']] = addslashes(html_entity_decode($msg));
				}
			}
		}

		switch ($return)
		{
            case 'array':
                // for AJAX
                return $errors;
                break;
            case 'string':
                return $msg;
                break;
            default:
			    $_SESSION['msg'] = $msg;
                break;
		}
	}

	/**
	 * Return the form array with as index the name of the field
	 */
	public static function normalize_form(array $array) : array
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
	 */
	public static function to7bit(string $text) : string
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
	 * Slugify a string
	 */
	public static function slugify(
        string $str,
        bool $deep = false,         // If true replace '.' too
        bool $negative = false      // If true replace - with _
    ) : string
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
                switch ($m[0])
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
	 */
	public static function clean_str(string $str) : string
	{
		return stripslashes(str_replace(
			array('"'),
			array(''),
			$str
		));
	}

	/**
	 * Format money amount
	 */
	public static function currency(float $amount, string $currency = '&euro;', int $decimal = 2, bool $after = true) : string
	{
        $res = number_format($amount, $decimal, ',', '.');

		if ($after)
		{
			return $res .' '.$currency;
		}
        return $currency.' '.$res;
	}

	/**
	 * URL correction
	 */
	public static function check_url(string $url, string $protocol = 'http') : string
	{
		return (substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://')
            ? $url
            : $protocol.'://'.$url;
	}

	/**
	 * Get an integer from an ordinal string
	 * ordinal is a string of tokens, each token is a 4 char string
	 */
	public static function get_ordinal_value(string $ordinal, int $token) : int
	{
		$a = explode('!', chunk_split(substr($ordinal, 1), 3, '!'));
		return base_convert($a[$token], 36, 10);
	}

	/**
	 * Disable caching
	 */
	public static function nocache()
	{
		if (!defined('NOCACHE'))
        {
            define('NOCACHE', true);
        }
	}

	/**
	 * Return Client IP address
	 */
	public static function get_ip() : string
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			return $_SERVER['REMOTE_ADDR'];
		}
	}
}
