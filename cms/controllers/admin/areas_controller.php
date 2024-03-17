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
 * Controller for Area items
 *
 * @package X3CMS
 */
class Areas_controller extends X3ui_controller
{
	/**
	 * Constructor
	 * check if user is logged
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::logged();
	}

	/**
	 * Show areas (table view)
	 */
	public function _default() : void
	{
		$this->index($this->site->data->id);
	}

	/**
	 * Show areas (table view)
	 */
	public function index($id_site) : void
	{
		// load the dictionary
		$this->dict->get_wordarray(array('areas'));

		// get page
		$page = $this->get_page('areas');

		// contents
		$view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = AdminUtils_helper::link(
                'memo',
                'areas:'.$page->lang,
                [],
                _MEMO
            ).$this->actions();

		$view->content = new X4View_core('areas/areas');
		$view->content->page = $page;

		$mod = new Area_model();
        // sites
        $sites = $mod->get_my_sites();
        $view->content->sites = $sites;
        $view->content->id_site = $id_site;
        // areas
		list($id_area, $areas) = $mod->get_my_areas($id_site);
		$view->content->areas = $areas;

		$view->render(true);
	}

	/**
	 * Areas actions
	 */
	private function actions() : string
	{
		return '<a class="link" href="javascript:void(0)" @click="popup(\''.BASE_URL.'areas/edit\')" title="'._NEW_AREA.'">
            <i class="fa-solid fa-lg fa-circle-plus"></i>
        </a>';
	}

	/**
	 * Change status
	 */
	public function set(string $what, int $id, int  $value = 0) : void
	{
		$msg = null;
		// check permissions
		$msg = AdminUtils_helper::chk_priv_level($id, 'areas', $id, $what);
		if (is_null($msg))
		{
			// do action
			$mod = new Area_model();
			$result = $mod->update($id, array($what => $value));

			// set message
			$this->dict->get_words();
			$msg = AdminUtils_helper::set_msg($result);

			// set update
			if ($result[1])
			{
				$msg->update = array(
					'element' => 'page',
					'url' => $_SERVER['HTTP_REFERER']
				);
                // update tmp file with extra areas
				$mod->extra_areas();
			}
		}
		$this->response($msg);
	}

	/**
	 * New / Edit area form
	 */
	public function edit(int $id = 0) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'areas', 'themes'));

		// get area object
		$mod = new Area_model();
		$item = ($id)
			? $mod->get_area_data($id)
			: new Area_obj();

		// build the form
		$form_fields = new X4Form_core('area/area_edit');
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

        $view = new X4View_core('modal');
        $view->title = ($id)
			? _EDIT_AREA
			: _ADD_AREA;

		// contents
		$view->content = new X4View_core('editor');

        // can user edit?
        $submit = AdminUtils_helper::submit_btn($id, 'areas', $id, $item->xlock);
        // form builder
		$view->content->form = X4Form_helper::doform('editor', BASE_URL.'areas/edit/'.$id, $fields, array(_RESET, $submit, 'buttons'), 'post', '',
			'@click="submitForm(\'editor\')"');

		$view->render(true);
	}

	/**
	 * Register Edit / New Area
	 */
	private function editing(int $id_area, array $_post) : void
	{
		$msg = null;
		// check permissions
		$msg = ($id_area)
			? AdminUtils_helper::chk_priv_level($id_area, 'areas', $id_area, 'edit')
			: AdminUtils_helper::chk_priv_level(1, '_area_creation', 0, 'create');
		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'name' => X4Utils_helper::slugify($_post['name']),
				'title' => $_post['title'],
				'description' => $_post['description'],
                'id_site' => $_post['id_site'],
				'id_theme' => $_post['id_theme'],
				'private' => intval(isset($_post['private']) && $_post['private']),
				'folder' => $_post['folder'],
                'xdefault' => intval(isset($_post['xdefault']) && $_post['xdefault']),
			);

			$mod = new Area_model();

			// check if area name already exists
			$check = (boolean) $mod->exists($post['name'], $id_area);
			if ($check)
			{
				$msg = AdminUtils_helper::set_msg(false, '', $this->dict->get_word('_AREA_ALREADY_EXISTS', 'msg'));
			}
			else
			{
				// enable logs
				if (LOGS && DEVEL)
				{
					$mod->set_log(true);
				}

				// update or insert
				if ($id_area)
				{
					$result = $mod->update($id_area, $post);
				}
				else
				{
					$result = $mod->insert($post);
					if ($result[1])
					{
                        $this->permission_on_area($result[0]);
					}
				}

				if ($result[1])
				{
					// update lang and  theme settings
                    $this->update_lang_and_theme_settings($result[0], $_post);

                    // clear cache
					APC && apcu_clear_cache();
				}

				// set message
				$msg = AdminUtils_helper::set_msg($result);

				// set what update
				if ($result[1])
				{
                    // reset xdefault for other areas
                    if ($post['xdefault'])
                    {
                        $mod->reset_xdefault($post['id_site'], $id_area);
                    }

                    $msg->update = array(
                        'element' => 'page',
                        'url' => BASE_URL.'areas',
                    );
				}
			}
		}
		$this->response($msg);
	}

    /**
	 * Set permission on new area
	 */
    private function permission_on_area(int $id_area) : void
    {
        $perm = new Permission_model();

        // aprivs permissions
        $domain = X4Array_helper::obj2array($perm->get_aprivs($_SESSION['xuid']), '', 'id_area');
        $domain[] = $id_area;
        $perm->set_aprivs($_SESSION['xuid'], $domain);
        // uprivs premissions
        $perm->refactory($_SESSION['xuid']);
    }

    /**
	 * Update lang and theme settings
	 */
    private function update_lang_and_theme_settings(int $id_area, array $_post) : void
    {
        // refresh languages related to area
        $lang = new Language_model();
        $lang->set_alang($id_area, $_post['languages'], $_post['lang']);

        if ($id_area && $_post['id_theme'] != $_post['old_id_theme'])
        {
            $menu = new Menu_model();
            // reset tpl, css, id_menu, ordinal
            $menu->reset($id_area);
            $langs = $lang->get_languages();
            // restore ordinal
            foreach ($langs as $i)
            {
                $menu->ordinal($id_area, $i->code, 'home', 'A');
            }
            // reset section settings
            $section = new Section_model();
            // reset section settings
            $section->reset($id_area);
        }
    }

	/**
	 * SEO form
	 */
	public function seo(int $id_area) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'areas', 'themes'));

        // get the current data,
        // areas data relating to SEO are stored in 'alang'
        $mod = new Language_model();
        $items = $mod->get_seo_data($id_area);

		// build the form
		$form_fields = new X4Form_core('area/area_seo');
		$form_fields->id_area = $id_area;
		$form_fields->items = $items;

        // get the fields array
		$fields = $form_fields->render();

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
		$view = new X4View_core('modal');
		$view->title = _SEO_DATA;

        // contents
		$view->content = new X4View_core('editor');
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

    	$view->render(true);
	}

	/**
	 * Register SEO form
	 */
	private function editing_seo_data(int $id_area, array $_post) : void
	{
		$msg = null;
		// check permission
		$msg = AdminUtils_helper::chk_priv_level($id_area, 'areas', $id_area, 'edit');

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
            {
				$mod->set_log(true);
            }
			$result = $mod->update_seo_data($post);

			$msg = AdminUtils_helper::set_msg($result);

			// set what update
			if ($result[1])
			{
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'areas',
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Delete Area form
	 */
	public function delete(int $id) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'areas'));

		// get object
		$area = new Area_model();
		$item = $area->get_by_id($id, 'areas', 'id, name');

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
			$this->deleting($item);
			die;
		}

		$view = new X4View_core('modal');
        $view->title = _DELETE_AREA;
        // contents
        $view->content = new X4View_core('delete');
		$view->content->item = $item->name;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

	/**
	 * Delete area
	 */
	private function deleting(stdClass $item) : void
	{
		$msg = null;
		// check permissions
		$msg = AdminUtils_helper::chk_priv_level($item->id, 'areas', $item->id, 'delete');
		if (is_null($msg))
		{
			// action
			$area = new Area_model();
			$result = $area->delete_area($item->id, $item->name);

			// set message
			$msg = AdminUtils_helper::set_msg($result);

			// clear useless permissions
			if ($result[1])
			{
                AdminUtils_helper::delete_priv('areas', $item->id);

				// set what update
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'areas',
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Show area map
	 */
	public function map(int $id_area, string $lang) : void
	{
		// load the dictionary
		$this->dict->get_wordarray(array('areas'));

        $view = new X4View_core('modal');
        $view->title = _AREA_LANG_MAP;

		// content
		$view->content = new X4View_core('areas/map');

		$mod = new Page_model($id_area, $lang);
		$view->content->area = $mod->get_by_id($id_area, 'areas');
		$view->content->lang = $lang;
		$view->content->map = $this->site->get_map($mod->get_page('home'), false, false);

		$view->render(true);
	}

	/**
	 * Rename area (secret method)
	 * If for whatever reason you need to rename an area you can call this script
	 * /admin/areas/reaname_area/ID_AREA/NEW_NAME
	 */
	public function rename_area(int $id_area, string $new_name) : void
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
