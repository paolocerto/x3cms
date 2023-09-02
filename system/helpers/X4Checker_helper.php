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
 * Helper for the validation of special data
 *
 * @package X4WEBAPP
 */
class X4Checker_helper
{
	/**
	 * Check a date/datetime string
	 *
	 * @static
	 * @param string	$datetime	The date or datetime to check
	 * @param mixed		$return		The type of return value
	 * @param string	$date_format	The dateformat to use for the check
	 * @return boolean
	 */
	public static function isDateTime($datetime, $return = false, $date_format = 'Y-m-d H:i:s')
	{
		// check length
		$dt = date($date_format);
		if (strlen($dt) == strlen($datetime))
		{
			$d = X4Time_helper::parse_date($datetime, $date_format);

			// fix for 'trailing data' error
			$errors = array_values($d['errors']);

			if ($d['error_count'] == 0 || ($d['error_count'] == 1 && $errors[0] == 'Trailing data'))
			{
				if (checkdate($d['month'], $d['day'], $d['year']))
				{
					switch ($return)
					{
					case 'date':
						return $d['year'].'-'.str_pad($d['month'], 2, '0', STR_PAD_LEFT).'-'.str_pad($d['day'], 2, '0', STR_PAD_LEFT);
						break;

					case 'datetime':
						return $d['year'].'-'.str_pad($d['month'], 2, '0', STR_PAD_LEFT).'-'.str_pad($d['day'], 2, '0', STR_PAD_LEFT).'
							'.str_pad($d['hour'], 2, '0', STR_PAD_LEFT).':'.str_pad($d['minute'], 2, '0', STR_PAD_LEFT).':'.str_pad($d['second'], 2, '0', STR_PAD_LEFT);
						break;

					default:
						return true;
						break;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Check italian Fiscal ID for companies
	 *
	 * @static
	 * @param string	Fiscal ID
	 * @return boolean
	 */
	public static function isPIVA($pi)
	{
	    if(!preg_match('/^[0-9]+$/', $pi))
	    	return false;
	    $s = 0;
	    for( $i = 0; $i <= 9; $i += 2 )
	    	$s += ord($pi[$i]) - ord('0');

	    for( $i = 1; $i <= 9; $i += 2 )
	    {
	    	$c = 2*( ord($pi[$i]) - ord('0') );
	    	if( $c > 9 )
				$c = $c - 9;
			$s += $c;
	    }

	    if( ( 10 - $s%10 )%10 != ord($pi[10]) - ord('0') )
	    	return false;
	    return true;
	}

	/**
	 * Check italian Fiscal ID for people
	 *
	 * @static
	 * @param string	Fiscal ID
	 * @return boolean
	 */
	public static function isCF($cf)
	{
		$cf = strtoupper($cf);
		if(!preg_match('/^[A-Z0-9]+$/', $cf))
			return false;

		$s = 0;
		for( $i = 1; $i <= 13; $i += 2 )
		{
			$c = $cf[$i];
			if( '0' <= $c && $c <= '9' )
				$s += ord($c) - ord('0');
			else
				$s += ord($c) - ord('A');
		}

		for( $i = 0; $i <= 14; $i += 2 )
		{
			$c = $cf[$i];
			switch( $c )
			{
				case '0':  $s += 1;  break;
				case '1':  $s += 0;  break;
				case '2':  $s += 5;  break;
				case '3':  $s += 7;  break;
				case '4':  $s += 9;  break;
				case '5':  $s += 13;  break;
				case '6':  $s += 15;  break;
				case '7':  $s += 17;  break;
				case '8':  $s += 19;  break;
				case '9':  $s += 21;  break;
				case 'A':  $s += 1;  break;
				case 'B':  $s += 0;  break;
				case 'C':  $s += 5;  break;
				case 'D':  $s += 7;  break;
				case 'E':  $s += 9;  break;
				case 'F':  $s += 13;  break;
				case 'G':  $s += 15;  break;
				case 'H':  $s += 17;  break;
				case 'I':  $s += 19;  break;
				case 'J':  $s += 21;  break;
				case 'K':  $s += 2;  break;
				case 'L':  $s += 4;  break;
				case 'M':  $s += 18;  break;
				case 'N':  $s += 20;  break;
				case 'O':  $s += 11;  break;
				case 'P':  $s += 3;  break;
				case 'Q':  $s += 6;  break;
				case 'R':  $s += 8;  break;
				case 'S':  $s += 12;  break;
				case 'T':  $s += 14;  break;
				case 'U':  $s += 16;  break;
				case 'V':  $s += 10;  break;
				case 'W':  $s += 22;  break;
				case 'X':  $s += 25;  break;
				case 'Y':  $s += 24;  break;
				case 'Z':  $s += 23;  break;
			}
		}
		if( chr($s%26 + ord('A')) != $cf[15] )
			return false;

		return true;
	}

	/**
	 * verify_eu_vat()
	 *
	 * VIES (VAT Information Exchange System) enquiries
	 *
	 * @static
	 * @param mixed $vat_number
	 * @return boolean
	 */
	public static function verify_eu_vat($vat_number)
	{
		$country_id = substr($vat_number, 0, 2);
		$vat_id = substr($vat_number, 2);
		$url = "http://ec.europa.eu/taxation_customs/vies/viesquer.do?Lang=EN&ms=$country_id&iso=$country_id&vat=$vat_id";
		$reply = @file_get_contents($url);
		return strpos($reply, 'Yes, valid VAT number')
			? true
			: false;
	}

	/**
	 * Check DNS (for Windows Systems)
	 *
	 * @static
	 * @param string	host name
	 * @param string	host type
	 * @return boolean
	 */
	private static function win_checkdnsrr($host, $type = 'MX')
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN')
			return;

		if (empty($host))
			return;

		$types=array('A', 'MX', 'NS', 'SOA', 'PTR', 'CNAME', 'AAAA', 'A6', 'SRV', 'NAPTR', 'TXT', 'ANY');

		if (!in_array($type,$types))
		{
			user_error("checkdnsrr() Type '$type' not supported", E_USER_WARNING);
			return;
		}
		@exec('nslookup -type='.$type.' '.escapeshellcmd($host), $output);
		foreach ($output as $line)
		{
			if (preg_match('/^'.$host.'/',$line))
            {
				return true;
            }
		}
	}

	/**
	 * Check valid email address
	 *
	 * @static
	 * @param string	email address
	 * @return boolean
	 */
	public static function check_email($email, $force = false)
	{
        $chk = filter_var($email, FILTER_VALIDATE_EMAIL);

        if ($chk)
        {
            $mail = explode('@',$email);
            $domain = array_pop($mail);
            $chk = checkdnsrr($domain, 'MX');
        }
        return $force
            ? $chk
            : ($chk || DEBUG);
	}

	/**
	 * Check valid URL
	 *
	 * @static
	 * @param string	URL
	 * @return boolean
	 */
	public static function check_url($str)
	{
		return (filter_var($str, FILTER_VALIDATE_URL) !== false);
	}

	public static function curl_headers($url)
	{
		$c = curl_init($url);
		curl_setopt($c,  CURLOPT_RETURNTRANSFER, TRUE);
/*
		curl_setopt($c, CURLOPT_HEADER, true);
		curl_setopt($c, CURLOPT_NOBODY, true);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2);
//		curl_setopt($c, CURLOPT_URL, $url);
*/
		$headers = curl_exec($c);
		curl_close($c);
		return ( bool ) preg_match ( '#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers );
	}

	/**
	 * alternative header checker
	 *
	 * @static
	 * @param string	iban string
	 * @return boolean
	 */
	public static function alternative_headers($url)
	{
		$url_info = @parse_url($url);
		if (isset($url_info['scheme']) && $url_info['scheme'] == 'https')
		{
		   $port = 443;
		   @$fp=fsockopen('ssl://'.$url_info['host'], $port, $errno, $errstr, 10);
		}
		else
		{
		   $port = isset($url_info['port']) ? $url_info['port'] : 80;
		   @$fp=fsockopen($url_info['host'], $port, $errno, $errstr, 10);
		}
		if($fp)
		{
		   stream_set_timeout($fp, 10);
		   $head = "HEAD ".@$url_info['path']."?".@$url_info['query'];
		   $head .= " HTTP/1.0\r\nHost: ".@$url_info['host']."\r\n\r\n";
		   fputs($fp, $head);
		   while(!feof($fp))
		   {
			   if($header=trim(fgets($fp, 1024)))
			   {
				   $sc_pos = strpos( $header, ':' );
				   if( $sc_pos === false )
				   {
					   $headers['status'] = $header;
				   }
				   else
				   {
					   $label = substr( $header, 0, $sc_pos );
					   $value = substr( $header, $sc_pos+1 );
					   $headers[strtolower($label)] = trim($value);
				   }
			   }
		   }
		   return ( bool ) preg_match ( '#^HTTP/.*\s+[(200|301|302)]+\s#i', implode(' ', $headers));
		}
		else
		{
		   return false;
		}
    }

	/**
	 * verify iban code
	 *
	 * @static
	 * @param string	iban string
	 * @return boolean
	 */
	public static function verify_iban($value)
	{
		// Uppercase and trim spaces from left
		$value = ltrim(strtoupper($value));

		if (function_exists('preg_replace_callback'))
		{
			// Remove IBAN from start of string, if present
			$value = preg_replace_callback(
				'/^IBAN/is',
				function($m)
				{
					return '';
				},
				$value);
			// Remove all non basic roman letter / digit characters
			$value = preg_replace_callback(
				'/[^A-Z0-9]+/is',
				function($m)
				{
					return '';
				},
				$value);
		}
		else
		{
			// Remove IBAN from start of string, if present
			$value = preg_replace('/^IBAN/','',$value);
			// Remove all non basic roman letter / digit characters
			$value = preg_replace('/[^A-Z0-9]/','',$value);
		}

		// Get country of IBAN
		$c = substr($value,0,2);

		// Get length of IBAN
		if(strlen($value)!=27) return false;

		// Get checksum of IBAN
		$checksum = substr($value,2,2);;

		// Get country-specific IBAN format regex
		$regex = '/^IT(\d{2})([A-Z]{1})(\d{5})(\d{5})([A-Za-z0-9]{12})$/';

		// Check regex
		if(preg_match($regex,$value))
		{
			// Regex passed, check checksum
			if(!X4Checker_helper::iban_verify_checksum($value))
			{
				return false;
			}
		}
		else
		{
			return false;
		}

		// Otherwise it 'could' exist
		return true;
	}

	/**
	 * Check the checksum of an IBAN - code modified from Validate_Finance PEAR class
	 *
	 */
	private static function iban_verify_checksum($iban)
	{
		// move first 4 chars (countrycode and checksum) to the end of the string
		$tempiban = substr($iban, 4).substr($iban, 0, 4);
		// subsitutute chars
		$tempiban = X4Checker_helper::iban_checksum_string_replace($tempiban);
		// mod97-10
		$result = X4Checker_helper::iban_mod97_10($tempiban);
		// checkvalue of 1 indicates correct IBAN checksum
		if ($result != 1)
		{
			return false;
		}
		return true;
	}

	/**
	 * Character substitution required for IBAN MOD97-10 checksum validation/generation
	 *
	 *  $s  Input string (IBAN)
	 */
	private static function iban_checksum_string_replace($s)
	{
		$iban_replace_chars = range('A','Z');
		foreach (range(10,35) as $tempvalue)
		{
			$iban_replace_values[]=strval($tempvalue);
		}
		return str_replace($iban_replace_chars,$iban_replace_values,$s);
	}

	/**
	 * Perform MOD97-10 checksum calculation
	 *
	 * $s  Input string (IBAN)
	 */
	private static function iban_mod97_10($s)
	{
		$tr = intval(substr($s, 0, 1));
		for ($pos = 1; $pos < strlen($s); $pos++)
		{
			$tr *= 10;
			$tr += intval(substr($s,$pos,1));
			$tr %= 97;
		}
		return $tr;
	}

	/**
	 * verify EAN 13 code
	 *
	 * @static
	 * @param string	$ean string
	 * @return boolean
	 */
	public static function isEAN($ean)
    {
        // check to see if barcode is 13 digits long
        if (!preg_match("/^[0-9]{13}$/", $ean))
        {
            return false;
        }

        $digits = $ean;

        // 1. Add the values of the digits in the
        // even-numbered positions: 2, 4, 6, etc.
        $even_sum = $digits[1] + $digits[3] + $digits[5] + $digits[7] + $digits[9] + $digits[11];

        // 2. Multiply this result by 3.
        $even_sum_three = $even_sum * 3;

        // 3. Add the values of the digits in the
        // odd-numbered positions: 1, 3, 5, etc.
        $odd_sum = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8] + $digits[10];

        // 4. Sum the results of steps 2 and 3.
        $total_sum = $even_sum_three + $odd_sum;

        // 5. The check character is the smallest number which,
        // when added to the result in step 4, produces a multiple of 10.
        $next_ten = (ceil($total_sum / 10)) * 10;
        $check_digit = $next_ten - $total_sum;

        // if the check digit and the last digit of the
        // barcode are OK return true;
        if ($check_digit == $digits[12])
        {
            return true;
        }

        return false;
    }
}
