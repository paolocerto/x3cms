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
 * Model for Template Items
 *
 * @package X3CMS
 */
class Template_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 */
	public function __construct()
	{
		parent::__construct('templates');
	}

	/**
	 * Get installed templates by Theme ID
	 */
	public function get_tpl_installed(int $id_theme) : array
	{
		return $this->db->query('SELECT t.*, IF(p.id IS NULL, u.level, p.level) AS level
			FROM templates t
			JOIN uprivs u ON u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('templates').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = t.id
			WHERE t.id_theme = '.$id_theme.'
			GROUP BY t.id
			ORDER BY t.sections ASC');
	}

	/**
	 * Get installable templates by Theme ID and Theme name
	 */
	public function get_tpl_installable(int $id_theme, string $theme_name) : array
	{
		// templates path
		$path = realpath('themes/'.$theme_name.'/templates');

		// uploaded templates
		$tpls = array();
		foreach (glob($path.'/*') as $i)
		{
			$tpls[] = str_replace('.php', '', $i);
		}

		// installed templates
		$installed = $this->db->query('SELECT *	FROM templates WHERE id_theme = '.$id_theme.' ORDER BY name ASC');
		$ed = array();
		foreach ($installed as $i)
		{
			$ed[] = $path.'/'.$i->name;
		}

		// return difference
		return array_diff($tpls, $ed);
	}

	/**
	 * Install a new template
	 */
	public function install_tpl(int $id_theme, string $theme_name) : array
	{
		$error = [];
		if ($this->exists($id_theme, $theme_name))
        {
			$error[] = array('error' => '_ALREADY_INSTALLED', 'label' => $name);
        }
        else
		{
			// check if template file exists
			if (file_exists('themes/'.$theme_name.'_install.php'))
			{
				// load template installer (SQL instructions)
				require_once('themes/'.$theme_name.'_install.php');

				// install
				$result = $this->db->single_exec($sql);
				if ($result[1])
				{
					return $result[0];
				}
				else
                {
					$error[] = array('error' => '_TEMPLATE_NOT_INSTALLED', 'label' => $theme_name);
                }
			}
			else
            {
				$error[] = array('error' => '_TEMPLATE_INSTALLER_NOT_FOUND', 'label' => $theme_name);
            }
		}
		return $error;
	}

	/**
	 * Uninstall a template
	 */
	public function uninstall(int $id, string $tpl_name) : array
	{
		// get object
		$tpl = $this->get_by_id($id);

		$error = [];
		// base is the default template and cannot be uninstalled
		if ($tpl->name != 'base')
		{
			$result = $this->db->single_exec('DELETE FROM templates WHERE id = '.$id);
			if ($result[1])
            {
				return 1;
            }
			else
            {
				$error[] = array('error' => '_TEMPLATE_NOT_UNINSTALLED', 'label' => $tpl_name);
            }
		}
		else
        {
			$error[] = array('error' => '_DEFAULT_TEMPLATE_CANT_BE_UNINSTALLED', 'label' => $tpl_name);
        }
		return $error;
	}

	/**
	 * Get CSS name of a template
	 */
	public function get_css(int $id_area, string $tpl) : string
	{
		return (string) $this->db->query_var('SELECT t.css
			FROM templates t
			INNER JOIN themes th ON th.id = t.id_theme
			INNER JOIN areas a ON a.id_theme = th.id AND a.id = '.$id_area.'
			WHERE t.name = '.$this->db->escape($tpl));
	}

	/**
	 * Check if a template is already installed in a theme
	 */
	private function exists(int $id_theme, string $tpl_name, int $id = 0) : int
	{
		// condition
		$where = ($id)
			? ' AND id <> '.$id
			: '';

		return (int) $this->db->query_var('SELECT COUNT(*)
			FROM templates
			WHERE id_theme = '.$id_theme.' AND name = '.$this->db->escape($tpl_name).' '.$where);
	}

	/**
	 * Reset page's sections
	 * replace sections settings with the new template settings
	 * Called when you change the template of a page
	 */
	public function reset_sections(int $id_area, int $id_page, string $tpl_name) : void
	{
		// get id_theme
		$id_theme = $this->db->query_var('SELECT id_theme FROM areas WHERE id = '.$id_area);

		// get template data
		$tpl = $this->db->query_row('SELECT settings, sections FROM templates WHERE id_theme = '.$id_theme.' AND name = '.$this->db->escape($tpl_name));


		if ($tpl && !empty($tpl->settings))
		{
			$settings = json_decode($tpl->settings, true);

			// update the predefined sections
			for ($i = 1; $i <= $tpl->sections; $i++)
			{
				// if no settings in the template we will use default settings from section_model
				$set = (isset($settings['s'.$i]))
					? json_encode($settings['s'.$i])
					: '';
				$this->db->single_exec('UPDATE sections SET settings = '.$this->db->escape($set).' WHERE id_page = '.$id_page.' AND progressive = '.intval($i));
			}

			// check for extra sections
			$set = (isset($settings['sn']))
				? json_encode($settings['sn'])
				: '';

			$this->db->single_exec('UPDATE sections SET settings = '.$this->db->escape($set).' WHERE id_page = '.$id_page.' AND progressive > '.intval($tpl->sections));
		}
		else
		{
			// remove settings from all sections
			$this->db->single_exec('UPDATE sections SET settings = \'\' WHERE id_page = '.$id_page);
		}
	}
}
