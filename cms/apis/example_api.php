<?php 
defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
 
/**
 * API class
 * Here a very simple implementation 
 * - Basic hello world
 * - Four cases GET, POST, PUT, DELETE (with protected require authorization)
 *
 */
class Example 
{
	/**
	 * Model to use
	 */
	private $mod;
	
	/**
	 * Fields to use
	 */
	private $fields = array('lang', 'name', 'title', 'description');
	
	/**
	 * Constructor
	 *
	 * @return  void
	 */
	function __construct()
	{
		// set the model to use
		$this->mod = new Area_model();
	}
	
	/**
	 * Basic hello world example
	 * URL to call http://localhost/x3cms/api/example/hello/foo
	 *
	 * @return string
	 */
	function hello($to = 'world') 
	{
		return "Hello $to!";
	}
	
	/**
	 * GET
	 * XML format URL to call http://localhost/x3cms/api/example.xml
	 * XML format URL to call http://localhost/x3cms/api/example/2.xml
	 * JSON format URL to call http://localhost/x3cms/api/example.json
	 * JSON format URL to call http://localhost/x3cms/api/example/2.json
	 */
	public function get($id = NULL) 
	{
		return is_null($id) 
			? $this->mod->get_all() 
			: $this->mod->get_by_id($id);
	}
	
	/**
	 * POST
	 * set as protected so need authorization
	 */
	protected function post($request_data = NULL) 
	{
		return $this->mod->insert($this->_validate($request_data));
	}
	
	/**
	 * PUT
	 * set as protected so need authorization
	 */
	protected function put($id = NULL, $request_data = NULL) 
	{
		return $this->mod->update($id, $this->_validate($request_data));
	}
	
	/**
	 * DELETE
	 * set as protected so need authorization
	 */
	protected function delete($id = NULL) 
	{
		return $this->mod->delete($id);
	}
	
	/**
	 * Simple validation
	 */
	private function _validate($data)
	{
		$a = array();
		foreach ($this->fields as $i) 
		{
			//you may also vaildate the data here
			
			if (!isset($data[$i]))
				throw new RestException(417,"$i field missing");
			
			$a[$i] = $data[$i];
		}
		
		return $a;
	}
}

/**
 * Example of simple authorization
 * just for a test
 * URL to call http://localhost/x3cms/api/example?key=my_secret
 * 
 */
class SimpleAuth implements iAuthenticate
{
	/**
	 * The secret key
	 */
	const KEY = null;	// 'my_secret';
	
	/**
	 * Check the key
	 * Required method
	 */
	function __isAuthenticated() 
	{
		return isset($_GET['key']) && !is_null(SimpleAuth::KEY) && $_GET['key']==SimpleAuth::KEY ? TRUE : FALSE;
	}
	
	/**
	 * Return the key
	 */
	function key()
	{
		return SimpleAuth::KEY;
	}
}

