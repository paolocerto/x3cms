<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2016 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */

/**
 * Manage Mongo Db connection and queries
 * Uses Mongo Class
 *
 * @package		X4WEBAPP
 */
final class X4mongo_driver extends X3db_driver
{
	/**
	 * @var Switcher for NoSQL
	 */
	public $sql = false;
	
	/**
	 * Sets up the database configuration
	 *
	 */
	public function __construct($name = 'default')
	{
		if (isset(X4Core_core::$db[$name]))
		{
			$this->name = $name;
			$db_port = (empty(X4Core_core::$db[$this->name]['db_port']))
				? ''
				: ':'.X4Core_core::$db[$this->name]['db_port'];
			$this->dsn = 'mongodb://'.X4Core_core::$db[$this->name]['db_user'].':'.X4Core_core::$db[$this->name]['db_pass'].'@'.X4Core_core::$db[$name]['db_host'].$db_port;	// .'/'.X4Core_core::$db[$name]['db_name']
		}
	}
	
	/**
	 * Simple connect method to get the database queries up and running.
	 *
	 * @return  void
	 */
	public function connect()
	{
		if (!is_object($this->link)) 
		{
			$mc = new Mongo($this->dsn);
			// Select the DB
			$this->link = $mc->selectDB($this->name);
		}
	}
	
	/**
	 * A primitive debug system
	 *
	 * @param   array  SQL array
	 * @param   array  Error messages
	 * @return  string
	 */
	public function print_error($sql = array(), $msg = array()) 
	{
		// If there is an error then take note of it
		echo  '<h1>SQL/DB Error</h1><blockquote><b>SQL: ';
		print_r($sql);
		echo '</b><br /><br />ERROR: ';
		print_r($msg);
		echo '</blockquote><br /><br />';
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
	 * @param   array  SQL query array
	 * @param   string  Collection name
	 * @param   array	fields array
	 * @param   array	sorting rules
	 * @return  Database_Result
	 */
	public function query($sql = array(), $collection = '', $fields = array(), $sort = array())
	{
		if (empty($sql))
		{
			return FALSE;
		}
		
		// No link? Connect!
		$this->link or $this->connect();
		
		$this->latest_query = $sql;
		$res = array();
		
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
		// query counter
		self::$queries++;
		return $res;
	}
	
	/**
	 * Runs a query and returns the first result row.
	 *
	 * @param   array  SQL query to execute
	 * @param   string  Collection name
	 * @param	array	Fields array 
	 * @param	array	Sorting array
	 * @return  Database_Result
	 */
	public function query_row($sql = array(), $collection = '', $fields = array(), $sort = array())
	{
		if (empty($sql))
		{
			return FALSE;
		}
		
		// No link? Connect!
		$this->link or $this->connect();
		
		$this->latest_query = $sql;
		
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
		// query counter
		self::$queries++;
		return $res;
	}
	
	/**
	 * Runs a query and returns the first value.
	 *
	 * @param   array  SQL array query to execute
	 * @param   string  Collection name
	 * @param   string  Field name to retrieve
	 * @param   array	sort array
	 * @return  Database_Result
	 */
	public function query_var($sql = array(), $collection = '', $field = '', $sort = array())
	{
		if ($sql == '') 
		{
			return FALSE;
		}
		$res = '';
		// No link? Connect!
		$this->link or $this->connect();
		
		$this->latest_query = $sql;
		
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
		// query counter
		self::$queries++;
		return $res;
	}
	
	/**
	 * Compiles an exec query and runs it.
	 *
	 * @param   array  SQL query array to execute
	 * @param   string  Collection name
	 * @param   array  Query options ('fsync' => true, 'timeout' => 10000)
	 * @return  Database_Result  Query result
	 */
	public function single_exec($sql = array(), $collection = '', $options = array())
	{
		if (empty($sql))
		{
			return false;
		}
		
		// No link? Connect!
		$this->link or $this->connect();
		
		$this->latest_query = $sql;
		
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
		// Query counter
		self::$queries++;
		return $result;
	}
	
	/**
	 * Compiles an array of exec query and runs it.
	 *
	 * @param   array of queries  sql
	 * @param   string  Collection name
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
		return $result;
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
		return '\''.$value.'\'';
	}
}