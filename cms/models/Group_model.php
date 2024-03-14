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
 * Model for Group Items
 *
 * @package X3CMS
 */
class Group_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 */
	public function __construct()
	{
		parent::__construct('xgroups');
	}

	/**
	 * Get groups
	 */
	public function get_groups() : array
	{
		return $this->db->query('SELECT g.*, a.title, IF(p.id IS NULL, u.level, p.level) AS level
				FROM xgroups g
				JOIN uprivs u ON u.id_area = g.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('xgroups').'
				LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = g.id
				JOIN areas a ON a.id = g.id_area
				ORDER BY a.id ASC, g.name ASC');
	}

	/**
	 * Get a group by User ID
	 */
	public function get_group_by_user(int $id_user) : stdClass
	{
		return $this->db->query_row('SELECT g.*
			FROM xgroups g
			JOIN users u ON u.id_group = g.id
			WHERE u.id = '.$id_user);
	}

}

/**
 * Empty Group object
 * Necessary for the creation form of new group
 *
 * @package X3CMS
 */
class Group_obj
{
	public $id_area = 0;
	public $name;
	public $description;
    public $xlock = 0;
}
