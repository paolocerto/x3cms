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
 * View class
 * THIS FILE IS DERIVED FROM KOHANA
 *
 * @package X4WEBAPP
 */
class X4View_core
{
	protected $filename = false;
	protected $type = false;

	protected $local_data = array();
	protected static $global_data = array();

	/**
	 * Attempts to load a view and pre-load view data
	 */
	public function __construct(string $name, string $module = '', array $data = [])
	{
		if (!empty($name))
		{
			// Set the filename
			if (strstr($name, 'templates') != '')
			{
				// into theme
				if (!file_exists($_SERVER['DOCUMENT_ROOT'].$name.'.php'))
				{
					$token = explode('/', $name);
					$token[5] = 'base';
					$name = implode('/', $token);
				}
				$this->filename = $_SERVER['DOCUMENT_ROOT'].$name.'.php';
			}
			else
			{
                if (!empty($module) && file_exists(PATH.'plugins/'.$module.'/views/'.$name.'_view.php'))
				{
                    // plugin
					$this->filename = PATH.'plugins/'.$module.'/views/'.$name.'_view.php';
				}
				elseif (file_exists(APATH.'views/'.X4Route_core::$folder.'/'.$name.'_view.php'))
				{
					$this->filename = APATH.'views/'.X4Route_core::$folder.'/'.$name.'_view.php';
				}
				elseif (file_exists(APATH.'views/'.$name.'_view.php'))
				{
					$this->filename = APATH.'views/'.$name.'_view.php';
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
	 * Magically sets a view variable
	 */
	public function __set(string $key, mixed $value) : void
	{
		if (!isset($this->$key))
		{
			$this->local_data[$key] = $value;
		}
	}

	/**
	 * Magically gets a view variable
	 */
	public function __get(string $key) : mixed
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
	 * Magically converts view object to string
	 */
	public function __toString() : string
	{
		return $this->render();
	}

	/**
	 * Renders a view.
	 */
	public function render(bool $print = false)
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

}
