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
 * Form class
 *
 * @package X4WEBAPP
 */
class X4Form_core
{
	// The form file name and type
	protected $filename = false;
	protected $type = false;

	// Form variable storage
	protected $local_data = array();

	/**
	 * Attempts to load a view and pre-load view data.
	 *
	 * @throws  Kohana_Exception  if the requested view cannot be found
	 * @param   string  $name   form name
     * @param   string  $module Mudule name
	 * @param   array   $data   array of pre-load data
	 * @param   string  $type   type of file: html, css, js, etc.
	 * @return  void
	 */
	public function __construct(string $name, string $module = '', array $data = [], string $type = EXT)
	{
        if (!empty($name))
		{
			// Set the filename

            // plugin
            if (!empty($module) && file_exists(PATH.'plugins/'.$module.'/forms/'.$name.'_form'.$type))
            {
                $this->filename = PATH.'plugins/'.$module.'/forms/'.$name.'_form'.$type;
            }
            elseif (file_exists(APATH.'forms/'.X4Route_core::$folder.'/'.$name.'_form'.$type))
            {
                $this->filename = APATH.'forms/'.X4Route_core::$folder.'/'.$name.'_form'.$type;
            }
            elseif (file_exists(APATH.'forms/'.$name.'_form'.$type))
            {
                $this->filename = APATH.'forms/'.$name.'_form'.$type;
            }
		}

		if (!empty($data))
		{
			// Preload data using array_merge, to allow user extensions
			$this->local_data = array_merge($this->local_data, $data);
		}
	}

	/**
	 * Magically sets a form variable.
	 *
	 * @param   string  variable key
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
	 * Magically gets a form variable.
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

		if (isset($this->$key))
		{
			return $this->$key;
		}
	}

	/**
	 * Magically converts form object to string.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->render(true);
	}

	/**
	 * Refresh variable inside local_data
	 *
	 * @param	array	$vars	New vars created by the included file
	 * @return  void
	 */
	private function refresh(array $vars)
	{
		// refresh local_data
		foreach ($this->local_data as $k => $v)
		{
			if (!is_object($vars[$k]))
			{
				// objects shouldn't be changed
				$this->local_data[$k] = $vars[$k];
				// remove from vars
				unset($vars[$k]);
			}
		}

		// are there any newly created?
		foreach ($vars as $k => $v)
		{
			if (!is_object($v))
			{
				$this->__set($k, $v);
			}
		}
	}

	/**
	 * Renders a form
	 *
	 * @param   boolean   set to TRUE to return the form as a section of a form instead of returning an array
	 * @return  mixed     array if print is FALSE string if print is TRUE
	 */
	public function render(bool $print = FALSE)
	{
		// Import the view variables to local namespace
		extract($this->local_data, EXTR_SKIP);

		if (empty($this->filename))
		{
			if (DEBUG)
			{
				echo 'form file not found +++'.$this->filename.'+++';
				die;
			}
			else
			{
				return array();
			}
		}
		else
		{
			// include the subform file
			include $this->filename;
		}

		// get defined vars
		$vars = get_defined_vars();

		// update local_data
		$this->refresh($vars);

		if ($print)
		{
			// return form section
			return X4Form_helper::doform_section($fields);
		}
		else
		{
            // return all data
			return $fields;
		}
	}

}
