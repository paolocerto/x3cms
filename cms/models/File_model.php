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
 * Model for File Items
 *
 * @package X3CMS
 */
class File_model extends X4Model_core
{
	/**
	 * Absolute path to files managed
	 */
	public $file_path = '';
	
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('files');
		
		$this->file_path = APATH.'files/filemanager/';
	}
	
	/**
	 * Get file categories by Area ID
	 * Used for autocompletion
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$value A piece of category name
	 * @return  array	Array of objects
	 */
	public function get_cat($id_area, $value = '')
	{
		// condition
		$like = (empty($value)) 
			? '' 
			: ' AND category LIKE '.$this->db->escape($value.'%');
			
		return $this->db->query('SELECT DISTINCT category AS ctg FROM files WHERE id_area = '.intval($id_area).$like.' ORDER BY category ASC');
	}
	
	/**
	 * Get file subcategories by Area ID
	 * Used for autocompletion
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$category Category name
	 * @param   string	$value A piece of subcategory name
	 * @return  array	Array of objects
	 */
	public function get_subcat($id_area, $category = '', $value = '')
	{
		// conditions
		$where = (empty($category) || $category == '-') 
			? '' 
			: ' AND category = '.$this->db->escape($category);
		
		$where .= (empty($value)) 
			? '' 
			: ' AND subcategory LIKE '.$this->db->escape($value.'%');
			
		return $this->db->query('SELECT DISTINCT subcategory AS sctg FROM files WHERE id_area = '.intval($id_area).$where.' ORDER BY subcategory ASC');
	}
	
	/**
	 * Get areas
	 * Join with privs table
	 *
	 * @return  array	Array of objects
	 */
	public function get_areas()
	{
		return $this->db->query('SELECT a.id, a.title, a.description, IF(p.id IS NULL, u.level, p.level) AS level
			FROM areas a
			JOIN uprivs u ON u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('areas').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = a.id
			ORDER BY a.title ASC');
	}
	
	/**
	 * Get tree of areas, categories and subcategories
	 * Use areas table and join with files and privs table
	 *
	 * @return  array	Array of objects
	 */
	public function get_tree()
	{
		return $this->db->query('SELECT a.id, a.title, a.description, f.category, f.subcategory, IF(p.id IS NULL, u.level, p.level) AS level
			FROM areas a
			JOIN files f ON f.id_area = a.id
			JOIN uprivs u ON u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('files').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = f.id
			GROUP BY a.id, f.category, f.subcategory 
			ORDER BY a.title ASC, f.category ASC, f.subcategory ASC');
	}
	
	/**
	 * Get file types
	 *
	 * @return  array	Array of objects
	 */
	public function get_types()
	{
		$a = array(
			array('name' => _ALL_FILES, 'value' => -1), 
			array('name' => _DOCUMENTS, 'value' => 1), 
			array('name' => _IMAGES, 'value' => 0),
			array('name' => _MEDIA, 'value' => 2),
			array('name' => _TEMPLATES, 'value' => 3)
		);
		
		return X4Utils_helper::array2obj($a, 'value', 'name');
	}
	
	/**
	 * Get categories by Area ID
	 *
	 * @param   integer $id_area Area ID
	 * @return  array	Array of objects
	 */
	public function get_categories($id_area)
	{
		return $this->db->query('SELECT DISTINCT category FROM files WHERE id_area = '.intval($id_area).' ORDER BY category ASC');
	}
	
	/**
	 * Get subcategories by Area ID and category name
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$category Category name
	 * @return  array	Array of objects
	 */
	public function get_subcategories($id_area, $category)
	{
		return $this->db->query('SELECT DISTINCT subcategory FROM files WHERE id_area = '.intval($id_area).' AND category = '.$this->db->escape($category).' ORDER BY subcategory ASC');
	}
		
	/**
	 * Get files by Area ID, Category and Subcategory
	 * Join with privs table
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$category Category
	 * @param   string	$subcategory Subcategory
	 * @param   integer $xtype file type (0 => image, 1 = generic, 2 => media, 3 => template)
	 * @param   string	$str Search string
	 * @return  array	Array of objects
	 */
	public function get_files($id_area, $category = '', $subcategory = '', $xtype = -1, $str = '')
	{
		// category condition
		$where = (empty($category) || $category == '-') 
			? '' 
			: ' AND f.category = '.$this->db->escape($category);
		
		// subcategory condition
		$where .= (empty($subcategory) || $subcategory == '-') 
			? '' 
			: ' AND f.subcategory = '.$this->db->escape($subcategory);
			
		// xtype condition
		$where .= ($xtype < 0)
			? ''
			: ' AND f.xtype = '.intval($xtype);
			
		if (!empty($str))
		{
			$w = array();
			$tok = explode(' ', urldecode($str));
			foreach($tok as $i)
			{
				$a = trim($i);
				if (!empty($a))
					$w[] = 'name LIKE '.$this->db->escape('%'.$a.'%').' OR 
						alt LIKE '.$this->db->escape('%'.$a.'%');
				
			}
			
			if (!empty($w))
				$where .= ' AND ('.implode(') AND (', $w).')';
		}
		
		return $this->db->query('SELECT f.*, IF(p.id IS NULL, u.level, p.level) AS level
			FROM files f 
			JOIN uprivs u ON u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('files').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = f.id
			WHERE f.id_area = '.intval($id_area).$where.' 
			GROUP BY f.id
			ORDER BY f.name ASC');
	}
	
	/**
	 * Add record to files table
	 * Multiple insert
	 *
	 * @param   array	$array Array of files data
	 * @return  array
	 */
	public function insert_file($array)
	{
		// build queries
		$sql = array();
		foreach($array as $i) {
			$field = $insert = '';
			foreach($i as $k => $v) {
				$field .= ', '.$k;
				$insert .= ', '.$this->db->escape($v);
			}
			$sql[] = 'INSERT INTO files (updated '.$field.') VALUES (NOW() '.$insert.')';
		}
		return $this->db->multi_exec($sql); 
	}
	
	/**
	 * Delete file
	 *
	 * @param   integer	$id File ID
	 * @return  array
	 */
	public function delete_file($id)
	{
		// folders
		$what = array('img', 'files', 'media', 'template');
		
		// get object 
		$file = $this->get_by_id($id);
		
		// delete record
		$result = $this->db->single_exec('DELETE FROM files WHERE id = '.intval($id));
		
		// delete file
		if ($result[1] && file_exists($this->file_path.$what[$file->xtype].'/'.$file->name))
		{
			// check if the same file is registered in other areas
			$check = $this->in_other_area($file->name);
			
			// delete the file
			if ($check == 0) {
				chmod($this->file_path.$what[$file->xtype].'/'.$file->name, 0766);
				unlink($this->file_path.$what[$file->xtype].'/'.$file->name);
			}
		}
		return $result;
	}
	
	/**
	 * Check if a file is registered in files table
	 *
	 * @param   string	$filename File name
	 * @return  integer	Number of files found
	 */
	private function in_other_area($filename)
	{
		return $this->db->query_var('SELECT COUNT(id) FROM files WHERE name = '.$this->db->escape($filename));
	}
	
	/**
	 * Get js lists
	 * Used in TinyMCE 
	 *
	 * @param 	integer	$id_area	Area ID
	 * @param   string	$type		Type of files
	 * @return void
	 */
	public function get_js_list($id_area, $type) 
	{
		// type are also folders
		$what = array(
			'img' => 0, 
			'files' => 1, 
			'media' => 2, 
			'template' => 3
		);
		
		// get files
		$files = $this->get_files($id_area, '', '', $what[$type]);
		
		$c = 0;
		$txt = '';
		foreach ($files as $i) 
		{
			if ($c > 0) 
			{
				$txt .= ',';
			}
			
			if ($type == 'template')
			{
				$txt .= NL.'{"title": "'.addslashes(str_replace('\'', '&quote;', $i->alt)).'", "url": "'.FPATH.$type.'/'.$i->name.'", "description": "'.$i->alt.'"}';
			}
			else
			{
				$txt .= NL.'{"title": "'.addslashes(str_replace('\'', '&quote;', $i->alt)).'", "value": "'.FPATH.$type.'/'.$i->name.'"}';
			}
			$c++;
		}
		return '['.$txt.']';
	}
}
