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
 * Model for Language Items
 *
 * @package X3CMS
 */
class Language_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('languages');
	}
	
	/**
	 * Get languages
	 * Join with privs table
	 *
	 * @param   integer $xon Language status
	 * @return  array	array of objects
	 */
	public function get_languages($xon = 2)
	{
		// condition
		$where = ($xon < 2) 
			? ' WHERE l.xon = '.$xon 
			: '';
			
		return $this->db->query('SELECT l.*, IF(p.id IS NULL, u.level, p.level) AS level
				FROM languages l
				JOIN uprivs u ON u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('languages').'
				LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = l.id AND p.level > 0
				'.$where.'
				ORDER BY l.language ASC');
	}
	
	/**
	 * Check if a language already exists
	 *
	 * @param   array	$array associative array with Language data ('language' =>, 'code' =>)
	 * @param   integer $id Language ID
	 * @return  integer	the number of languages found
	 */
	public function exists($array, $id = 0) 
	{
		// condition
		$where = ($id == 0) 
			? '' 
			: ' AND id <> '.intval($id);
			
		return $this->db->query_var('SELECT COUNT(id) 
			FROM languages 
			WHERE (language = '.$this->db->escape($array['language']).' OR code = '.$this->db->escape($array['code']).') '.$where);
	}
	
	/**
	 * Delete language
	 *
	 * @param   integer $id Language ID
	 * @return  integer	the number of languages found
	 */
	public function delete_lang($id)
	{
		// get object
		$lang = $this->get_by_id($id);
		
		// build queries
		$sql = array();
		
		// clear privs table
		$sql[] = 'DELETE p.* FROM privs p JOIN dictionary d ON d.id = p.id_what AND d.lang = '.$this->db->escape($lang->code).' WHERE p.what = \'dictionary\'';
			
		// clear dictionary table
		$sql[] = 'DELETE FROM dictionary WHERE lang = '.$this->db->escape($lang->code);
		
		// clear language table 
		$sql[] = 'DELETE FROM languages WHERE id = '.intval($id);
		
		return $this->db->multi_exec($sql);
	}
	
	/**
	 * Get language codes of an area by ID
	 * Use alang table
	 *
	 * @param   integer $id_area Area ID
	 * @return  array	Array of strings
	 */
	public function get_alang_array($id_area)
	{
		$a = $this->db->query('SELECT code FROM alang WHERE id_area = '.intval($id_area).' ORDER BY language ASC');
		
		// populate array
		$b = array();
		foreach($a as $i)
		{
			$b[] = $i->code;
		}
		return $b;
	}
	
	/**
	 * Get languages by Area ID
	 * Use alang table
	 *
	 * @param   integer $id_area Area ID
	 * @return  array	Array of objects
	 */
	public function get_alanguages($id_area)
	{
		return $this->db->query('SELECT code, language FROM alang WHERE id_area = '.intval($id_area).' ORDER BY language ASC');
	}
	
	/**
	 * Set the language associated with an area
	 * Use alang table
	 * Each area can have many languages
	 * If an user call a page without language code then X3CMS will serve the predefined language
	 *
	 * @param   integer $id_area Area ID
	 * @param   array 	$langs Array of Language code
	 * @param   string 	$predefined predefined Language code
	 * @return  array
	 */
	public function set_alang($id_area, $langs, $predefined)
	{
		// get languages setted
		$setted = $this->db->query('SELECT id, code FROM alang WHERE id_area = '.intval($id_area).' ORDER BY language ASC');
		
		// current situation
		$set = array();
		if ($setted) 
		{
			foreach($setted as $i) $set[$i->code] = $i->id;
		}
		
		// check differences between codes_lang and setted
		$sql = array();
		foreach ($langs as $i)
		{
			if (!isset($set[$i])) 
			{
				// not exists in alang, get language name
				$a = $this->get_language_by_code($i);
				// insert query
				$sql[] = 'INSERT INTO alang (updated, id_area, language, code, rtl, xon) VALUES (NOW(), '.intval($id_area).', '.$this->db->escape($a->language).', '.$this->db->escape($i).', '.intval($a->rtl).', 1)';
			}
			unset($set[$i]);
		}
		
		// set predefined language
		$sql[] = 'UPDATE alang SET predefined = 0 WHERE id_area = '.intval($id_area);
		$sql[] = 'UPDATE alang SET predefined = 1 WHERE id_area = '.intval($id_area).' AND code = '.$this->db->escape($predefined);
		
		// delete not confirmed languages
		foreach($set as $k => $v) $sql[] = 'DELETE FROM alang WHERE id = '.$v;
		$this->db->multi_exec($sql);
	}
	
	/**
	 * Get language by code
	 *
	 * @param   string	$code Language code
	 * @return  object	Language item
	 */
	public function get_language_by_code($code)
	{
		return $this->db->query_row('SELECT language, rtl FROM languages WHERE code = '.$this->db->escape($code));
	}
	
	/**
	 * Get SEO data by Area ID
	 * Use alang table
	 * Join languages and privs tables
	 * Search Engine Optimization data like site title and description are stored in alang table
	 *
	 * @param   integer	$id_area Area ID
	 * @return  array	Array of objects
	 */
	public function get_seo_data($id_area)
	{
		return $this->db->query('SELECT DISTINCT a.*, IF(p.id IS NULL, u.level, p.level) AS level
				FROM alang a
				JOIN languages l ON l.code = a.code
				JOIN uprivs u ON u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('languages').'
				LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = l.id AND p.level > 0
				WHERE a.id_area = '.intval($id_area).'
				ORDER BY a.language ASC');
	}
	
	/**
	 * Update SEO data
	 * Use alang table
	 *
	 * @param   array	$_post _POST array
	 * @return  array
	 */
	public function update_seo_data($post)
	{
		// build array of queries
		$sql = array();
		foreach($post as $id => $i) 
		{
			$update = '';
			foreach($i as $k => $v) {
				$update .= ', '.addslashes($k).' = '.$this->db->escape($v);
			}
			$sql[] = 'UPDATE alang SET updated = NOW() '.$update.' WHERE id = '.intval($id);
		}
		return $this->db->multi_exec($sql);
	}
	
	/**
	 * Get RTL value by Language code
	 *
	 * @param   string	$lang Language code
	 * @return  integer
	 */
	public function rtl($lang)
	{
		return $this->db->query_var('SELECT rtl FROM languages WHERE code = '.$this->db->escape($lang)); 
	}
	
	/**
	 * Exchange languages in a table and in an area
	 *
	 * @param   integer	$id_area
	 * @param   string	$table
	 * @param   string	$old_lang
	 * @param   string	$new_lang
	 * @return  array
	 */
	public function switch_languages($id_area, $table, $old_lang, $new_lang)
	{
		// build array of queries
		$sql = array();
		
		// update items with new_lang with temporary
		$sql[] = 'UPDATE `'.$table.'` SET lang = \'xx\' WHERE lang = '.$this->db->escape($new_lang);
		
		// update items with old_lang with new_lang
		$sql[] = 'UPDATE `'.$table.'` SET lang = '.$this->db->escape($new_lang).' WHERE lang = '.$this->db->escape($old_lang);
		
		// update items with temporary with old_lang
		$sql[] = 'UPDATE `'.$table.'` SET lang = '.$this->db->escape($old_lang).' WHERE lang = \'xx\'';

		return $this->db->multi_exec($sql);
	}
}

/**
 * Empty Language object
 * Necessary for the creation form of new language
 *
 * @package X3CMS
 */
class Lang_obj 
{
	public $language;
	public $code;
	public $rtl = 0;
}
