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
 * Model for Log Items
 *
 * @package X3CMS
 */
class Log_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 */
	public function __construct($db = 'default')
	{
		parent::__construct('logs', $db);
	}

    /**
     * Unlog - remove log
     */
    public function unlog(string $case, array $data)
    {
        $sql = 'DELETE FROM logs
            WHERE who = '.intval($data['who']).' AND
                action ='.$this->db->escape($case).' AND
                what = '.$this->db->escape($data['what']).' AND
                id_what = '.intval($data['id_what']);
                
        return $this->db->single_exec($sql);
    }

}
