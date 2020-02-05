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
 * Model for Dictionary Items
 *
 * @package X3CMS
 */
class Dictionary_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('dictionary');
	}
	
	/**
	 * Get dictionary keys by area name and language code
	 *
	 * @param   string	$lang Language code
	 * @param   string	$area Area name
	 * @return  array	array of objects
	 */
	public function get_keys($lang, $area)
	{
		return $this->db->query('SELECT DISTINCT what 
				FROM dictionary 
				WHERE lang = '.$this->db->escape($lang).' AND area = '.$this->db->escape($area).'
				ORDER BY what ASC');
	}
	
	/**
	 * Get areas by language code
	 *
	 * @param   string	$lang Language code
	 * @return  array	array of objects
	 */
	public function get_areas($lang)
	{
		return $this->db->query('SELECT DISTINCT area FROM dictionary WHERE lang = '.$this->db->escape($lang).' ORDER BY area ASC');
	}
	
	/**
	 * Get dictionary words by area name, language code and key
	 * Join with privs table
	 *
	 * @param   string	$lang Language code
	 * @param   string	$area Area name
	 * @param   string	$what Dictionary section
	 * @return  array	array of objects
	 */
	public function get_words($lang, $area, $what)
	{
		return $this->db->query('SELECT d.*, IF(p.id IS NULL, u.level, p.level) AS level
			FROM dictionary d
			JOIN areas a ON a.name = d.area
			JOIN uprivs u ON u.id_area = a.id AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('dictionary').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = d.id
			WHERE 
				d.lang = '.$this->db->escape($lang).' AND 
				d.area = '.$this->db->escape($area).' AND 
				d.what = '.$this->db->escape($what).'
			GROUP BY d.xkey
			ORDER BY d.xkey ASC');
	}
	
	/**
	 * Get dictionary words by area name, language code and key avoiding duplicates
	 * Join with privs table
	 *
	 * @param   string	$lang Language code
	 * @param   string	$area Area name
	 * @param   string	$what Dictionary section
	 * @param   string	$new_lang Language code
	 * @param   string	$new_area Area name
	 * @return  array	array of objects
	 */
	public function get_words_to_import($lang, $area, $what, $new_lang, $new_area)
	{
		return $this->db->query('SELECT DISTINCT d.*, IF(p.id IS NULL, u.level, p.level) AS level
			FROM dictionary d
			LEFT JOIN dictionary d2 ON (
				d2.what = d.what AND 
				d2.xkey = d.xkey AND
				d2.lang = '.$this->db->escape($new_lang).' AND 
				d2.area = '.$this->db->escape($new_area).'
			)
			JOIN areas a ON a.name = d.area
			JOIN uprivs u ON u.id_area = a.id AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('dictionary').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = d.id
			WHERE 
				d.lang = '.$this->db->escape($lang).' AND 
				d.area = '.$this->db->escape($area).' AND 
				d.what = '.$this->db->escape($what).' AND 
				d2.id IS NULL
			GROUP BY d.id
			ORDER BY d.xkey ASC');
	}
	
	/**
	 * Get dictionary sections 
	 * A section consists of the triad of values language_code-area_name-key_value
	 *
	 * @return  array	array of objects
	 */
	public function get_section_options()
	{
		// sections
		$sections = $this->db->query('SELECT DISTINCT CONCAT(lang, \'-\', area, \'-\', what) AS lang_what 
			FROM dictionary 
			WHERE xon = 1 
			ORDER BY lang_what ASC');
		
		// to add ALL option
		
		// areas
		$areas = $this->db->query('SELECT DISTINCT CONCAT(lang, \'-\', area, \'-ALL\') AS lang_what 
			FROM dictionary 
			WHERE xon = 1 
			ORDER BY lang_what ASC');
		
		// merge
		$res = array_merge($sections, $areas);
		
		// to array
		$res = X4Array_helper::obj2array($res, null, 'lang_what');
		sort($res);
		
		// to obj
		return  X4Array_helper::array2obj($res);
	}
	
	/**
	 * Get dictionary sections 
	 * A section consists of the triad of values language_code-area_name-key_value
	 *
	 * @param   string	$lang Language code
	 * @param   string	$area Area name
	 * @return  array	array of objects
	 */
	public function get_sections($lang, $area)
	{
		// sections
		return $this->db->query('SELECT what 
			FROM dictionary 
			WHERE 
				area = '.$this->db->escape($area).' AND 
				lang = '.$this->db->escape($lang).' AND
				xon = 1 
			ORDER BY what ASC');
	}
	
	/**
	 * Check if an dictionary word already exists
	 *
	 * @param   array	$_post Associative array ('area' => value, 'lang' => value, 'what' =>, 'key' => )
	 * @return  integer	the number of words with the searched values
	 */
	public function exists($post) 
	{
		return $this->db->query_var('SELECT COUNT(id) 
			FROM dictionary 
			WHERE 
				area = '.$this->db->escape($post['area']).' AND 
				lang = '.$this->db->escape($post['lang']).' AND 
				what = '.$this->db->escape($post['what']).' AND 
				xkey = '.$this->db->escape($post['xkey']));
	}
	
	/**
	 * Search dictionary words by area name, and string
	 * Join with privs table
	 *
	 * @param   string	$lang Language code
	 * @param   string	$area Area name
	 * @return  array	array of objects
	 */
	public function search_words($area, $str)
	{
		return $this->db->query('SELECT d.*, IF(p.id IS NULL, u.level, p.level) AS level
			FROM dictionary d
			JOIN areas a ON a.name = d.area
			JOIN uprivs u ON u.id_area = a.id AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('dictionary').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = d.id
			WHERE 
				d.area = '.$this->db->escape($area).' AND 
				d.xkey LIKE '.$this->db->escape('%'.strtoupper($str).'%').'
			GROUP BY d.id
			ORDER BY d.xkey ASC, d.lang ASC');
	}
	
}
