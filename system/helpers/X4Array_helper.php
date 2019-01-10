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
	public static function simplearray2obj($array)
	{
		$a = array();
		foreach($array as $i) 
		{
			$a[] = array('value' => $i, 'option' => $i);
		}

		return self::array2obj($a, 'value', 'option');
	}
	
	/**
	 * Convert an associative  array in array usable with array2obj 
	 *
	 * @static
	 * @param array		$array associative array
	 * @return array
	 */
	public static function sequentialarray2obj($array)
	{
		$a = array();
		foreach($array as $k => $v) 
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
	 * @return array
	 */
	public static function array2obj($array, $val = null, $opt = null)
	{
		$o = array();
		if (is_null($opt) || is_null($val))
		{
			if (array_values($array) === $array)
			{
				// is a sequential array
				foreach($array as $i) 
				{
					$o[] = new Obj_opt2($i, $i);
				}
			}
			else
			{
				foreach($array as $k => $v) 
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
			foreach($array as $i) 
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
			foreach($array as $i) 
			{
				$a[] = $i->$value;
			}
		else 
			foreach($array as $i) {
				$a[$i->$key] = $i->$value;
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
	 * @return array
	 */
	public static function str2array($str, $row_sep = '§', $value_sep = '|')
	{
		$a = array();
		$rows = explode($row_sep, $str);
		foreach($rows as $r)
		{
		    $v = explode($value_sep, $r);
		    if (!empty($v[0]) && !is_null($v[1]))
		    {
		        $a[$v[0]] = $v[1];
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
		foreach($array as $k => $v)
		{
		    $rows[] = $k.$value_sep.str_replace(array('<span class="AM">', '<', '>', '"'), array('<spam>', 'XLTX', 'XGTX', 'XQTX'), $v);
		}
		return implode($row_sep, $rows);
	}
	
	/**
	 * Get index of an element in an array
	 *
	 * @static
	 * @param array		$array array of objects
	 * @param mixed		$element The element searched
	 * @return integer
	 */
	public static function indexof($array, $element)
	{
		foreach($array as $k => $v) 
		{
			if ($v == $element)
			{
				return $k;
			}
		}
		return -1;
	}
	
	/**
	 * push array
	 *
	 * @static
	 * @param	array	$a First array
	 * @param	array	$to_add Array of array to add
	 * @return	void
	 */
	public static function push_array(&$a, $to_add)
	{
		// add
		foreach($to_add as $i)
		{
			if (is_array($i))
			{
				foreach($i as $ii)
					$a[] = $ii;
			}
			else
			{
				$a[] = $i;
			}
		}
	}
	
	/**
	 * Indicize array, replace index with IDs
	 *
	 * @param	array	$array Array to indicize
	 * @param	string	$index Field to use as index
	 * @return array
	 */
	public static function indicize($array, $index)
	{
		$a = array();
		foreach($array as $i)
		{
		    // to handle null values
		    $final_index = (is_null($i->$index))
		        ? -1
		        : $i->$index;
		    
			$a[$final_index] = $i;
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
		foreach($array as $i)
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
