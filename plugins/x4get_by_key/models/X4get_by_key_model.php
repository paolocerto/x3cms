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
        // check APC
		$c = (APC)
            ? apcu_fetch(SITE.'akeytag'.$id_area.$lang.$key.$tag)
            : array();

        if (empty($c))
        {
            $c = $this->db->query('SELECT a.* FROM
				(
				SELECT *
				FROM articles
				WHERE id_area = '.intval($id_area).' AND lang = '.$this->db->escape($lang).' AND xon = 1 AND date_in <= NOW() AND (date_out = 0 OR date_out >= NOW()) ORDER BY date_in DESC, updated DESC
				) a
			WHERE a.xkeys = '.$this->db->escape($key).' AND a.tags LIKE '.$this->db->escape('%'.$tag.'%').'
			GROUP BY a.bid
			ORDER BY a.date_in DESC, a.id DESC');

			if (APC)
			{
				apcu_store(SITE.'akeytag'.$id_area.$lang.$key.$tag, $c);
			}
		}
		return $c;
	}

    /**
	 * Get articles by key
	 *
	 * @param integer	area ID
	 * @param string	lang
	 * @param string	article key
	 * @return array	array of objects
	 */
	public function get_articles_by_key($id_area, $lang, $key)
	{
	    // check APC
		$c = (APC)
			? apcu_fetch(SITE.'akey'.$id_area.$lang.$key)
			: array();

		if (empty($c))
		{
		    $c = $this->db->query('SELECT a.*
                FROM articles a
                JOIN (
                    SELECT MAX(id) AS id, bid
                    FROM articles
                    WHERE
                        id_area = '.intval($id_area).' AND
                        lang = '.$this->db->escape($lang).' AND
                        xon = 1 AND
                        date_in <= NOW() AND
                        (date_out = 0 OR date_out >= NOW())
                    GROUP BY bid
                    ) b ON b.id = a.id AND b.bid = a.bid
                WHERE a.xkeys = '.$this->db->escape($key).'
                ORDER BY a.date_in DESC, a.id DESC');

			if (APC)
			{
				apcu_store(SITE.'akey'.$id_area.$lang.$key, $c);
			}
		}
		return $c;
	}

    // FOR INTERNAL SEARCHES

    /**
	 * Get page URL by plugin name and parameter
	 *
	 * @param integer	area ID
	 * @param string	lang
	 * @param string	plugin name
	 * @param string	parameter value, accepts * wildcard
	 * @return string	page URL
	 */
	public function get_page_to($id_area, $lang, $modname, $param = '')
	{
        $where = (strstr($param, '*') != '')
            ? '	AND a.param LIKE '.$this->db->escape(str_replace('*', '%', $param))
            : ' AND a.param = '.$this->db->escape($param);

        $sql = 'SELECT p.url FROM pages p
                JOIN articles a ON a.id_area = p.id_area AND a.id_page = p.id
                WHERE p.xon = 1 AND
                    p.id_area = '.$id_area.' AND
                    p.lang = '.$this->db->escape($lang).' AND
                    a.xon = 1 AND
                    a.date_in <= '.$this->time().' AND
                    (a.date_out = 0 OR a.date_out >= '.$this->time().') AND
                    a.module = '.$this->db->escape($modname).$where.'
                GROUP BY a.bid
                ORDER BY a.id DESC';

        return $this->db->query_var($sql);
	}

    /**
	 * Get an array of articles that contains search keys
	 *
	 * @param integer	area ID
	 * @param string	lang
	 * @param array		array of keys
	 * @return array	array of objects for search results
	 */
	public function search(int $id_area, string $lang, array $array)
	{
		// first step: get articles which can be highlighted
		$w = array();
		foreach ($array as $a) {
			$i = htmlentities(strtolower($a));
			$w[] = ' (
				LOWER(a.name) LIKE \'%'.$i.'%\' OR
				LOWER(a.ftext) LIKE \'%'.$i.'%\' OR
				LOWER(a.ftext) LIKE '.$this->db->escape('%'.html_entity_decode($i).'%').'
				) ';
		}

		$where = implode(' AND ', $w);

		$sql = 'SELECT a.bid, a.name, a.xkeys, a.tags
				FROM articles a
                JOIN (
                    SELECT MAX(id) AS id, bid
                    FROM articles
                    WHERE
                        id_area = '.intval($id_area).' AND
                        lang = '.$this->db->escape($lang).' AND
                        xon = 1 AND
                        date_in <= '.$this->time().' AND
                        (date_out = 0 OR date_out >= '.$this->time().')
                    GROUP BY bid
                    ) b ON b.id = a.id AND b.bid = a.bid
				WHERE
					(a.xkeys <> \'\') AND
					'.$where.'
				ORDER BY a.id DESC';

		$items = $this->db->query($sql);

		// second step: check if there are articles with x4get_by_key
		$sql = array();
		foreach ($items as $a)
		{
            if (!empty($a->xkeys))
			{
				// for keys
				$k = explode(' ', $a->xkeys);
				foreach($k as $i)
				{
					$sql[] = 'SELECT \''.$a->bid.'\' AS id, p.id_area, p.lang, \''.$i.'\' AS xkeys, p.name, p.description
					FROM articles a
                    JOIN (
                        SELECT MAX(id) AS id, bid
                        FROM articles
                        WHERE
                            id_area = '.$id_area.' AND
                            lang = '.$this->db->escape($lang).' AND
                            xon = 1 AND
                            date_in <= '.$this->time().' AND
                            (date_out = 0 OR date_out >= '.$this->time().')
                        GROUP BY bid
                        ) b ON b.id = a.id AND b.bid = a.bid
					JOIN pages p ON p.id = a.id_page
					WHERE
						p.xon = 1 AND
						p.id_area = '.$id_area.' AND
						p.lang = '.$this->db->escape($lang).' AND
						a.module = \'x4get_by_key\' AND
						a.param LIKE '.$this->db->escape($i.'|%');
                }
			}

		}

		// get results
		$a = array();
		foreach($sql as $q)
		{
			$tmp = $this->db->query_row($q);
			if ($tmp)
            {
                $a[] = $tmp;
            }
		}
		return $a;
	}

    // this module require a personalized url for the internal search engine
	public $personalized_url = true;

    /**
	 * Get url for search
	 * if you need a special URL with search
	 *
	 * @param object	Plugin obj
     * @param string    $topage if this function fails
	 * @return string
	 */
	public function get_url($obj, $topage)
	{
		$to_page = $this->get_page_to($obj->id_area, $obj->lang, 'x4get_by_key', $obj->xkeys.'|*');

        return ($to_page)
            ? $to_page
            : $topage;
	}
}
