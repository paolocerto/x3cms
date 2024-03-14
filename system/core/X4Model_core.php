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
    protected $db_name;

    /**
     * Db object
     */
	protected $db;

	protected $table;

	/**
	 * Logs are disabled as default
	 */
	protected $log = false;

	/**
	 * Shift is used to shift IDs on
	 */
	public $shift = 1000000;

	/**
	 * Loads the database instance, if the database is not already loaded
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
	 * Build the form array required to set the parameter for plugins
	 * This method have to be overrided with the plugin options
	 */
	public function configurator(int $id_area, string $lang, int $id_page, string $param) : array
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
	 */
	final public function close() : void
	{
		$this->db->close();
	}

	/**
	 * Log setter
	 */
	final public function set_log(bool $status) : void
	{
		$this->log = $status;
	}

	/**
	 * Replacement for MySQL NOW() function to use PHP timezone
	 */
	final public function now() : string
	{
		return date('Y-m-d H:i:s');
	}

	/**
	 * Replacement for MySQL UNIXTIMESTAMP function to use PHP timezone
	 */
	final public function time() : int
	{
		return time();
	}

	/**
	 * Get last query
	 */
	final public function last_query() : string
	{
		return $this->db->last_query();
	}

	/**
	 * Get all rows from a table
	 * to prevent the loading of different models to make base calls and to optimize queries you can set table and fields
	 */
	final public function get_all(string $table = '', string $fields = '*', array $criteria = [], string $sort = '') : array
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
	 */
	final public function find(string $table = '', string $fields = '*', array $criteria = [], string $sort = '') : mixed
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
	 */
	final public function get_by_id(int $id, string $table = '', string $fields = '*') : stdClass
	{
		$t = empty($table)
            ? $this->table
            : $table;

		return $this->db->query_row('SELECT '.$fields.' FROM '.$t.' WHERE id = '.$id);
	}

	/**
	 * Get a var from a table
	 * to prevent the loading of different models to make base calls and to optimize queries you can set table and fields
	 */
	final public function get_var(int $id, string $table = '', string $field = '') : mixed
	{
		$t = empty($table)
            ? $this->table
            : $table;

		return $this->db->query_var('SELECT '.$field.' FROM '.$t.' WHERE id = '.$id);
	}

    /**
	 * Get DB version
	 */
	final public function get_version() : string
	{
		return $this->db->query_var('SELECT version()');
	}

	/**
	 * Insert a row in a table
	 * to prevent the loading of different models to make base calls you can set table
	 * Return array(id row, success)
	 */
	final public function insert(array $data, string $table = '', array $floats = []) : array
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
	 * Get user ID for logger
	 */
	private function get_who() : int
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
	 * Return array(id row, success)
	 */
	final public function update(
        int $id,
        array $data,
        string $table = '',
        array $conditions = [],
        array $floats = [],
        array $concat = []
    ) : array
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
	 * Return array(rows affected, id row)
	 */
	final public function delete(int $id, string $table = '') : array
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
	 * Get last ID in a table
	 */
    public function get_last_id(string $table) : int
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
    public function logger(
        int $who,
        int $id_what,
        string $what,
        string $action,
        string $memo = '',
        int $xon = 1,
        string $extra = ''
    ) : array
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
                $res = $this->db->single_exec('INSERT INTO '.$table.'
                        (updated, who, what, id_what, action, memo, extra, xon)
                    VALUES (
                        \''.$this->now().'\',
                        '.$who.',
                        '.$this->db->escape($what).',
                        '.$id_what.',
                        '.$this->db->escape($action).',
                        '.$log.',
                        '.$this->db->escape($extra).',
                        '.$xon.')');
			}
		}
		return $res;
	}

	/**
	 * Get attribute
	 */
	public function get_attribute(string $attr) : string
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
	 * Returns a singleton instance of Database
	 */
	public static function & instance(string $name = 'default') : X3db_driver
	{
		if (!isset(self::$instances[$name]) && isset(X4Core_core::$db[$name]))
		{
            $driver = 'X4'.X4Core_core::$db[$name]['db_type'].'_driver';
            self::$instances[$name] = new $driver($name);
		}
		return self::$instances[$name];
	}

	/**
	 * Simple connect method to get the database queries up and running
	 */
	abstract function connect();

	/**
	 * A primitive debug system
	 */
	abstract function print_error(string $sql, string $msg);

	/**
	 * Runs a query and returns the result
	 */
	abstract public function query(string $sql);

	/**
	 * Runs a query and returns the first result row
	 */
	abstract public function query_row(string $sql);

	/**
	 * Runs a query and returns the first value
	 */
	abstract public function query_var(string $sql);

	/**
	 * Compiles an exec query and runs it
	 */
	abstract public function single_exec(string $sql);

	/**
	 * Compiles an array of exec query and runs it
	 */
	abstract public function multi_exec(array $sql);

	/**
	 * Escapes a value for a query
	 */
	abstract public function escape(string $value);

	/**
	 * Returns the last query run.
	 */
	public function last_query() : string
	{
	   return $this->latest_query;
	}

	/**
	 * close db connection
	 */
	public function close()
	{
		$this->link = null;
	}
}
