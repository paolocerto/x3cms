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
 * Helper for text handling
 *
 * @package X4WEBAPP
 */
class X4Text_helper
{
	/**
	 * Chars array
	 */
	public static $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
				   'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
				   'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
				   'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
				   'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '1', '2',
				   '3', '4', '5', '6', '7', '8', '9');

	/**
	 * Symbols array
	 */
	public static $symbols = array('!', '"', '#', '$', '%', '&', '(', ')', '*', '+', '-', '.', '/', ':', ';', '<', '=', '>', '?', '@', '[', ']', '^', '_', '|');

	/**
	 * Alphabet array
	 */
	public static $alphabet = array('A', 'B', 'C', 'D', 'E', 'F', 'G',
		'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
		'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

	/**
	 * Get the excerpt, long max_length, from a string
	 */
	public static function excerpt(string $str, int $max_length) : string
	{
		$l = strlen($str);
		if ($l <= $max_length)
		{
            return $str;
        }

        $parts = preg_split('/([\s\n\r]+)/', $str, null, PREG_SPLIT_DELIM_CAPTURE);
        $parts_count = count($parts);

        $length = 0;
        $last_part = 0;
        for (; $last_part < $parts_count; ++$last_part)
        {
            $length += strlen($parts[$last_part]);
            if ($length > $max_length) { break; }
        }
        return implode(array_slice($parts, 0, $last_part)).'...';
	}

    /**
	 * Replace first occurrence of a substring in a string
	 */
    public static function str_replace_first(string $search, string $replace, string $string) : string
    {
        $search = '/'.preg_quote($search, '/').'/';
        return preg_replace($search, $replace, $string, 1);
    }

    /**
	 * Remove empty rows from string
	 */
	public static function empty_rows(string $str) : string
	{
		return preg_replace_callback(
				"/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/iu",
				function($m)
				{
					return "\n";
				},
				$str);
	}

	/**
	 * Remove empty tags from html string
	 */
	public static function empty_tags(string $html) : string
	{
		$html = str_replace( '&nbsp;', ' ', $html );
		do {
			$tmp = $html;
			$html = preg_replace(
				'#<([^ >]+)[^>]*>[[:space:]]*</\1>#', '', $html );
		} while ( $html !== $tmp );
		return $html;
	}

	/**
	 * Remove from a string duplicate spaces or tabs
	 */
	public static function collapse_white_space(string $str) : string
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

	/**
	 * clean string
	 */
	public static function clean_string(string $str) : string
	{
		$src = array('</li>'.NL, '</ul>'.NL, '</blockquote>'.NL, '<br />'.NL, '<br/>'.NL);
		$rpl = array('</li>', '</ul>', '</blockquote>', '<br />', '<br/>');
		$str = str_replace($src, $rpl, $str);
		return strip_tags($str, '<a><b><i><br/><strong><em><ul><ol><li><blockquote><pre>');
	}

    /**
	 * Preg replace all
	 */
	public static function pregReplaceAll(string $find, string $replacement, string $str) : string
    {
        while(preg_match($find, $str)) {
            $str = preg_replace($find, $replacement, $str);
        }
        return $str;
    }

	/**
	 * Fixes the encoding to utf8
	 */
	public static function fix_encoding(string $str) : string
	{
		$cur_encoding = mb_detect_encoding($str) ;
		if ($cur_encoding != 'UTF-8' || !mb_check_encoding($str, 'UTF-8'))
		{
			$str = utf8_encode($str);
		}
		return preg_replace("/[\n\r]/","\n", $str);
	}

    /**
	 * Create random string
	 * /
	public static function randomize(int $lenght)
	{
		$codice_random = bin2hex(openssl_random_pseudo_bytes(6));
		$xcode = uniqid().$codice_random;
	}
	*/

	/**
	 * Create a random string
	 */
	public static function random_string(int $len, string $what = 'chars')
	{
		$chars = self::$$what;
	    shuffle($chars);
	    return implode('', array_slice($chars, 0, $len));
    }

    /**
	 * Create a random password
	 */
	public static function random_password(int $chars = 6, int $symbols = 2) : string
	{
        $tmp = self::random_string($chars, 'chars').self::random_string($symbols, 'symbols');
        $tmp = str_split($tmp);
        shuffle($tmp);
        return implode('', $tmp);
    }

	/**
	 * responsive table
	 *
	 * @static
	 * @param string	$html
	 * @return string
	 */
	public static function responsive_table(string $html, string $size = 'xs', string $fixed = 'th') : string
	{
	    if (empty($html))
	    {
            return $html;
        }

        $src = array('<thead>', '<td ', '<td>');
        $rpl = array(
            '<thead class="hidden-'.$size.'">',
            '<td class="hidden-'.$size.'" ',
            '<td class="hidden-'.$size.'">'
        );

        $html = str_replace($src, $rpl, $html);

        try
        {
            $doc = new DOMDocument('1.0', 'utf-8');
            libxml_use_internal_errors(true);
            $doc->loadHTML($html);

            // get table data
            $doc->preserveWhiteSpace = false;

            //the table by its tag name
            $tables = $doc->getElementsByTagName('table');

            if ($tables->length)
            {
                foreach ($tables as $table)
                {
                    //get all rows from the table
                    $rows = $table->getElementsByTagName('tr');
                    // get each column by tag name
                    $cols = $rows->item(0)->getElementsByTagName('th');
                    $row_headers = null;
                    foreach ($cols as $node)
                    {
                        $row_headers[] = $node->nodeValue;
                    }

                    //get all rows from the table
                    $rows = $table->getElementsByTagName('tr');

                    // handle rows
                    self::tableRow($row_headers, $doc, $rows, $size, $fixed);
                }
                return $doc->saveXML();
            }
        }
        catch (Exception $e)
        {
            if (DEBUG)
			{
				echo $e->getMessage();
				die;
			}
			else
			{
			    $mod = new Log_model();
			    $mod->logger(1, 1, 'responsive_table_debug', 'error: +++'.$e->getMessage().'+++');
			}
        }
        return $html;
	}

    /**
     * Handle table row
     */
    private static function tableRow(
        mixed $row_headers,
        DOMDocument $doc,
        DOMNodeList &$rows,
        string $size,
        string $fixed
    )
    {
        foreach ($rows as $row)
        {
            // get each column by tag name
            $cols = $row->getElementsByTagName('td');
            $tmp = array();
            $i = 0;
            foreach ($cols as $node)
            {
                if ($row_headers == null)
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
                if ($row_headers == null)
                {
                    $txt = implode('<br>', $tmp);
                }
                else
                {
                    $a = array();
                    foreach ($tmp as $k => $v)
                    {
                        $a[] = '<b>'.$k.'</b>: '.$v;
                    }
                    $txt = implode('<br />', $a);
                }
            }
            else
            {
                // first column as key
                foreach ($tmp as $k => $v)
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

	/**
     * Turn all URLs in clickable links
     */
    public static function linkify(
        string $value,
        array $protocols = ['http', 'https', 'mail', 'twitter'],
        array $attributes = []
    ) : string
    {
        // Link attributes
        $attr = '';
        foreach ($attributes as $key => $val)
        {
            $attr = ' '.$key.'="'.htmlentities($val).'"';
        }

        $links = array();

        // Extract existing links and tags
        $value = preg_replace_callback(
            '~(<a .*?>.*?</a>|<.*?>)~i',
            function ($match) use (&$links) {
                return '<' . array_push($links, $match[1]) . '>';
            },
            $value
        );

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
                                return '<' . array_push($links, "<a class=\"linked\" target=\"_blank\" $attr href=\"$protocol://$link\">$link</a>") . '>';
                            }
                        },
                        $value);
                    break;
                case 'mail':
                    $value = preg_replace_callback(
                        '~([^\s<]+?@[^\s<]+?\.[^\s<]+)(?<![\.,:])~',
                        function ($match) use (&$links, $attr)
                        {
                            return '<' . array_push($links, "<a class=\"linked\" target=\"_blank\" $attr href=\"mailto:{$match[1]}\">{$match[1]}</a>") . '>';
                        },
                        $value);
                    break;
                case 'twitter':
                    $value = preg_replace_callback(
                        '~(?<!\w)[@#](\w++)~',
                        function ($match) use (&$links, $attr)
                        {
                            return '<' . array_push($links, "<a class=\"linked\" target=\"_blank\" $attr href=\"https://twitter.com/" . ($match[0][0] == '@' ? '' : 'search/%23') . $match[1]  . "\">{$match[0]}</a>") . '>';
                        },
                        $value);
                    break;
                default:
                    $value = preg_replace_callback(
                        '~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i',
                        function ($match) use ($protocol, &$links, $attr)
                        {
                            return '<' . array_push($links, "<a class=\"linked\" target=\"_blank\" $attr href=\"$protocol://{$match[1]}\">{$match[1]}</a>") . '>';
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
            $value
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
	 */
	public static function diff(array $old, array $new) : array
	{
		$maxlen = 0;
		foreach ($old as $oindex => $ovalue)
		{
			$nkeys = array_keys($new, $ovalue);
			foreach ($nkeys as $nindex)
			{
				$matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1])
					? $matrix[$oindex - 1][$nindex - 1] + 1
					: 1;
				if ($matrix[$oindex][$nindex] > $maxlen)
				{
					$maxlen = $matrix[$oindex][$nindex];
					$omax = $oindex + 1 - $maxlen;
					$nmax = $nindex + 1 - $maxlen;
				}
			}
		}

		if ($maxlen == 0)
        {
			return array(array('d'=>$old, 'i'=>$new));
        }

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
	public static function htmlDiff(array $old, array $new) : string
	{
		$diff = self::diff(explode(' ', $old), explode(' ', $new));
		$ret = '';
        foreach ($diff as $k)
		{
			if (is_array($k))
            {
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
            }
			else
            {
				$ret .= $k . ' ';
            }
		}
		return $ret;
	}

	/**
	 * format address
	 */
	public static function write_address(mixed $address, string $nl = BR) : string
	{
		$a = $b = array();
        // address could be an array or an object
		if (is_array($address))
		{
            $address = json_decode(json_encode($address));
		}

		if (is_object($address))
		{
			if (isset($address->co) && !empty($address->co))
			{
				$a[] = 'C/O '.$address->co;
			}
			$a[] = $address->address;

            // middle block
            if (isset($address->zip_code) && !empty($address->zip_code))
			{
				$b[] = $address->zip_code;
			}
			$b[] = strtoupper($address->city);

			if (isset($address->region) && !empty($address->region))
			{
				$b[] = '('.strtoupper($address->region).')';
			}
			$a[] = implode(' ', $b);

			if (isset($address->country) && !empty($address->country))
			{
				$a[] = strtoupper($address->country);
			}
		}
		return implode($nl, $a);
	}

	/**
	 * remove unwanted tags and content
	 */
	public static function strip_tags_content(string $text, array $tags, $invert = false) : string
	{

		//preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
		//$tags = array_unique($tags[1]);

		if (is_array($tags) && count($tags) > 0)
		{
			if(!$invert)
			{
			  return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
			}
			else
			{
			  return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
			}
		}
		elseif(!$invert)
		{
			return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
		}
		return $text;
	}

	/**
	 * remove unwanted tags and remove unwanted attributes
	 */
	public static function stripUnwantedTagsAndAttrs(
        string $html_str,
        array $allowed_tags = array("html", "body", "b", "br", "em", "hr", "i", "li", "ol", "p", "blockquote", "s", "span", "table", "tr", "td", "u", "ul"),    // NOTE you MUST allow html and body otherwise entire string will be cleared
        array $allowed_attrs = array ("class", "id", "style")
    ): string
	{
		$xml = new DOMDocument();

		// Suppress warnings: proper error handling is beyond scope of example
		libxml_use_internal_errors(true);

		if (empty($html_str))
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

    /**
	 * Get the content of a CSV file in a string and return an associative array
	 */
    public static function csv2array(string $string) : array
    {
        $array = array_map('str_getcsv', explode(NL, $string));
        $header = array_shift($array);
        array_walk($array, '_combine_array', $header);
        return $array;
    }
}

/**
 * combine headers with values
 */
function _combine_array(array &$row, string $header)
{
    $row = array_combine($header, $row);
}

