
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
 * X3banners model
 *
 * @package		X3CMS
 */
class X3banners_model extends X4Model_core
{
    /*
	// uncomment if you need to personalize search inside this plugin
	// this module require a personalized url for internal search engine
	public $personalized_url = true;

	// here you can define the param to use for get_page_to
	public $search_param;
	*/

	/**
	 * Get url for search
	 * if you need a special URL with search
	 *
	 * @param object	Project obj
	 * @return string
	 */
	public function get_url(stdClass $obj, string $topage)
	{
		return $topage.'/'.$obj->id.'/'.$obj->url;
	}

	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('x3_banners', 'default');
	}

    /**** THIS IS THE DEFAULT CONFIGURATOR ****/
	// If you need only this you don't need to uncomment or edit it
	// Else you can use it as base for your needs

	/**
	 * Build the form array required to set the parameter
	 * This method have to be updated with the plugin options
	 *
	 * @param	integer $id_area Area ID
	 * @param	string	$lang Language code
	 * @param	integer $id_page
	 * @param	string	$param Parameter
	 * @return	array
	 */
	public function configurator(int $id_area, string $lang, int $id_page, string $param)
	{
	    $fields = array();

	    $fields[] = array(
			'label' => null,
			'type' => 'html',
			'value' => '<p>'._ARTICLE_PARAM_SETTING_NOT_REQUIRED.'</p>'
		);

		// comment this if you have options for param
        /*
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => 1,
			'name' => 'no_options'
		);
        */

		// options field store all possible cases and parts
		// cases are separated by §
		// parts are separated by |

		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => 'param1',
			'name' => 'options'
		);

		// this is for simple cases with param equal to get_modeule key
		$options = array(
		    array('value' => 'banner_top', 'option' => 'banner_top: Banner at the top of the page'),
		);

		$p = (empty($param))
	        ? array('', '', '')
	        : explode('|', urldecode($param));

        $fields[] = array(
            'label' => null,
            'type' => 'html',
            'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
        );

		// plugin option
		$fields[] = array(
			'label' => _ARTICLE_PARAM_OPTIONS,
			'type' => 'select',
			'value' => $p[0],
			'options' => array(X4Array_helper::array2obj($options, 'value', 'option'), 'value', 'option', ''),
			'name' => 'param1',
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
	 * Get items
	 * Join with privs table
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   array	$qs
	 * @return  array	array of objects
	 */
	public function get_items(int $id_area, string $lang, array $qs)
	{
		$where = '';

        if (!empty($qs['xstr']))
        {
            $w = array();
            $tok = explode(' ', urldecode($qs['xstr']));
            foreach ($tok as $i)
            {
                $a = trim($i);
                if (!empty($a))
                {
                    $w[] = 'x.title LIKE '.$this->db->escape('%'.$a.'%').' OR
                        x.description LIKE '.$this->db->escape('%'.$a.'%');
                }
            }

            if (!empty($w))
            {
                $where .= ' AND ('.implode(') AND (', $w).')';
            }
        }

        if ($qs['xid_page'] > 0)
        {
            $where .= ' AND x.id_page = '.intval($qs['xid_page']);
        }

		return $this->db->query('SELECT x.*, pg.name AS page, IF(p.id IS NULL, u.level, p.level) AS level
			FROM x3_banners x
			JOIN uprivs u ON u.id_area = x.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = \'x3_banners\'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = x.id
            JOIN pages pg ON pg.id = x.id_page
			WHERE x.id_area = '.$id_area.' AND x.lang = '.$this->db->escape($lang).$where.'
			GROUP BY x.id
			ORDER BY x.updated DESC');
	}

    /**
	 * Get pages for refresh list of pages when change contest
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @return  void
	 */
	public function get_pages(int $id_area, string $lang)
	{
		return $this->db->query('SELECT p.id, LPAD(p.name, CHAR_LENGTH(p.name)+p.deep, \'-\') AS name, IF(pr.id IS NULL, u.level, pr.level) AS level
				FROM pages p
				JOIN uprivs u ON u.id_area = p.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('pages').'
				LEFT JOIN privs pr ON pr.id_who = u.id_user AND pr.what = u.privtype AND pr.id_what = p.id
				WHERE p.id_area = '.$id_area.' AND p.lang = '.$this->db->escape($lang).'
				GROUP BY p.id
				ORDER BY p.ordinal ASC');
	}

    /**
	 * Get one from available banners in a page
	 *
	 * @param   integer $id_page
	 * @return  array	array of objects
	 */
	public function get_banner_by_id_page(int $id_page)
	{
		$items = $this->db->query('SELECT x.*
            FROM x3_banners x
            WHERE
                x.id_page = '.$id_page.' AND
                x.xon = 1 AND
                x.start_date <= NOW() AND
                x.end_date >= NOW()'
        );

        if (!empty($items))
        {
            // if there are more than one
            shuffle($items);

            return array_shift($items);
        }
        return false;
	}

}

class Obj_x3banners
{
	public $id_area;
	public $lang;
	public $title = '';
	public $description = '';

	public $id_page = 0;
	public $start_date = '';
	public $end_date = '';
	public $bg_color = '';
	public $fg_color = '';
	public $link_color = '';
	public $auto_hide = 0;
    public $xlock = 0;

	public function __construct($id_area, $lang)
	{
		$this->id_area = $id_area;
		$this->lang = $lang;
	}
}
