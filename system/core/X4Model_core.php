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
 * This is the abstract class for X3CMS models
 * Models have as their main task of interfacing with the database
 * This class provide the constructor which set the default table for the model
 * Other methods are: last_query, get_all, get_by_id, insert, update and delete
 * THIS FILE IS DERIVED FROM KOHANA
 *
 * @package		X3CMS
 */
abstract class X4Model_core 
{
	/**
	 * @var DB object
	 */
	protected $db;
	
	/**
	 * @var table name
	 */
	protected $table;
	
	/**
	 * @var mongo ID switcher
	 * if true uses the default Mongo ID object
	 */
	protected $mongo_id = true;
	
	/**
	 * @var logs are disabled as default
	 */
	protected $log = false;
	
	/**
	 * Loads the database instance, if the database is not already loaded.
	 *
	 * @param   string	table name
	 * @param   string	database name
	 * @param   string	alternative database name
	 * @return  void
	 */
	public function __construct($table_name, $db_name = 'default', $adb = '')
	{
		if ((is_object($this->db) && X4Db_core::$instances[$db_name] != $this->db) || !is_object($this->db)) 
		{
			// set the driver
			$driver = 'X4'.X4Core_core::$db[$db_name]['db_type'].'_driver';
			// Load the database
			$this->db = new $driver($db_name, $adb);
		}
		$this->table = $table_name;
	}
	
	/**
	 * Build the form array required to set the parameter
	 * This method have to be overrided with the plugin options
	 *
	 * @param	integer $id_area Area ID
	 * @param	string	$lang Language code
	 * @param	string	$param Parameter
	 * @return	array
	 */
	public function configurator($id_area, $lang, $param)
	{
	    $fields = array();
	    
	    $fields[] = array(
			'label' => null,
			'type' => 'html',
			'value' => '<p>'._ARTICLE_PARAM_SETTING_NOT_REQUIRED.'</p>'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => 1,
			'name' => 'no_options'
		);
		
		// options field store all possible cases and parts
		// cases are separated by §
		// parts are separated by |
		
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => '',
			'name' => 'options'
		);
		
		return $fields;
	}
	
	/**
	 * Close connection
	 *
	 * @return	void
	 */
	final public function close()
	{
		$this->db->close();
	}
	
	/**
	 * Log setter
	 *
	 * @param 	boolean	Log status
	 * @return	void
	 */
	final public function set_log($status)
	{
		$this->log = $status;
	}
	
	/**
	 * Replacement for MySQL NOW() function to use PHP timezone
	 *
	 * @return string
	 */
	final public function now()
	{
		return date('Y-m-d H:i:s');
	}
	
	/**
	 * Replacement for MySQL UNIXTIMESTAMP function to use PHP timezone
	 *
	 * @return string
	 */
	final public function time()
	{
		return time();
	}
	
	/**
	 * Get last query
	 *
	 * @return string
	 */
	final public function last_query()
	{
		return $this->db->last_query();
	}
	
	/**
	 * Get all rows from a table
	 * to prevent the loading of different models to make base calls and to optimize queries you can set table and fields
	 *
	 * @final
	 * @param   string	table name
	 * @param   string	field list
	 * @param	array	associative array of conditions
	 * @param   mixed	sort rule
	 * @return  array
	 */
	final public function get_all($table = '', $fields = '*', $criteria = array(), $sort = '')
	{
		$t = (empty($table)) 
			? $this->table 
			: $table;
		
		if ($this->db->sql)
		{
			// Relational DB
			$w = '';
			if (!empty($criteria))
			{
				$c = array();
				foreach($criteria as $k => $v)
				{
					$c[] = addslashes($k).' = '.$this->db->escape($v);
				}
				$w = ' WHERE '.implode(' AND ', $c);
			}
			
			if (!empty($sort))
			{
				$sort = ' ORDER BY '.$sort;
			}
			return $this->db->query('SELECT '.$fields.' FROM '.$t.$w.$sort);
		}
		else
		{
			// Mongo DB
			if (!is_array($fields))
			{
				$fields = array();
			}
			
			if (!is_array($sort))
			{
				$sort = array();
			}
			return $this->db->query($criteria, $t, $fields, $sort);
		}
	}
	
	/**
	 * Find a row in a table
	 * to prevent the loading of different models to make base calls and to optimize queries you can set table and fields
	 *
	 * @final
	 * @param   string	table name
	 * @param   mixed	fields list (string or array)
	 * @param	array	associative array of conditions
	 * @param   mixed	sorting rules (string or array)
	 * @return  array
	 */
	final public function find($table = '', $fields = '*', $criteria = array(), $sort = '')
	{
		$t = (empty($table)) 
			? $this->table 
			: $table;
			
		if ($this->db->sql)
		{
			// Relational DB
			$w = '';
			if (!empty($criteria))
			{
				$c = array();
				foreach($criteria as $k => $v)
				{
					$c[] = addslashes($k).' = '.$this->db->escape($v);
				}
				$w = ' WHERE '.implode(' AND ', $c);
			}
			
			if (!empty($sort))
			{
				$sort = ' ORDER BY '.$sort;
			}
			if ($fields == '*' || sizeof(explode(',', $fields)) > 1)
			{
			    return $this->db->query_row('SELECT '.$fields.' FROM '.$t.$w.$sort.' LIMIT 0, 1');
			}
			else
			{
			    return $this->db->query_var('SELECT '.$fields.' FROM '.$t.$w.$sort.' LIMIT 0, 1');
			}
		}
		else
		{
			// Mongo DB
			if (!is_array($fields))
			{
				$fields = array();
			}
			
			if (!is_array($sort))
			{
				$sort = array();
			}
			return $this->db->query_row($criteria, $t, $fields, $sort);
		}
	}
	
	/**
	 * Set the autoincrement ID, only for Mongo DB
	 *
	 * @final
	 * @param   integer	id value
	 * @param   mixed	Mongo ID switcher (null => default settings in X4Model_core, true => Mongo ID, false => autoincrement ID) 
	 * @return  mixed
	 */
	final public function set_mid($id, $mongo_id)
	{
		if (!$this->db->sql)
		{
			// set which type of ID to use
			if (is_null($mongo_id))
			{
				$mid = $this->mongo_id
					? new MongoId($id)
					: $id;
			}
			else
			{
				$mid = $mongo_id
					? new MongoId($id)
					: $id;
			}
			return $mid;
		}
	}
	
	/**
	 * Get the autoincrement ID, only for Mongo DB
	 *
	 * @final
	 * @param   string	table name
	 * @return  integer
	 */
	final public function get_mid($table)
	{
		if (!$this->db->sql)
		{
			$res = $this->modify(
				'indexes', 
				array('_id' => $table), 
				array('$inc' => array('seq' => 1)), 
				array('seq' => true)
			);

			if ($res[1])
			{
				return $res[0]['seq'];
			}
			else
			{
				// a temporary solution to avoid duplicates
				return time();
			}
		}
	}
	
	/**
	 * Get a row from a table
	 * to prevent the loading of different models to make base calls and to optimize queries you can set table and fields
	 *
	 * @final
	 * @param   integer	record ID
	 * @param   string	table name
	 * @param   string	field list
	 * @param   boolean	Mongo ID switcher
	 * @return  array
	 */
	final public function get_by_id($id, $table = '', $fields = '*', $mongo_id = null)
	{
		$t = (empty($table)) 
			? $this->table 
			: $table;
				
		if ($this->db->sql)
		{
			// Relational DB
			return $this->db->query_row('SELECT '.$fields.' FROM '.$t.' WHERE id = '.intval($id));
		}
		else
		{
			// Mongo DB
			if (!is_array($fields))
			{
				$fields = array();
			}
			
			$mid = $this->set_mid($id, $mongo_id);
			
			return $this->db->query_row(array('_id' => $mid), $t, $fields);
		}
	}
	
	/**
	 * Get a var from a table
	 * to prevent the loading of different models to make base calls and to optimize queries you can set table and fields
	 *
	 * @final
	 * @param   integer	record ID
	 * @param   string	table name
	 * @param   string	field name
	 * @param   array	sort array for Mongo DB
	 * @param   boolean	Mongo ID switcher
	 * @return  array
	 */
	final public function get_var($id, $table, $field = '', $sort = array(), $mongo_id = null)
	{
		$t = (empty($table)) 
				? $this->table 
				: $table;
				
		if ($this->db->sql)
		{
			// Relational DB
			return $this->db->query_var('SELECT '.$field.' FROM '.$t.' WHERE id = '.intval($id));
		}
		else
		{
			// Mongo DB
			
			// set which type of ID to use
			$mid = $this->set_mid($id, $mongo_id);
			
			return $this->db->query_var(array('_id' => $mid), $t, $field, $sort);
		}
	}
	
	/**
	 * Insert a row in a table
	 * to prevent the loading of different models to make base calls you can set table
	 *
	 * @final
	 * @param   array	data to insert
	 * @param   string	table name
	 * @param   array   array of floats    //boolean	Mongo ID switcher
	 * @return  array	(id row, success)
	 */
	final public function insert($data, $table = '', $floats = array())    //$mongo_id = null)
	{
		$t = (empty($table)) 
				? $this->table 
				: $table;
				
		if ($this->db->sql)
		{
			// Relational DB
			$field = $insert = '';
			foreach($data as $k => $v) 
			{
				$field .= ', '.$k;
				if (in_array($k, $floats))
				{
				    $insert .= ', '.str_replace(',', '.', floatval($v));
				}
				else
				{
				    $insert .= ', '.$this->db->escape($v);
				}
			}
			
			$res = $this->db->single_exec('INSERT INTO '.$t.' (updated '.$field.') VALUES (\''.$this->now().'\' '.$insert.')');
			
			if ($this->log && $res[1]) 
			{
				$uid = $this->get_who();
				$this->logger($uid, $res[0], $t, 'insert');
			}
		}
		else
		{
			// Mongo DB
			
			// add the updated value
			$data['updated'] = $this->now(); 
			
			$sql = array(
				'action' => 'insert',
				'data' => $data,
				'id' => (is_null($mongo_id)) 
						? $this->mongo_id
						: $mongo_id
			);
			$res = $this->db->single_exec($sql, $t);
		}
		return $res;
	}
	
	/**
	 * Set user ID for logger
	 *
	 *  @return  integer
	 */
	private function get_who()
	{
	    if (isset($_SESSION['xuid']))
	    {
	        return $_SESSION['xuid'];
	    }
	    
	    if (isset($_SESSION['uid']))
	    {
	        return $_SESSION['uid'];
	    }
	    return 0;
	}
	
	/**
	 * Update a row in a table
	 * to prevent the loading of different models to make base calls you can set table
	 *
	 * @final
	 * @param   integer	record ID
	 * @param   array	data to update
	 * @param   string	table name
	 * @param   array   array of floats    //boolean	Mongo ID switcher
	 * @return  array (id row, success)
	 */
	final public function update($id, $data, $table = '', $floats = array())    //$mongo_id = null)
	{
		$t = (empty($table)) 
				? $this->table 
				: $table;
		
		if ($this->db->sql)
		{
			// Relational DB
			$update = '';
			foreach($data as $k => $v) 
			{
			    if (in_array($k, $floats))
				{
				    $update .= ', '.addslashes($k).' = '.str_replace(',', '.', floatval($v));
				}
				else
				{
				    $update .= ', '.addslashes($k).' = '.$this->db->escape($v);
				}
			}
			
			$res = $this->db->single_exec('UPDATE '.$t.' SET updated = \''.$this->now().'\' '.$update.' WHERE id = '.intval($id));
			$res = array($id, $res[1]);
			
			if ($this->log && $res[1]) 
			{
				$uid = $this->get_who();
				$this->logger($uid, $id, $t, 'update');
			}
		}
		else
		{
			// Mongo DB
			
			// set which type of ID to use
			$mid = $this->set_mid($id, $mongo_id);
			
			// add the updated value
			$data['updated'] = $this->now(); 
			
			$sql = array(
				'action' => 'update',
				'criteria' => array('_id' => $mid),
				'data' => $data
			);
			$res = $this->db->single_exec($sql, $t);
		}
		return $res;
	}
	
	/**
	 * Delete a row from a table
	 * to prevent the loading of different models to make base calls you can set table
	 *
	 * @final
	 * @param   integer	record ID
	 * @param   string	table name
	 * @param   boolean	Mongo ID switcher
	 * @return  array(rows affected, id row)
	 */
	final public function delete($id, $table = '', $mongo_id = null)
	{
		$t = (empty($table)) 
			? $this->table 
			: $table;
				
		if ($this->db->sql)
		{
			// Relational DB
			$res = $this->db->single_exec('DELETE FROM '.$t.' WHERE id = '.intval($id));
			$res = array($id, $res[1]);
			
			if ($this->log && $res[1]) 
			{
				$uid = $this->get_who();
				$this->logger($uid, $id, $t, 'delete');
			}
			return $res;
		}
		else
		{
			// Mongo DB
			
			// set which type of ID to use
			$mid = $this->set_mid($id, $mongo_id);
				
			$sql = array(
				'action' => 'delete',
				'criteria' => array('_id' => $mid),
			);
			
			return $this->db->single_exec($sql, $t);
		}
	}
	
	/**
	 * Multiple delete, only for Mongo DB
	 * to prevent the loading of different models to make base calls you can set table
	 *
	 * @final
	 * @param   string	table name
	 * @param   array	array of criteria
	 * @return  array
	 */
	final public function multiple_delete($table = '', $criteria = array())
	{
		if (!$this->db->sql)
		{
			// Mongo DB
			$sql = array(
				'action' => 'multiple_delete',
				'criteria' => $criteria
			);
			return $this->db->single_exec($sql, $table);
		}
	}
	
	/**
	 * Multiple delete, only for Mongo DB
	 * to prevent the loading of different models to make base calls you can set table
	 *
	 * @final
	 * @param   string	table name
	 * @return  array
	 */
	final public function drop($table = '')
	{
		if (!$this->db->sql)
		{
			$t = (empty($table)) 
				? $this->table 
				: $table;
			
			// Mongo DB
			$sql = array(
				'action' => 'drop'
			);
			return $this->db->single_exec($sql, $t);
		}
	}
	
	/**
	 * List collections, only for Mongo DB
	 *
	 * @final
	 * @return  array
	 */
	final public function lister()
	{
		if (!$this->db->sql)
		{
			// Mongo DB
			$sql = array(
				'action' => 'list'
			);
			$res = $this->db->single_exec($sql);
			return $res[0];
		}
	}
	
	/**
	 * Find and Modify a row in a table, only for Mongo DB
	 * to prevent the loading of different models to make base calls you can set table
	 *
	 * @final
	 * @param   string	table name
	 * @param   array	array of criteria
	 * @param   array	(data to update)
	 * @param   array	fields to return
	 * @param   array	sort rules
	 * @return  array
	 */
	final public function modify($table = '', $criteria = array(), $data = array(), $fields = array(), $sort = array())
	{
		if (!$this->db->sql)
		{
			// Mongo DB
			$t = (empty($table)) 
				? $this->table 
				: $table;
			
			$sql = array(
				'action' => 'modify',
				'criteria' => $criteria,
				'data' => $data,
				'fields' => $fields,
				'sort' => $sort
			);
			return $this->db->single_exec($sql, $t);
		}
	}
	
	/**
	 * Count items in a collection filtered with criteria, only for Mongo DB
	 * to prevent the loading of different models to make base calls you can set table
	 *
	 * @final
	 * @param   string	table name
	 * @param   array	array of criteria
	 * @return  integer
	 */
	final public function count($table = '', $criteria = array(), $data = array(), $fields = array(), $sort = array())
	{
		if (!$this->db->sql)
		{
			// Mongo DB
			$t = (empty($table)) 
				? $this->table 
				: $table;
				
			$sql = array(
				'action' => 'count',
				'criteria' => $criteria
			);
			$res = $this->db->single_exec($sql, $t);
			
			return $res[0];
		}
	}
	
	/**
	 * Log an action
	 *
	 * @param   integer	User ID
	 * @param   integer	Record ID
	 * @param   string	Table name
	 * @param   string	Action to log
	 * @param   string	Memo to log 
	 * @return  array
	 */
	public function logger($who, $id_what, $what, $action, $memo = '', $xon = 1)
	{
		// default result
		$res = array(0, 0);
				
		if ($this->db->sql)
		{
			// Relational DB
			$log = (empty($memo))
				? $this->db->escape($this->db->latest_query)
				: $this->db->escape($memo);
			
			// do not store logs for $id_what == 0
			if (intval($id_what) != 0)
			{
			    // here we don't use insert to avoid loops
			    $res = $this->db->single_exec('INSERT INTO logs (updated, who, what, id_what, action, memo, xon) 
					VALUES (\''.$this->now().'\', '.intval($who).', '.$this->db->escape($what).', '.intval($id_what).', '.$this->db->escape($action).', '.$log.','.intval($xon).')');
			}
		}
		return $res;
	}
	
	/**
	 * Get attribute
	 *
	 * @param   string	Attribute name
	 * @return	string
	 */
	public function get_attribute($attr)
	{
		if ($this->db->sql)
		{
			return $this->db->get_attribute($attr);
		}
		else
		{
			return '';
		}
	}
}

/**
 * Abstract class for X3CMS database drivers
 */
abstract class X3db_driver
{
	// Database instances
	protected $name = '';
	public static $instances = array();
	// DSN
	protected $dsn = '';
	// query counter
	public static $queries = 0;
	// the connection
	protected $link;
	// last query memo
	public $latest_query = '';
	
	/**
	 * @var Switcher for NoSQL DBs
	 */
	public $sql = null;
	
	/**
	 * Returns a singleton instance of Database.
	 *
	 * @static
	 * @param   string	DB name
	 * @return  X4Db driver object
	 */
	public static function & instance($name = 'default')
	{
		if (!isset(self::$instances[$name]))
		{
			// Create a new instance
			if (isset(X4Core_core::$db[$name]))
			{
				$driver = 'X4'.X4Core_core::$db[$name]['db_type'].'_driver';
				self::$instances[$name] = new $driver(X4Core_core::$db[$name]);
			}
		}
		return self::$instances[$name];
	}
	
	/**
	 * Simple connect method to get the database queries up and running.
	 *
	 * @return  void
	 */
	abstract function connect();
	
	/**
	 * A primitive debug system
	 *
	 * @param   mixed  SQL query to execute
	 * @param   mixed  Error message
	 * @return  string
	 */
	abstract function print_error($sql, $msg);
	
	/**
	 * Runs a query and returns the result.
	 *
	 * @param   mixed  SQL query to execute or array for MongoDB
	 * @param   string  Collection name for Mongo DB
	 * @param   array	fields array for Mongo DB
	 * @param   array	sorting rules for Mongo DB
	 * @return  Database_Result
	 */
	abstract public function query($sql, $collection = '', $fields = array(), $sort = array());
	
	/**
	 * Runs a query and returns the first result row.
	 *
	 * @param   mixed  SQL query to execute or array for MongoDB
	 * @param   string  Collection name for Mongo DB
	 * @param	array	Fields array for Mongo DB 
	 * @param	array	Sorting array for Mongo DB 
	 * @return  Database_Result
	 */
	abstract public function query_row($sql, $collection = '', $fields = array(), $sort = array());
	
	/**
	 * Runs a query and returns the first value.
	 *
	 * @param   mixed  SQL query to execute or array for MongoDB
	 * @param   string  Collection name for Mongo DB
	 * @param   string  Field name to retrieve for Mongo DB
	 * @param   array	sort array for Mongo DB
	 * @return  Database_Result
	 */
	abstract public function query_var($sql, $collection = '', $field = '', $sort = array());
	
	/**
	 * Compiles an exec query and runs it.
	 *
	 * @param   mixed  SQL query to execute or array for MongoDB
	 * @param   string  Collection name for Mongo DB or a string to force the return of the last Inserted ID 
	 * @param   array  Query options for Mongo DB ('fsync' => true, 'timeout' => 10000)
	 * @return  Database_Result  Query result
	 */
	abstract public function single_exec($sql = '', $collection = '', $options = array());
	
	/**
	 * Compiles an array of exec query and runs it.
	 *
	 * @param   array of queries  sql
	 * @param   string  Collection name for Mongo DB
	 * @return  Database_Result  Query result
	 */
	abstract public function multi_exec($sql, $collection = '');
	
	/**
	 * Escapes a value for a query.
	 *
	 * @param   mixed   value to escape
	 * @return  string
	 */
	abstract public function escape($value);
	
	/**
	 * Returns the last query run.
	 *
	 * @return  string SQL
	 */
	public function last_query()
	{
	   return $this->latest_query;
	}
	
	/**
	 * close db connection
	 */
	public function close()
	{
		// close db
		$this->link = null;
	}
}
