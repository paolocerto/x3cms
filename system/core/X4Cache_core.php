<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */
 
/**
* This library is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this software; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* 
* Copyright 2005 Richard Heyes
*/

/**
* Caching Libraries for PHP5
* 
* Handles data and output caching. Defaults to /dev/shm
* (shared memory). All methods are static.
* 
* Eg: (output caching)
* 
* if (!OutputCache::Start('group', 'unique id', 600)) {
* 
*   // ... Output
* 
*   OutputCache::End();
* }
* 
* Eg: (data caching)
* 
* if (!$data = DataCache::Get('group', 'unique id')) {
* 
*   $data = time();
* 
*   DataCache::Put('group', 'unique id', 10, $data);
* }
* 
* echo $data;
*
* ------------------------
* WITH SOME LITTLE CHANGES
* @package		X4WEBAPP
*
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
	* 
	* @static
	* @param string $id    Unique ID of this data
	* @param int    $ttl   How long to cache for (in seconds)
	*/
	protected static function write($id, $ttl, $data)
	{
		$filename = self::getFilename($id);
		file_put_contents($filename, $data, LOCK_EX);
		chmod($filename, 0755);
		// Set filemtime
		touch($filename, time() + $ttl);
	}
	
	/**
	* Reads data
	* 
	* @static
	* @param string $id    Unique ID of this data
	*/
	protected static function read($id)
	{
		$filename = self::getFilename($id);
		return file_get_contents($filename);
	}
	
	/**
	* Determines if an entry is cached
	* 
	* @static
	* @param string $id    Unique ID of this data
	*/
	protected static function isCached($id)
	{
		$filename = self::getFilename($id);
		if (file_exists($filename) && filemtime($filename) > time()) 
			return true;
		
		@unlink($filename);
		return false;
	}
	
	/**
	* Builds a filename/path from group, id and
	* store.
	* 
	* @static
	* @param string $id    Unique ID of this data
	*/
	protected static function getFilename($id)
	{
		$id = md5($id);
		return self::$store . self::$prefix . "_{$id}";
	}
	
	/**
	* Sets the filename prefix to use
	* 
	* @static
	* @param string $prefix Filename Prefix to use
	*/
	public static function setPrefix($prefix)
	{
		self::$prefix = $prefix;
	}

	/**
	* Sets the store for cache files. Defaults to
	* /dev/shm. Must have trailing slash.
	* 
	* @static
	* @param string $store The dir to store the cache data in
	*/
	public static function setStore($store)
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
	* 
	* @static
	* @param  string $id    Unique ID of this data
	* @param  int    $ttl   How long to cache for (in seconds)
	* @return bool          True if cached, false if not
	*/
	public static function Start($id, $ttl)
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
	* Ends caching. Writes data to disk.
	*
	* @static
	* @param  string $data
	* @return void
	*/
	public static function End($data)
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
	* 
	* @static
	* @param  string $id    Unique ID of the data
	* @return mixed         Either the resulting data, or null
	*/
	public static function Get($id)
	{
		if (self::isCached($id)) 
			return unserialize(self::read($id));
		return null;
	}
	
	/**
	* Stores data in the cache
	* 
	* @static
	* @param string $id    Unique ID of the data
	* @param int    $ttl   How long to cache for (in seconds)
	* @param mixed  $data  The data to store
	*/
	public static function Put($id, $ttl, $data)
	{
		self::write($id, $ttl, serialize($data));
	}
}
