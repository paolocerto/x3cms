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
 * Controller for Group items
 *
 * @package X3CMS
 */
class Groups_controller extends X3ui_controller
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
	 * Change status
	 */
	public function set(string $what, int $id_area, int $id, int $value = 0) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($id_area, 'xgroups', $id, $what);
		if (is_null($msg))
		{
			// do action
			$group = new Group_model();
			$result = $group->update($id, array($what => $value));

			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);

			// set update
			if ($result[1])
            {
				$msg->update = array(
					'element' => 'page',
					'url' => $_SERVER['HTTP_REFERER']
				);
            }
		}
		$this->response($msg);
	}

	/**
	 * New / Edit group form
	 */
	public function edit(int $id = 0) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'groups'));

		// get object
		$mod = new Group_model();
		$item = ($id)
			? $mod->get_by_id($id)
			: new Group_obj();

        // build the form
		$form_fields = new X4Form_core('group/group_edit');
		$form_fields->id = $id;
		$form_fields->item = $item;
        $form_fields->mod = new Area_model();

		// get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->editing($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        $view = new X4View_core('modal');
        $view->title = ($id)
			? _EDIT_GROUP
			: _ADD_GROUP;

		// contents
		$view->content = new X4View_core('editor');
        // can user edit?
        $submit = AdmUtils_helper::submit_btn($item->id_area, 'xgroups', $id, $item->xlock);
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, $submit, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');
		$view->render(true);
	}

	/**
	 * Register Edit / New group
	 */
	private function editing(array $_post) : void
	{
		$msg = null;
		// check permission
		$msg = ($_post['id'])
			? AdmUtils_helper::chk_priv_level($_post['id_area'], 'groups', $_post['id'], 'edit')
			: AdmUtils_helper::chk_priv_level($_post['id_area'], '_group_creation', 0, 'create');

		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'name' => $_post['name'],
				'id_area' => $_post['id_area'],
				'description' => $_post['description']
			);

			// update or insert
			$mod = new Group_model();
			$result = ($_post['id'])
                ? $mod->update($_post['id'], $post)
                : $mod->insert($post);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// set what update
			if ($result[1])
			{
                if (!$_post['id'])
                {
                    // permissions
                    $perm = new Permission_model();
                    $array[] = array(
                            'action' => 'insert',
                            'id_what' => $result[0],
                            'id_user' => $_SESSION['xuid'],
                            'level' => 4);
                    $perm->pexec('xgroups', $array, $post['id_area']);
                }

				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'users'
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Edit group permission
	 */
	public function gperm(int $id_group) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'groups'));

		// get objects (group permissions)
		$mod = new Permission_model();
		$gprivs = X4Array_helper::obj2array($mod->get_gprivs($id_group), 'what', 'level');

		// get area data
		$group_id_area = $mod->get_var($id_group, 'xgroups', 'id_area');
		$private = $mod->get_var($group_id_area, 'areas', 'private');

		// build the form
		$form_fields = new X4Form_core('group/group_privs');
		$form_fields->id_group = $id_group;
		$form_fields->gprivs = $gprivs;
        $form_fields->private = $private;
        // available permission levels
        $form_fields->levels = $mod->get_levels();
        // registered group permissions
        $form_fields->types = $mod->get_privtypes($private);

		// get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->permitting($group_id_area, $_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
        $view = new X4View_core('modal');
        $view->title = _GROUP_PERMISSION;
        $view->wide = ' xl:w-2/3';

		// contents
		$view->content = new X4View_core('editor');
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');
		$view->render(true);
	}

	/**
	 * Register edited group permissions
	 */
	private function permitting(int $id_area, array $_post) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($id_area, 'xgroups', $_post['id'], 'edit');

		if (is_null($msg))
		{
			// get all available permissions
			$perm = new Permission_model();
			$types = $perm->get_privtypes($_post['xrif']);

			// build action arrays
			$insert = $update = $delete = array();
			foreach ($types as $i)
			{
				if (isset($_post[$i->name]) && $_post[$i->name] != $_post['old_'.$i->name])
				{
					if ($_post[$i->name])
					{
						// insert or update
						if ($_post['old_'.$i->name])
                        {
							$update[$i->name] = $_post[$i->name];
                        }
						else
                        {
							$insert[$i->name] = $_post[$i->name];
                        }
					}
					else
                    {
						$delete[] = $i->name;
                    }
				}
			}

			// update privs
			$result = $perm->update_gprivs($_post['id'], $insert, $update, $delete);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// set what update
			if ($result[1])
			{
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'users'
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Delete Group form
	 */
	public function delete(int $id) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'groups'));

        // get object
		$group = new Group_model();
		$item = $group->get_by_id($id, 'xgroups', 'id, id_area, name');

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
        $view->title = _DELETE_GROUP;

		// contents
		$view->content = new X4View_core('delete');
		$view->content->item = $item->name;
		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

	/**
	 * Delete Group
	 */
	private function deleting(stdClass $item) : void
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($item->id_area, 'xgroups', $item->id, 'delete');

		if (is_null($msg))
		{
			// action
			$group = new Group_model();
			$result = $group->delete($item->id);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// clear useless permissions
			if ($result[1])
			{
				$perm = new Permission_model();
				$perm->deleting_by_what('xgroups', $item->id);

				// set what update
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'users'
				);
			}
		}
		$this->response($msg);
	}
}
