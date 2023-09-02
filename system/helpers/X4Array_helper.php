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
 * Helper for arrays
 *
 * @package X4WEBAPP
 */
class X4Array_helper
{
	/**
	 * Convert a simple array of items to an array of objects to use in select options
	 *
	 * @static
	 * @param array		$array array of objects
	 * @return array
	 */
	public static function simplearray2obj($array, $val = 'value', $opt = 'option')
	{
		$a = array();
		foreach ($array as $i)
		{
			$a[] = array('value' => $i, 'option' => $i);
		}

		return self::array2obj($a, 'value', 'option');
	}

	/**
	 * Convert an associative array in array usable with array2obj
	 *
	 * @static
	 * @param array		$array associative array
	 * @return array
	 */
	public static function sequentialarray2obj($array)
	{
		$a = array();
		foreach ($array as $k => $v)
		{
			$a[] = array('value' => $k, 'option' => $v);
		}

		return self::array2obj($a, 'value', 'option');
	}

	/**
	 * Convert simple array or an associative array to an array of objects
	 *
	 * @static
	 * @param array		$array associative array
	 * @param string	$val key field name
	 * @param string	$opt value field name
	 * @param boolean   $assoc force associative array
	 * @return array
	 */
	public static function array2obj($array, $val = null, $opt = null, $assoc = false)
	{
		$o = array();
		if (is_null($opt) || is_null($val))
		{
			if (!$assoc && array_values($array) === $array)
			{
				// is a sequential array
				foreach ($array as $i)
				{
					$o[] = new Obj_opt2($i, $i);
				}
			}
			else
			{
				foreach ($array as $k => $v)
				{
				    if (!is_null($v))
				    {
				        $o[] = new Obj_opt2($k, $v);
				    }
				}
			}
		}
		else
		{
			foreach ($array as $i)
			{
				$o[] = new Obj_opt2($i[$val], $i[$opt]);
			}
		}
		return $o;
	}

	/**
	 * Convert an array of objects to an associative array
	 *
	 * @static
	 * @param array		$array array of objects
	 * @param string	$key key field name
	 * @param string	$value value field name
	 * @return array
	 */
	public static function obj2array($array, $key, $value)
	{
		$a = array();
		if (is_null($key))
		{
			foreach ($array as $i)
			{
				$a[] = $i->$value;
			}
		}
		else
		{
			foreach ($array as $i)
			{
				$a[$i->$key] = $i->$value;
			}
		}
		return $a;
	}

	/**
	 * Convert a string to an associative array
	 *
	 * @static
	 * @param string	$str
	 * @param string	$row_sep
	 * @param string	$value_sep
     * @param array     $fields index positions of fields to store in sequential array
	 * @return array
	 */
	public static function str2array($str, $row_sep = '§', $value_sep = '|', $fields = [])
	{
		$a = array();
		$rows = explode($row_sep, $str);
		foreach ($rows as $r)
		{
            if (!is_null($value_sep))
            {
                $v = explode($value_sep, $r);
                if (!empty($v[0]) && !is_null($v[1]))
                {
                    if (empty($fields))
                    {
                        // associative array
                        $a[$v[0]] = $v[1];
                    }
                    else
                    {
                        // sequential array
                        $tmp = [];
                        foreach ($fields as $i)
                        {
                            if (isset($v[$i]))
                            {
                                if (sizeof($fields) == 1)
                                {
                                    $a[] = $v[$i];
                                }
                                else
                                {
                                    $tmp[] = $v[$i];
                                }
                            }
                        }
                        if (sizeof($fields) > 1)
                        {
                            $a[] = $tmp;
                        }
                    }
                }
            }
            else
            {
                $a[] = trim($r);
            }
		}
		return $a;
	}

	/**
	 * Convert a string to an associative array
	 *
	 * @static
	 * @param string	$array
	 * @param string	$row_sep
	 * @param string	$value_sep
	 * @return str
	 */
	public static function array2str($array, $row_sep = '§', $value_sep = '|')
	{
		$rows = array();
		foreach ($array as $k => $v)
		{
		    $rows[] = $k.$value_sep.str_replace(array('<span class="AM">', '<', '>', '"'), array('<spam>', 'XLTX', 'XGTX', 'XQTX'), $v);
		}
		return implode($row_sep, $rows);
	}

	/**
	 * Indicize array, replace index with IDs
	 *
	 * @param	array	$array Array to indicize
	 * @param	string	$index Field to use as index
	 * @return array
	 */
	public static function indicize($array, $indexes)
	{
		$a = array();
		$ii = explode(':', $indexes);
		if (is_array($array))
		{
			foreach ($array as $i)
			{
				if (sizeof($ii) == 1)
				{
					$a[$i->$indexes] = $i;
				}
				else
				{
					// to handle null values
					$fi = array();
					foreach ($ii as $tmp)
					{
						$fi[] = (is_null($i->$tmp))
							? 0    // was -1
							: $i->$tmp;
					}
					$final_index = implode('_', $fi);
					$a[$final_index] = $i;
				}

			}
		}
		return $a;
	}

	/**
	 * Extract from array by index
	 *
	 * @param	array	$array Array
	 * @param	string	$index Field to extract
	 * @return array
	 */
	public static function extractize($array, $index)
	{
		$a = array();
		foreach ($array as $i)
		{
		    // to handle null values
		    $final_index = (is_null($i->$index))
		        ? -1
		        : $i->$index;

			$a[] = $final_index;
		}
		return $a;
	}

	/**
	 * Sort an array by the length of its values
	 *
	 * @param	first value
	 * @param	second value
	 * @return boolean
	 */
	public static function sort_by_length($a, $b)
	{
		return strlen($b) - strlen($a);
	}

	/**
	 * Get next key array
	 *
	 * @param	array	$array
	 * @param	mixed	$key
	 * @return mixed
	 */
	public static function get_next_key_array($array, $key)
	{
		$keys = array_keys($array);
		$position = array_search($key, $keys);
		if (isset($keys[$position + 1]))
		{
			$nextKey = $keys[$position + 1];
		}
		return $nextKey;
	}

    /**
	 * Array count
     * remove duplicates and add number of presence in array
	 *
	 * @param	array	$array
	 * @return array
	 */
	public static function array_count($array)
	{
        $a = [];
        $array = array_filter($array);
        foreach ($array as $i)
        {
            if (!isset($a[$i]))
            {
                $a[$i] = 1;
            }
            else
            {
                $a[$i]++;
            }
        }
        array_walk($a, function (&$value, $key) {
            $value = strip_tags($key).' ('.$value.')';
        });
		return $a;
	}

    /**
	 * Shuffle associative array
     * remove duplicates and add number of presence in array
	 *
	 * @param	array	$array
	 * @return array
	 */
    public static function shuffle_assoc($array)
    {
        $tmp = array_keys($array);

        shuffle($tmp);
        $a = [];
        foreach ($tmp as $i)
        {
            $a[$i] = $array[$i];
        }
        return $a;
    }

    /**
	 * Delete ite from array by value
     *
	 * @param	array	$array
     * @param   mixed   $value
	 * @return array
	 */
    public static function delete_value(array &$array, $value)
    {
        if (($key = array_search($value, $array)) !== false)
        {
            unset($array[$key]);
        }
    }

    /**
	 * Converts an XML string to array
     *
	 * @param	string  $XML
     * @param   mixed   $value
	 * @return array
	 */
    public static function XML2Array($XML)
    {
        $xml_parser = xml_parser_create();
        xml_parse_into_struct($xml_parser, $XML, $vals);
        xml_parser_free($xml_parser);
        // wyznaczamy tablice z powtarzajacymi sie tagami na tym samym poziomie
        $_tmp='';
        foreach ($vals as $xml_elem)
        {
            $x_tag=$xml_elem['tag'];
            $x_level=$xml_elem['level'];
            $x_type=$xml_elem['type'];
            if ($x_level!=1 && $x_type == 'close') {
                if (isset($multi_key[$x_tag][$x_level]))
                    $multi_key[$x_tag][$x_level]=1;
                else
                    $multi_key[$x_tag][$x_level]=0;
            }
            if ($x_level!=1 && $x_type == 'complete') {
                if ($_tmp==$x_tag)
                    $multi_key[$x_tag][$x_level]=1;
                $_tmp=$x_tag;
            }
        }
        // jedziemy po tablicy
        foreach ($vals as $xml_elem)
        {
            $x_tag=$xml_elem['tag'];
            $x_level=$xml_elem['level'];
            $x_type=$xml_elem['type'];
            if ($x_type == 'open')
                $level[$x_level] = $x_tag;
            $start_level = 1;
            $php_stmt = '$xml_array';
            if ($x_type=='close' && $x_level!=1)
                $multi_key[$x_tag][$x_level]++;
            while ($start_level < $x_level) {
                $php_stmt .= '[$level['.$start_level.']]';
                if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
                    $php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
                $start_level++;
            }
            $add='';
            if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete')) {
                if (!isset($multi_key2[$x_tag][$x_level]))
                    $multi_key2[$x_tag][$x_level]=0;
                else
                    $multi_key2[$x_tag][$x_level]++;
                $add='['.$multi_key2[$x_tag][$x_level].']';
            }
            if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes', $xml_elem)) {
                if ($x_type == 'open')
                    $php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                else
                    $php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
                eval($php_stmt_main);
            }
            if (array_key_exists('attributes', $xml_elem)) {
                if (isset($xml_elem['value'])) {
                    $php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                    eval($php_stmt_main);
                }
                foreach ($xml_elem['attributes'] as $key=>$value) {
                    $php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
                    eval($php_stmt_att);
                }
            }
        }
        return $xml_array;
    }
}

/**
 * Option object
 *
 * @package		X4WEBAPP
 */
class Obj_opt2
{
	public $value;
	public $option;
	public $opt;

	/**
	 * Constructor
	 * set the folder name
	 *
	 * @param   string $v option value
	 * @param   string $o option name
	 * @param   string $a alternative option name
	 * @return  void
	 */
	public function __construct($v, $o, $a = null)
	{
		$this->value = $v;
		if (is_null($a))
		{
			$this->option = $o;
		}
		else
		{
			$this->opt = $o;
		}
	}
}
