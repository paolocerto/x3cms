<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

/**
 * Controller for CLI
 *
 * @package X3CMS
 */
class Create_controller extends X4Cms_controller
{
	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * default method
	 *
	 * @return void
	 */
	public function _default()
	{
	    // Error message
		echo NL.
		    'WARNING: The create command require three parameters:'.NL.
		    ' - type of object (controller, model, view, dictionary or mvc (that means model + view + controller + dictionary))'.NL.
		    ' - area (area name)'.NL.
		    ' - name (name for the object)'.NL.
		    ' Example: php x3 create controller public "foo bar"'.NL;
	}

	/**
	 * Create controller
	 *
	 * @param string    $area
	 * @param string    $name
	 * @return void
	 */
	public function controller(string $area, string $name)
	{
	    // get the final name
	    $name = X4Utils_helper::slugify($name, true, true);

	    // check if the object already exists
        if (file_exists(APATH.'controllers/'.$area.'/'.$name.'_controller'.EXT))
        {
            // print an error message
	        echo NL.'WARNING: A file named '.$name.'_controller'.EXT.' already exists in '.APATH.'controllers/'.$area.'/'.NL;
        }
	    else
	    {
	        // create the controller
	        $mod = new X3cli_model();
	        $res = $mod->create_controller($area, $name);

            if ($res)
            {
                echo NL.'The controller '.$name.' was created successfully!'.NL;
            }
            else
            {
                echo NL.'WARNING: an error occurred'.NL.'Please, check for write permissions on the folder '.APATH.'controllers/'.$area.NL;
            }
	    }
	}

	/**
	 * Create model
	 *
	 * @param string    $area
	 * @param string    $name
	 * @return void
	 */
	public function model(string $area, string $name)
	{
	    // get the final name
	    $name = X4Utils_helper::slugify($name, true, true);

	    // check if the object already exists
	    if (file_exists(APATH.'models/'.$name.'_model'.EXT))
        {
            // print an error message
	        echo NL.
                'WARNING: A file named '.$name.'_model'.EXT.' already exists in '.APATH.'models/'.NL;
        }
        else
        {
            // create the model
            $mod = new X3cli_model();
	        $res = $mod->create_model($area, $name);

            if ($res)
            {
                echo NL.'The model '.$name.' was created successfully!'.NL;
            }
            else
            {
                echo NL.'WARNING: an error occurred'.NL.'Please, check for write permissions on the folder '.APATH.'models/'.NL;
            }
        }
	}

	/**
	 * Create view
	 *
	 * @param string    $area
	 * @param string    $name
	 * @return void
	 */
	public function view(string $area, string $name)
	{
	    // get the final name
	    $name = X4Utils_helper::slugify($name, true, true);

	    // check if the object already exists
        if (file_exists(APATH.'views/'.$area.'/'.$name.'_view'.EXT))
        {
            // print an error message
	        echo NL.
                'WARNING: A file named '.$name.'_view'.EXT.' already exists in '.APATH.'views/'.$area.'/'.NL;
        }
	    else
	    {
	        // create the view
	        $mod = new X3cli_model();
	        $res = $mod->create_view($area, $name);

            if ($res)
            {
                echo NL.'The view '.$name.' was created successfully!'.NL;
            }
            else
            {
                echo NL.'WARNING: an error occurred'.NL.'Please, check for write permissions on the folder '.APATH.'views/'.$area.'/'.NL;
            }
	    }
	}

	/**
	 * Create a basic dictionary
	 *
	 * @param string    $area
	 * @param string    $name
	 * @return void
	 */
	public function dictionary(string $area, string $name)
	{
	    if ($area == 'admin')
	    {
            // get the final name
            $name = X4Utils_helper::slugify($name, true, true);

            // create the dictionary section
            $mod = new X3cli_model();
            $res = $mod->create_dictionary($area, $name);

            if (!is_null($res))
            {
                echo NL.'The dictionary section "'.$name.'" was created successfully!'.NL;
            }
            else
            {
                echo NL.'WARNING: an error occurred'.NL;
            }
        }
        else
        {
            // dictionary is available only for admin area
            echo NL.'WARNING: you can create dictionary section only for the "admin" area'.NL;
        }
	}

	/**
	 * Create mvc (controller + model + view + dictionary)
	 *
	 * @param string    $area
	 * @param string    $name
	 * @return void
	 */
	public function mvc(string $area, string $name)
	{
	    $this->controller($area, $name);
	    $this->model($area, $name);
	    $this->view($area, $name);
	    if ($area == 'admin')
	    {
	        $this->dictionary($area, $name);
	    }
	}
}
