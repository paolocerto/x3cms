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
 * Helper for text handling
 * 
 * @package X4WEBAPP
 */
class X4Text_helper 
{
	/**
	 * Excerpt
	 *
	 * @static
	 * @param string	$str string to excerpt
	 * @param integer	$max_length max string length
	 * @return string
	 */
	public static function excerpt($str, $max_length)
	{
		$l = strlen($str);
		if ($l > $max_length)
		{
			$parts = preg_split('/([\s\n\r]+)/', $str, null, PREG_SPLIT_DELIM_CAPTURE);
			$parts_count = count($parts);
			
			$length = 0;
			$last_part = 0;
			for (; $last_part < $parts_count; ++$last_part) 
			{
				$length += strlen($parts[$last_part]);
				if ($length > $max_length) 
					break;
			}
			return implode(array_slice($parts, 0, $last_part)).'...';
		}
		else
			return $str;
	}
	
	/**
	 * Remove from a string duplicate empty rows
	 *
	 * @static
	 * @param string	$str string to clean
	 * @return string
	 */
	public static function empty_rows($str)
	{
		if (function_exists('preg_replace_callback'))
		{
			return preg_replace_callback(
				"/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/iu",
				function($m)
				{
					return "\n";	
				}, 
				$str);
		}
		else
		{
			return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $str);
		}
	}
	
	/**
	 * Remove empty tags from html string
	 *
	 * @static
	 * @param string	$str string to clean
	 * @return string
	 */
	public static function empty_tags($html)
	{
		$html = str_replace( '&nbsp;', ' ', $html );
		do 
		{
			$tmp = $html;
			$html = preg_replace(
				'#<([^ >]+)[^>]*>[[:space:]]*</\1>#', '', $html );
		} while ( $html !== $tmp );
	
		return $html;
	}
	
	/**
	 * Remove from a string duplicate spaces or tabs
	 *
	 * @static
	 * @param string	$str string to clean
	 * @return string
	 */
	public static function collapse_white_space($str)
	{
		if (function_exists('preg_replace_callback'))
		{
		    $str = str_replace("\t", '', $str);
			return preg_replace_callback(
				'/\\s+/iu',
				function($m)
				{
					return ' ';
				}, 
				$str);
		}
		else
		{
			return preg_replace('/\\s+/iu', ' ', $str);
		}
	}
	
	/**
	 * clean string
	 *
	 * @static
	 * @param	string	$str String to clean
	 * @return  string	cleaned string
	 */
	public static function clean_string($str)
	{
		$src = array('</li>'.NL, '</ul>'.NL, '</blockquote>'.NL, '<br />'.NL, '<br/>'.NL);
		$rpl = array('</li>', '</ul>', '</blockquote>', '<br />', '<br/>');
		$str = str_replace($src, $rpl, $str);
		$cleaned = strip_tags($str, '<a><b><i><br/><strong><em><ul><ol><li><blockquote><pre>');
		return $cleaned;
	}
	
	/**
	 * Fixes the encoding to utf8
	 *
	 * @static
	 * @param	string	$str String to fix
	 * @return  string	fixed string
	 */ 
	public static function fix_encoding($str)
	{
		$cur_encoding = mb_detect_encoding($str) ;
		if ($cur_encoding == 'UTF-8' && mb_check_encoding($str, 'UTF-8'))
		{
			//nothing
		}
		else
		{
			$str = utf8_encode($str);
		}
		return preg_replace("/[\n\r]/","\n", $str);
	}
	
	/**
	 * Create a random string
	 *
	 * @static
	 * @param integer	$len length of the string
	 * @return string
	 */
	public static function random_string($len)
	{
	    $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
				   'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
				   'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
				   'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
				   'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '1', '2',
				   '3', '4', '5', '6', '7', '8', '9');
	   
        shuffle($chars);
        $string = implode('', array_slice($chars, 0, $len));
        return $string;
	}
	
	/**
	 * responsive table
	 *
	 * @static
	 * @param string	$html
	 * @return string
	 */
	public static function responsive_table($html, $size = 'xs', $fixed = 'th')
	{   
	    if (!empty($html))
	    {
            $src = array('<thead>', '<td ', '<td>');
            $rpl = array('<thead class="hidden-'.$size.'">', '<td class="hidden-'.$size.'" ', '<td class="hidden-'.$size.'">');
            
            $html = str_replace($src, $rpl, $html);
            
            try
            {
                $doc = new DOMDocument('1.0', 'utf-8');
                $internalErrors = libxml_use_internal_errors(true);
                $doc->loadHTML($html);
                
                // get table data
                $doc->preserveWhiteSpace = false;
                
                //the table by its tag name
                $tables = $doc->getElementsByTagName('table');
            
                if ($tables->length)
                {
                    foreach($tables as $table)
                    {
                        //get all rows from the table
                        $rows = $table->getElementsByTagName('tr');
                        // get each column by tag name
                        $cols = $rows->item(0)->getElementsByTagName('th');
                        $row_headers = NULL;
                        foreach ($cols as $node)
                        {
                            //print $node->nodeValue."\n";
                            $row_headers[] = $node->nodeValue;
                        }
                        
                        //get all rows from the table  
                        $rows = $table->getElementsByTagName('tr');
                        foreach ($rows as $row)
                        {   
                            // get each column by tag name
                            $cols = $row->getElementsByTagName('td');
                            $tmp = array();
                            $i = 0;
                            foreach ($cols as $node) {
                                # code...
                                //print $node->nodeValue."\n";
                                if ($row_headers == NULL)
                                {
                                    $tmp[] = $node->nodeValue;
                                }
                                else
                                {
                                    $tmp[$row_headers[$i]] = $node->nodeValue;
                                }
                                $i++;
                            }   
                            
                            // build TDs
                            if ($fixed == 'th')
                            {
                                // headers as column
                                if ($row_headers == NULL)
                                {
                                    $txt = implode('<br>', $tmp);
                                }
                                else
                                {
                                    $a = array();
                                    foreach($tmp as $k => $v)
                                    {
                                        $a[] = '<b>'.$k.'</b>: '.$v;
                                    }
                                    $txt = implode('<br />', $a);
                                }
                            }
                            else
                            {
                                // first column as key
                                // TODO
                                foreach($tmp as $k => $v)
                                {
                                    $a[] = '<b>'.$k.'</b>: '.$v;
                                }
                                $txt = implode('<br />', $a);
                            }
			$tmp = $doc->createDocumentFragment();
			$chk = $tmp->appendXML('<td class="visible-'.$size.'">'.$txt.'</td>');
			if ($chk)
			{
				$row->appendChild($tmp);
			}
                        }
                    }
                    return $doc->saveXML();
                }
            }
            catch (Exception $e)
            {
                // do none
            }
            return $html;
        }
        else
        {
            return $html;
        }
	}
	
	/**
     * Turn all URLs in clickable links.
     * 
     * @param string $value
     * @param array  $protocols  http/https, ftp, mail, twitter
     * @param array  $attributes
     * @param string $mode       normal or all
     * @return string
     */
    public static function linkify($value, $protocols = array('http', 'mail', 'twitter'), array $attributes = array())
    {
        // Link attributes
        $attr = '';
        foreach ($attributes as $key => $val) 
        {
            $attr = ' ' . $key . '="' . htmlentities($val) . '"';
        }
        
        $links = array();
        
        if (function_exists('preg_replace_callback'))
        {
        	// Extract existing links and tags
			$value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) { return '<' . array_push($links, $match[1]) . '>'; }, $value);
			
			// Extract text links for each protocol
			foreach ((array)$protocols as $protocol) 
			{
				switch ($protocol) 
				{
					case 'http':
					case 'https':   
						$value = preg_replace_callback(
							'~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', 
							function ($match) use ($protocol, &$links, $attr) 
							{ 
								if ($match[1]) 
								{
									$protocol = $match[1]; 
									$link = $match[2] ?: $match[3]; 
									return '<' . array_push($links, "<a target=\"_blank\" $attr href=\"$protocol://$link\">$link</a>") . '>'; 
								}
							}, 
							$value); 
						break;
					case 'mail':    
						$value = preg_replace_callback(
							'~([^\s<]+?@[^\s<]+?\.[^\s<]+)(?<![\.,:])~', 
							function ($match) use (&$links, $attr) 
							{
								return '<' . array_push($links, "<a target=\"_blank\" $attr href=\"mailto:{$match[1]}\">{$match[1]}</a>") . '>'; 
							}, 
							$value); 
						break;
					case 'twitter': 
						$value = preg_replace_callback(
							'~(?<!\w)[@#](\w++)~', 
							function ($match) use (&$links, $attr) 
							{ 
								return '<' . array_push($links, "<a target=\"_blank\" $attr href=\"https://twitter.com/" . ($match[0][0] == '@' ? '' : 'search/%23') . $match[1]  . "\">{$match[0]}</a>") . '>'; 
							}, 
							$value); 
						break;
					default:
						$value = preg_replace_callback(
							'~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i', 
							function ($match) use ($protocol, &$links, $attr) 
							{ 
								return '<' . array_push($links, "<a target=\"_blank\" $attr href=\"$protocol://{$match[1]}\">{$match[1]}</a>") . '>'; 
							},
							$value); 
						break;
				}
			}
			
			// Insert all link
			return preg_replace_callback(
				'/<(\d+)>/', 
				function ($match) use (&$links) 
				{ 
					return $links[$match[1] - 1]; 
				}, 
				$value);
		}
		else
		{
			return $value;
		}
    }
	
	/**
	 *	Paul's Simple Diff Algorithm v 0.1
	 *	(C) Paul Butler 2007 <http://www.paulbutler.org/>
	 *	May be used and distributed under the zlib/libpng license.
	 *	
	 *	This code is intended for learning purposes; it was written with short
	 *	code taking priority over performance. It could be used in a practical
	 *	application, but there are a few ways it could be optimized.
	 *	
	 *	Given two arrays, the function diff will return an array of the changes.
	 *	I won't describe the format of the array, but it will be obvious
	 *	if you use print_r() on the result of a diff on some test data.
	 *	
	 *	htmlDiff is a wrapper for the diff command, it takes two strings and
	 *	returns the differences in HTML. The tags used are <ins> and <del>,
	 *	which can easily be styled with CSS.
	 *
	 * @static
	 * @param array	$old Array of strings
	 * @param array	$new Array of strings
	 * @return array
	 */
	public static function diff($old, $new)
	{
		$maxlen = 0;
		foreach($old as $oindex => $ovalue)
		{
			$nkeys = array_keys($new, $ovalue);
			foreach($nkeys as $nindex)
			{
				$matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) 
					? $matrix[$oindex - 1][$nindex - 1] + 1 
					: 1;
				if($matrix[$oindex][$nindex] > $maxlen)
				{
					$maxlen = $matrix[$oindex][$nindex];
					$omax = $oindex + 1 - $maxlen;
					$nmax = $nindex + 1 - $maxlen;
				}
			}
		}
		if($maxlen == 0) 
			return array(array('d'=>$old, 'i'=>$new));
		return array_merge(
			self::diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
			array_slice($new, $nmax, $maxlen),
			self::diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen))
			);
	}
	
	/**
	 *	Paul's Simple Diff Algorithm v 0.1
	 *	(C) Paul Butler 2007 <http://www.paulbutler.org/>
	 *	May be used and distributed under the zlib/libpng license.
	 *	
	 *	This code is intended for learning purposes; it was written with short
	 *	code taking priority over performance. It could be used in a practical
	 *	application, but there are a few ways it could be optimized.
	 *	
	 *	Given two arrays, the function diff will return an array of the changes.
	 *	I won't describe the format of the array, but it will be obvious
	 *	if you use print_r() on the result of a diff on some test data.
	 *	
	 *	htmlDiff is a wrapper for the diff command, it takes two strings and
	 *	returns the differences in HTML. The tags used are <ins> and <del>,
	 *	which can easily be styled with CSS.
	 *
	 * @static
	 * @param array	$old Array of strings
	 * @param array	$new Array of strings
	 * @return array
	 */
	public static function htmlDiff($old, $new)
	{
		$diff = self::diff(explode(' ', $old), explode(' ', $new));
		foreach($diff as $k)
		{
			if(is_array($k))
				$ret .= (
					!empty($k['d'])
						? "<del>".implode(' ',$k['d'])."</del> "
						: ''
						).
					(
					!empty($k['i'])
						? "<ins>".implode(' ',$k['i'])."</ins> "
						: ''
					);
			else 
				$ret .= $k . ' ';
		}
		return $ret;
	}
	
	/**
	 * format address
	 *
	 * @param   mixed	$address object or array
	 * @param   string	$nl New line item
	 * @return  string
	 */
	public static function write_address($address, $nl = BR)
	{
		$a = '';
		if (is_array($address)) 
		{
			if (isset($address['co']) && !empty($address['co']))
			{
				$a .= $nl.'C/O '.$address['co'];
			}
			
			$a .= $nl.$address['address'].$nl;
			
			if (isset($address['zip_code']) && !empty($address['zip_code']))
			{
				$a .= $address['zip_code'].' - ';
			}
			
			$a .= strtoupper($address['city']);
			
			if (isset($address['region']) && !empty($address['region']))
			{
				$a .= ' ('.$address['region'].')';
			}
			
			if (isset($address['country']) && !empty($address['country']))
			{
				$a .= $nl.strtoupper($address['country']);
			}
		}
		
		if (is_object($address)) 
		{
			if (isset($address->co) && !empty($address->co))
			{
				$a .= $nl.'C/O '.$address->co;
			}
			
			$a .= $nl.$address->address.$nl;
			
			if (isset($address->zip_code) && !empty($address->zip_code))
			{
				$address->zip_code.' - ';
			}
			$a .= strtoupper($address->city);
			
			if (isset($address->region) && !empty($address->region))
			{
				$a .= ' ('.$address->region.')';
			}
			if (isset($address->country) && !empty($address->country))
			{
				$a .= $nl.strtoupper($address->country);
			}
		}
		return $a;
	}
	
	/**
	 * remove unwanted tags and content
	 *
	 * @param   string	$text
	 * @param   array	$tags
	 * @param   boolean	$invert
	 * @return  string
	 */
	public static function strip_tags_content($text, $tags, $invert = FALSE) 
	{ 

		//preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags); 
		//$tags = array_unique($tags[1]); 
		
		if(is_array($tags) AND count($tags) > 0) 
		{ 
			if($invert == FALSE) 
			{ 
			  return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text); 
			} 
			else 
			{ 
			  return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text); 
			} 
		} 
		elseif($invert == FALSE) 
		{ 
			return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text); 
		} 
		return $text; 
	}
	
	/**
	 * remove unwanted tags and remove unwanted attributes
	 *
	 * @param   string	$text
	 * @param   array	$allowed_tags List the tags you want to allow here, NOTE you MUST allow html and body otherwise entire string will be cleared
	 * @param   array	$allowed_attrs List the attributes you want to allow here
	 * @return  string
	 */
	public static function stripUnwantedTagsAndAttrs($html_str, $allowed_tags = array("html", "body", "b", "br", "em", "hr", "i", "li", "ol", "p", "blockquote", "s", "span", "table", "tr", "td", "u", "ul"), $allowed_attrs = array ("class", "id", "style"))
	{
		$xml = new DOMDocument();
		
		//Suppress warnings: proper error handling is beyond scope of example
		libxml_use_internal_errors(true);
		
		if (!strlen($html_str))
		{
			return false;
		}
		
		if ($xml->loadHTML($html_str, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD))
		{
			foreach ($xml->getElementsByTagName("*") as $tag)
			{
				if (!in_array($tag->tagName, $allowed_tags))
				{
					$tag->parentNode->removeChild($tag);
				}
				else
				{
					foreach ($tag->attributes as $attr)
					{
						if (!in_array($attr->nodeName, $allowed_attrs))
						{
							$tag->removeAttribute($attr->nodeName);
						}
					}
				}
			}
		}
		return $xml->saveHTML();
	}
}

