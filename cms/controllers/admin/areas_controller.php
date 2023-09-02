<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

/**
 * Controller for Area items
 *
 * @package X3CMS
 */
class Areas_controller extends X3ui_controller
{
	/**
	 * Constructor
	 * check if user is logged
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::logged();
	}

	/**
	 * Show areas (table view)
	 *
	 * @return  void
	 */
	public function _default()
	{
		$this->index();
	}

	/**
	 * Show areas (table view)
	 *
	 * @return  void
	 */
	public function index()
	{
		// load the dictionary
		$this->dict->get_wordarray(array('areas'));

		// get page
		$page = $this->get_page('areas');
		$navbar = array($this->site->get_bredcrumb($page));

		// contents
		$view = new X4View_core('areas/areas');
		$view->page = $page;
		$view->navbar = $navbar;
		$mod = new Area_model();
		list($id_area, $areas) = $mod->get_my_areas();
		$view->areas = $areas;

		$view->render(TRUE);
	}

	/**
	 * Areas filter
	 *
	 * @return  void
	 */
	public function filter()
	{
		// load the dictionary
		$this->dict->get_wordarray(array('areas'));

		echo '<a class="btf" href="'.BASE_URL.'areas/edit/-1" title="'._NEW_AREA.'"><i class="fas fa-plus fa-lg"></i></a>
<script>
window.addEvent("domready", function()
{
	buttonize("filters", "btf", "modal");
});
</script>';
	}

	/**
	 * Change status
	 *
	 * @param   string	$what field to change
	 * @param   integer $id ID of the item to change
	 * @param   integer $value value to set (0 = off, 1 = on)
	 * @return  void
	 */
	public function set(string $what, int $id, int  $value = 0)
	{
		$msg = null;
		// check permissions
		$val = ($what == 'xlock')
			? 4
			: 3;

		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'areas', $id, $val);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();

			// do action
			$mod = new Area_model();
			$result = $mod->update($id, array($what => $value));

			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);

			// set update
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => $qs['div'],
					'url' => urldecode($qs['url']),
					'title' => null
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * New / Edit area form (use Ajax)
	 *
	 * @param   integer  $id item ID (if 0 then is a new item)
	 * @return  void
	 */
	public function edit(int $id = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'areas', 'themes'));

		// handle id
		$chk = false;
		if ($id < 0)
		{
			$id = 0;
			$chk = true;
		}

		// get area object
		$mod = new Area_model();
		$item = ($id)
			? $mod->get_area_data($id)
			: new Area_obj();

		// build the form
		$form_fields = new X4Form_core('area_edit');
		$form_fields->id = $id;
		$form_fields->item = $item;
        	$form_fields->mod = $mod;

		// get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->editing($id, $_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

		// contents
		$view = new X4View_core('editor');

		$view->title = ($id)
			? _EDIT_AREA
			: _ADD_AREA;

		// form builder
		$view->form = X4Form_helper::doform('editor', BASE_URL.'areas/edit/'.$id, $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
			'onclick="setForm(\'editor\');"');

		$view->js = '';

		if ($id > 0 || $chk)
		{
			$view->render(TRUE);
		}
		else
		{
			return $view->render();
		}
	}

	/**
	 * Register Edit / New Area form data
	 *
	 * @access	private
	 * @param   integer $id item ID (if 0 then is a new item)
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing(int $id_area, array $_post)
	{
		$msg = null;
		// check permissions
		$msg = ($id_area)
			? AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'areas', $id_area, 2)
			: AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_area_creation', 0, 4);

		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'lang' => $_post['lang'],
				'name' => X4Utils_helper::slugify($_post['name']),
				'title' => $_post['title'],
				'description' => $_post['description'],
				'id_theme' => $_post['id_theme'],
				'private' => intval(isset($_post['private'])) && $_post['private'],
				'folder' => $_post['folder']
			);

			$mod = new Area_model();

			// check if area name already exists
			$check = (boolean) $mod->exists($post['name'], $id_area);
			if ($check)
			{
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_AREA_ALREADY_EXISTS', 'msg'));
			}
			else
			{
				// Redirect checker
				$redirect = false;

				// enable logs
				if (LOGS && DEVEL)
				{
					$mod->set_log(true);
				}

				// update or insert
				if ($id_area)
				{
					$result = $mod->update($id_area, $post);

					if ($_post['id'] == 1 && X4Route_core::$lang != $post['lang'])
					{
						$redirect = true;
					}
				}
				else
				{
					$result = $mod->insert($post);

					// create permissions
					if ($result[1])
					{
						$id_area = $result[0];
						$perm = new Permission_model();

						// aprivs permissions
						$domain = X4Array_helper::obj2array($perm->get_aprivs($_SESSION['xuid']), null, 'id_area');
						$domain[] = $id_area;
						$res = $perm->set_aprivs($_SESSION['xuid'], $domain);
						// uprivs premissions
						$perm->set_uprivs($_SESSION['xuid'], $id_area, 'areas', 4);
					}
				}

				if ($result[1])
				{
					// refresh languages related to area
					$lang = new Language_model();
					$lang->set_alang($id_area, $_post['languages'], $_post['lang']);

					// update theme settings
					if ($_post['id'] && $_post['id_theme'] != $_post['old_id_theme'])
					{
						$menu = new Menu_model();
						// reset tpl, css, id_menu, ordinal
						$result = $menu->reset($_post['id']);
						$langs = $lang->get_languages();
						// restore ordinal
						foreach ($langs as $i)
						{
							$menu->ordinal($_post['id'], $i->code, 'home', 'A');
						}
						// reset section settings
						$section = new Section_model();
						// reset section settings
						$section->reset($_post['id']);
					}

                    			// clear cache
					APC && apcu_clear_cache();
				}

				// set message
				$msg = AdmUtils_helper::set_msg($result);

				// set what update
				if ($result[1])
				{
					if ($redirect)
					{
						$msg->update[] = array(
							'element' => 'topic',
							'url' => BASE_URL.'home/redirect/admin',
							'title' => null
						);
					}
					else
					{
						$msg->update[] = array(
							'element' => 'topic',
							'url' => BASE_URL.'areas',
							'title' => null
						);
					}
				}
			}
		}
		$this->response($msg);
	}

	/**
	 * SEO form data (use Ajax)
	 *
	 * @param   integer $id_area area ID
	 * @return  void
	 */
	public function seo(int $id_area)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'areas', 'themes'));

		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $id_area,
			'name' => 'id_area'
		);

		$fields[] = array(
			'label' => null,
			'type' => 'html',
			'value' => '<div id="accordion">'
		);

		// get the current data,
		// areas data relating to SEO are stored in 'alang'
		$lang = new Language_model();
		$m = $lang->get_seo_data($id_area);

		$c = 0;
		// for each enabled language
		foreach ($m as $i)
		{
			$fields[] = array(
				'label' => null,
				'type' => 'hidden',
				'value' => $i->id,
				'name' => 'id_'.$c
			);
			$fields[] = array(
				'label' => null,
				'type' => 'html',
				'value' => '<h4 class="context">'.ucfirst($i->language).'</h4><div class="section">'
			);
			$fields[] = array(
				'label' => _NAME,
				'type' => 'text',
				'value' => $i->title,
				'name' => 'title_'.$c,
				'rule' => 'required',
				'extra' => 'class="large"'
			);
			$fields[] = array(
				'label' => _DESCRIPTION,
				'type' => 'textarea',
				'value' => $i->description,
				'name' => 'description_'.$c
			);
			$fields[] = array(
				'label' => _KEYS,
				'type' => 'textarea',
				'value' => $i->keywords,
				'name' => 'keywords_'.$c
			);
			$fields[] = array(
				'label' => null,
				'type' => 'html',
				'value' => '</div>'
			);
			$c++;
		}

		$fields[] = array(
			'label' => null,
			'type' => 'html',
			'value' => '</div>'
		);

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->editing_seo_data($id_area, $_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

		// contents
		$view = new X4View_core('editor');
		$view->title = _SEO_DATA;

		// form builder
		$view->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
			'onclick="setForm(\'editor\');"');

		$view->js = '
<script>
window.addEvent("domready", function()
{
	saccordion("accordion", "#accordion h4", "#accordion .section");
});
</script>';

		$view->render(TRUE);
	}

	/**
	 * Register SEO form data
	 *
	 * @access	private
	 * @param   integer $id_area Area ID
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing_seo_data(int $id_area, array $_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'areas', $id_area, 2);

		if (is_null($msg))
		{
			// handle _POST
			$c = 0;
			$post = array();
			while (isset($_post['id_'.$c]))
			{
				$post[$_post['id_'.$c]] = array(
					'title' => $_post['title_'.$c],
					'description' => $_post['description_'.$c],
					'keywords' => $_post['keywords_'.$c]
				);
				$c++;
			}

			// areas data relating to SEO are stored in 'alang'
			$mod = new Language_model();

			// enable logs
			if (LOGS && DEVEL)
				$mod->set_log(true);

			$result = $mod->update_seo_data($post);

			$msg = AdmUtils_helper::set_msg($result);

			// set what update
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => 'topic',
					'url' => BASE_URL.'areas/index/1',
					'title' => null
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Delete Area form (use Ajax)
	 *
	 * @param   integer $id Area ID
	 * @return  void
	 */
	public function delete(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'areas'));

		// get object
		$area = new Area_model();
		$obj = $area->get_by_id($id, 'areas', 'name');

		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $id,
			'name' => 'id'
		);

		// if submitted
		if (X4Route_core::$post)
		{
			$this->deleting($id, $obj->name);
			die;
		}

		// contents
		$view = new X4View_core('delete');
		$view->title = _DELETE_AREA;
		$view->item = $obj->name;

		// form builder
		$view->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
			'onclick="setForm(\'delete\');"');
		$view->render(TRUE);
	}

	/**
	 * Delete area
	 *
	 * @access	private
	 * @param   integer	$id Area ID
	 * @param   string 	$name Area name
	 * @return  void
	 */
	private function deleting(int $id, string $name)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'areas', $id, 4);

		if (is_null($msg))
		{
			// action
			$area = new Area_model();
			$result = $area->delete_area($id, $name);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// clear useless permissions
			if ($result[1])
			{
				$perm = new Permission_model();
				$perm->deleting_by_what('areas', $id);

				// set what update
				$msg->update[] = array(
					'element' => 'topic',
					'url' => BASE_URL.'areas/index/1',
					'title' => null
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Show areas map (tree view)
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang language code
	 * @return  void
	 */
	public function map(int $id_area, string $lang)
	{
		// load the dictionary
		$this->dict->get_wordarray(array('areas'));

		// content
		$view = new X4View_core('areas/map');
		$mod = new Page_model($id_area, $lang);
		$view->area = $mod->get_by_id($id_area, 'areas');
		$view->lang = $lang;
		$view->map = $this->site->get_map($mod->get_page('home'), false, false);

		$view->render(TRUE);
	}

	/**
	 * Rename area (secret method)
	 * If for whatever reason you need to rename an area you can call this script
	 * /admin/areas/reaname_area/ID_AREA/NEW_NAME
	 *
	 * @param   integer $id_area Area ID to rename
	 * @param   string  $new_name New name to set
	 * @return  string
	 */
	public function rename_area(int $id_area, string $new_name)
	{
		// Comment the next row to enable the method
		die('Operation disabled!');

		$mod = new Area_model();

		// clean the new name
		$new = X4Utils_helper::slugify(urldecode($new_name), true);

		// check if already exists
		$chk = $mod->exists($new, $id_area);

		// get the old area name
		$old = $mod->get_var($id_area, 'areas', 'name');

		if (!$chk && $old && $old != $new && strlen($new) > 2)
		{
			// replace name
			$res = $mod->rename_area($id_area, $old, $new);

			if ($res[1])
			{
				echo '<h1>CONGRATULATIONS!</h1>';
				echo '<p>The changes on the database are applied.</p>';

				// print instructions for manual changes
				echo '<p>Follow this instructions to perform manual changes.</p>
				<ul>
					<li>Rename the folder /cms/controllers/'.$old.' to /cms/controllers/'.$new.'</li>
					<li>Rename the folder /cms/views/'.$old.' to /cms/views/'.$new.'</li>
					<li>In the file system/core/X4Route_core.php replace the old area name "'.$old.'" with the new "'.$new.'" in the static vars</li>
					<li>In the file cms/config/config.php replace the old area name "'.$old.'" with the new "'.$new.'" in the $default array</li>
				</ul>
				<p>Done!</p>

				<p>NOTE: this operation acts on the core system of the CMS, if you use plugins you have to check if they need to be changed.</p>';
			}
			else
			{
				echo '<h1>WARNING!</h1>';
				echo '<p>Something went wrong, changes are not applied.</p>';
			}
		}
		else
		{
			echo '<h1>WARNING!</h1>';

			if (!$old)
			{
				echo '<p>Not exists an area with ID '.$id_area.'.</p>';
			}
			else
			{
				if (strlen($new) < 3)
				{
					echo '<p>The new name "'.$new.'" is too short (the minimum is 3 chars).</p>';
				}

				if (!$chk)
				{
					echo '<p>An area with the same name "'.$new.'" already exists.</p>';
				}

				if ($old == $new)
				{
					echo '<p>The old name "'.$old.'" and the new name "'.$new.'" are equal.</p>';
				}
			}
		}
		die;
	}
}
