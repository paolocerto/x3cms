<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */
 
/**
 * Model for matches (table for many to many relations)
 *
 * @package X4WEBAPP
 */
class X4Matches_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('matches');
	}
	
	/**
	 * Get relations by from ID
	 *
	 * @param   string	Table from name
	 * @param   string	Table to name
	 * @param   integer From ID
	 * @return  array	Array of objects
	 */
	public function get_by_from($xfrom, $xto, $id_from)
	{
		return $this->db->query('SELECT id_to FROM matches WHERE xfrom = '.$this->db->escape($xfrom).' AND xto = '.$this->db->escape($xto).' AND id_from = '.intval($id_from).' AND xon = 1');
	}
	
	/**
	 * Get relations by from ID with fields
	 * Use external table
	 *
	 * @param   string	Table from name
	 * @param   string	Table to name
	 * @param   integer From ID
	 * @param   array	Array of fields
	 * @param   string	Sort key
	 * @return  array	Array of objects
	 */
	public function get_by_from_with_fields($xfrom, $xto, $id_from, $fields, $order = '')
	{
		// sort order
		$orderby = (empty($order)) 
			? '' 
			: ' ORDER BY x.'.$order;
		
		// prefix fields
		array_walk($fields, 'prefix', 'x.');
		
		return $this->db->query('SELECT '.implode(',', $fields).' 
			FROM matches m
			JOIN '.$xto.' x ON x.id = m.id_to AND x.xon = 1
			WHERE m.xfrom = '.$this->db->escape($xfrom).' AND m.xto = '.$this->db->escape($xto).' AND m.id_from = '.intval($id_from).' AND m.xon = 1'.$orderby);
	}
	
	/**
	 * Get related items by from with fields
	 * Use external table
	 *
	 * @param   string	Table from name
	 * @param   string	Table to name
	 * @param   integer From ID
	 * @param   integer To ID
	 * @param   array	Array of fields
	 * @return  object
	 */
	public function get_related($xfrom, $xto, $id_from, $id_to, $fields)
	{
		// prefix fields
		array_walk($fields, 'prefix', 'x.');
		
		return $this->db->query_row('SELECT m.id, '.implode(',', $fields).' 
			FROM matches m
			JOIN '.$xto.' x ON x.id = m.id_to
			WHERE m.xfrom = '.$this->db->escape($xfrom).' AND m.xto = '.$this->db->escape($xto).' AND m.id_from = '.intval($id_from).' AND m.id_to = '.intval($id_to));
	}
	
	/**
	 * Get relations by to ID
	 *
	 * @param   string	Table from name
	 * @param   string	Table to name
	 * @param   integer To ID
	 * @return  array	Array of objects
	 */
	public function get_by_to($xfrom, $xto, $id_to)
	{
		return $this->db->query('SELECT id_from FROM matches WHERE xfrom = '.$this->db->escape($xfrom).' AND xto = '.$this->db->escape($xto).' AND id_to = '.intval($id_to).' AND xon = 1');
	}
	
	/**
	 * Delete relations by from ID
	 *
	 * @param   string	Table from name
	 * @param   string	Table to name
	 * @param   integer From ID
	 * @return  array
	 */
	public function delete_by_from($xfrom, $xto, $id_from)
	{
		return $this->db->single_exec('DELETE FROM matches WHERE xfrom = '.$this->db->escape($xfrom).' AND xto = '.$this->db->escape($xto).' AND id_from = '.intval($id_from));
	}
	
	/**
	 * Delete relations by to ID
	 *
	 * @param   string	Table from name
	 * @param   string	Table to name
	 * @param   integer To ID
	 * @return  array
	 */
	public function delete_by_to($xfrom, $xto, $id_to)
	{
		return $this->db->single_exec('DELETE FROM matches WHERE xfrom = '.$this->db->escape($xfrom).' AND xto = '.$this->db->escape($xto).' AND id_to = '.intval($id_to));
	}
}

/**
 * Called by array_walk
 * Prefix item value
 */
function prefix(&$item, $key, $prefix) 
{
 	 $item = $prefix.$item;
}

