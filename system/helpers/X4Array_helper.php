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
 * Helper for arrays
 *
 * @package X4WEBAPP
 */
class X4Array_helper
{
	/**
	 * Convert a simple array of items to an array of objects to use in select options
	 */
	public static function simplearray2obj(array $array, string $val = 'value', string $opt = 'option') : array
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
	 */
	public static function sequentialarray2obj(array $array) : array
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
	 */
	public static function array2obj(array $array, mixed $val = null, mixed $opt = null, bool $assoc = false) : array
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
	 */
	public static function obj2array(array $array, string $key, string $value) : array
	{
		$a = array();
		if (empty($key))
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
	 * where $fields is an array of index positions of fields to store in sequential array
	 */
	public static function str2array(string $str, string $row_sep = '§', string $value_sep = '|', array $fields = []) : array
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
	 * Convert an associative array to a string
	 */
	public static function array2str(array $array, string $row_sep = '§', string $value_sep = '|') : string
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
	 */
	public static function indicize(array $array, string $indexes) : array
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
	 */
	public static function extractize(array $array, string $index) : array
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
	 */
	public static function sort_by_length(string $a, string $b) : bool
	{
		return strlen($b) - strlen($a);
	}

	/**
	 * Get next key array
	 */
	public static function get_next_key_array(array $array, mixed $key)
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
	 */
	public static function array_count(array $array) : array
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
	 */
    public static function shuffle_assoc(array $array) : array
    {
        $keys = array_keys($array);

        shuffle($keys);
        $a = [];
        foreach ($keys as $i)
        {
            $a[$i] = $array[$i];
        }
        return $a;
    }

    /**
	 * Delete item from array by value
	 */
    public static function delete_value(array &$array, mixed $value)
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
    public static function XML2Array(string $XML) : array
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
            if ($x_level!=1 && $x_type == 'close')
            {
                if (isset($multi_key[$x_tag][$x_level]))
                {
                    $multi_key[$x_tag][$x_level]=1;
                }
                else
                {
                    $multi_key[$x_tag][$x_level]=0;
                }
            }
            if ($x_level!=1 && $x_type == 'complete')
            {
                if ($_tmp==$x_tag)
                {
                    $multi_key[$x_tag][$x_level]=1;
                }
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
            {
                $level[$x_level] = $x_tag;
            }
            $start_level = 1;
            $php_stmt = '$xml_array';
            if ($x_type=='close' && $x_level!=1)
            {
                $multi_key[$x_tag][$x_level]++;
            }

            while ($start_level < $x_level)
            {
                $php_stmt .= '[$level['.$start_level.']]';
                if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
                {
                    $php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
                }
                $start_level++;
            }

            $add='';
            if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete'))
            {
                if (!isset($multi_key2[$x_tag][$x_level]))
                {
                    $multi_key2[$x_tag][$x_level]=0;
                }
                else
                {
                    $multi_key2[$x_tag][$x_level]++;
                }
                $add='['.$multi_key2[$x_tag][$x_level].']';
            }

            if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes', $xml_elem))
            {
                if ($x_type == 'open')
                {
                    $php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                }
                else
                {
                    $php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
                }
                eval($php_stmt_main);
            }

            if (array_key_exists('attributes', $xml_elem))
            {
                if (isset($xml_elem['value']))
                {
                    $php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                    eval($php_stmt_main);
                }
                foreach ($xml_elem['attributes'] as $key=>$value)
                {
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
	 */
	public function __construct(string $v, string $o, mixed $a = null)
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
