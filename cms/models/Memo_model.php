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
 * Model for memo
 *
 * @package X3CMS
 */
class Memo_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 */
	public function __construct()
	{
		parent::__construct('memos');
	}

    /**
	 * Get memos
	 */
	public function get_memos(string $url, int $id_user) : array
	{
		return $this->db->query('SELECT m.*,
                IF (u.id IS NULL, \'Unknown\', u.username) AS author,
                IF (u.id IS NULL, \'nomail\', u.mail) AS email
			FROM memos m
            LEFT JOIN users u ON u.id = m.xuid
			WHERE m.url = '.$this->db->escape($url).' AND
                (m.personal = 0 OR m.xuid = '.$id_user.')
			ORDER BY m.likes DESC');
	}
}

/**
 * Empty Memo object
 * Necessary for the creation form of new memos
 *
 * @package X3CMS
 */
class Memo_obj
{
    public $id = 0;
	public $lang;
    public $xuid = 0;
    public $url;
	public $title;
	public $description = 0;
    public $personal = 0;

    /**
	 * Constructor
	 * Initialize the new category
	 */
	public function __construct(string $lang, string $url)
	{
		$this->lang = $lang;
        $this->url = $url;
	}
}
