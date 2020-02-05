<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
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
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('templates');
	}
	
	/**
	 * Get installed templates by Theme ID
	 * Join with privs
	 *
	 * @param   integer $id_theme Theme ID
	 * @return  array	Array of objects
	 */
	public function get_tpl_installed($id_theme)
	{
		return $this->db->query('SELECT DISTINCT t.*, IF(p.id IS NULL, u.level, p.level) AS level
			FROM templates t
			JOIN uprivs u ON u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('templates').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = t.id
			WHERE t.id_theme = '.intval($id_theme).'
			GROUP BY t.id
			ORDER BY t.sections ASC');
	}
	
	/**
	 * Get installable templates by Theme ID and Theme name
	 *
	 * @param   integer $id_theme Theme ID
	 * @param   string	$name Theme name (theme folder name)
	 * @return  array	Array of strings
	 */
	public function get_tpl_installable($id_theme, $name)
	{
		// templates path
		$path = realpath('themes/'.$name.'/templates');
		
		// uploaded templates
		$tpls = array();
		foreach (glob($path.'/*') as $i) 
		{
			$tpls[] = str_replace('.php', '', $i);
		}
		
		// installed templates
		$installed = $this->db->query('SELECT *	FROM templates WHERE id_theme = '.intval($id_theme).' ORDER BY name ASC');
		$ed = array();
		foreach($installed as $i) 
		{
			$ed[] = $path.'/'.$i->name;
		}
		
		// return difference
		return array_diff($tpls, $ed);
	}
	
	/**
	 * Install a new template
	 *
	 * @param   integer $id_theme Theme ID
	 * @param   string	$name Theme name (theme folder name)
	 * @return  array	Array of errors
	 */
	public function install_tpl($id_theme, $name)
	{
		$error = array();
		
		// check if already installed
		if ($this->exists($id_theme, $name)) 
			$error[] = array('error' => '_ALREADY_INSTALLED', 'label' => $name);
		else
		{
			// check if template file exists 
			if (file_exists('themes/'.$name.'_install.php')) 
			{
				// load template installer (SQL instructions)
				require_once('themes/'.$name.'_install.php');
				
				// install
				$result = $this->db->single_exec($sql);
				if ($result[1]) 
				{
					return $result[0];
				}
				else
					$error[] = array('error' => '_TEMPLATE_NOT_INSTALLED', 'label' => $name);
			}
			else 
				$error[] = array('error' => '_TEMPLATE_INSTALLER_NOT_FOUND', 'label' => $name);
		}
		
		return $error;
	}
	
	/**
	 * Uninstall a template
	 *
	 * @param   integer $id Template ID
	 * @param   string	$name Template name
	 * @return  array	Array of errors
	 */
	public function uninstall($id, $name)
	{
		// get object
		$tpl = $this->get_by_id($id);
		
		$error = array();
		// base is the default template and cannot be uninstalled
		if ($tpl->name != 'base') 
		{
			$result = $this->db->single_exec('DELETE FROM templates WHERE id = '.intval($id));
			if ($result[1])
				return 1;
			else 
				$error[] = array('error' => '_TEMPLATE_NOT_UNINSTALLED', 'label' => $name);
		}
		else 
			$error[] = array('error' => '_DEFAULT_TEMPLATE_CANT_BE_UNINSTALLED', 'label' => $name);
		return $error;
	}
	
	/**
	 * Get CSS name of a template
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$tpl Template name
	 * @return  string
	 */
	public function get_css($id_area, $tpl)
	{
		return $this->db->query_var('SELECT t.css 
			FROM templates t 
			INNER JOIN themes th ON th.id = t.id_theme
			INNER JOIN areas a ON a.id_theme = th.id AND a.id = '.intval($id_area).'
			WHERE t.name = '.$this->db->escape($tpl));
	}
	
	/**
	 * Check if a template is already installed in a theme
	 *
	 * @param   integer $id_theme Theme ID
	 * @param   string	$name Template name
	 * @param   integer $id Template ID
	 * @return  integer
	 */
	private function exists($id_theme, $name, $id = 0)
	{
		// condition
		$where = ($id) 
			? ' AND id <> '.intval($id)
			: '';
			
		return $this->db->query_var('SELECT COUNT(id) 
			FROM templates 
			WHERE id_theme = '.intval($id_theme).' AND name = '.$this->db->escape($name).' '.$where);
	}
}
