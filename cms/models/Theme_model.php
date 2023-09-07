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
 * Model for Theme Items
 *
 * @package X3CMS
 */
class Theme_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('themes');
	}

	/**
	 * Get installed themes
	 * Join with privs and areaas
	 *
	 * @param   integer $xon, status index
	 * @return  array	Array of objects
	 */
	public function get_installed(int $xon = 2)
	{
		// condition
		// if xon == 2 get enabled and disabled themes
		$where = ($xon < 2)
			? ' WHERE t.xon = '.$xon
			: '';

		return $this->db->query('SELECT t.*, a.title AS area, IF(p.id IS NULL, u.level, p.level) AS level
			FROM themes t
			JOIN uprivs u ON u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('themes').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = t.id AND p.level > 0
			LEFT JOIN areas a ON a.id_theme = t.id
			'.$where.'
			GROUP BY t.id
			ORDER BY t.name ASC');
	}

	/**
	 * Get installable themes
	 *
	 * @return  array	Array of strings
	 */
	public function get_installable()
	{
		// path
		$path = PATH.'themes';

		// uploaded themes
		$themes = glob($path.'/*', GLOB_ONLYDIR);

		// installed themes
		$installed = $this->db->query('SELECT *	FROM themes ORDER BY name ASC');
		$ed = array();
		foreach ($installed as $i)
		{
			$ed[] = $path.'/'.$i->name;
		}

		return array_diff($themes, $ed);
	}

	/**
	 * Install a new theme
	 *
	 * @param   string	$name Theme name (theme folder name)
	 * @return  array	Array of errors
	 */
	public function install(string $name)
	{
		$error = array();

		if ($this->exists($name) == 0 && file_exists('themes/'.$name.'/install.php'))
		{
			// load installer (arrays with SQL instructions)
			require_once('themes/'.$name.'/install.php');

			// install
			$result = $this->db->single_exec($sql);
			if ($result[1])
			{
				$sql = array();

				// templates
				foreach ($templates as $i)
					$sql[] = str_replace('XXX', $result[0], $i);

				// menus
				foreach ($menus as $i)
					$sql[] = str_replace('XXX', $result[0], $i);

				$res = $this->db->multi_exec($sql);
				return $result[0];
			}
			else
            {
				$error[] = array('error' => '_theme_not_installed', 'label' => $name);
            }
		}
		else
        {
			$error[] = array('error' => '_already_installed', 'label' => $name);
        }
		return $error;
	}

	/**
	 * Uninstall a theme
	 *
	 * @param   integer $id_theme Theme ID
	 * @param   string	$name Theme name
	 * @return  array	Array of errors
	 */
	public function uninstall(int $id_theme, string $name)
	{
		$error = array();

		if (file_exists('themes/'.$name.'/uninstall.php'))
		{
			// load uninstaller (SQL instructions)
			require_once('themes/'.$name.'/uninstall.php');

			$result = $this->db->multi_exec($sql);
			if ($result[1])
            {
				return 1;
            }
			else
            {
				$error[] = array('error' => '_THEME_NOT_UNINSTALLED', 'label' => $name);
            }
		}
		else
        {
			$error[] = array('error' => '_THEME_NOT_UNINSTALLED', 'label' => $name);
        }
		return $error;
	}

	/**
	 * Check if a theme is already installed
	 *
	 * @param   string	$name Theme name
	 * @param   integer $id Theme ID
	 * @return  integer
	 */
	private function exists(string $name, int $id = 0)
	{
		// condition
		$where = ($id)
			? ' AND id <> '.$id
			: '';

		return $this->db->query_var('SELECT COUNT(id)
			FROM themes
			WHERE name = '.$this->db->escape($name).' '.$where);
	}

	/**
	 * Get CSSs of all templates in a theme
	 *
	 * @param   integer $id_theme Theme ID
	 * @return  array	Array of objects
	 */
	public function get_css(int $id_theme)
	{
		return $this->db->query('SELECT DISTINCT css
			FROM templates
			WHERE id_theme = '.$id_theme);
	}

	/**
	 * Get JSs of all templates in a theme
	 *
	 * @param   integer $id_theme Theme ID
	 * @return  array	Array of objects
	 */
	public function get_js(int $id_theme)
	{
		return $this->db->query('SELECT DISTINCT js
			FROM templates
			WHERE id_theme = '.$id_theme);
	}

	/**
	 * Minimize a CSS
	 *
	 * @param   string 	$str A CSS
	 * @return  string
	 */
	public function compress_css(string $str)
	{
		// Remove comments
		$str = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $str);

		// Remove space after colons
		$str = str_replace(
				array(': ', ', ', ' {', '{ ', ' }', ';}'),
				array(':', ',', '{', '{', '}', '}'),
				$str
			);

		// Remove whitespace
		$str = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $str);
		return $str;
	}

	/**
	 * Minimize a JS
	 *
	 * @param   string 	$str A JS
	 * @return  string
	 */
	public function compress_js(string $str)
	{
		/* remove comments */
        $str = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", "", $str);
        /* remove tabs, spaces, newlines, etc. */
        $str = str_replace(array("\r\n","\r","\t","\n",'  ','    ','     '), '', $str);
        /* remove other spaces before/after ) */
        $str = preg_replace(array('(( )+\))','(\)( )+)'), ')', $str);

		return $str;
	}

}
