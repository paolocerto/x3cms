<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

/**
 * Model for Help
 *
 * @package X3CMS
 */
class Help_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('pages');
	}
	
	/**
	 * Get subpages
	 *
	 * @param   object $page Page object
	 * @return  object
	 */
	public function get_subpages($page)
	{
		return $this->db->query('SELECT * 
			FROM pages 
			WHERE id_area = '.intval($page->id_area).' AND lang = '.$this->db->escape($page->lang).' AND xfrom = '.$this->db->escape($page->url).' AND xon = 1
			ORDER BY xpos ASC');
	}
}

