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
 * Model for X4get_by_key
 *
 * @package X3CMS
 */
class X4get_by_key_model extends X4Model_core
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
	 * Build the form array required to set the parameter
	 * This method have to be updated with the plugin options
	 *
	 * @param	integer $id_area Area ID
	 * @param	string	$lang Language code
	 * @param   	integer $id_page Page ID
	 * @param	string	$param Parameter
	 * @return	array
	 */
	public function configurator(int $id_area, string $lang, int $id_page, string $param)
	{
	    $p = (empty($param))
	        ? array('', '')
	        : explode('|', urldecode($param));

	    $fields = array();

        $fields[] = array(
            'label' => null,
            'type' => 'html',
            'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
        );

	    $fields[] = array(
			'label' => null,
			'type' => 'html',
			'value' => '<p>'._X4GET_BY_KEY_CONFIGURATOR_MSG.'</p>'
		);

		// options field store all possible cases and parts
		// cases are separated by §
		// parts are separated by |
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => 'param1|param2',
			'name' => 'options'
		);

		// plugin option
		$fields[] = array(
			'label' => _X4GET_BY_KEY_OPTION,
			'type' => 'select',
			'value' => $p[0],
			'options' => array($this->get_keys($id_area, $lang), 'xkeys', 'xkeys', ''),
			'name' => 'param1',
			'rule' => 'required',
			'extra' => 'class="w-full"'
		);

        $options = ['no_tags', 'with_tags'];

        // plugin option
		$fields[] = array(
			'label' => _X4GET_BY_TAG_OPTION,
			'type' => 'select',
			'value' => $p[1],
			'options' => array(X4Array_helper::simplearray2obj($options), 'value', 'option', ''),
			'name' => 'param2',
			'rule' => 'required',
			'extra' => 'class="w-full"'
		);

        $fields[] = array(
            'label' => null,
            'type' => 'html',
            'value' => '</div>'
        );

		return $fields;
	}

	/**
	 * Get keys
	 *
	 * @param integer	$id_area Area ID
	 * @param string	$lang 	Language code
	 * @return array	array
	 */
	private function get_keys($id_area, $lang)
	{
		return $this->db->query('SELECT xkeys
		        FROM articles
				WHERE xkeys != \'\' AND id_area = '.intval($id_area).' AND lang = '.$this->db->escape($lang).' AND xon = 1 AND date_in <= NOW() AND (date_out = 0 OR date_out >= NOW())
				GROUP BY xkeys
				ORDER BY xkeys ASC');
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
				WHERE id_area = '.intval($id_area).' AND lang = '.$this->db->escape($lang).' AND xon = 1 AND date_in <= NOW() AND (date_out = 0 OR date_out >= NOW()) ORDER BY date_in DESC, updated DESC
				) a
			WHERE a.xkeys = '.$this->db->escape($key).' AND a.tags LIKE '.$this->db->escape('%'.$tag.'%').'
			GROUP BY a.bid
			ORDER BY a.date_in DESC, a.id DESC');
	}
}
