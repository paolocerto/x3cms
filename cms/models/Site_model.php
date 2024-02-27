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
 * Model for Site Items
 *
 * @package X3CMS
 */
class Site_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('sites');
	}

    /**
	 * Get items
	 *
     * @param   integer $xuid
	 * @return  array	array of objects
	 */
	public function get_items(int $xuid)
	{
		$sql = 'SELECT s.*
            FROM sites s
			WHERE s.domain LIKE '.$this->db->escape('%'._DOMAIN_.'%').' OR '.$xuid.' = 1
			ORDER BY id ASC';

		return $this->db->query($sql);
	}

    /**
	 * Get site params
	 *
	 * @param integer	site ID
	 * @return array	array of objects
	 */
	public function get_params(int $id_site)
	{
		return $this->db->query('SELECT pa.*
				FROM param pa
				WHERE pa.xrif = \'site\' AND pa.id_area = '.$id_site.' ORDER BY pa.id ASC');
	}

    /**
	 * Init params
     * Copy params from site 1
	 *
     * @param integer	site ID
	 * @return array	array of objects
	 */
	public function init_params(int $id_site)
	{
		$params = $this->db->query('SELECT *
				FROM param
				WHERE xrif = \'site\' AND id_area = 0 ORDER BY id ASC');

        foreach ($params as $i)
        {
            $post = array(
                'id_area' => $id_site,
                'xrif' => 'site',
                'name' => $i->name,
                'description' => $i->description,
                'xtype' => $i->xtype,
                'xvalue' => $i->xvalue,
                'required' => $i->required,
                'xon' => $i->xon
            );

            $this->insert($post, 'param');
        }

        return $this->get_params($id_site);
	}

}

class Obj_site
{
	public $domain = '';
    public $xcode = '';
    public $xdatabase = '';
    public $version = '';

	public function __construct()
	{
		$this->version = X4VERSION;
	}
}
