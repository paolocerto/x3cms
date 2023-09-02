<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

/**
 * Model for Widget Items
 *
 * @package X3CMS
 */
class Widget_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('widgets');
	}

	/**
	 * Build an array of user widgets
	 *
	 * @return  array	array of widget strings
	 */
	public function widgets()
	{
		// get user widgets
		$widgets = $this->get_my_widgets(1);

		$a = array();
		if ($widgets)
		{
			// build widgets items
			foreach($widgets as $i)
			{
				$w = ucfirst($i->name).'_model';
				// load the model
				$mod = new $w;

				// widget item
				$a[] = $mod->get_widget($i->description, $i->id_area, $i->area);
			}
		}
		return $a;
	}

	/**
	 * Get user widgets
	 *
	 * @return  array	array of widget objects
	 */
	public function get_my_widgets(int $xon = 2)
	{
		$where = ($xon < 2)
			? ' AND x.xon = '.$xon
			: '';

		return $this->db->query('SELECT x.*, a.title AS area
			FROM widgets x
			JOIN areas a ON a.id = x.id_area
			WHERE x.id_user = '.intval($_SESSION['xuid']).$where.'
			ORDER BY x.xpos ASC');
	}

	/**
	 * Get user available widgets
	 * Join with areas, privs and widgets tables
	 *
	 * @param   integer $id_user User ID
	 * @return  array	array of widget objects
	 */
	public function get_available_widgets(int $id_user)
	{
        return $this->db->query('SELECT m.id, CONCAT(a.title, \' - \', m.title) AS what, IF(w.id = \'null\', 0, w.id) AS wid
			FROM modules m
			JOIN areas a ON a.id = m.id_area
			JOIN uprivs u ON u.id_area = a.id AND u.id_user = '.$id_user.' AND u.privtype = '.$this->db->escape('widgets').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = m.id
			LEFT JOIN widgets w ON w.id_user = '.$id_user.' AND m.id = w.id_module
			WHERE widget = 1 AND
				((u.id IS NOT NULL AND u.level > 2) OR (p.id IS NOT NULL AND p.level > 2))
			GROUP BY m.id
			ORDER BY a.name ASC, w.name ASC');
	}

	/**
	 * Set user widgets
	 *
	 * @param   array	$insert Widgets to insert
	 * @param   array	$delete Widgets to delete
	 * @return  array
	 */
	public function set_widgets(array $insert, array $delete)
	{
		$sql = array();

		foreach ($insert as $i)
		{
			$sql[] = 'INSERT INTO widgets (updated, id_area, id_user, id_module, name, description, xon)
				VALUES (NOW(), '.intval($i['id_area']).', '.intval($i['id_user']).', '.intval($i['id_module']).', '.$this->db->escape($i['name']).', '.$this->db->escape($i['description']).', 1)';
		}

		foreach ($delete as $i)
		{
			$sql[] = 'DELETE FROM widgets WHERE id = '.intval($i['id_widget']).' AND id_user = '.intval($i['id_user']);
		}

		$result = $this->db->multi_exec($sql);

		// order
		if ($result[1])
		{
			$this->order();
		}
		return $result;
	}

	/**
	 * Get the position of the next widget
	 *
	 * @param   integer $id_user User ID
	 * @return  integer
	 */
	public function get_max_pos(int $id_user)
	{
		return intval($this->db->query_var('SELECT MAX(xpos)
			FROM widgets
			WHERE id_user = '.$id_user.'
			ORDER BY xpos DESC')) + 1;
	}

	/**
	 * Reorder widgets
	 *
	 * @param   string 	$order ID sequence separated by commas
	 * @return  integer
	 */
	public function reorder(string $order)
	{
		$ids = explode(',', $order);
		$c = 1;
		$sql = array();
		foreach ($ids as $i)
		{
			if (!empty($i) && $i != 'sort') {
				$sql[] = 'UPDATE widgets SET xpos = '.intval($c).' WHERE id = '.intval($i).' AND id_user = '.$_SESSION['xuid'];
				$c++;
			}
		}
		$this->db->multi_exec($sql);
	}

	/**
	 * Delete widget
	 * Refresh xpos value
	 *
	 * @param   integer	$id Widget ID
	 * @return  array
	 */
	public function my_delete(int $id)
	{
		// get xpos
		$obj = $this->get_by_id($id, 'widgets', 'xpos');

		$sql = array();
		$sql[] = 'UPDATE widgets SET xpos = (xpos - 1) WHERE id_user = '.$_SESSION['xuid'].' AND xpos > '.$obj->xpos;
		$sql[] = 'DELETE FROM widgets WHERE id_user = '.$_SESSION['xuid'].' AND id = '.$id;
		return $this->db->multi_exec($sql);
	}
}
