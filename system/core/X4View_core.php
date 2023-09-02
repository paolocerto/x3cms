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
 * View class
 * THIS FILE IS DERIVED FROM KOHANA
 *
 * @package X4WEBAPP
 */
class X4View_core
{
	// The view file name and type
	protected $filename = false;
	protected $type = false;

	// View variable storage
	protected $local_data = array();
	protected static $global_data = array();

	/**
	 * Attempts to load a view and pre-load view data.
	 *
	 * @throws  Kohana_Exception  if the requested view cannot be found
	 * @param   string  $name   view name
     * @param   string  $module
	 * @param   array   $data   pre-load data
	 * @param   string  $type   type of file: html, css, js, etc.
	 * @return  void
	 */
	public function __construct(string $name, string $module = '', array $data = [], string $type = EXT)
	{
		if (!empty($name))
		{
			// Set the filename
			if (strstr($name, 'templates') != '')
			{
				// into theme
				if (!file_exists($_SERVER['DOCUMENT_ROOT'].$name.$type))
				{
					$token = explode('/', $name);
					$token[5] = 'base';
					$name = implode('/', $token);
				}
				$this->filename = $_SERVER['DOCUMENT_ROOT'].$name.$type;
			}
			else
			{
				// plugin
				if (!empty($module) && file_exists(PATH.'plugins/'.$module.'/views/'.$name.'_view'.$type))
				{
					$this->filename = PATH.'plugins/'.$module.'/views/'.$name.'_view'.$type;
				}
				elseif (file_exists(APATH.'views/'.X4Route_core::$folder.'/'.$name.'_view'.$type))
				{
					$this->filename = APATH.'views/'.X4Route_core::$folder.'/'.$name.'_view'.$type;
				}
				elseif (file_exists(APATH.'views/'.$name.'_view'.$type))
				{
					$this->filename = APATH.'views/'.$name.'_view'.$type;
				}
				else
				{
					$this->filename = SPATH.'views/X4Default.php';
				}
			}
		}

		if (!empty($data))
		{
			// Preload data using array_merge, to allow user extensions
			$this->local_data = array_merge($this->local_data, $data);
		}
	}

	/**
	 * Magically sets a view variable.
	 *
	 * @param   string   variable key
	 * @param   mixed   variable value
	 * @return  void
	 */
	public function __set(string $key, $value)
	{
		if (!isset($this->$key))
		{
			$this->local_data[$key] = $value;
		}
	}

	/**
	 * Magically gets a view variable.
	 *
	 * @param  string  variable key
	 * @return mixed   variable value if the key is found
	 */
	public function __get(string $key)
	{
		if (isset($this->local_data[$key]))
		{
			return $this->local_data[$key];
		}
		if (isset(self::$global_data[$key]))
		{
			return self::$global_data[$key];
		}
		if (isset($this->$key))
		{
			return $this->$key;
		}
	}

	/**
	 * Magically converts view object to string.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Renders a view.
	 *
	 * @param   boolean   set to TRUE to echo the output instead of returning it
	 * @param   callback  special renderer to pass the output through
	 * @return  mixed     object if print is FALSE string if print is TRUE
	 */
	public function render(bool $print = FALSE, bool $renderer = FALSE)
	{
		// Merge global and local data, local overrides global with the same name
		$data = array_merge(self::$global_data, $this->local_data);

		// Load the view in the controller for access to $this
		$output = X4Core_core::$insta->load_view($this->filename, $data);

		if ($print)
		{
			// Display the output
			echo $output;
		}
		else
		{
			return $output;
		}
	}

} // End View_core
