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
 * Model for Bookmark Items
 *
 * @package X3CMS
 */
class Bookmark_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('bookmarks');
	}

    /**
	 * Check if a bookmark already exists
	 *
	 * @param   string  $bookmark link
	 * @return  integer	the number of bookmarks set for the same user for the same link
	 */
	public function exists(int $id_user, string $bookmark)
	{
        

		return $this->db->query_var('SELECT COUNT(id)
			FROM bookmarks
			WHERE id_user = '.$id_user.' AND url = '.$this->db->escape($bookmark));
	}

}