<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X4WEBAPP
 */

class Cache
{

	/**
	* Place to store the cache files
	* @var string
	*/
	protected static $store = '';

	/**
	* Prefix to use on cache files
	* @var string
	*/
	protected static $prefix = '';

	/**
	* Stores data
	*/
	protected static function write(string $id_data, int $ttl, string $data) : void
	{
		$filename = self::getFilename($id_data);
		file_put_contents($filename, $data, LOCK_EX);
		chmod($filename, 0755);
		// Set filemtime
		touch($filename, time() + $ttl);
	}

	/**
	* Reads data
	*/
	protected static function read(string $id_data) : string
	{
		$filename = self::getFilename($id_data);
		return file_get_contents($filename);
	}

	/**
	* Determines if an entry is cached
	*/
	protected static function isCached(string $id_data) : bool
	{
		$filename = self::getFilename($id_data);
		if (file_exists($filename) && filemtime($filename) > time())
        {
			return true;
        }
		@unlink($filename);
		return false;
	}

	/**
	* Builds a filename/path from id and stores
	*/
	protected static function getFilename(string $id_data) : string
	{
		$id = md5($id_data);
		return self::$store . self::$prefix . "_{$id}";
	}

	/**
	* Sets the filename prefix to use
	*/
	public static function setPrefix(string $prefix) : void
	{
		self::$prefix = $prefix;
	}

	/**
	* Sets the store path for cache files. Defaults to
	* /dev/shm. Must have trailing slash
	*/
	public static function setStore(string $store) : void
	{
		self::$store = $store;
	}
}

/**
* Output Cache extension of base caching class
* @package		X4WEBAPP
*/
class X4Cache_core extends Cache
{
	/**
	* ID of currently being recorded data
	* @var string
	*/
	private static $id;

	/**
	* Ttl of currently being recorded data
	* @var int
	*/
	private static $ttl;

	/**
	* Starts caching off. Returns true if cached, and dumps
	* the output. False if not cached and start output buffering.
	* $id    Unique ID of this data
	* $ttl   How long to cache for (in seconds)
	*/
	public static function start(string $id, int $ttl) : bool
	{
		if (self::isCached($id))
		{
			echo self::read($id);
			return true;
		}
		else
		{
			self::$id    = $id;
			self::$ttl   = $ttl;
			return false;
		}
	}

	/**
	* Ends caching. Writes data to disk
	*/
	public static function end(string $data) : void
	{
		self::write(self::$id, self::$ttl, $data);
	}
}

/**
* Data cache extension of base caching class
* @package		X4WEBAPP
*/
class DataCache extends Cache
{
	/**
	* Retrieves data from the cache
	*/
	public static function get(string $id) : mixed
	{
		if (self::isCached($id))
        {
			return unserialize(self::read($id));
        }
		return null;
	}

	/**
	* Stores data in the cache
	*/
	public static function put(string $id, int $ttl, string $data) : void
	{
		self::write($id, $ttl, serialize($data));
	}
}
