<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

/**
 * Model for Context Items
 *
 * @package X3CMS
 */
class Context_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('contexts');
	}
	
	/**
	 * Get context by Context code
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   integer $code Context code
	 * @return  object	Context object
	 */
	public function get_by_code($id_area, $lang, $code)
	{
		return $this->db->query_row('SELECT * 
			FROM contexts 
			WHERE id_area = '.intval($id_area).' AND lang = '.$this->db->escape($lang).' AND code = '.intval($code));
	}
	
	/**
	 * Get contexts by Area ID and Language code
	 * Join with privs table
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @return  array	Array of Context objects
	 */
	public function get_contexts($id_area, $lang)
	{
		return $this->db->query('SELECT c.*, p.level 
				FROM contexts c
				JOIN privs p ON p.id_who = '.intval($_SESSION['xuid']).' AND p.what = \'contexts\' AND p.id_what = c.id AND p.level > 0
				WHERE c.id_area = '.intval($id_area).' AND c.lang = '.$this->db->escape($lang).'
				GROUP BY c.id 
				ORDER BY c.name ASC');
	}
	
	/**
	 * Check if a context already exists
	 *
	 * @param   array	$context Context associative array ('id_area' => value, 'lang' => value, 'xkey' => value, 'description' => value)
	 * @param   integer $id Context ID
	 * @return  integer	the number of contexts with the searched name
	 */
	public function exists($context, $id = 0) 
	{
		$where = ($id == 0) ? '' : ' AND id <> '.intval($id);
		return $this->db->query_var('SELECT COUNT(id) 
			FROM contexts 
			WHERE id_area = '.intval($context['id_area']).' AND lang = '.$this->db->escape($context['lang']).' AND xkey = '.$this->db->escape($context['xkey']).' '.$where);
	}
	
	/**
	 * Get the highest context code relative to an area and a language
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @return  integer	Context code
	 */
	public function get_max_code($id_area, $lang)
	{
		return $this->db->query_var('SELECT MAX(code) FROM contexts WHERE id_area = '.intval($id_area).' AND lang = '.$this->db->escape($lang));
	}
	
	/**
	 * Check if a context name already exists in the admin dictionary
	 * If not then insert it
	 *
	 * @param   array 	$array Associative array ('lang' => 'language code', 'name' => 'context name')
	 * @param   integer	$xon Context status
	 * @return  void
	 */
	public function check_dictionary($array, $xon = 0)
	{
		// prepare post array
		$post = array(
			'area' => 'admin',
			'lang' => $array['lang'],
			'what' => 'articles',
			'xkey' => '_CONTEXT_'.strtoupper($array['name']),
			'xval' => ucfirst($array['name']),
			'xon' => $xon
			);
		
		// check if context exists
		$dict = new Dictionary_model();
		$check = $dict->exists($post);
		
		// insert
		if (!$check) $dict->insert($post);
	}
	
	/**
	 * Get pages for refresh list of pages when change contest
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @return  void
	 */
	public function get_pages($id_area, $lang)
	{
		return $this->db->query('SELECT p.id, LPAD(p.name, CHAR_LENGTH(p.name)+p.deep, \'-\') AS name, pr.level 
				FROM pages p
				JOIN privs pr ON pr.id_who = '.intval($_SESSION['xuid']).' AND pr.what = \'pages\' AND pr.id_what = p.id
				WHERE p.id_area = '.intval($id_area).' AND p.lang = '.$this->db->escape($lang).'
				ORDER BY p.ordinal ASC');
	}

}

/**
 * Empty Context object
 * Necessary for the creation form of new context
 *
 * @package X3CMS
 */
class Context_obj 
{
	public $id_area = 0;
	public $lang = '';
	public $name;
	
	/**
	 * Constructor
	 * Initialize the new context
	 *
		 * @return  void
	 */
	public function __construct($id_area, $lang)
	{
		$this->id_area = $id_area;
		$this->lang = $lang;
	}
}
