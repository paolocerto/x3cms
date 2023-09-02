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
 * Manage MySQL Db connection and queries
 * Uses PDO
 *
 * @package		X4WEBAPP
 */
final class X4mysql_driver extends X3db_driver
{
	/**
	 * @var Switcher for NoSQL
	 */
	public $sql = true;

	/**
	 * Sets up the database configuration
     *
     * @param   string  $name
	 */
	public function __construct(string $name = 'default')
	{
		if (isset(X4Core_core::$db[$name]))
		{
			$this->name = $name;

			// create DSN
			// without or with socket
			$this->dsn = (empty(X4Core_core::$db[$name]['db_socket']))
				? X4Core_core::$db[$name]['db_type'].':host='.X4Core_core::$db[$name]['db_host'].';dbname='.X4Core_core::$db[$name]['db_name']
				: X4Core_core::$db[$name]['db_type'].':unix_socket='.X4Core_core::$db[$name]['db_socket'].';dbname='.X4Core_core::$db[$name]['db_name'];
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
				$this->link = new PDO(
					$this->dsn,
					X4Core_core::$db[$this->name]['db_user'],
					X4Core_core::$db[$this->name]['db_pass'],
					array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '".X4Core_core::$db[$this->name]['charset']."'"));
				$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                //$this->link->setAttribute(PDO::ATTR_AUTOCOMMIT, false);

                // add connection to driver instances
                self::instance($this->name);
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
	 * @param   string  $sql    SQL query to execute
	 * @param   string  $msg    Error message
	 * @return  string
	 */
	public function print_error(string $sql, string $msg)
	{
		// If there is an error then take note of it
		echo  '<h1>SQL/DB Error</h1><blockquote><b>SQL: '.$sql.'</b><br /><br />ERROR: '.$msg.'</blockquote><br /><br />';
	}

	/**
	 * Runs a query and returns the result.
	 *
	 * @param   mixed   $sql    SQL query to execute
	 * @return  Database_Result
	 */
	public function query(string $sql)
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
	 * Runs a query and returns the result.
	 *
	 * @param   mixed   $sql    SQL query to execute
	 * @return  Database_Result
	 */
	public function query_debug(string $sql)
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
            echo '+++'.$sth->queryString.'+++';
            echo '+++'.$sth->debugDumpParams().'+++';
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
	 * @param   string  $sql
	 * @return  Database_Result
	 */
	public function query_row(string $sql)
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

		$res = array(0, 0);
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
	 * @param   string  $sql
	 * @return  Database_Result
	 */
	public function query_var(string $sql)
	{
		if (empty($sql))
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
	 * @param   string  $sql SQL query to execute
	 * @return  Database_Result  Query result
	 */
	public function single_exec(string $sql = '')
	{
		if (empty($sql))
		{
			return false;
		}

		// No link? Connect!
		$this->link || $this->connect();

		$this->latest_query = $sql;

		try
		{
			$res = $this->link->exec($sql);

			// check res
			$res = intval(!($res === false));

			// to avoid errors with query without ID, like create table or truncate table
            $last_id = (substr($sql, 0, 15) == 'TRUNCATE TABLE ')
                ? 1
                : (int) $this->link->lastInsertId();
			$result = array(intval($last_id), $res);
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
	 * @param   array   $sql    Array of queries
	 * @return  Database_Result  Query result
	 */
	public function multi_exec(array $sql)
	{
		if (empty($sql))
		{
			return false;
		}

		// No link? Connect!
		$this->link || $this->connect();
        $this->link->beginTransaction();
		try
		{
			$res = 0;
			foreach ($sql as $q)
			{
				$this->latest_query = $q;
				self::$queries++;
                $tmp = $this->link->exec($q);
				if ($tmp !== false)
                {
                    $res += $tmp;
                }
                elseif (DEBUG)
                {
                    print_r($this->link->errorInfo());
                    die;
                }
			}
			// to avoid errors with query without ID, like create table or truncate table
            $last_id = (strpos($q, 'ATE TABLE ') === false)
                ? 1
                : (int) $this->link->lastInsertId();
            // commit
            // some queries could 
            if ($this->link->inTransaction())
            {
                $this->link->commit();
            }
			$result = array($last_id, $res);
		}
		catch (PDOException $e)
		{
            if ($this->link->inTransaction())
            {
                $this->link->rollback();
            }
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
	 * @param   string   value to escape
	 * @return  string
	 */
	public function escape($value)
	{
        // convert to string
        $value = trim(strval($value));
        $this->link || $this->connect();
        return $this->link->quote($value);
	}

	/**
	 * Optimize MySQL tables
	 * Probably useless with InnoDB tables
	 *
	 * @return Database_Result  Query result
	 */
	public function optimize()
	{
		$tables = $this->query('SHOW TABLES STATUS');
		$sql = array();
		foreach ($tables as $i)
		{
			$sql[] = 'OPTIMIZE TABLE '.$i->name;
		}
		return $this->multi_exec($sql);
	}

	/**
	 * Get attribute
	 *
	 * @param   string	Attribute name
	 * @return	string
	 */
	public function get_attribute(string $attr)
	{
		$this->link or $this->connect();
		return $this->link->getAttribute(constant('PDO::ATTR_'.$attr));
	}
}
