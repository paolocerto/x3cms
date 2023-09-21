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
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::logged();
	}

	/**
	 * Change status
	 *
	 * @param   string  $what field to change
     * @param   integer $id_area
	 * @param   integer $id ID of the item to change
	 * @param   integer $value value to set (0 = off, 1 = on)
	 * @return  void
	 */
	public function set(string $what, int $id_area, int $id, int $value = 0)
	{
		$msg = null;
		// check permission
		$val = ($what == 'xlock')
			? 4
			: 3;
		$msg = AdmUtils_helper::chk_priv_level($id_area, $_SESSION['xuid'], 'xgroups', $id, $val);
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
	 * New / Edit group form (use Ajax)
	 *
	 * @param   integer  $id item ID (if 0 then is a new item)
	 * @return  void
	 */
	public function edit(int $id = 0)
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
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');
		$view->render(true);
	}

	/**
	 * Register Edit / New group form data
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing(array $_post)
	{
		$msg = null;
		// check permission
		$msg = ($_post['id'])
			? AdmUtils_helper::chk_priv_level($_post['id_area'], $_SESSION['xuid'], 'menus', $_post['id'], 2)
			: AdmUtils_helper::chk_priv_level($_post['id_area'], $_SESSION['xuid'], '_group_creation', 0, 4);

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
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'users'
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Edit group permission (use Ajax)
	 *
	 * @param   integer	$id_group Group ID
	 * @return  void
	 */
	public function gperm(int $id_group)
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
	 *
	 * @access	private
     * @param   integer $id_area
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function permitting(int $id_area, array $_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($id_area, $_SESSION['xuid'], 'xgroups', $_post['id'], 4);

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
	 * Delete Group form (use Ajax)
	 *
	 * @param   integer $id Group ID
	 * @return  void
	 */
	public function delete(int $id)
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
	 *
	 * @access	private
	 * @param   stdClass $item
	 * @return  void
	 */
	private function deleting(stdClass $item)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($item->id_area, $_SESSION['xuid'], 'xgroups', $item->id, 4);

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
