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
	public function get_modules(int $id_area, int $hidden = 2)
	{
		$where = ($hidden < 2)
			? ' AND hidden = '.intval($hidden)
			: '';

		return $this->db->query('SELECT m.*, IF(p.id IS NULL, u.level, p.level) AS level
			FROM modules m
			JOIN uprivs u ON u.id_area = m.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('modules').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = m.id
			WHERE m.id_area = '.intval($id_area).' AND m.pluggable = 1 AND m.xon = 1 '.$where.'
			GROUP BY m.id
			ORDER BY m.title ASC');
	}

	/**
	 * Get plugin's parameters
	 *
	 * @param string	plugin name
	 * @param integer	area ID
	 * @return array	array of parameter objects
	 */
	public function get_param(string $plugin_name, int $id_area)
	{
		return $this->db->query('SELECT DISTINCT pa.*, IF(p.id IS NULL, u.level, p.level) AS level
			FROM param pa
			JOIN modules m ON m.name = pa.xrif AND m.id_area = pa.id_area
			JOIN uprivs u ON u.id_area = m.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('modules').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = m.id
			WHERE pa.xrif = '.$this->db->escape($plugin_name).' AND pa.id_area = '.intval($id_area).'
			ORDER BY pa.id ASC');
	}

	/**
	 * Update parameters after a configure
	 *
	 * @param array		array of associative array (id parameter => value)
	 * @return array	(0, boolean update result)
	 */
	public function update_param(array $array)
	{
		if (empty($array))
        {
			return array(0, 1);
        }
		else
		{
			$sql = array();
			foreach ($array as $k => $v)
			{
				$sql[] = 'UPDATE param SET updated = NOW(), xvalue =  '.$this->db->escape($v).' WHERE id = '.intval($k);
			}
			return $this->db->multi_exec($sql);
		}
	}

    /**
	 * Update parameters by name
	 *
	 * @param integer   $id_area
     * @param string    $xrif
     * @param string    $name
     * @param mixed     $value
	 * @return array	(0, boolean update result)
	 */
	public function update_param_by_name(int $id_area, string $xrif, string $name, $value)
	{
		$sql = 'UPDATE param
            SET updated = NOW(), xvalue = '.$this->db->escape($value).'
            WHERE id_area = '.$id_area.' AND
            xrif = '.$this->db->escape($xrif).' AND
            name = '.$this->db->escape($name);

        return $this->db->single_exec($sql);
	}

	/**
	 * Installed plugins by area with privileges
	 *
	 * @param integer	area ID
	 * @return array	array of plugin objects
	 */
	public function get_installed(int $id_area)
	{
		return $this->db->query('SELECT DISTINCT m.*, up.level, IF(p.id IS NULL, up.level, p.level) AS adminlevel
		FROM modules m
		JOIN uprivs up ON up.id_area = m.id_area AND up.id_user = '.intval($_SESSION['xuid']).' AND (
			up.privtype = '.$this->db->escape('modules').' OR
			REPLACE(up.privtype, \'x3_\', \'x3\') = m.name OR
			REPLACE(up.privtype, \'x4_\', \'x4\') = m.name OR
			REPLACE(up.privtype, \'x5_\', \'x5\') = m.name OR
			(up.privtype = \'x3_forms\' AND m.name = \'x3form_builder\') OR
			up.privtype = \'x3_plugins\'
		) AND up.level > 1
		LEFT JOIN privs p ON p.id_who = up.id_user AND p.what = up.privtype AND p.id_what = m.id
		WHERE m.id_area = '.intval($id_area).' ORDER BY m.name ASC');
	}

	/**
	 * Installable plugins
	 *
	 * @param integer	area ID
	 * @return array	array of plugin paths
	 */
	public function get_installable(int $id_area)
	{
		// uploaded plugins
		$plugins = glob(PATH.'plugins/*', GLOB_ONLYDIR);
		// installed
		$installed = $this->get_installed($id_area);
		$a = array();
		foreach ($installed as $i)
		{
			$a[] = PATH.'plugins/'.$i->name;
		}
		return array_diff($plugins, $a);
	}

	/**
	 * Check plugin requirements
	 *
	 * @param array		array of strings, names of requirements
	 * @param integer	area ID
	 * @param int   	if true check if needed else check if required
	 * @return array	array of error strings
	 */
	private function check_required(array $array, int $id_area, int $value)
	{
		$error = array();
		$msg = ($value) ? '_plugin_needed_by' : '_required_plugin';
		foreach ($array as $i)
		{
			if ($this->exists($i, $id_area) == $value)
            {
				$error[] = array('error' => array($msg), 'label' => $i);
            }
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
	public function install(int $id_area, string $name)
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
	private function compatibility(string $version)
	{
		$c = true;
		// cms version (the same for all sites)
		$cms_version = $this->db->query_var('SELECT version FROM sites WHERE id = 1');
		$cv = explode(' ', $cms_version);
		// supported version
		$sv = explode(' ', $version);
		// possible values
		$a = array('ALFA', 'BETA1', 'BETA2', 'BETA3', 'BETA4', 'RC1', 'RC2', 'RC3', 'STABLE');

		// release number
		if ($sv[0] > $cv[0])
        {
			$c = false;
        }
		elseif ($sv[0] == $cv[0])
		{
			$b = array_flip($a);
			if ($b[strtoupper($sv[1])] > $b[strtoupper($cv[1])])
            {
				$c = false;
            }
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
	public function uninstall(int $id)
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
			return $result[1];
		}
		else
        {
			return $error;
        }
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
	public function exists(string $plugin_name, int $id_area, bool $global = false, int $status = 2)
	{
		// condition
		$where = ($global)
			? ' AND id_area <> '.intval($id_area)
			: ' AND id_area = '.intval($id_area);

		// status
		if ($status < 2)
        {
			$where .= ' AND xon = '.intval($status);
        }
		return $this->db->query_var('SELECT COUNT(id) FROM modules WHERE name = '.$this->db->escape($plugin_name).' '.$where);
	}

	/**
	 * Check if a plugin is installed and enabled
	 *
	 * @param string	plugin name
	 * @param integer	area ID
	 * @return integer	number of installed plugins
	 */
	public function usable(string $plugin_name, int $id_area)
	{
		return $this->db->query_var('SELECT xon FROM modules WHERE name = '.$this->db->escape($plugin_name).' AND id_area = '.intval($id_area));
	}

	/**
	 * Get installed plugins into an area which supports search
	 *
	 * @param integer	area ID
	 * @return array	array of objects
	 */
	public function get_searchable(int $id_area)
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
     * @param string    $url
	 * @return mixed
	 */
	public function check_redirect(array $array, string $url)
	{
	    $redirect = null;
		foreach ($array as $i)
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

    /**
	 * Get page URL by plugin name and parameter
	 *
	 * @param integer	area ID
	 * @param string	lang
	 * @param string	plugin name
	 * @param string	parameter value, accepts * wildcard
	 * @return string	page URL
	 */
	public function get_page_to(int $id_area, string $lang, string $modname, string $param = '')
	{
		// check APC
		$c = (APC)
			? apcu_fetch(SITE.'pageto'.$id_area.$lang.$modname.$param)
			: array();

		if (empty($c))
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
						a.date_in <= '.$this->now.' AND
						(a.date_out = 0 OR a.date_out >= '.$this->now.') AND
						a.module = '.$this->db->escape($modname).$where.'
					GROUP BY a.bid
					ORDER BY a.id DESC';

			$c = $this->db->query_var($sql);

			if (APC)
			{
				apcu_store(SITE.'pageto'.$id_area.$lang.$modname.$param, $c);
			}
		}
		return $c;
	}

	/**
	 * Duplicate modules for another language
	 * This method have to be arranged foe each website
	 *
	 * @return  array(array_of_installed_modules, res)
	 * /
	public function duplicate_modules_lang($id_area, $old_lang, $new_lang)
	{
	    // this list have to be adapted for each website
	    $modules = array(
	        'x3_forms' => null,
	        'x3_fields' => array('x3_forms' => 'id_form'),
	    );

	    $no_lang = array();

	    $images = array(

	    );

	    // those table are empty
	    $tables = array();

	    $path = APATH.'files/'.SPREFIX.'/filemanager/img/';

	    foreach ($modules as $k => $v)
	    {

echo 'MODULE = '.$k.BR;

	        if (is_null($v))
	        {
	            // simple case

	            // get data
	            $items = $this->db->query('SELECT * FROM '.$k.' WHERE id_area = '.intval($id_area).' AND lang = '.$this->db->escape($old_lang).' ORDER BY id ASC');

	            if ($items)
	            {
	                foreach ($items as $i)
	                {
	                    $post = (array) $i;

	                    // remove useless fields
	                    unset($post['id'], $post['updated']);

	                    // update
	                    $post['lang'] = $new_lang;

	                    // imges?
                        if (isset($images[$k]))
                        {
                            foreach ($images[$k] as $ii)
                            {
                                $file = X4Files_helper::get_final_name($path, $i->$ii);
                                $chk = @copy($path.$i->$ii, $path.$file);
                                if ($chk)
                                {
                                    $post[$ii] = $file;
                                }
                            }
                        }

	                    // insert
	                    $res = $this->insert($post, $k);

	                    if ($res[1])
	                    {
	                        $table[$k][$i->id] = $res[0];
	                    }
	                }
	            }

	        }
	        else
	        {
	            // dependency case

	            // get data
	            if (in_array($k, $no_lang))
	            {
	                $items = $this->db->query('SELECT * FROM '.$k.' WHERE id_area = '.intval($id_area).' ORDER BY id ASC');
	            }
	            else
	            {
	                $items = $this->db->query('SELECT * FROM '.$k.' WHERE id_area = '.intval($id_area).' AND lang = '.$this->db->escape($old_lang).' ORDER BY id ASC');
	            }

	            if ($items)
                {
                    foreach ($items as $i)
                    {
                        $post = (array) $i;

                        // remove useless fields
                        unset($post['id'], $post['updated']);

                        // update
                        if (!in_array($k, $no_lang))
                        {
                            $post['lang'] = $new_lang;
                        }

                        // relations
                        foreach ($v AS $t => $f)
                        {
                            if ($k == 'x3_tree' && $i->id_from == 0)
                            {

                            }
                            else
                            {
                                $post[$f] = $table[$t][$i->$f];
                            }
                        }

                        // imges?
                        if (isset($images[$k]))
                        {
                            foreach ($images[$k] as $ii)
                            {
                                $file = X4Files_helper::get_final_name($path, $i->$ii);
                                $chk = @copy($path.$i->$ii, $path.$file);
                                if ($chk)
                                {
                                    $post[$ii] = $file;
                                }
                            }
                        }

                        // insert
                        $res = $this->insert($post, $k);

                        if ($res[1])
	                    {
	                        $table[$k][$i->id] = $res[0];
	                    }
                    }
                }
	        }
	    }
	    return 1;
	}
*/
}
