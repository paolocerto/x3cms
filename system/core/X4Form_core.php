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
	 * Attempts to load a view and pre-load view data
	 */
	public function __construct(string $name, string $module = '', array $data = [])
	{
        if (!empty($name))
		{
			// Set the filename

            // plugin
            if (!empty($module) && file_exists(PATH.'plugins/'.$module.'/forms/'.$name.'_form.php'))
            {
                $this->filename = PATH.'plugins/'.$module.'/forms/'.$name.'_form.php';
            }
            elseif (file_exists(APATH.'forms/'.X4Route_core::$folder.'/'.$name.'_form.php'))
            {
                $this->filename = APATH.'forms/'.X4Route_core::$folder.'/'.$name.'_form.php';
            }
            elseif (file_exists(APATH.'forms/'.$name.'_form.php'))
            {
                $this->filename = APATH.'forms/'.$name.'_form.php';
            }
		}

		if (!empty($data))
		{
			// Preload data using array_merge, to allow user extensions
			$this->local_data = array_merge($this->local_data, $data);
		}
	}

	/**
	 * Magically sets a form variable
	 */
	public function __set(string $key, mixed $value) : void
	{
		if (!isset($this->$key))
		{
			$this->local_data[$key] = $value;
		}
	}

	/**
	 * Magically gets a form variable
	 */
	public function __get(string $key) : mixed
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
	 * Magically converts form object to string
	 */
	public function __toString() : string
	{
		return $this->render(true);
	}

	/**
	 * Refresh variable inside local_data
	 */
	private function refresh(array $vars) : void
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
	 */
	public function render(bool $print = false) : mixed
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

        // if true return the form as a section of a form instead of returning an array
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
