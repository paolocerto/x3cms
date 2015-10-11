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
 * Manage Db connection and queries
 * Use PDO or Mongo Class
 *
 * @package		X4WEBAPP
 */
final class X4Db_core 
{
	// Database instances
	protected $name = '';
	public static $instances = array();
	// DSN
	protected $dsn = '';
	// query counter
	public static $queries = 0;
	protected $link;
	public $latest_query = '';
	
	/**
	 * @var Switcher for Mongo DB
	 */
	public $sql = true;
	
	/**
	 * Returns a singleton instance of Database.
	 *
	 * @static
	 * @param   mixed   configuration array or DSN
	 * @return  X4Db
	 */
	public static function & instance($name = 'default')
	{
		if (!isset(self::$instances[$name]))
		{
			// Create a new instance
			if (isset(X4Core_core::$db[$name]))
				self::$instances[$name] = new X4Db_core(X4Core_core::$db[$name]);
		}
		return self::$instances[$name];
	}
	
	/**
	 * Sets up the database configuration
	 *
	 */
	public function __construct($name = 'default')
	{
		if (isset(X4Core_core::$db[$name]))
		{
			$this->name = $name;
			// create DSN
			switch (X4Core_core::$db[$name]['db_type']) 
			{
				case 'sqlite':
					$this->dsn = X4Core_core::$db[$name]['db_type'].':'.APATH.X4Core_core::$db[$name]['db_name'];
					break;
				case 'mysql':
					// without or with socket
					$this->dsn = (empty(X4Core_core::$db[$name]['db_socket'])) 
						? X4Core_core::$db[$name]['db_type'].':host='.X4Core_core::$db[$name]['db_host'].';dbname='.X4Core_core::$db[$name]['db_name']
						: X4Core_core::$db[$name]['db_type'].':unix_socket='.X4Core_core::$db[$name]['db_socket'].';dbname='.X4Core_core::$db[$name]['db_name'];
					break;
				case 'pgsql':
					$this->dsn = X4Core_core::$db[$name]['db_type'].':host='.X4Core_core::$db[$name]['db_host'].';dbname='.X4Core_core::$db[$name]['db_name'];
					break;
				case 'mongo':
					$db_port = (empty(X4Core_core::$db[$this->name]['db_port']))
						? ''
						: ':'.X4Core_core::$db[$this->name]['db_port'];
					$this->dsn = 'mongodb://'.X4Core_core::$db[$this->name]['db_user'].':'.X4Core_core::$db[$this->name]['db_pass'].'@'.X4Core_core::$db[$name]['db_host'].$db_port;	// .'/'.X4Core_core::$db[$name]['db_name']
					$this->sql = false;
					break;
			}
		}
	}
	
	/**
	 * Simple connect method to get the database queries up and running.
	 *
	 * @return  void
	 */
	private function connect()
	{
		if (!is_object($this->link)) 
		{
			if ($this->sql || X4Core_core::$db[$this->name]['db_type'] != 'mongo')
			{
				try 
				{
					switch (X4Core_core::$db[$this->name]['db_type']) 
					{
						case 'sqlite':
							// create mysql functions
							function NOW() {return time();}
							function LOWER($str) {return strtolower($str);}
							function UPPER($str) {return strtoupper($str);}
							function concat($s1, $s2, $s3) {return $s1.$s2.$s3;}
							function concat_ws($s1, $s2, $s3, $s4) {return $s2.$s1.$s3.$s1.$s4;}
							
							$this->link = new PDO($this->dsn);
							$this->link->sqliteCreateFunction('NOW', 'time', 1);
							$this->link->sqliteCreateFunction('LOWER', 'strtolower', 1);
							$this->link->sqliteCreateFunction('UPPER', 'strtoupper', 1);
							$this->link->sqliteCreateFunction('CONCAT', 'concat', 3);
							$this->link->sqliteCreateFunction('CONCAT_WS', 'concat_ws', 4);
							break;
						case 'mysql':
							$this->link = new PDO($this->dsn, X4Core_core::$db[$this->name]['db_user'], X4Core_core::$db[$this->name]['db_pass'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '".X4Core_core::$db[$this->name]['charset']."'"));
							break;
					}
					$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				}
				catch (PDOException $e) 
				{
					echo '<h1>Error establishing a database connection!</h1>';
					die($e->getMessage());
				}
			}
			else
			{
				if (X4Core_core::$db[$this->name]['db_type'] == 'mongo')
				{
					// Mongo connection
					$mc = new Mongo($this->dsn);
					// Select the DB
					$this->link = $mc->selectDB($this->name);
				}
			}
		}
	}
	
	/**
	 * close db connection
	 */
	public function close()
	{
		// close db
		$this->link = null;
	}
	
	/**
	 * A primitive debug system
	 *
	 * @param   mixed  SQL query to execute or array for MongoDB
	 * @param   mixed  Error message or error array for MongoDB
	 * @return  string
	 */
	private function print_error($sql = '', $msg = '') 
	{
		// If there is an error then take note of it
		if ($this->sql)
		{
			echo  '<h1>SQL/DB Error</h1><blockquote><b>SQL: '.$sql.'</b><br /><br />ERROR: '.$msg.'</blockquote><br /><br />';
		}
		else
		{
			echo  '<h1>SQL/DB Error</h1><blockquote><b>SQL: ';
			print_r($sql);
			echo '</b><br /><br />ERROR: ';
			print_r($msg);
			echo '</blockquote><br /><br />';	
		}
	}
	
	/**
	 * A converter for arrays, used with Mongo DB results
	 *
	 * @private
	 * @param   array  $array
	 * @return  object
	 */
	private function array2obj($array)	
	{
		$obj = new stdClass();
		foreach($array as $k => $v)
		{
			$obj->$k = $v;	
		}
		return $obj;
	}
	
	/**
	 * Runs a query and returns the result.
	 *
	 * @param   mixed  SQL query to execute or array for MongoDB
	 * @param   string  Collection name for Mongo DB
	 * @param   array	fields array for Mongo DB
	 * @param   array	sorting rules for Mongo DB
	 * @return  Database_Result
	 */
	public function query($sql = '', $collection = '', $fields = array(), $sort = array())
	{
		if (empty($sql))
		{
			return FALSE;
		}
		
		// No link? Connect!
		$this->link or $this->connect();
		
		$this->latest_query = $sql;
		$res = array();
		if ($this->sql)
		{
			// MySQL
			try 
			{
				$sth = $this->link->prepare($sql);
				$sth->execute();
				$sth->setFetchMode(PDO::FETCH_OBJ);
				$res = $sth->fetchAll();
				$sth = null;
			}
			catch (PDOException $e) 
			{
				if (DEBUG) 
				{
					$this->print_error($sql, $e->getMessage());
					die;
				}
			}
		}
		else
		{
			// Mongo DB
			try 
			{
				if (isset($sql['distinct']))
				{
					// return an array of distinct values
					$res = $this->link->$collection->distinct($sql['distinct'], $sql['query']);
				}
				else
				{
					// get the cursor
					$res = $this->link->$collection->find($sql, $fields);
					
					if ($res instanceof Traversable)
					{
						if (!empty($sort))
						{
							$res->sort($sort);	
						}
						// return an array
						$res = iterator_to_array($res);
					}
					else
					{
						$res = array();	
					}
				}
			}
			catch (Exception $e)
			{
				if (DEBUG) 
				{
					$this->print_error($sql, $e->getMessage());
					die;
				}
			}
		}
		// query counter
		self::$queries++;
		return $res;
	}
	
	/**
	 * Runs a query and returns the first result row.
	 *
	 * @param   mixed  SQL query to execute or array for MongoDB
	 * @param   string  Collection name for Mongo DB
	 * @param	array	Fields array for Mongo DB 
	 * @param	array	Sorting array for Mongo DB 
	 * @return  Database_Result
	 */
	public function query_row($sql = '', $collection = '', $fields = array(), $sort = array())
	{
		if (empty($sql))
		{
			return FALSE;
		}
		
		// No link? Connect!
		$this->link or $this->connect();
		
		$this->latest_query = $sql;

		if ($this->sql)
		{
			// MySQL
			
			// add limit if needed
			if (strstr($sql, 'LIMIT 0, 1') == '')
			{
				$sql .= ' LIMIT 0, 1';
			}
			
			try 
			{
				$sth = $this->link->prepare($sql);
				$sth->execute();
				$sth->setFetchMode(PDO::FETCH_OBJ);
				$res = $sth->fetch();
				$sth->closeCursor();
			}
			catch (PDOException $e) 
			{
				if (DEBUG) 
				{
					$this->print_error($sql, $e->getMessage());
					die;
				}
			}
		}
		else
		{
			// Mongo
			try 
			{
				if (false && isset($sql['_id']) && sizeof($sql) == 1)
				{
					// is_a a get_by_id
					$res = (object) $this->link->$collection->findOne($sql);
				}
				else
				{
					$res = (empty($fields))
						? $this->link->$collection->find($sql)
						: $this->link->$collection->find($sql, $fields);

					if (!empty($sort))
					{
						$res = $res->sort($sort);
					}
					$res = (object) $res->limit(1)->getNext();
				}
			}
			catch (Exception $e)
			{
				if (DEBUG) 
				{
					$this->print_error($sql, $e->getMessage());
					die;
				}
			}
		}
		// query counter
		self::$queries++;
		return $res;
	}
	
	/**
	 * Runs a query and returns the first value.
	 *
	 * @param   mixed  SQL query to execute or array for MongoDB
	 * @param   string  Collection name for Mongo DB
	 * @param   string  Field name to retrieve for Mongo DB
	 * @param   array	sort array for Mongo DB
	 * @return  Database_Result
	 */
	public function query_var($sql = '', $collection = '', $field = '', $sort = array())
	{
		if ($sql == '') 
		{
			return FALSE;
		}
		$res = '';
		// No link? Connect!
		$this->link or $this->connect();
		
		$this->latest_query = $sql;
		
		if ($this->sql)
		{
			// MySQL
			try 
			{
				$sth = $this->link->prepare($sql);
				$sth->execute();
				$sth->setFetchMode(PDO::FETCH_OBJ);
				$res = $sth->fetchColumn();
				$sth->closeCursor();
			}
			catch (PDOException $e) 
			{
				if (DEBUG) 
				{
					$this->print_error($sql, $e->getMessage());
					die;
				}
			}
		}
		else
		{
			// Mongo
			if (empty($sort))
			{
				$res = $this->link->$collection->findOne($sql, array($field));
			}
			else
			{
				$res = $this->link->$collection->find($sql, array($field));
				if ($res)
				{
					$res = $res->sort($sort);
					$res = $res->limit(1)->getNext();
					$res = $res[$field];
				}
			}
		}
		// query counter
		self::$queries++;
		return $res;
	}
	
	/**
	 * Compiles an exec query and runs it.
	 *
	 * @param   mixed  SQL query to execute or array for MongoDB
	 * @param   string  Collection name for Mongo DB or a string to force the return of the last Inserted ID 
	 * @param   array  Query options for Mongo DB ('fsync' => true, 'timeout' => 10000)
	 * @return  Database_Result  Query result
	 */
	public function single_exec($sql = '', $collection = '', $options = array())
	{
		if (empty($sql))
		{
			return false;
		}
		
		// No link? Connect!
		$this->link or $this->connect();
		
		$this->latest_query = $sql;
		
		if ($this->sql)
		{
			// Relational DB
			try 
			{
				$res = $this->link->exec($sql);
				// to avoid errors with query without ID, like create table
				$result = (empty($collection))
					? array($this->link->lastInsertId(), $res)
					: array(0, $res);
			}
			catch (PDOException $e) 
			{
				if (DEBUG) 
				{
					$this->print_error($sql, $e->getMessage());
					die;
				}
				$result = array(0, 0);
			}
		}
		else
		{
			// Mongo
			try
			{
				$id = '$id';
			
				// sql is an array (
				//	action => (insert|update|delete|modify), 
				//	criteria => (array of criteria for update or remove), 
				//	data => (object or array of data to insert or to update))
				//	fields => (array of fields to return) only for modify
				//	id => boolean (with true uses the Mongo $id, else an autoincrement) 
				switch ($sql['action'])
				{
					case 'insert':
						$res = $this->link->$collection->insert($sql['data']);
						$result_id = (is_object($sql['data']['_id']))
							? $sql['data']['_id']->$id
							: $sql['data']['_id'];
						break;
						
					case 'update':
						$res = $this->link->$collection->update(
							$sql['criteria'], 
							array('$set' => $sql['data'])
						);
						$result_id = (is_object($sql['criteria']['_id']))
							? $sql['criteria']['_id']->$id
							: $sql['criteria']['_id'];
						break;
						
					case 'delete':
						$res = $this->link->$collection->remove($sql['criteria']);
						$result_id = (is_object($sql['criteria']['_id']))
							? $sql['criteria']['_id']->$id
							: $sql['criteria']['_id'];
						break;
						
					case 'multiple_delete':
						$res = $this->link->$collection->remove($sql['criteria']);
						$result_id = 0;
						break;
						
					case 'modify':
						// handle the fields value
						$fields = (empty($sql['fields']))
							? null
							: $sql['fields'];
						
						// options
						$options = array(
							'new' => true
						);
						
						if (!empty($sql['sort']))
						{
							$options['sort'] = $sort;	
						}
						
						// check if data have already a key
						$key = substr(key($sql['data']), 0, 1);
						$data = ($key == '$')
							? $sql['data']
							: array('$set' => $sql['data']);
						
						$res = $this->link->$collection->findAndModify(
							$sql['criteria'], 
							$data, 
							$fields,
							$options
						);
						// return the updated document
						$result_id = $res;
						
						// reassign $res for the error check
						$res = (isset($res['error']) && isset($res['ok']))
							? $res
							: 1;
						break;
						
					case 'count':
						$res = $this->link->$collection->count($sql['criteria']);
						// return the number of items found
						$result_id = $res;
						
						// reassign $res for the error check
						$res = (isset($res['error']) && isset($res['ok']))
							? $res
							: 1;
						break;
						
					case 'drop':
						// drop a collection
						$res = $this->link->$collection->drop();
						$result_id = 0;
						$res = 1;
						break;
						
					case 'list':
						// list of collections
						$res = $this->link->listCollections();
						$result_id = $res;
						$res = 1;
						break;
				}
				$result = array($result_id, 1);
			}
			catch (Exception $e)
			{
				if (DEBUG) 
				{
					$this->print_error($sql, $e->getMessage());
					die;
				}
				$result = array(0, 0);
			}
		}
		// Query counter
		self::$queries++;
		return $result;
	}
	
	/**
	 * Compiles an array of exec query and runs it.
	 *
	 * @param   array of queries  sql
	 * @param   string  Collection name for Mongo DB
	 * @return  Database_Result  Query result
	 */
	public function multi_exec($sql, $collection = '')
	{
		if (empty($sql) || !is_array($sql)) 
		{
			return false;
		}
		
		// No link? Connect!
		$this->link or $this->connect();
		
		if ($this->sql)
		{
			// Relational DB
			try 
			{
				$res = 0;
				$this->link->beginTransaction();
				foreach ($sql as $q) 
				{
					$this->latest_query = $q;
					self::$queries++;
					$res += $this->link->exec($q);
				}
				// to avoid errors with query without ID, like create table
				$last_id = (empty($collection))
					? $this->link->lastInsertId()
					: 0;
				$this->link->commit();
				$result = array($last_id, $res);
			}
			catch (PDOException $e) 
			{
				$this->link->rollback();
				if (DEBUG) 
				{
					$this->print_error($this->latest_query, $e->getMessage());
					die;
				}
				$result = array(0, 0);
			}
		}
		else
		{
			// Mongo
			try
			{
				$insert = array();
				foreach ($sql as $q) 
				{
					if ($q['action'] == 'insert')
					{
						// collect inserts
						$insert[] = $q['data'];
					}
					else
					{
						// update or delete
						$result = $this->single_exec($q, $collection);
						self::$queries++;
					}
				}
	
				if (!empty($insert))
				{
					$this->latest_query = $insert;
					// Collection
					$col = new MongoCollection($this->link, $collection);
					$res = $col->batchInsert($insert);
					
					$result = array(0, 1);
					self::$queries += sizeof($insert);
				}
			}
			catch (Exception $e)
			{
				if (DEBUG)
				{
					$this->print_error($sql, $res);
					die;
				}
				$result = array(0, 0);
			}
		}
		return $result;
	}
	
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
	 * Escapes a value for a query.
	 *
	 * @param   mixed   value to escape
	 * @return  string
	 */
	public function escape($value)
	{
		$value = trim($value); 
		if ($this->sql)
		{
			// Relational DB
			$this->link or $this->connect();
			return $this->link->quote($value);
		}
		else
		{
			// Mongo DB
			return '\''.$value.'\'';
		}
	}
	
	/**
	 * Optimize MySQL tables
	 *
	 * @return Database_Result  Query result
	 */
	public function optimize()
	{
		if ($this->sql)
		{
			// Relational DB
			$tables = $this->query('SHOW TABLES STATUS');
			$sql = array();
			foreach($tables as $i)
			{
				$sql[] = 'OPTIMIZE TABLE '.$i->name;
			}
			return $this->multi_exec($sql);
		}
	}
	
	/**
	 * Get attribute
	 *
	 * @param   string	Attribute name
	 * @return	string
	 */
	public function get_attribute($attr)
	{
		if ($this->sql)
		{
			// Relational DB
			$this->link or $this->connect();
			return $this->link->getAttribute(constant('PDO::ATTR_'.$attr));
		}
	}
}
