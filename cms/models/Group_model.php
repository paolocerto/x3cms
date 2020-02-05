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
 * Model for Group Items
 *
 * @package X3CMS
 */
class Group_model extends X4Model_core 
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('groups');
	}
	
	/**
	 * Get groups
	 * Join with privs and areas tables
	 *
	 * @return  array	array of area objects
	 */
	public function get_groups()
	{
		return $this->db->query('SELECT g.*, a.title, IF(p.id IS NULL, u.level, p.level) AS level
				FROM groups g 
				JOIN uprivs u ON u.id_area = g.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('groups').'
				LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = g.id
				JOIN areas a ON a.id = g.id_area
				ORDER BY a.id ASC, g.name ASC');
	}
	
	/**
	 * Get a group by User ID
	 *
	 * @param   integer $id_user User ID
	 * @return  array	array of area objects
	 */
	public function get_group_by_user($id_user)
	{
		return $this->db->query_row('SELECT g.* 
			FROM groups g 
			JOIN users u ON u.id_group = g.id 
			WHERE u.id = '.intval($id_user));
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
}
