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
 * Controller for User items
 *
 * @package X3CMS
 */
class Users_controller extends X3ui_controller
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
	 * Show groups
	 */
	public function _default() : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('groups', 'users', 'msg'));

		// get page
		$page = $this->get_page('users');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = $this->actions('group');

		// content
		$view->content = new X4View_core('users/group_list');
		$view->page = $page;

		$mod = new Group_model();
		$view->content->groups = $mod->get_groups();
		$view->render(true);
	}

	/**
	 * Show users in a group
	 */
	public function users(int $id_group) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('users'));

		$mod = new User_model();

		$group = $mod->get_var($id_group, 'xgroups', 'name');

        $view = new X4View_core('modal');
        $view->title = $group._TRAIT_._USERS_LIST;

		// content
		$view->content = new X4View_core('users/users_list');
		$view->content->items = $mod->get_users($id_group);
		$view->content->link = BASE_URL.'users/user';
		$view->content->class = 'class="link"';
		$view->content->title = 'username';
		$view->content->value = 'username';

		$view->render(true);
	}

	/**
	 * Group and Users actions
	 */
	private function actions(string $what, int $id_group = 0, int $id = 0) : string
	{
        if ($what == 'group')
        {
            return '<a class="link" @click="popup(\''.BASE_URL.'groups/edit\')" title="'._NEW_GROUP.'">
                <i class="fa-solid fa-lg fa-circle-plus"></i>
            </a>';
        }
        else
        {
            // get obj
            $mod = new User_model();
            $user = $mod->get_user_by_id($id);

            $statuses = AdmUtils_helper::statuses($user, ['xon', 'xlock']);

            $actions = '';

            // check permission
            if ((($user->plevel > 1 && $user->xlock == 0) || $user->plevel >= 3))
            {
                $actions = AdmUtils_helper::link('edit', 'users/edit/'.$user->id.'/'.$user->id_group);
                // manager or admin user
                if ($user->plevel > 2)
                {
                    $actions .= AdmUtils_helper::link('xon', 'users/set/xon/'.$user->id.'/'.(($user->xon+1)%2), $statuses);
                }

                // admin user
                if ($user->plevel >= 4)
                {
                    if ($user->hidden == 1)
                    {
                        $hide = _HIDE_USER;
			            $hide_status = 'off';
                    }
                    else
                    {
                        $hide = _SHOW_USER;
			            $hide_status = 'on';
                    }

                    $actions .= '<a class="link" @click="setter(\''.BASE_URL.'users/set/hidden/'.$user->id.'/'.(($user->hidden+1)%2).'\')" title="'._STATUS.' '.$hide.'">
                        <i class="fa-solid fa-lg fa-user '.$hide_status.'"></i>
                    </a>';
                    $actions .= AdmUtils_helper::link('xlock', 'users/set/xlock/'.$user->id.'/'.(($user->xon+1)%2), $statuses);
                    if ($user->id > 1 || $user->id == $_SESSION['xuid'])
                    {
                        $actions .= AdmUtils_helper::link('delete','users/delete/'.$user->id);
                    }
                }
            }
            return $actions;
        }
	}

	/**
	 * Change status
	 */
	public function set(string $what, int $id, int $value = 0) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level(1, 'users', $id, $what);
		if (is_null($msg))
		{
			// do action
			$mod = new User_model();
			$result = $mod->update($id, array($what => $value));

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
	 * Show user data
	 */
	public function user(int $id) : void
	{
	    // load dictionaries
		$this->dict->get_wordarray(array('users', 'form', 'login'));

		// check permission
		AdmUtils_helper::chk_priv_level(1, 'users', $id, 'read');

        // get user data
        $mod = new User_model();
        $user = $mod->get_user_by_id($id);

		// get page
		$page = $this->get_page('users/detail');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = $this->actions('user', $user->id_group, $id);

        $view->content = new X4View_core('users/user_detail');
        $view->content->user = $user;

        // get user privileges
        $perm = new Permission_model();
        $view->content->aprivs = $perm->get_aprivs($id);
		$view->render(true);
	}

	/**
	 * New / Edit user form
	 */
	public function edit(int $id, int $id_group = 0) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'login', 'users'));

		$lang = X4Route_core::$lang;

		// get object
		$mod = new User_model();
		$user = ($id)
			? $mod->get_by_id($id)
			: new User_obj($id_group, $lang);

		// get group
		$group = $mod->get_by_id($user->id_group, 'xgroups', 'id_area, name');

		// build the form
		$form_fields = new X4Form_core('user/user_edit');
		$form_fields->id = $id;
		$form_fields->user = $user;
        $form_fields->group = $group;
        $form_fields->levels = $mod->get_levels();
        // languages
		$mod = new Language_model();
        $form_fields->languages = $mod->get_languages();

        $mod = new Permission_model();
        $form_fields->aprivs = $mod->get_aprivs($id);
		$mod = new Area_model();
        $form_fields->areas = $mod->get_areas($this->site->data->id, $group->id_area);

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
			? _EDIT_USER
			: _ADD_USER;

		// contents
		$view->content = new X4View_core('editor');
        // can user edit?
        $submit = AdmUtils_helper::submit_btn(1, 'users', $id, $user->xlock);
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, $submit, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');
		$view->render(true);
	}

	/**
	 * Register Edit / New User
	 */
	private function editing(int $id, array $_post) : void
	{
		$msg = null;
		// check permission
		$msg = ($id)
			? AdmUtils_helper::chk_priv_level(1, 'users', $id, 'edit')
			: AdmUtils_helper::chk_priv_level(1, '_user_creation', 0, 'create');

		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'lang' => $_post['lang'],
				'id_group' => $_post['id_group'],
				'username' => $_post['username'],
				'description' => $_post['description'],
				'mail' => $_post['mail'],
				'phone' => $_post['phone'],
				'level' => $_post['level'],
			);

			// update password
			if (!empty($_post['password']))
			{
				$post['password'] = X4Utils_helper::hashing($_post['password']);
			}

			// check if an user with the same username or password already exists
			$mod = new User_model();
			$check = (boolean) $mod->exists($post['username'], $post['mail'], $id);
			if ($check)
			{
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_USER_ALREADY_EXISTS', 'msg'));
			}
			else
			{
                // for redirect
                $where = '';
			    $perm = new Permission_model();
                $perm->set_aprivs($id, $_post['domain']);
				if ($id)
				{
					$result = $mod->update($id, $post);
					$where = '/user/'.$id;
				}
				else
				{
					$result = $mod->insert($post);
					if ($result[1])
					{
						$id = $result[0];
                        $perm->set_uprivs($_SESSION['xuid'], $id, 'areas', $_post['level']);
					}
				}

				$msg = AdmUtils_helper::set_msg($result);

				if ($result[1])
				{
                    if (!$id)
                    {
                        Admin_utils_helper::set_priv($_SESSION['xuid'], $result[0], 'users', $post['id_area']);
                    }

					$msg->update = array(
						'element' => 'page',
						'url' => BASE_URL.'users'.$where
					);
				}
			}
		}
		$this->response($msg);
	}

	/**
	 * Edit user permission form
	 */
	public function perm(int $id_user, int $id_area, int $table = 0) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'groups', 'users'));

		$mod = new Permission_model();

		// user data
		$user = $mod->get_by_id($id_user, 'users', 'id_group, username');

		// build the form
		$form_fields = new X4Form_core('user/user_privs');
        $form_fields->id_area = $id_area;
		$form_fields->id_user = $id_user;
        $form_fields->table = $table;
		$form_fields->user = $user;
        // permission level
        $form_fields->levels = $mod->get_levels();
        // url for external script
        $form_fields->js_url = $this->site->data->domain.'/admin/users/set_for_all';
        // user permission
		$form_fields->what = $mod->get_uprivs($id_user, $id_area);

        // get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editpriv');
			if ($e)
			{
				$this->permitting($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        $view = new X4View_core('modal');
        $view->title = ($id_area)
			? _EDIT_PRIV.': '.$user->username
			: _EDIT_PRIV.': '._GLOBAL_PRIVS;

		// contents
		$view->content = new X4View_core('editor');

		// form builder
		$view->content->form = X4Form_helper::doform('editpriv', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editpriv\')"');

		$view->render(true);
	}

	/**
	 * Refresh upriv table and then privs
	 */
	private function permitting(array $_post) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level(1, 'users', $_post['id'], 'manage');

		if (is_null($msg))
		{
			// get privilege types
			$mod = new Permission_model();
			$types = $mod->get_privtypes(1);

			// check the differences
			$insert = $update = $delete = array();
			foreach ($types as $i)
			{
				// if the new value do not match the old value
				if (isset($_post[$i->name]) && $_post[$i->name] != $_post['old_'.$i->name])
				{
					// if the new value is greater than zero
					if ($_post[$i->name])
					{
						// update if the old value was greater than zero
						if ($_post['old_'.$i->name])
                        {
							$update[$i->name] = $_post[$i->name];
                        }
						else
						{
							// if old value was zero

							// delete old value
							$delete[$i->name] = $_post['old_'.$i->name];

							// insert new value
							$insert[$i->name] = $_post[$i->name];
						}
					}
					else
					{
						// the new value is zero => no permission
						$update[$i->name] = $_post[$i->name];
					}
				}
			}

			// perform the refresh
			$result = $mod->update_uprivs($_post['id'], $_post['id_area'], $insert, $update, $delete);

			$msg = AdmUtils_helper::set_msg($result);

			// set what update
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
	 * Syncronize User permission with group's settings
	 * User will lose any customizations
	 */
	public function reset(int $id_user) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level(1, 'users', $id_user, 'manage');
		if (is_null($msg))
		{
			// do action
			$mod = new Permission_model();
			$result = $mod->refactory($id_user, true);

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
	 * Refresh User permission with group's settings
	 * User will keep all customizations
	 */
	public function refactory(int $id_user) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level(1, 'users', $id_user, 'manage');
		if (is_null($msg))
		{
			// do action
			$mod = new Permission_model();
			$result = $mod->refactory($id_user);

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
	 * Edit User permission on table's records
	 */
	public function permissions(int $id_user, int $id_area, string $table) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'groups', 'users'));

		$mod = new Permission_model();

		// get area name
		$area = $mod->get_by_id($id_area, 'areas', 'name');

		// build the form
		$form_fields = new X4Form_core('user/user_permissions');
        $form_fields->id_area = $id_area;
		$form_fields->id_user = $id_user;
        $form_fields->table = $table;
        // permission level
        $form_fields->levels = $mod->get_levels();
        // get user privileges on the table
		$form_fields->what = $mod->get_detail($id_user, $id_area, $table);

        // get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'detpriv');
			if ($e)
			{
				$this->detailing($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        $view = new X4View_core('modal');
        $view->title = _EDIT_PRIV.': '.$area->name._TRAIT_.ucfirst($table);
		// content
		$view->content = new X4View_core('editor');
		// form builder

        $extra_btn = [
            'title' => _GO_BACK,
            'element' => 'modal',
            'url' => BASE_URL.'users/perm/'.$id_user.'/'.$id_area.'/1'
        ];

		$view->content->form = X4Form_helper::doform('detpriv', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons', $extra_btn), 'post', '',
            '@click="submitForm(\'detpriv\')"');

		$view->render(true);
	}

    /**
	 * Script for set for all
	 */
	public function set_for_all() : void
	{
        header('Content-Type: text/javascript');
        header("Content-Disposition: attachment; filename=set_for_all.js");
		echo '// extra check on configurator item for set_for_all
function setForAll(val) {
    var items = document.getElementsByClassName("resettable");
    for (var i = 0; i < items.length; i++) {
        items[i].value = val;
    }
}';
	}

	/**
	 * Update user permissions on table records
	 */
	private function detailing(array $_post) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level(1, 'users', $_post['id_user'], 'manage');

		if (is_null($msg))
		{
			$mod = new Permission_model();

			// handle _post
			$c = 0;
			$post = array();
			while(isset($_post['id_'.$c]))
			{
				// if the new value do not match the old value
				if ($_post['value_'.$c] != $_post['old_value_'.$c])
				{
					$post[] = array('id' => $_post['id_'.$c], 'value' => $_post['value_'.$c]);
				}
				$c++;
			}

			if (!empty($post))
			{
				// perform the update
				$result = $mod->update_detail_privs($_post['id_user'], $_post['id_area'], $_post['what'], $post);
			}
			else
			{
				// simulate update
				$result = array(0,1);
			}

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// set what update
			if ($result[1])
			{
				$msg->update = array(
					'element' => 'modal',
					'url' => BASE_URL.'users/perm/'.$_post['id_user'].'/'.$_post['id_area'].'/1'
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Delete User form
	 */
	public function delete(int $id) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'users'));

        // get item
		$user = new User_model();
		$item = $user->get_by_id($id, 'users', 'id, username, level');

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
        $view->title = _DELETE_USER;

		// contents
		$view->content = new X4View_core('delete');
		$view->content->item = $item->username;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

	/**
	 * Delete user
	 */
	private function deleting(stdClass $item) : void
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level(1, 'users', $item->id, 'delete');
        // check user level
        if (!is_null($msg) || $_SESSION['level'] < $item->level)
        {
            $msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_NOT_PERMITTED', 'msg'));
        }
        else
		{
			$mod = new User_model();
			$result = $mod->delete($item->id);

			$msg = AdmUtils_helper::set_msg($result);

			if ($result[1])
			{
				AdmUtils_helper::delete_priv('users', $item->id);

                $perm = new Permission_model();
				$perm->deleting_by_user($item->id);

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
