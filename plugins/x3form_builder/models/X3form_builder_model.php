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
 * X3form_builder model
 *
 * @package		X3CMS
 */
class X3form_builder_model extends X4Model_core
{
    // origin db
    private $odb = '';

	/**
	 * Constructor
	 */
	public function __construct(string $db = 'default')
	{
		parent::__construct('x3_forms', $db);
	}

	/**
	 * Build the form array required to set the parameter
	 * This method have to be updated with the plugin options
	 */
	public function configurator(int $id_area, string $lang, int $id_page, string $param) : array
	{
	    $fields = array();

        $fields[] = array(
            'label' => null,
            'type' => 'html',
            'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
        );

	    $fields[] = array(
			'label' => null,
			'type' => 'html',
			'value' => '<p>'._ARTICLE_PARAM_DEFAULT_MSG.'</p>'
		);

		// options field store all possible cases and parts
		// cases are separated by §
		// parts are separated by |
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => 'param1',
			'name' => 'options'
		);

		// the form builder plugin has only one possible call
		// the parameter is the form name
		$fields[] = array(
			'label' => _X3FB_FORM_NAME,
			'type' => 'select',
			'value' => $param,
			'options' => array($this->get_forms($id_area, $lang, 1, 1), 'name', 'name', ''),
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
	 * Get forms
	 */
	public function get_forms(int $id_area, string $lang, int $xon = 2) : array
	{
	    $where = ($xon < 2)
		    ? ' AND x.xon = 1'
		    : '';

		return $this->db->query('SELECT DISTINCT x.*, COUNT(r.id) AS n, IF(pr.id IS NULL, u.level, pr.level) AS level
			FROM x3_forms x
			LEFT JOIN x3_forms_results r ON r.id_form = x.id AND r.xon = 0
            JOIN uprivs u ON u.id_area = x.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('x3_forms').'
			LEFT JOIN privs pr ON pr.id_who = u.id_user AND pr.what = u.privtype AND pr.id_what = x.id
			WHERE x.id_area = '.$id_area.' AND x.lang = '.$this->db->escape($lang).$where.'
			GROUP BY x.id
			ORDER BY x.name ASC');
	}

	/**
	 * Get fields in a form
	 */
	public function get_form_fields(int $id_area, string $lang, int $id_form) : array
	{
		return $this->db->query('SELECT DISTINCT x.*, IF(pr.id IS NULL, u.level, pr.level) AS level
			FROM x3_forms_fields x
            JOIN uprivs u ON u.id_area = x.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('x3_forms_fields').'
			LEFT JOIN privs pr ON pr.id_who = u.id_user AND pr.what = u.privtype AND pr.id_what = x.id
			WHERE x.id_form = '.$id_form.' AND x.id_area = '.$id_area.' AND x.lang = '.$this->db->escape($lang).'
			GROUP BY x.id
			ORDER BY x.xpos ASC');
	}

	/**
	 * Get results related to a form
	 */
	public function get_form_results(int $id_area, string $lang, int $id_form) : array
	{
		return $this->db->query('SELECT DISTINCT x.*, IF(pr.id IS NULL, u.level, pr.level) AS level
			FROM x3_forms_results x
            JOIN uprivs u ON u.id_area = x.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('x3_forms_results').'
			LEFT JOIN privs pr ON pr.id_who = u.id_user AND pr.what = u.privtype AND pr.id_what = x.id
			WHERE x.id_form = '.$id_form.' AND x.id_area = '.$id_area.' AND x.lang = '.$this->db->escape($lang).'
			GROUP BY x.id
			ORDER BY x.id DESC');
	}

    /**
	 * Get blacklist by area and language
	 */
	public function get_blacklist(int $id_area, string $lang) : array
	{
		return $this->db->query('SELECT DISTINCT x.*, IF(pr.id IS NULL, u.level, pr.level) AS level
			FROM x3_forms_blacklist x
            JOIN uprivs u ON u.id_area = x.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('x3_forms_blacklist').'
			LEFT JOIN privs pr ON pr.id_who = u.id_user AND pr.what = u.privtype AND pr.id_what = x.id
			WHERE x.id_area = '.$id_area.' AND x.lang = '.$this->db->escape($lang).'
			GROUP BY x.id
			ORDER BY x.name ASC');
	}

	/**
	 * Get areas
	 */
	public function get_areas() : array
	{
        return $this->db->query('SELECT id, name
			FROM areas
			WHERE id <> 1
			ORDER BY name ASC');
	}

	/**
	 * Get languages
	 */
	public function get_languages() : array
	{
        return $this->db->query('SELECT code, language
				FROM languages
				ORDER BY language ASC');
	}

	/**
	 * Check if a form already exists
	 */
	public function form_exists(int $id_area, string $lang, string $name, int $id = 0) : int
	{
		$where = (!$id)
		    ? ''
		    : ' AND id <> '.$id;

		return (int) $this->db->query_var('SELECT COUNT(*)
            FROM x3_forms
			WHERE
				id_area = '.$id_area.' AND
				lang = '.$this->db->escape($lang).' AND
				name = '.$this->db->escape($name).$where);
	}

	/**
	 * Check if a field in a form already exists
	 */
	public function field_exists(int $id_form, string $name, int $id = 0) : int
	{
		$where = (!$id)
		    ? ''
		    : ' AND id <> '.$id;

		return (int) $this->db->query_var('SELECT COUNT(*)
            FROM x3_forms_fields
			WHERE
				id_form = '.$id_form.' AND
				name = '.$this->db->escape($name).$where);
	}

	/**
	 * Get the position of the last field in a form
	 */
	public function get_max_pos(string $table, string $what, int $id_what) : int
	{
		return (int) $this->db->query_var('SELECT xpos FROM x3_forms_'.$table.' WHERE '.$what.' = '.$id_what.' ORDER BY xpos DESC');
	}

	/**
	 * Get related fields name in a form
	 */
	public function get_related(int $id_form) : array
	{
		return $this->db->query('SELECT name
			FROM x3_forms_fields x
			WHERE
				x.id_form = '.$id_form.' AND
				x.xtype <> \'html\' AND
				x.xtype <> \'hidden\'
			ORDER BY x.xpos ASC');
	}

    /**
	 * Check if a blacklist item already exists in the same area and language
	 */
	public function blackitem_exists(string $name, array $post, int $id = 0) : int
	{
		$where = (!$id)
		    ? ''
		    : ' AND id <> '.$id;

		return (int) $this->db->query_var('SELECT COUNT(*)
            FROM x3_forms_blacklist
			WHERE
				id_area = '.$post['id_area'].' AND
                lang = '.$this->db->escape($post['lang']).' AND
				name = '.$this->db->escape($name).$where);
	}

    /**
	 * Handle the result of a form submission for results view
	 */
	public function show_message(string $result) : string
	{
		$array = json_decode($result, true);
		$str = '';
		foreach ($array as $k => $v)
		{
			if (is_array($v))
			{
				$str .= strtoupper($k).': <ul>';
				foreach ($v as $i)
				{
					$str .= '<li><strong>'.$i.'</strong></li>';
				}
				$str .= '</ul>';
			}
			elseif ($k != 'x4token')
			{
				$str .= strtoupper($k).': <strong>'.$v.'</strong>'.BR;
			}
		}
		return $str;
	}

	/**
	 * Build the widget
	 */
	public function get_widget(string $title, int $id_area, string $area, bool $container = true) : string
	{
		// new submissions
		$n = (int) $this->db->query_var('SELECT COUNT(x.id) AS n
			FROM x3_forms_results x
			JOIN uprivs u ON u.id_area = x.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('x3_forms_results').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = x.id
			WHERE x.id_area = '.$id_area.' AND x.updated > \''.$_SESSION['last_in'].'\'
			GROUP BY x.id');

		// to check
		$noff = (int) $this->db->query_var('SELECT COUNT(x.id) AS n
			FROM x3_forms_results x
			JOIN uprivs u ON u.id_area = x.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('x3_forms_results').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = x.id
			WHERE x.id_area = '.$id_area.' AND x.xon = 0
			GROUP BY x.id');

		// total
		$ntot = (int) $this->db->query_var('SELECT COUNT(x.id) AS n
			FROM x3_forms_results x
			JOIN uprivs u ON u.id_area = x.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('x3_forms_results').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = x.id
			WHERE x.id_area = '.$id_area.'
			GROUP BY x.id');

		// dictionary
		$dict = new X4Dict_model(X4Route_core::$folder, X4Route_core::$lang);
		$dict->get_wordarray(array('x3form_builder'));

		// title
		$w = '<div class="bg rounded-t px-4 py-4 flex items-center justify-between">
                    <h4>'.$title._TRAIT_.'<span class="text-sm">'.$area.'</span></h4>
                    <div class="space-x-4">
                        <a class="link" @click="refresh(\'x3fb_widget\', \''.BASE_URL.'x3form_builder/rewidget/'.urlencode($title).'/'.$id_area.'/'.urlencode($area).'\')" title="'._X3FB_RELOAD.'"><i class="fa-solid fa-lg fa-rotate"></i></a>
                        <a class="link" @click="pager(\''.BASE_URL.'x3form_builder/mod/'.$id_area.'\')" title="'.$title.'"><i class="fa-solid fa-lg fa-chevron-right"></i></a>
                    </div>
                </div>
                <div class="bg2 h-full px-4 pt-4">
                    <table>
                        <tr>
                            <td>'._X3FB_LATEST.'</td>
                            <td class="text-right font-bold">'.$n.'/'.$ntot.'</td>
                        </tr>
                        <tr>
                            <td>'._X3FB_TO_CHECK.'</td>
                            <td class="text-right font-bold">'.$noff.'/'.$ntot.'</td>
                        </tr>
                    </table>
                </div>';

		return $container
            ? '<div id="x3fb_widget">'.$w.'</div>'
            : $w;
	}

	// public

	/**
	 * Get a form by name
	 */
	public function get_form_by_name(int $id_area, string $lang, string $form) : stdClass
	{
		return $this->db->query_row('SELECT *
			FROM x3_forms
			WHERE name = '.$this->db->escape($form).' AND id_area = '.$id_area.' AND lang = '.$this->db->escape($lang));
	}

	/**
	 * Get the fields in a form by name
	 */
	public function get_fields_by_form(int $id_area, string $lang, string $form) : array
	{
		return $this->db->query('SELECT fi.*
			FROM x3_forms_fields fi
			JOIN x3_forms fo ON fo.id = fi.id_form AND fo.xon = 1
			WHERE fo.name = '.$this->db->escape($form).' AND fi.xon = 1 AND fo.id_area = '.$id_area.' AND fo.lang = '.$this->db->escape($lang).'
			ORDER BY fi.xpos ASC');
	}

    /**
	 * Build rule
	 */
	public function build_rule(stdClass $rule) : string
	{
		$token = [$rule->rule_name];
        if (!empty($rule->field_value))
        {
            $token[] = $rule->field_value;
        }
        if (!empty($rule->param_value))
        {
            $token[] = $rule->param_value;
        }
        return implode('§', $token);
	}

    /**
	 * build message
	 */
	public function messagize(int $id_area, string $form, array $fields, array $files = []) : string
	{
		$str = 'FORM <strong>'.$form.'</strong> '.date('Y-m-d H:i:s').BR.BR;

		foreach ($fields as $k => $v)
		{
			if ($k != strrev($form))
			{
				if (is_array($v))
				{
                    // no spam check here because is an array of fixed options
					$str .= strtoupper($k).': <ul>';
					foreach ($v as $i)
					{
						$str .= '<li><strong>'.$i.'</strong></li>';
					}
					$str .= '</ul>';
				}
				else
				{
				    // blacklist
				    $chk = $this->spam_check($id_area, $v);
                    if ($chk > 0)
				    {
				        return -$chk;
				    }

                    // remove token
					if ($k != 'x4token')
					{
						$str .= strtoupper($k).': <strong>'.$v.'</strong>'.BR;
					}
				}
			}
		}
		// for files
		if (!empty($files))
		{
			foreach ($files as $k => $v)
			{
				if (!empty($v))
                {
                    $str .= strtoupper($k).': <a href="'.$v.'" title="download">'.$v.'</a>'.BR;
                }
			}
		}
		$str .= BR.'---';

		$str = '<html><head><style>html, body {font-family:helvetica, arial, sans-serif;}</style></head><body>'.$str.'</body></html>';

		return iconv(mb_detect_encoding($str, mb_detect_order(), true), "UTF-8//ignore", $str);
	}

    /**
	 * Check if a string contains a blacklisted item
	 */
	public function spam_check(int $id_area, string $string) : int
	{
        $string = strtolower($string);
        // get a string of all blacklist items
		$bad_words = X4Array_helper::indicize(
            $this->db->query('SELECT name, xlock
                FROM x3_forms_blacklist
                WHERE id_area = '.$id_area.' AND xon = 1')
            , 'name'
        );

        $words = implode('|', array_keys($bad_words));
        $matches = array();
        $match_found = preg_match_all(
                        "/(" . $words . ")/i",
                        $string,
                        $matches
                    );

        if ($match_found)
        {
            // spam found
            foreach ($matches[0] as $i)
            {
                if (isset($bad_words[$i]) && $bad_words[$i]->xlock)
                {
                    return 2;
                }
            }
            return 1;
        }
        return 0;
	}

    /**
	 * build message xml format
     * for special needs but not used here
	 */
	public function messagize_xml($form,  $fields, $files = array()) : string
	{
		$str = '<form>'.$form.'</form>
			<date>'.date('Y-m-d H:i:s').'</date>';
		foreach ($fields as $k => $v)
		{
			if ($k != strrev($form)) {
				if (is_array($v)) {
					// parent
					$str .= '<'.strtolower($k).'>';
					foreach ($v as $i)
					{
						$str .= '<value>'.$i.'</value>';
					}
					$str .= '</'.strtolower($k).'>';
				}
				else
				{
					if ($k != 'x4token')
					{
						$str .= '<'.strtolower($k).'>'.$v.'</'.strtolower($k).'>';
					}
				}
			}
		}

		// for files
		if (!empty($files))
		{
			foreach ($files as $k => $v)
			{
				if (!empty($v)) $str .= '<'.strtolower($k).'>'.$v.'</'.strtolower($k).'>';
			}
		}
		return mb_convert_encoding($str, 'ISO-8859-1', 'auto');
	}

}

class Obj_form {
	public $id_area;
	public $lang;
	public $name;
	public $title;
	public $description;
	public $mailto = '';
	public $msg_ok = '';
	public $msg_failed = '';
	public $submit_button;
	public $reset_button;
    public $xlock = 0;

	public function __construct($id_area, $lang)
	{
		$this->id_area = $id_area;
		$this->lang = $lang;
	}
}

class Obj_field {
	public $id_area;
	public $lang;
	public $id_form;
	public $xtype;
	public $label;
	public $name;
	public $value;
	public $suggestion;
	public $rule = '';
	public $extra = '';
    public $xlock = 0;

	public function __construct($id_area, $lang, $id_form)
	{
		$this->id_area = $id_area;
		$this->lang = $lang;
		$this->id_form = $id_form;
	}

}

class Obj_blackitem {
	public $id_area;
	public $lang;
    public $name;
    public $xlock = 0;

	public function __construct($id_area, $lang)
	{
		$this->id_area = $id_area;
		$this->lang = $lang;
	}

}
