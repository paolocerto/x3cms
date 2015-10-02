<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */

/**
 * Model for X3get_by_key
 *
 * @package X3CMS
 */
class X3get_by_key_model extends X4Model_core 
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('articles');
	}
	
	/**
	 * Get articles by key and tag
	 *
	 * @param integer	$id_area Area ID
	 * @param string	$lang 	Language code
	 * @param string	$key	Article key
	 * @param string	$tag 	Article tag
	 * @return array	array of objects
	 */
	public function get_articles_by_key_and_tag($id_area, $lang, $key, $tag)
	{
		return $this->db->query('SELECT a.* FROM 
				(
				SELECT * 
				FROM articles 
				WHERE id_area = '.intval($id_area).' AND lang = '.$this->db->escape($lang).' AND xon = 1 AND date_in <= '.$this->now.' AND (date_out = 0 OR date_out >= '.$this->now.') ORDER BY date_in DESC, updated DESC
				) a
			WHERE a.xkeys = '.$this->db->escape($key).' AND a.tags LIKE '.$this->db->escape('%'.$tag.'%').' 
			GROUP BY a.bid
			ORDER BY a.date_in DESC, a.id DESC');
	}
}
?>
