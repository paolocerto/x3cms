<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
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
	 */
	public function __construct()
	{
		parent::__construct('matches');
	}

	/**
	 * Get relations by from ID
	 */
	public function get_by_from(string $xfrom, string $xto, int $id_from) : array
	{
		return $this->db->query('SELECT id_to
            FROM matches
            WHERE
                xfrom = '.$this->db->escape($xfrom).' AND
                xto = '.$this->db->escape($xto).' AND
                id_from = '.$id_from.' AND
                xon = 1'
        );
	}

	/**
	 * Get relations by from ID with fields
	 * Use external table
	 */
	public function get_by_from_with_fields(
        string $xfrom,
        string $xto,
        int $id_from,
        array $fields,
        string $order = ''
    ) : array
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
			WHERE
                m.xfrom = '.$this->db->escape($xfrom).' AND
                m.xto = '.$this->db->escape($xto).' AND
                m.id_from = '.$id_from.' AND
                m.xon = 1
            '.$orderby);
	}

	/**
	 * Get related items by from with fields
	 * Use external table
	 */
	public function get_related(string $xfrom, string $xto, int $id_from, int $id_to, array $fields) : stdClass
	{
		// prefix fields
		array_walk($fields, 'prefix', 'x.');

		return $this->db->query_row('SELECT m.id, '.implode(',', $fields).'
			FROM matches m
			JOIN '.$xto.' x ON x.id = m.id_to
			WHERE
                m.xfrom = '.$this->db->escape($xfrom).' AND
                m.xto = '.$this->db->escape($xto).' AND
                m.id_from = '.$id_from.' AND
                m.id_to = '.$id_to);
	}

	/**
	 * Get relations by to ID
	 */
	public function get_by_to(string $xfrom, string $xto, int $id_to) : array
	{
		return $this->db->query('SELECT id_from
            FROM matches
            WHERE
                xfrom = '.$this->db->escape($xfrom).' AND
                xto = '.$this->db->escape($xto).' AND
                id_to = '.$id_to.' AND
                xon = 1');
	}

	/**
	 * Delete relations by from ID
	 */
	public function delete_by_from(string $xfrom, string $xto, int $id_from) : array
	{
		return $this->db->single_exec('DELETE FROM matches
            WHERE
                xfrom = '.$this->db->escape($xfrom).' AND
                xto = '.$this->db->escape($xto).' AND
                id_from = '.$id_from);
	}

	/**
	 * Delete relations by to ID
	 */
	public function delete_by_to(string $xfrom, string $xto, int $id_to) : array
	{
		return $this->db->single_exec('DELETE FROM matches
            WHERE
                xfrom = '.$this->db->escape($xfrom).' AND
                xto = '.$this->db->escape($xto).' AND
                id_to = '.$id_to);
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

