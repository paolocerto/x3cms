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
	 */
	public function __construct()
	{
		parent::__construct('bookmarks');
	}

    /**
	 * Check if a bookmark already exists
	 */
	public function exists(int $id_user, string $bookmark) : int
	{
		return (int) $this->db->query_var('SELECT COUNT(*)
			FROM bookmarks
			WHERE id_user = '.$id_user.' AND url = '.$this->db->escape($bookmark));
	}

}