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
 * Model for Category Items
 *
 * @package X3CMS
 */
class Category_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('categories');
	}
	
	/**
	 * Get categories by Area ID and Language code
	 * Join with privs table
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string 	$tag Category tag
	 * @return  array	array of category objects
	 */
	public function get_categories($id_area, $lang, $tag = '')
	{
	    $where = (empty($tag))
	        ? ''
	        : ' AND c.tag = '.$this->db->escape($tag);
	        
		return $this->db->query('SELECT c.*, p.level 
			FROM categories c 
			JOIN privs p ON p.id_who = '.intval($_SESSION['xuid']).' AND p.what = \'categories\' AND p.id_what = c.id
			WHERE c.id_area = '.intval($id_area).' AND c.lang = '.$this->db->escape($lang).$where.'
			ORDER BY c.name ASC');
	}
	
	/**
	 * Get categories tags by Area ID and Language code
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @return  array	array of category objects
	 */
	public function get_tags($id_area, $lang)
	{
		return $this->db->query('SELECT c.tag 
			FROM categories c 
			WHERE c.id_area = '.intval($id_area).' AND c.lang = '.$this->db->escape($lang).'
			GROUP BY c.tag
			ORDER BY c.tag ASC');
	}
	
	/**
	 * Check if a category already exists
	 *
	 * @param   array	$ctg Category associative array ('id_area' => value, 'lang' => value, 'name' => value, 'description' => value)
	 * @param   integer $id Category ID
	 * @return  integer	the number of categories with the searched name
	 */
	public function exists($ctg, $id = 0) 
	{
		$where = ($id == 0) ? '' : ' AND id <> '.intval($id);
		return $this->db->query_var('SELECT COUNT(id) 
			FROM categories 
			WHERE id_area = '.intval($ctg['id_area']).' AND lang = '.$this->db->escape($ctg['lang']).' AND name = '.$this->db->escape($ctg['name']).' '.$where);
	}
	
}

/**
 * Empty Category object
 * Necessary for the creation form of new categories
 *
 * @package X3CMS
 */
class Category_obj 
{
	public $id_area;
	public $lang;
	public $name;
	public $title;
	public $tag;
	
	/**
	 * Constructor
	 * Initialize the new category
	 *
	 * @return  void
	 */
	public function __construct($id_area, $lang, $tag)
	{
		$this->id_area = $id_area;
		$this->lang = $lang;
		$this->tag = $tag;
	}
}
