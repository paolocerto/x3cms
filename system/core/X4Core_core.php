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
 * Core class
 * THIS FILE IS DERIVED FROM KOHANA
 *
 * @package X4WEBAPP
 */
final class X4Core_core
{
	// controller path
	private static $controller = '';
	// The singleton instance of the controller
	public static $insta;
	// The final output that will displayed
	public static $output = '';
	// Db connections data
	public static $db = array();
	// Caching active
	private static $caching = false;
	// Return instance
	private static $inst = true;

	/**
	 * Set the contest
	 *
	 * @static
     * @param   string  $default
     * @param   string  $db
     * @param   string  $cli
	 * @return  void
	 */
	public static function setCore($default, $db = NULL, $cli = '')
	{
		// set the route
		if (defined('X3CLI') && !empty($cli))
		{
		    X4Route_core::set_route($cli, $default);
		}
		else
		{
		    X4Route_core::set_route($_SERVER['REQUEST_URI'], $default);
		}

		// check if controller exists
		self::$controller = X4Route_core::controller_path();

		// set db data
		if (!is_null($db))
		{
			self::$db = $db;
		}

		// Start output buffering
		ob_start(array('X4Core_core', 'output_buffer'));

		// Set autoloader
		spl_autoload_register(array('X4Core_core', 'auto_load'));

		// Run the controller
		self::instance();

		// Enable output handling
		self::shutdown();
	}

	/**
	 * Load the controller and run the method
	 *
	 * @static
	 * @return  object  instance of controller
	 */
	final public static function & instance()
	{
		if (self::$insta === NULL)
		{
			// Include the Controller file

			require_once self::$controller;

			// Make sure the controller class exists
			try
			{
				// Start validation of the controller
				$class = new ReflectionClass(ucfirst(str_replace('-', '_', X4Route_core::$control).'_controller'));
			}
			catch (ReflectionException $e)
			{
				// Controller does not exist
				$class = new ReflectionClass('X4Page_controller');
			}

			// Create a new controller instance
			$controller = $class->newInstance();

			// caching
            self::caching();

			if (self::$inst)
			{
				try
				{
					// Load the controller method
					$method = $class->getMethod(str_replace('-', '_', X4Route_core::$method));
					if ($method->isProtected() or $method->isPrivate())
					{
						// Do not attempt to invoke protected methods
						throw new ReflectionException('protected controller method');
					}
					// Default arguments
					$arguments = X4Route_core::$args;
				}
				catch (ReflectionException $e)
				{
					// Use __call instead
					$method = $class->getMethod('__call');
					// Use arguments in __call format
					$arguments = array(X4Route_core::$method, X4Route_core::$args);
				}
				// Execute the controller method
				$method->invokeArgs($controller, $arguments);
			}
		}
		return self::$insta;
	}

    /**
	 * caching
	 *
	 * @static
	 * @param   string  current output buffer
	 * @return  string
	*/
	final public static function caching()
	{
        // only if area is public and _POST is empty
        if (CACHE && X4Route_core::$folder == 'public' && !X4Route_core::$post)
        {
            X4Cache_core::setPrefix(COOKIE);
            X4Cache_core::setStore(APATH.'files/tmp/');

            // if no cache to read
            if (!X4Cache_core::Start($_SERVER['REQUEST_URI'], CACHE_TIME))
            {
                self::$caching = true;
            }
            else
            {
                self::$inst = false;
                if (DEBUG)
                {
                    echo X4Bench_core::info('<p class="text-center pt-4 text-xs">X4WebApp v. {x4wa_version} - execution time: {execution_time} - memory usage: {memory_usage} - queries: {queries} - included files: {included_files}</p>');
                }
            }
        }
    }

	/**
	 * output handler.
	 *
	 * @static
	 * @param   string  current output buffer
	 * @return  string
	*/
	final public static function output_buffer(string $output)
	{
		// Set final output
		self::$output = $output;

		// Set and return the final output
		return self::$output;
	}

	/**
	 * Triggers the shutdown by closing the output buffer
	 *
	 * @static
	 * @return  void
	 */
	public static function shutdown()
	{
		// This will flush the buffer
		if (ob_get_level())
		{
			while (@ob_end_flush());
		}

		// close caching
		if (self::$caching && !defined('NOCACHE'))
		{
			X4Cache_core::End(self::$output);
		}
	}

	/**
	 * Provides class auto-loading.
	 *
	 * @static
	 * @param   string  name of class
	 * @return  bool
	 */
	public static function auto_load(string $class)
	{
		if (class_exists($class, false))
		{
			return true;
		}

		$what = explode('_', str_replace('-', '_', $class));

		switch($what[sizeof($what) - 1])
		{
		case 'model':
			//class directories
			$dirs = array(
				SPATH.'models/',
				APATH.'models/',
				PATH.'plugins/'.strtolower(str_replace('_model', '', $class)).'/models/'
			);
			break;
		case 'core':
			$dirs = array(SPATH.'core/');
			break;
		case 'driver':
			$dirs = array(SPATH.'drivers/');
			break;
		case 'helper':
			$dirs = array(
				SPATH.'helpers/',
				APATH.'helpers/'
			);
			break;
		case 'library':
			$dirs = array(SPATH.'libraries/');
			break;
		case 'plugin':
			$dirs = array(
				PATH.'plugins/',
				PATH.'plugins/'.strtolower(str_replace('_plugin', '', $class)).'/controllers/'
			);
			break;
		case 'api':
			$dirs = array(APATH.'apis/');
			break;
		case 'vendor':
		    $class = 'autoload';
		    $dirs = array(
				PATH.'vendor/'
			);
		    break;
		default:
			$dirs = array(
				SPATH.'controllers/',
				APATH.'controllers/',
				PATH.'plugins/'.strtolower(str_replace('_controller', '', $class)).'/controllers/'
			);
			break;
		}

        foreach ($dirs as $d)
        {
            if(file_exists($d.$class.EXT))
            {
                require_once($d.$class.EXT);
                return true;
            }
        }
		return false;
	}
}
