<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */

/**
 * Model for Plugins
 *
 * @package X3CMS
 */
class X4Plugin_model extends X4Model_core 
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('modules');
	}
	
	/**
	 * Get installed plugins by area
	 *
	 * @param integer	area ID
	 * @param integer	hidden status
	 * @return array	array of objects
	 */
	public function get_modules($id_area, $hidden = 2)
	{
		$where = ($hidden < 2)
			? ' AND hidden = '.intval($hidden)
			: '';
			
		return $this->db->query('SELECT DISTINCT m.*, p.level 
			FROM modules m
			JOIN privs p ON p.id_who = '.intval($_SESSION['xuid']).' AND p.what = \'modules\' AND p.id_what = m.id
			WHERE m.id_area = '.intval($id_area).' AND m.xon = 1 '.$where.' ORDER BY m.name ASC');
	}
	
	/**
	 * Get plugin's parameters
	 *
	 * @param string	plugin name
	 * @param integer	area ID
	 * @return array	array of parameter objects
	 */
	public function get_param($plugin_name, $id_area)
	{
		return $this->db->query('SELECT DISTINCT pa.*, p.level 
			FROM param pa
			JOIN modules m ON m.name = pa.xrif AND m.id_area = pa.id_area
			JOIN privs p ON p.id_who = '.intval($_SESSION['xuid']).' AND p.what = \'modules\' AND p.id_what = m.id
			WHERE pa.xrif = '.$this->db->escape($plugin_name).' AND p.id_area = '.intval($id_area).'
			ORDER BY pa.id ASC');
	}
	
	/**
	 * Update parameters after a configure
	 *
	 * @param array		array of associative array (id parameter => value)
	 * @return array	(0, boolean update result)
	 */
	public function update_param($array)
	{
		if (empty($array)) 
			return array(0, 1);
		else 
		{
			$sql = array();
			foreach($array as $k => $v) 
			{
				$sql[] = 'UPDATE param SET updated = NOW(), xvalue =  '.$this->db->escape($v).' WHERE id = '.intval($k);
			}
			return $this->db->multi_exec($sql);
		}
	}
	
	/**
	 * Installed plugins by area with privileges
	 *
	 * @param integer	area ID
	 * @return array	array of plugin objects
	 */
	public function get_installed($id_area)
	{
		return $this->db->query('SELECT DISTINCT m.*, p.level 
			FROM modules m
			JOIN privs p ON p.id_who = '.intval($_SESSION['xuid']).' AND p.what = \'modules\' AND p.id_what = m.id
			WHERE p.id_area = '.intval($id_area).' ORDER BY m.name ASC');
	}
	
	/**
	 * Installable plugins
	 *
	 * @param integer	area ID
	 * @return array	array of plugin paths
	 */
	public function get_installable($id_area)
	{
		// uploaded plugins
		$plugins = glob(PATH.'plugins/*', GLOB_ONLYDIR);
		// installed
		$installed = $this->get_installed($id_area);
		$ed = array();
		foreach($installed as $i) 
		{
			$ed[] = PATH.'plugins/'.$i->name;
		}
		return array_diff($plugins, $ed);
	}
	
	/**
	 * Check plugin requirements
	 *
	 * @param array		array of strings, names of requirements
	 * @param integer	area ID
	 * @param boolean	if true check if needed else check if required
	 * @return array	array of error strings
	 */
	private function check_required($array, $id_area, $value)
	{
		$error = array();
		$msg = ($value) ? '_plugin_needed_by' : '_required_plugin';
		foreach($array as $i)
		{
			if ($this->exists($i, $id_area) == $value) 
				$error[] = array('error' => array($msg), 'label' => $i);
		}
		return $error;
	}
	
	/**
	 * Install a plugin
	 *
	 * @param integer	area ID
	 * @param string	plugin name (is the same name of the folder)
	 * @return mixed	integer if all runs fine, else an array of error strings
	 */
	public function install($id_area, $name)
	{
		$error = array();
		if (!$this->exists($name, $id_area)) 
		{
			if (file_exists(PATH.'plugins/'.$name.'/install.php')) 
			{
				// area name, required with some installer
				$area = $this->get_by_id($id_area, 'areas', 'name');
				
				// load installer
				require_once(PATH.'plugins/'.$name.'/install.php');
				
				// check requirements
				$error = $this->check_required($required, $id_area, 0);
				
				// check area requirements
				if (isset($area_limit) && !in_array($area->name, $area_limit))
				{
					$error[] = array('error' => array('_incompatible_area'), 'label' => implode(', ', $area_limit));
				}
				
				// check compatibility
				if (!isset($compatibility) || !$this->compatibility($compatibility)) 
				{
					$error[] = array('error' => array('_incompatible_plugin'), 'label' => $name);
				}
				
				if (empty($error)) 
				{
					// global queries
					if (!$this->exists($name, $id_area, 1)) 
					{
						foreach ($sql0 as $i) 
						{
							$result = $this->db->single_exec($i);
						}
					}
					
					// area dipendent queries
					foreach ($sql1 as $i) 
					{
						$result = $this->db->single_exec($i);
					}
					
					if ($result[1]) 
					{
						// initialize Mongo DB autoincrement index
						if (isset($sql2))
						{
							$model = $sql2['model'];
							$mod = new $model();
							$res = $mod->insert($sql2['index'], 'indexes');
						}
						
						$perm = new Permission_model();
						$perm->refactory($_SESSION['xuid']);
						// return an integer if installation run fine
						return $result[0];
					}
					else 
					{
						$error[] = array('error' => array('_plugin_not_installed'), 'label' => $name);
					}
				}
			}
			else 
			{
				$error[] = array('error' => array('_missing_plugin_installer'), 'label' => $name);
			}
		}
		else 
		{
			$error[] = array('error' => array('_already_installed'), 'label' => $name);
		}
		// return an array if happen an error
		return $error;
	}
	
	/**
	 * Check plugin compatibility
	 *
	 * @param string	plugin compatibility
	 * @return boolean	compatibility value
	 */
	private function compatibility($version)
	{
		$c = true;
		// cms version
		$cms_version = $this->db->query_var('SELECT version FROM sites WHERE id = 1');
		$cv = explode(' ', $cms_version);
		// supported version
		$sv = explode(' ', $version);
		// possible values
		$a = array('ALFA', 'BETA1', 'BETA2', 'BETA3', 'BETA4', 'RC1', 'RC2', 'RC3', 'STABLE');
		
		// release number
		if ($sv[0] > $cv[0]) 
			$c = false;
		elseif ($sv[0] == $cv[0]) 
		{
			$b = array_flip($a);
			if ($b[strtoupper($sv[1])] > $b[strtoupper($cv[1])]) 
				$c = false; 
		}
		return $c;
	}
	
	/**
	 * Uninstall a plugin
	 * if the plugin was installed into many areas then uninstall only from one area
	 * delete only dictionary and records related to this area 
	 * else uninstall all, drop tables and delete all administration items
	 *
	 * @param integer	plugin ID
	 * @return mixed	integer if all runs fine, else an array of error strings
	 */
	public function uninstall($id)
	{
		$error = array();
		$plugin = $this->get_by_id($id);
		if ($this->exists($plugin->name, $plugin->id_area, 1)) 
		{
			// area uninstall
			if (file_exists(PATH.'plugins/'.$plugin->name.'/area_uninstall.php')) 
			{
				require_once(PATH.'plugins/'.$plugin->name.'/area_uninstall.php');
				$error = $this->check_required($required, $plugin->id_area, 1);
			}
		}
		else 
		{
			// global uninstall
			if (file_exists(PATH.'plugins/'.$plugin->name.'/global_uninstall.php')) 
			{
				require_once(PATH.'plugins/'.$plugin->name.'/global_uninstall.php');
				$error = $this->check_required($required, $plugin->id_area, 1);
			}
		}
		if (empty($error)) 
		{
			$result = $this->db->multi_exec($sql);
			
			if ($result[1])
			{
				// initialize Mongo DB autoincrement index
				if (isset($sql2))
				{
					$model = $sql2['model'];
					$mod = new $model();
					if (isset($sql2['data']))
					{
						// delete item from area
						$mod->multiple_delete($sql2['collection'], $sql2['data']);
					}
					else
					{
						// drop the collection
						$res = $mod->drop($sql2['collection']);
					}
				}
			}
			
			return $result[1];
		}
		else 
			return $error;
	}
	
	/**
	 * Check if a plugin is already installed into an area
	 *
	 * @param string	plugin name
	 * @param integer	area ID
	 * @param boolean	if true check if installed into area else check if installed into others area
	 * @param integer	check the status
	 * @return integer	number of installed plugins
	 */
	public function exists($plugin_name, $id_area, $global = false, $status = 2)
	{
		// condition
		$where = ($global) 
			? ' AND id_area <> '.intval($id_area) 
			: ' AND id_area = '.intval($id_area);
		
		// status
		if ($status < 2)
			$where .= ' AND xon = '.intval($status);
			
		return $this->db->query_var('SELECT COUNT(id) FROM modules WHERE name = '.$this->db->escape($plugin_name).' '.$where);
	}
	
	/**
	 * Check if a plugin is installed and enabled
	 *
	 * @param string	plugin name
	 * @param integer	area ID
	 * @return integer	number of installed plugins
	 */
	public function usable($plugin_name, $id_area)
	{
		return $this->db->query_var('SELECT xon FROM modules WHERE name = '.$this->db->escape($plugin_name).' AND id_area = '.intval($id_area));
	}
	
	/**
	 * Get installed plugins into an area which supports search
	 *
	 * @param integer	area ID
	 * @return array	array of objects
	 */
	public function get_searchable($id_area)
	{
		return $this->db->query('SELECT name 
			FROM modules 
			WHERE 
				id_area = '.$this->db->escape($id_area).' AND 
				xon = 1 AND
				searchable = 1');
	}
	
	/**
	 * Check plugin redirects
	 *
	 * @param array		array of models
	 * @return mixed
	 */
	public function check_redirect($array, $url)
	{
	    $redirect = null;
		foreach($array as $i)
		{
		    $mod = new $i();
		    $redirect = $mod->check_redirect($url);
		    if ($redirect)
		    {
		        break;
		    }
		}
		return $redirect;
	}
}
