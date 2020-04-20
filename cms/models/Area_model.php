<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

/**
 * Model for Area Items
 *
 * @package X3CMS
 */
class Area_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('areas');
	}
	
	/**
	 * Get area data and predefined language code
	 * Join with alang
	 *
	 * @param   integer $id_area Area ID
	 * @return  object
	 */
	public function get_area_data($id_area)
	{
		return $this->db->query_row('SELECT a.*, l.code 
			FROM areas a 
			JOIN alang l ON a.id = l.id_area AND l.predefined = 1 
			WHERE a.id = '.intval($id_area));
	}
	
	/**
	 * Get area ID by area name
	 *
	 * @param   string	$name Area name
	 * @return  integer	Area ID
	 */
	public function get_area_id($name)
	{
		return $this->db->query_var('SELECT id FROM areas WHERE name = '.$this->db->escape($name));
	}
	
	/**
	 * Get areas data as an array
	 * Join with privs table
	 *
	 * @param   integer $id_area Area ID
	 * @return  array	array of area objects
	 */
	public function get_areas($id_area = 1) 
	{
		// condition
		$where = ($id_area == 1) 
			? '' 
			: ' AND a.id = '.intval($id_area);
		
        $sql = 'SELECT a.*, u.level
            FROM areas a
			JOIN aprivs p ON p.id_area = a.id
			JOIN uprivs u ON u.id_area = a.id AND u.id_user = p.id_user AND u.privtype = '.$this->db->escape('areas').'
			WHERE u.id_user = '.intval($_SESSION['xuid']).' '.$where.'
			GROUP BY a.id
            ORDER BY a.id ASC';
		
		return $this->db->query($sql);
	}
	
	/**
	 * Get areas data as an array
	 * Join with privs table
	 *
	 * @param   integer $id_area Area ID
	 * @return  array	array
	 */
	public function get_my_areas($id_area = 0) 
	{
	    $items = X4Array_helper::indicize($this->get_areas(), 'id');
		
	    // check user group
	    $id_group = $this->get_var($_SESSION['xuid'], 'users', 'id_group');
	    
	    if ($id_group > 1)
	    {
	        unset($items[1]);
	    }
	    
	    if ($id_area && !isset($items[$id_area]))
	    {
	        reset($items);
	        $id_area = key($items);
	    }
	    
	    return array($id_area, $items);
	}
	
	/**
	 * Create a tmp file where store extra areas data
	 *
	 * @return  void
	 */
	public function extra_areas() 
	{
		$items = $this->db->query('SELECT id, name, private FROM areas WHERE id > 3  AND xon = 1 ORDER BY id ASC');
		
		$a = array();
		foreach($items as $i)
		{
		    $a[$i->name] = ($i->private) 
		        ? 'private'
		        : 'public';
		    
		    $a[$i->name.'_id'] = $i->id;
		}
		
		$file = serialize($a);
		file_put_contents(APATH.'files/'.SFOLDER.'/'.SFOLDER.'.txt', $file);
	}
	
	/**
	 * Get user's permissions over areas
	 * use aprivs table
	 *
	 * @return  array	array of objects
	 */
	public function get_aprivs_id() 
	{
		$sql = 'SELECT id_area, area
			FROM aprivs 
			WHERE id_user = '.intval($_SESSION['xuid']).'
			ORDER BY id ASC';
			
		return $this->db->query($sql);
	}
	
	/**
	 * Get the default template of the area
	 * Use templates table
	 * Join with themes and areas
	 *
	 * @param   integer $id_area Area ID
	 * @return  array	array of objects
	 */
	public function get_default_template($id_area) 
	{
		$sql = 'SELECT t.id
			FROM templates t
			JOIN themes th ON th.id = t.id_theme 
			JOIN areas a ON a.id_theme = th.id AND a.id = '.intval($id_area).'
			WHERE t.xon = 1
			ORDER BY t.id ASC';
			
		return $this->db->query_var($sql);
	}
	
	/**
	 * Get controller's folders
	 * This information is needed when create a new area
	 *
	 * @return  array	array of objects
	 */
	public function get_folders() 
	{
		// get all controller's folders
		$folders = glob(APATH.'/controllers/*', GLOB_ONLYDIR);
		
		$a = array();
		foreach($folders as $i)
		{
			if(function_exists('preg_replace_callback'))
			{
				$tmp = preg_replace_callback('/(.*)\/(.*)/is', 
					function($m)
					{
						return $m[2];
					}, 
					$i);
			}
			else
			{
				$tmp = preg_replace('/(.*)\/(.*)/is', '$2', $i, 1);
			}
			// exclude admin folder and x3cli
			if ($tmp != 'admin' && $tmp != 'x3cli') 
			{
				$a[] = new Obj_folder($tmp);
			}
		}
		return $a;
	}
	
	/**
	 * Check if an area name already exists
	 *
	 * @param   string	$area Area name to check
	 * @param   integer $id Area ID
	 * @return  integer	the number of areas with the searched name
	 */
	public function exists($area, $id = 0) 
	{
		// condition
		$where = ($id) 
			? ' AND id <> '.intval($id)		// if is an update
			: '';
		
		$sql = 'SELECT COUNT(id) 
			FROM areas 
			WHERE name = '.$this->db->escape($area).' '.$where;
			
		return $this->db->query_var($sql);
	}
	
	/**
	 * Delete area
	 *
	 * @param   integer $id Area ID
	 * @param   string 	$name Area name
	 * @return  array
	 */
	public function delete_area($id, $name) 
	{
		$sql = array();
		$id = intval($id);
		
		// alang
		$sql[] = 'DELETE FROM alang WHERE id_area = '.$id;
		// aprivs
		$sql[] = 'DELETE FROM aprivs WHERE id_area = '.$id;
		// articles
		$sql[] = 'DELETE FROM articles WHERE id_area = '.$id;
		// categories
		$sql[] = 'DELETE FROM categories WHERE id_area = '.$id;
		// contexts
		$sql[] = 'DELETE FROM contexts WHERE id_area = '.$id;
		// dictionary
		$sql[] = 'DELETE FROM dictionary WHERE area = '.$this->db->escape($name);
		// files
		$sql[] = 'DELETE FROM files WHERE id_area = '.$id;
		// groups
		$sql[] = 'DELETE FROM groups WHERE id_area = '.$id;
		// modules
		$sql[] = 'DELETE FROM modules WHERE id_area = '.$id;
		// pages
		$sql[] = 'DELETE FROM pages WHERE id_area = '.$id;
		// param
		$sql[] = 'DELETE FROM param WHERE id_area = '.$id;
		// privs
		$sql[] = 'DELETE FROM privs WHERE id_area = '.$id;
		// sections
		$sql[] = 'DELETE FROM sections WHERE id_area = '.$id;
		// uprivs
		$sql[] = 'DELETE FROM uprivs WHERE id_area = '.$id;
		// users (with groups)
		// TO DO
		// widgets
		$sql[] = 'DELETE FROM widgets WHERE id_area = '.$id;
		
		// areas
		$sql[] = 'DELETE FROM areas WHERE id = '.$id;
		
		return $this->db->multi_exec($sql);
	}
	
	/**
	 * Rename area
	 *
	 * @param   integer $id Area ID
	 * @param   string 	$old Old Area name
	 * @param   string 	$new New Area name
	 * @return  array
	 */
	public function rename_area($id_area, $old, $new) 
	{
		$sql = array();
		$id = intval($id_area);
		
		// aprivs
		$sql[] = 'UPDATE aprivs SET area = '.$this->db->escape($new).' WHERE id_area = '.$id;
		
		// areas
		$sql[] = 'UPDATE areas SET name = '.$this->db->escape($new).' WHERE id = '.$id;
		
		// dictionary
		$sql[] = 'UPDATE dictionary SET area = '.$this->db->escape($new).' WHERE area = '.$this->db->escape($old);
		
		return $this->db->multi_exec($sql);
	}
	
}

/**
 * Empty Area object
 * Necessary for the creation form of new area
 *
 * @package X3CMS
 */
class Area_obj 
{
	// object vars
	public $name;
	public $title;
	public $description;
	public $lang;
	public $code;
	public $old_id_theme = 0;
	public $id_theme = 0;
	public $private = 0;
	public $folder;
}

/**
 * Folder object
 * Necessary for the creation form of new area
 *
 * @package X3CMS
 */
class Obj_folder 
{
	// folder name
	public $folder;
	
	/**
	 * Constructor
	 * set the folder name
	 *
	 * @param   string $folder folder name
	 * @return  void
	 */
	public function __construct($folder)
	{
		$this->folder = $folder;
	}
}
