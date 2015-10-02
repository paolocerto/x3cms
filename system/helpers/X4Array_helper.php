<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
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
					$o[] = new Obj_opt2($k, $v);
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
