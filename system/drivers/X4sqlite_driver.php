<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 	CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */

/**
 * Manage SQLite Db connection and queries
 * Uses PDO
 *
 * @package		X4WEBAPP
 */
final class X4sqlite_driver extends X3db_driver
{
	/**
	 * @var Switcher for NoSQL
	 */
	public $sql = true;
	
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
			$this->dsn = X4Core_core::$db[$name]['db_type'].':'.APATH.X4Core_core::$db[$name]['db_name'];
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
			try 
			{
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
				$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch (PDOException $e) 
			{
				echo '<h1>Error establishing a database connection!</h1>';
				die($e->getMessage());
			}
		}
	}
	
	/**
	 * A primitive debug system
	 *
	 * @param   mixed  SQL query to execute or array for MongoDB
	 * @param   mixed  Error message or error array for MongoDB
	 * @return  string
	 */
	public function print_error($sql = '', $msg = '') 
	{
		// If there is an error then take note of it
		echo  '<h1>SQL/DB Error</h1><blockquote><b>SQL: '.$sql.'</b><br /><br />ERROR: '.$msg.'</blockquote><br /><br />';
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
		// query counter
		self::$queries++;
		return $res;
	}
	
	/**
	 * Compiles an exec query and runs it.
	 *
	 * @param   string  SQL query to execute
	 * @param   string  Collection name a string to force the return of the last Inserted ID
	 * @param   array  Query options
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
		$this->link or $this->connect();
		return $this->link->quote($value);
	}
}
