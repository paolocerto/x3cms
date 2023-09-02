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
	public function get_categories(int $id_area, string $lang, string $tag = '')
	{
        switch ($tag)
        {
            case 'xxxall':
                // all tags
                $where = '';
                break;
            case '':
                $where = ' AND c.tag = \'\'';
                break;
            default:
                $where = ' AND c.tag = '.$this->db->escape($tag);
        }

		return $this->db->query('SELECT c.*, IF(p.id IS NULL, u.level, p.level) AS level
			FROM categories c
			JOIN uprivs u ON u.id_area = c.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('categories').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = c.id
			WHERE c.id_area = '.$id_area.' AND c.lang = '.$this->db->escape($lang).$where.'
			GROUP BY c.id
			ORDER BY c.name ASC, c.tag ASC');
	}

	/**
	 * Get categories tags by Area ID and Language code
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @return  array	array of category objects
	 */
	public function get_tags(int $id_area, string $lang)
	{
		return $this->db->query('SELECT c.tag
			FROM categories c
			WHERE c.id_area = '.$id_area.' AND c.lang = '.$this->db->escape($lang).' AND c.tag <> \'\'
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
	public function exists(array $ctg, int $id = 0)
	{
		$where = ($id == 0)
            ? ''
            : ' AND id <> '.$id;

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
    public $id = 0;
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
	public function __construct(int $id_area, string $lang, string $tag)
	{
		$this->id_area = $id_area;
		$this->lang = $lang;
        // exclude fake tag
		$this->tag = ($tag == 'xxxall') ? '' : $tag;
	}
}
