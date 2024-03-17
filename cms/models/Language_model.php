<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
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
	 */
	public function __construct()
	{
		parent::__construct('languages');
	}

	/**
	 * Get languages
	 */
	public function get_languages(int $xon = 2) : array
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
				GROUP BY l.id
				ORDER BY l.language ASC');
	}

	/**
	 * Check if a language already exists
	 */
	public function exists(
        array $array,   // associative array with Language data ('language' =>, 'code' =>)
        int $id = 0
    ) : int
	{
		// condition
		$where = ($id == 0)
			? ''
			: ' AND id <> '.$id;

		return (int) $this->db->query_var('SELECT COUNT(*)
			FROM languages
			WHERE (language = '.$this->db->escape($array['language']).' OR code = '.$this->db->escape($array['code']).') '.$where);
	}

	/**
	 * Delete language
	 */
	public function delete_lang(int $id) : array
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
		$sql[] = 'DELETE FROM languages WHERE id = '.$id;

		return $this->db->multi_exec($sql);
	}

	/**
	 * Get language codes of an area by ID
	 */
	public function get_alang_array(int $id_area) : array
	{
		$a = $this->db->query('SELECT code FROM alang WHERE id_area = '.$id_area.' ORDER BY language ASC');

		// populate array
		$b = array();
		foreach ($a as $i)
		{
			$b[] = $i->code;
		}
		return $b;
	}

	/**
	 * Get languages by Area ID
	 */
	public function get_alanguages(int $id_area) : array
	{
		return $this->db->query('SELECT code, language FROM alang WHERE id_area = '.$id_area.' ORDER BY language ASC');
	}

	/**
	 * Set the language associated with an area
	 * Use alang table
	 * Each area can have many languages
	 * If an user call a page without language code then X3CMS will serve the default language
	 */
	public function set_alang(int $id_area, array $languages, string $default) : void
	{
		// get languages setted
		$setted = $this->db->query('SELECT id, code FROM alang WHERE id_area = '.$id_area.' ORDER BY language ASC');

		// current situation
		$set = array();
		if ($setted)
		{
			foreach ($setted as $i)
            {
                $set[$i->code] = $i->id;
            }
		}

		// check differences between codes_lang and setted
		$sql = array();
		foreach ($languages as $i)
		{
			if (!isset($set[$i]))
			{
				// not exists in alang, get language name
				$a = $this->get_language_by_code($i);
				// insert query
				$sql[] = 'INSERT INTO alang (updated, id_area, language, code, rtl, xon) VALUES (NOW(), '.$id_area.', '.$this->db->escape($a->language).', '.$this->db->escape($i).', '.$a->rtl.', 1)';
			}
			unset($set[$i]);
		}

		// set default language
		$sql[] = 'UPDATE alang SET xdefault = 0 WHERE id_area = '.$id_area;
		$sql[] = 'UPDATE alang SET xdefault = 1 WHERE id_area = '.$id_area.' AND code = '.$this->db->escape($default);

		// delete not confirmed languages
		foreach ($set as $k => $v)
        {
            $sql[] = 'DELETE FROM alang WHERE id = '.$v;
        }
		$this->db->multi_exec($sql);
	}

	/**
	 * Get language by code
	 */
	public function get_language_by_code(string $code) : stdClass
	{
		return $this->db->query_row('SELECT language, rtl FROM languages WHERE code = '.$this->db->escape($code));
	}

	/**
	 * Get SEO data by Area ID
	 * Search Engine Optimization data like site title and description are stored in alang table
	 */
	public function get_seo_data(int $id_area) : array
	{
		return $this->db->query('SELECT DISTINCT a.*, IF(p.id IS NULL, u.level, p.level) AS level
				FROM alang a
				JOIN languages l ON l.code = a.code
				JOIN uprivs u ON u.id_area = a.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('areas').'
				LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = a.id_area AND p.level > 0
				WHERE a.id_area = '.$id_area.'
				ORDER BY a.language ASC');
	}

	/**
	 * Update SEO data
	 */
	public function update_seo_data(array $post) : array
	{
		// build array of queries
		$sql = array();
		foreach ($post as $id => $i)
		{
			$update = '';
			foreach ($i as $k => $v)
            {
				$update .= ', '.addslashes($k).' = '.$this->db->escape($v);
			}
			$sql[] = 'UPDATE alang SET updated = NOW() '.$update.' WHERE id = '.intval($id);
		}
		return $this->db->multi_exec($sql);
	}

	/**
	 * Get RTL value by Language code
	 */
	public function rtl(string $lang) : int
	{
		return (int) $this->db->query_var('SELECT rtl FROM languages WHERE code = '.$this->db->escape($lang));
	}

	/**
	 * Exchange languages in a table and in an area
	 */
	public function switch_languages(int $id_area, string $table, string $old_lang, string $new_lang) : array
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
    public $xlock = 0;
}
