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
	protected $db_name;

	/**
	 * @var DB object
	 */
	protected $db;

	/**
	 * @var table name
	 */
	protected $table;

	/**
	 * @var logs are disabled as default
	 */
	protected $log = false;

	/**
	 * @var shift used to shift ids of partners on tables with shared data
	 */
	public $shift = 1000000;

	/**
	 * Loads the database instance, if the database is not already loaded.
	 *
	 * @param   string	table name
	 * @param   string	database name
	 * @param   string	alternative database name
	 * @return  void
	 */
	public function __construct(string $table_name, string $db_name = 'default', string $adb = '')
	{
        $this->table = $table_name;
        $this->db_name = $db_name;
        // set the driver
        $driver = 'X4'.X4Core_core::$db[$db_name]['db_type'].'_driver';
        // get or create the driver instance
        $this->db = $driver::instance($db_name);
	}

	/**
	 * Build the form array required to set the parameter
	 * This method have to be overrided with the plugin options
	 *
	 * @param	integer $id_area Area ID
	 * @param	string	$lang Language code
	 * @param   integer $id_page Page ID
	 * @param	string	$param Parameter
	 * @return	array
	 */
	public function configurator(int $id_area, string $lang, int $id_page, string $param)
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
	final public function set_log(bool $status)
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
	final public function get_all(string $table = '', string $fields = '*', array $criteria = [], string $sort = '')
	{
		$t = empty($table)
            ? $this->table
            : $table;

        // Relational DB
        $w = '';
        if (!empty($criteria))
        {
            $c = array();
            foreach ($criteria as $k => $v)
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
	final public function find(string $table = '', string $fields = '*', array $criteria = [], string $sort = '')
	{
		$t = empty($table)
            ? $this->table
            : $table;

		// Relational DB
        $w = '';
        if (!empty($criteria))
        {
            $c = array();
            foreach ($criteria as $k => $v)
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

	/**
	 * Get a row from a table
	 * to prevent the loading of different models to make base calls and to optimize queries you can set table and fields
	 *
	 * @final
	 * @param   integer	record ID
	 * @param   string	table name
	 * @param   string	field list
	 * @return  array
	 */
	final public function get_by_id(int $id, string $table = '', string $fields = '*')
	{
		$t = empty($table)
            ? $this->table
            : $table;

		// Relational DB
		return $this->db->query_row('SELECT '.$fields.' FROM '.$t.' WHERE id = '.$id);
	}

	/**
	 * Get a var from a table
	 * to prevent the loading of different models to make base calls and to optimize queries you can set table and fields
	 *
	 * @final
	 * @param   integer	record ID
	 * @param   string	table name
	 * @param   string	field name
	 * @return  array
	 */
	final public function get_var(int $id, string $table = '', string $field = '')
	{
		$t = empty($table)
            ? $this->table
            : $table;

		// Relational DB
		return $this->db->query_var('SELECT '.$field.' FROM '.$t.' WHERE id = '.$id);
	}

    /**
	 * Get DB version
	 *
	 * @final
	 * @return  string
	 */
	final public function get_version()
	{
		// Relational DB
		return $this->db->query_var('SELECT version()');
	}

	/**
	 * Insert a row in a table
	 * to prevent the loading of different models to make base calls you can set table
	 *
	 * @final
	 * @param   array	data to insert
	 * @param   string	table name
	 * @param   array   array of floats
	 * @return  array	(id row, success)
	 */
	final public function insert(array $data, string $table = '', array $floats = [])
	{
		$t = empty($table)
            ? $this->table
            : $table;

        // Relational DB
        $field = $insert = [];
        foreach ($data as $k => $v)
        {
            $field[] = $k;
            $insert[] = (in_array($k, $floats))
                ? str_replace(',', '.', floatval($v))
                : $this->db->escape($v);
        }

        $sql = (in_array('updated', $field))
            ? 'INSERT INTO '.$t.' ('.implode(',', $field).') VALUES ('.implode(',', $insert).')'
            : 'INSERT INTO '.$t.' (updated, '.implode(',', $field).') VALUES (\''.$this->now().'\', '.implode(',', $insert).')';

        $res = $this->db->single_exec($sql, 'insert');

        if ($this->log && $res[1])
        {
            $uid = $this->get_who();
            $this->logger($uid, $res[0], $t, 'insert');
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
     * @param   array   array of conditions
	 * @param   array   array of floats
     * @param   array   array of fields to concat
	 * @return  array   (id row, success)
	 */
	final public function update(int $id, array $data, string $table = '', array $conditions = [], array $floats = [], array $concat = [])
	{
		$t = empty($table)
            ? $this->table
            : $table;

		// Relational DB
        $update = '';
        foreach ($data as $k => $v)
        {
            if (in_array($k, $floats))
            {
                $update .= ', '.addslashes($k).' = '.str_replace(',', '.', floatval($v));
            }
            elseif (in_array($k, $concat))
            {
                $update .= ', '.addslashes($k).' = CONCAT('.addslashes($k).', '.$v.')';
            }
            else
            {
                $update .= ', '.addslashes($k).' = '.$this->db->escape($v);
            }
        }

        $where = '';
        if (!empty($conditions))
        {
            foreach($conditions as $k => $v)
            {
                $where .= ' AND '.addslashes($k).' '.$v['relation'].' '.$this->db->escape($v['value']);
            }
        }

        $res = $this->db->single_exec('UPDATE '.$t.'
            SET updated = \''.$this->now().'\' '.$update.'
            WHERE id = '.$id.$where, 'update');

        $res = array($id, $res[1]);

        if ($this->log && $res[1])
        {
            $uid = $this->get_who();
            $this->logger($uid, $id, $t, 'update');
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
	 * @param   mixed	Mongo ID switcher
	 * @return  array(rows affected, id row)
	 */
	final public function delete(int $id, string $table = '', mixed $mongo_id = null)
	{
		$t = empty($table)
            ? $this->table
            : $table;

        // Relational DB
        $res = $this->db->single_exec('DELETE FROM '.$t.' WHERE id = '.$id, 'delete');
        $res = array($id, $res[1]);

        if ($this->log && $res[1])
        {
            $uid = $this->get_who();
            $this->logger($uid, $id, $t, 'delete');
        }
        return $res;
	}

	/**
	 * Log an action
	 *
	 * @param   integer	$who        User ID
	 * @param   integer	$id_what    Record ID
	 * @param   string	$what       Table name
	 * @param   string	$action     Action to log
	 * @param   string	$memo       Memo to log
     * @param   integer $xon        Log status
     * @param   integer $related    ID of related log
     * @param   string  $extra      Additional data
	 * @return  array
	 */
    public function get_last_id($table)
	{
		return (int) $this->db->query_var('SELECT id FROM '.$table.' ORDER BY id DESC');
	}

	/**
	 * Log an action
	 *
	 * @param   integer	$who        User ID
	 * @param   integer	$id_what    Record ID
	 * @param   string	$what       Table name
	 * @param   string	$action     Action to log
	 * @param   string	$memo       Memo to log
     * @param   integer $xon        Log status
     * @param   string  $extra      Additional data
	 * @return  array
	 */
    public function logger(int $who, int $id_what, string $what, string $action, string $memo = '', int $xon = 1, string $extra = '')
	{
		// default result
		$res = array(0, 0);

		if (LOGS && $this->db->sql)
		{
			// Relational DB
			$log = (empty($memo))
				? $this->db->escape($this->db->latest_query)
				: $this->db->escape($memo);

			// do not store logs for $id_what == 0
			if (intval($id_what) != 0)
			{
				$table = (
                    strpos($what, 'debug') === false &&
                    strpos($action, 'debug') === false &&
                    $_SESSION['id_area'] > 1
                )
				    ? 'logs'
                    : 'debug';

                // here we don't use insert to avoid loops
                $res = $this->db->single_exec('INSERT INTO '.$table.' (updated, who, what, id_what, action, memo, extra, xon)
                    VALUES (\''.$this->now().'\', '.$who.', '.$this->db->escape($what).', '.$id_what.', '.$this->db->escape($action).', '.$log.','.$this->db->escape($extra).', '.$xon.')');
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
	public function get_attribute(string $attr)
	{
		return $this->db->get_attribute($attr);
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
	public static function & instance(string $name = 'default')
	{
		if (!isset(self::$instances[$name]) && isset(X4Core_core::$db[$name]))
		{
            $driver = 'X4'.X4Core_core::$db[$name]['db_type'].'_driver';
            self::$instances[$name] = new $driver($name);
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
	abstract function print_error(string $sql, string $msg);

	/**
	 * Runs a query and returns the result.
	 *
	 * @param   mixed  SQL query to execute or array for MongoDB
	 * @param   string  Collection name for Mongo DB
	 * @param   array	fields array for Mongo DB
	 * @param   array	sorting rules for Mongo DB
	 * @return  Database_Result
	 */
	abstract public function query(string $sql);     //, string $collection = '', array $fields = [], array $sort = []);

	/**
	 * Runs a query and returns the first result row.
	 *
	 * @param   mixed  SQL query to execute or array for MongoDB
	 * @param   string  Collection name for Mongo DB
	 * @param	array	Fields array for Mongo DB
	 * @param	array	Sorting array for Mongo DB
	 * @return  Database_Result
	 */
	abstract public function query_row(string $sql);     //, string $collection = '', array $fields = [], array $sort = []);

	/**
	 * Runs a query and returns the first value.
	 *
	 * @param   mixed  SQL query to execute or array for MongoDB
	 * @param   string  Collection name for Mongo DB
	 * @param   string  Field name to retrieve for Mongo DB
	 * @param   array	sort array for Mongo DB
	 * @return  Database_Result
	 */
	abstract public function query_var(string $sql);     // , string $collection = '', string $field = '', array $sort = []);

	/**
	 * Compiles an exec query and runs it.
	 *
	 * @param   mixed  SQL query to execute or array for MongoDB
	 * @param   string  Collection name for Mongo DB or a string to force the return of the last Inserted ID
	 * @param   array  Query options for Mongo DB ('fsync' => true, 'timeout' => 10000)
	 * @return  Database_Result  Query result
	 */
	abstract public function single_exec(string $sql);   //, string $collection = '', array $options = []);

	/**
	 * Compiles an array of exec query and runs it.
	 *
	 * @param   array of queries  sql
	 * @param   string  Collection name for Mongo DB
	 * @return  Database_Result  Query result
	 */
	abstract public function multi_exec(array $sql);     //, string $collection = '');

	/**
	 * Escapes a value for a query.
	 *
	 * @param   string  value to escape
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
