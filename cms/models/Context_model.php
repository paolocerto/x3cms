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
	public function get_by_code(int $id_area, string $lang, int $code)
	{
		return $this->db->query_row('SELECT *
			FROM contexts
			WHERE id_area = '.$id_area.' AND lang = '.$this->db->escape($lang).' AND code = '.$code);
	}

	/**
	 * Get contexts by Area ID and Language code
	 * Join with privs table
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @return  array	Array of Context objects
	 */
	public function get_contexts(int $id_area, string $lang)
	{
		return $this->db->query('SELECT c.*, IF(p.id IS NULL, u.level, p.level) AS level
				FROM contexts c
				JOIN uprivs u ON u.id_area = c.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('categories').'
				LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = c.id
				WHERE c.id_area = '.$id_area.' AND c.lang = '.$this->db->escape($lang).'
				GROUP BY c.id
				ORDER BY c.id ASC');
	}

	/**
	 * Check if a context already exists
	 *
	 * @param   array	$context Context associative array ('id_area' => value, 'lang' => value, 'xkey' => value, 'description' => value)
	 * @param   integer $id Context ID
	 * @return  integer	the number of contexts with the searched name
	 */
	public function exists(array $context, int $id = 0)
	{
		$where = ($id == 0) ? '' : ' AND id <> '.$id;
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
	public function get_max_code(int $id_area, string $lang)
	{
		return $this->db->query_var('SELECT MAX(code) FROM contexts WHERE id_area = '.$id_area.' AND lang = '.$this->db->escape($lang));
	}

	/**
	 * Check if a context name already exists in the admin dictionary
	 * If not then insert it
	 *
	 * @param   array 	$array Associative array ('lang' => 'language code', 'name' => 'context name')
	 * @param   integer	$xon Context status
	 * @return  void
	 */
	public function check_dictionary(array $array, int $xon = 0)
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
		$check = $dict->exists(0, $post);

		// insert
		if (!$check) $dict->insert($post);
	}


    /**
	 * Get contexts codes
	 * Join with privs table
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @return  array	Array of Context objects
	 */
	public function get_codes(int $id_area, string $lang)
	{
		return $this->db->query('SELECT c.code, c.name, IF(p.id IS NULL, u.level, p.level) AS level
				FROM contexts c
				JOIN uprivs u ON u.id_area = c.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('categories').'
				LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = c.id
				WHERE c.id_area = '.$id_area.' AND c.lang = '.$this->db->escape($lang).'
				GROUP BY c.id
				ORDER BY c.name ASC');
	}

	/**
	 * Get pages for refresh list of pages when change contest
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @return  void
	 */
	public function get_pages(int $id_area, string $lang)
	{
		return $this->db->query('SELECT p.id, LPAD(p.name, CHAR_LENGTH(p.name)+p.deep, \'-\') AS name, IF(pr.id IS NULL, u.level, pr.level) AS level
				FROM pages p
				JOIN uprivs u ON u.id_area = p.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('pages').'
				LEFT JOIN privs pr ON pr.id_who = u.id_user AND pr.what = u.privtype AND pr.id_what = p.id
				WHERE p.id_area = '.$id_area.' AND p.lang = '.$this->db->escape($lang).'
				GROUP BY p.id
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
    public $id = 0;
	public $id_area = 0;
	public $lang = '';
	public $name;
    public $xlock = 0;

	/**
	 * Constructor
	 * Initialize the new context
	 *
		 * @return  void
	 */
	public function __construct(int $id_area, string $lang)
	{
		$this->id_area = $id_area;
		$this->lang = $lang;
	}
}
