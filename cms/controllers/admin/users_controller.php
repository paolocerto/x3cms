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
 * Controller for User items
 * 
 * @package X3CMS
 */
class Users_controller extends X3ui_controller
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
	 * Show groups
	 *
	 * @return  void
	 */
	public function _default()
	{
		// load dictionaries
		$this->dict->get_wordarray(array('groups', 'users'));
		
		// get page
		$page = $this->get_page('users');
		
		// content
		$view = new X4View_core('container');
		
		$view->content = new X4View_core('users/group_list');
		$view->content->page = $page;
		
		$group = new Group_model();
		$view->content->groups = $group->get_groups();
		$view->render(TRUE);
	}
	
	/**
	 * Show userss
	 *
	 * @return  void
	 */
	public function users($id_group)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('users'));
		
		$mod = new User_model();
		
		$group = $mod->get_var($id_group, 'groups', 'name');
		
		// content
		$view = new X4View_core('editor');
		$view->title = $group._TRAIT_._USERS_LIST;
		// list
		$view->form = new X4View_core('ul');
		$view->form->items = $mod->get_users($id_group);
		$view->form->link = BASE_URL.'users/detail';
		$view->form->class = 'class="btr"';
		$view->form->title = 'username';
		$view->form->value = 'username';
		
		$view->js = '
<script>
window.addEvent("domready", function()
{
	buttonize("simple-modal", "btr", "tdown");
});
</script>';
		$view->render(TRUE);
	}
	
	/**
	 * Users filter
	 *
	 * @return  void
	 */
	public function filter($id_group, $id)
	{
		// load the dictionary
		$this->dict->get_wordarray(array('users'));
		
		// get obj
		$mod = new User_model();
		$u = $mod->get_user_by_id($id);
		
		if ($u->xon) 
		{
			$status = _ON;
			$on_status = 'orange';
		}
		else 
		{
			$status = _OFF;
			$on_status = 'gray';
		}
		
		if ($u->xlock) 
		{
			$lock = _LOCKED;
			$lock_status = 'lock';
		}
		else 
		{
			$lock = _UNLOCKED;
			$lock_status = 'unlock-alt';
		}
		
		if ($u->hidden) 
		{
			$hide = _HIDE_USER;
			$hide_status = 'gray';
		}
		else 
		{
			$hide = _SHOW_USER;
			$hide_status = 'orange';
		}
		
		$hide = ($u->hidden == 1) 
			? _HIDE_USER 
			: _SHOW_USER;
		
		$actions = $delete = '';
		
		// check permission
		if ((($u->plevel > 1 && $u->xlock == 0) || $u->plevel == 4)) 
		{
			$actions = '<a class="btf" href="'.BASE_URL.'users/edit/'.$u->id.'/'.$u->id_group.'" title="'._EDIT.'"><i class="fa fa-pencil fa-lg"></i></a>';
			
			// manager or admin user
			if ($u->plevel > 2 || $u->plevel == 4) 
			{
				$actions .= ' <a class="btl" href="'.BASE_URL.'users/set/xon/'.$u->id.'/'.(($u->xon+1)%2).'" title="'._STATUS.' '.$status.'"><i class="fa fa-lightbulb-o fa-lg '.$on_status.'"></i></a>';
			}
			
			// admin user
			if ($u->plevel == 4) 
				$delete =  ' <a class="btl" href="'.BASE_URL.'users/set/hidden/'.$u->id.'/'.(($u->hidden+1)%2).'" title="'._STATUS.' '.$hide.'"><i class="fa fa-user fa-lg '.$hide.'"></i></a> 
				<a class="btl" href="'.BASE_URL.'users/set/xlock/'.$u->id.'/'.(($u->xlock+1)%2).'" title="'._STATUS.' '.$lock.'"><i class="fa fa-'.$lock_status.' fa-lg"></i></a> 
				<a class="btf" href="'.BASE_URL.'users/delete/'.$u->id.'" title="'._DELETE.'"><i class="fa fa-trash fa-lg red"></i></a>';
		}
		
		echo $actions.$delete.'
<script>
window.addEvent("domready", function()
{
	buttonize("filters", "btf", "modal");
	actionize("filters",  "btl", "filters", escape("users/filter/'.$id_group.'/'.$id.'"));
});
</script>';
	}
	
	/**
	 * Change status
	 *
	 * @param   string  $what field to change
	 * @param   integer $id ID of the item to change
	 * @param   integer $value value to set (0 = off, 1 = on)
	 * @return  void
	 */
	public function set($what, $id, $value = 0)
	{
		$msg = null;
		// check permission
		$val = ($what == 'xlock') 
			? 4 
			: 3;
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'users', $id, $val);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// do action
			$mod = new User_model();
			$result = $mod->update($id, array($what => $value));
			
			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);
			
			// set update
			if ($result[1])
				$msg->update[] = array(
					'element' => $qs['div'],
					'url' => urldecode($qs['url']),
					'title' => null
				);
		}
		$this->response($msg);
	}
	
	/**
	 * Show user data
	 *
	 * @param   integer $id User ID
	 * @return  void
	 */
	public function detail($id)
	{
	    // load dictionaries
		$this->dict->get_wordarray(array('users', 'form', 'login'));
		
		// check permission
		AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'users', $id, 3);
		
		// get page
		$page = $this->get_page('users/detail');
		
		// content
		$view = new X4View_core('container');
		
		$view->content = new X4View_core('users/user_detail');
		$view->content->page = $page;
		
		// get user data
		$user = new User_model();
		$view->content->u = $user->get_user_by_id($id);
		
		// get user privileges
		$perm = new Permission_model();
		$view->content->a = $perm->get_aprivs($id);
		$view->render(TRUE);
	}
	
	/**
	 * New / Edit user form (use Ajax)
	 *
	 * @param   integer  $id User ID (if 0 then is a new item)
	 * @param   integer  $id_group Group ID (if 0 then is a new item)
	 * @return  void
	 */
	public function edit($id, $id_group = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'login', 'users'));
		
		$lang = X4Route_core::$lang;
		
		// get object
		$user = new User_model();
		$u = ($id) 
			? $user->get_by_id($id)
			: new User_obj($id_group, $lang);
		
		// get group
		$group = new Group_model();
		$g = $group->get_by_id($u->id_group, 'groups', 'id_area, name');
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $u->id_group,
			'name' => 'id_group'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $g->id_area,
			'name' => 'id_area'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<h4>'._GROUP.': '.$g->name.'</h4>'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
		);
		
		// languages
		$lmod = new Language_model();
		$fields[] = array(
			'label' => ucfirst(_LANGUAGE),
			'type' => 'select',
			'value' => $u->lang,
			'options' => array($lmod->get_languages(), 'code', 'language'),
			'name' => 'lang',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _USERNAME,
			'type' => 'text',
			'value' => $u->username,
			'name' => 'username',
			'suggestion' => _USERNAME_RULE,
			'rule' => 'required|minlength§6|alphanumeric',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div></div>'
		);
		
		// password
		if ($id) 
		{
			$fields[] = array(
				'label' => null,
				'type' => 'html',
				'value' => '<h4 class="acenter zerom">'._PASSWORD_CHANGE_MSG.'</h4>'
			);
			$rule = '';
		}
		else 
		{
			// for a new user you must insert a password
			$rule = 'required|';
		}
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _PASSWORD,
			'type' => 'password',
			'value' => '',
			'name' => 'password',
			'suggestion' => _PASSWORD_RULE,
			'rule' => $rule.'minlength§6|alphanumeric',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _REPEAT_PASSWORD,
			'type' => 'password',
			'value' => '',
			'name' => 'password2',
			'rule' => $rule.'equal-password',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div></div>'
		);
		
		$fields[] = array(
			'label' => _DESCRIPTION,
			'type' => 'textarea', 
			'value' => $u->description,
			'name' => 'description',
			'sanitize' => 'string',
			'rule' => 'required'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _EMAIL,
			'type' => 'text',
			'value' => $u->mail,
			'name' => 'mail',
			'rule' => 'required|mail',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _PHONE,
			'type' => 'text',
			'value' => $u->phone,
			'name' => 'phone',
			'rule' => 'phone',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div></div><div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _LEVEL,
			'type' => 'select',
			'value' => $u->level,
			'options' => array($user->get_levels(), 'id', 'name'),
			'name' => 'level',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div><div class="one-half xs-one-whole">'
		);
		
		// permissions on areas
		$perm = new Permission_model();
		$area = new Area_model();
		$fields[] = array(
			'label' => _DOMAIN,
			'type' => 'select',
			'value' => X4Utils_helper::obj2array($perm->get_aprivs($id), null, 'id_area'),
			'options' => array($area->get_areas($g->id_area), 'id', 'name'),
			'multiple' => 4,
			'name' => 'domain',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div></div>'
		);
		
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
			? _EDIT_USER 
			: _ADD_USER;
		
		// form builder
		$view->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'editor\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Register Edit / New User form data
	 *
	 (if 0 then is a new item)
	 * @param   integer $id item ID (if 0 then is a new item)
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing($id, $_post)
	{
		$msg = null;
		// check permission
		$msg = ($id) 
			? AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'users', $id, 2)
			: AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_user_creation', 0, 4);
		
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
				$post['password'] = X4Utils_helper::hashing($_post['password']);
			
			// check if an user with the same username or password already exists
			$user = new User_model();
			$check = (boolean) $user->exists($post['username'], $post['mail'], $id);
			
			if ($check) 
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_USER_ALREADY_EXISTS', 'msg'));
			else 
			{
				$perm = new Permission_model();
				if ($id) 
				{
					// update
					$result = $user->update($id, $post);
					
					// update user privileges on areas
					$perm->set_aprivs($id, $_post['domain']);
					
					// redirect
					$where = '/detail/'.$id;
				}
				else 
				{
					// insert
					$result = $user->insert($post);
					
					// redirect
					$where = '';
					
					if ($result[1])
					{
						$id = $result[0];
						
						// set privileges on areas
						$perm->set_aprivs($id, $_post['domain']);
						
						// add privs on new user
						$array[] = array(
								'action' => 'insert', 
								'id_what' => $result[0], 
								'id_user' => $_SESSION['xuid'], 
								'level' => 4);
						$res = $perm->pexec('users', $array, $_post['id_area']);
						
						// refactory permissions for the user
						$perm->refactory($id);
					}
				}
				
				// set message
				$msg = AdmUtils_helper::set_msg($result);
				
				// set what update
				if ($result[1])
				{
					$msg->update[] = array(
						'element' => 'tdown', 
						'url' => BASE_URL.'users'.$where,
						'title' => null
					);
				}
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Edit user permission form (use Ajax)
	 *
	 * @param   integer  $id_user User ID
	 * @param   integer  $id_area Area ID
	 * @param   integer  $table if equal to 0 manage abstract permission (creation, installation) else manage real permission over table records
	 * @return  void
	 */
	public function perm($id_user, $id_area, $table = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'groups', 'users'));
		
		$mod = new Permission_model();
		
		// user data
		$u = $mod->get_by_id($id_user, 'users', 'id_group, username');
		
		// user permission
		$what = $mod->get_uprivs($id_user, $id_area);
		
		// permission level
		$l = $mod->get_levels();
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id_user,
			'name' => 'id'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $u->id_group,
			'name' => 'id_group'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id_area,
			'name' => 'id_area'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $table,
			'name' => 'table'
		);
		
		// tables without items
		$nodetail = array('areas', 'sites');
		
		// tables for administrators
		$onlyadmin = array('themes', 'templates', 'menus', 'groups', 'users', 'languages', 'sites', 'privs');
		
		// tables if advanced editing
		$exclude = (ADVANCED_EDITING) 
			? array('contents', 'logs') 
			: array('blocks', 'sections', 'logs');
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div class="band inner-pad clearfix">'
		);
			
		foreach($what as $t) 
		{
			if ($table == 0) 
			{
				// only abstract permissions
				if (substr($t->privtype, 0, 1) == '_') 
				{
					$fields[] = array(
						'label' => null,
						'type' => 'html', 
						'value' => '<div class="one-half xs-one-whole">'
					);
					
					$fields[] = array(
						'label' => constant(strtoupper($t->privtype)),
						'type' => 'select',
						'value' => $t->level,
						'name' => $t->privtype,
						'options' => array($l, 'id', 'name', 0),
						'extra' => 'class="large"'
					);
					
					$fields[] = array(
						'label' => null,
						'type' => 'html', 
						'value' => '</div>'
					);
				}
			}
			else 
			{
				// only real permissions on tables
				if (substr($t->privtype, 0, 1) != '_' && !in_array($t->privtype, $exclude)) 
				{
					
					
					// relative to admin area or not only for administrators
					if ($id_area == 1 || !in_array($t->privtype, $onlyadmin)) 
					{
						$fields[] = array(
							'label' => null,
							'type' => 'html', 
							'value' => '<div class="one-half xs-one-whole">'
						);
						
						// if in tables with items
						if (!in_array($t->privtype, $nodetail)) 
						{
							$fields[] = array(
								'label' => constant(strtoupper($t->privtype)),
								'type' => 'select',
								'value' => $t->level,
								'name' => $t->privtype,
								'options' => array($l, 'id', 'name', 0),
								'suggestion' => '',
								'extra' => 'class="large"'
							);
							
							$fields[] = array(
								'label' => null,
								'type' => 'html', 
								'value' => '</div>
									<div class="one-half xs-one-whole double-pad-top">
										<a class="btop" href="'.BASE_URL.'users/permissions/'.$id_user.'/'.$id_area.'/'.$t->privtype.'" title="'._EDIT_DETAIL_PRIV.'">'._EDIT_DETAIL_PRIV.'</a>
									</div>
									<div class="clear"></div>'
							);
						}
						else 
						{
							$fields[] = array(
								'label' => constant(strtoupper($t->privtype)),
								'type' => 'select',
								'value' => $t->level,
								'name' => $t->privtype,
								'options' => array($l, 'id', 'name', 0),
								'extra' => 'class="large"'
							);
							
							$fields[] = array(
								'label' => null,
								'type' => 'html', 
								'value' => '</div><div class="clear"></div>'
							);
						}
					}
				}
			}
			
			// old value memo
			$fields[] = array(
				'label' => null,
				'type' => 'hidden',
				'value' => $t->level,
				'name' => 'old_'.$t->privtype
			);
		}
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div>'
		);
		
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
		
		// contents
		$view = new X4View_core('editor');
		$view->title = ($id_area) 
			? _EDIT_PRIV.': '.$u->username 
			: _EDIT_PRIV.': '._GLOBAL_PRIVS;
		
		// form builder
		$view->form = '<div id="scrolled">'.X4Form_helper::doform('editpriv', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'editpriv\');"').'</div>';
		
		$view->js = '
<script>
window.addEvent("domready", function()
{
	buttonize("simple-modal", "btop", "modal");
	var myScroll = new Scrollable($("scrolled"));
});
</script>';
		
		$view->render(TRUE);
	}
	
	/**
	 * Refresh upriv table and then privs
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function permitting($_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'users', $_post['id'], 3);
		
		if (is_null($msg))
		{
			// get privilege types
			$mod = new Permission_model();
			$types = $mod->get_privtypes(1);
		
			// check the differences
			$insert = $update = $delete = array();
			foreach($types as $i)
			{
				// if the new value do not match the old value
				if (isset($_post[$i->name]) && $_post[$i->name] != $_post['old_'.$i->name]) 
				{
					// if the new value is greater than zero
					if ($_post[$i->name]) 
					{
						// update if the old value was greater than zero
						if ($_post['old_'.$i->name]) 
							$update[$i->name] = $_post[$i->name];
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
				$msg->update[] = array(
					'element' => 'tdown', 
					'url' => BASE_URL.'users/detail/'.$_post['id'],
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Syncronize User permission with group's settings
	 * User will lose any customizations
	 *
	 * @param   integer	$id_user User ID
	 * @return  void
	 */
	public function reset($id_user)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'users', $id_user, 3);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// do action
			$mod = new Permission_model();
			$result = $mod->refactory($id_user, 1);
			
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
	 * Refresh User permission with group's settings
	 * User will keep all customizations
	 *
	 * @param   integer	$id_user User ID
	 * @return  void
	 */
	public function refactory($id_user)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'users', $id_user, 3);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// do action 
			$mod = new Permission_model();
			$result = $mod->refactory($id_user, null);
			
			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);
			
			// set update
			if ($result[1])
				$msg->update[] = array(
					'element' => $qs['div'],
					'url' => urldecode($qs['url']),
					'title' => null
				);
		}
		$this->response($msg);
	}
	
	/**
	 * Edit User permission on table's records
	 *
	 * @param   integer	$id_user User ID
	 * @param   integer	$id_area Area ID
	 * @param   string	$table Table name
	 * @return  void
	 */
	public function permissions($id_user, $id_area, $table)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'groups', 'users'));
		
		$mod = new Permission_model();
		
		// get user name
		$u = $mod->get_by_id($id_user, 'users', 'username');
		
		// get area name
		$a = $mod->get_by_id($id_area, 'areas', 'name');
		
		// get user privileges on the table
		$what = $mod->get_detail($id_user, $id_area, $table);
		
		// permission levels
		$l = $mod->get_levels();
		
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id_user,
			'name' => 'id_user'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id_area,
			'name' => 'id_area'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $table,
			'name' => 'what'
		);
		
		$c = 0;
		// if table is not empty
		if ($what) 
		{
			$fields[] = array(
				'label' => null,
				'type' => 'html', 
				'value' => '<div class="band inner-pad clearfix">'
			);
			
			// each record
			foreach($what as $i) 
			{
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '<div class="one-half xs-one-whole">'
				);
				
				$value = is_null($i->level) ? 0 : $i->level;
				$fields[] = array(
					'label' => null,
					'type' => 'hidden', 
					'value' => $i->id,
					'name' => 'id_'.$c
					);
				$fields[] = array(
					'label' => null,
					'type' => 'hidden', 
					'value' => $value,
					'name' => 'old_value_'.$c
					);
				$fields[] = array(
					'label' => $i->name,
					'type' => 'select',
					'value' => $value,
					'name' => 'value_'.$c,
					'options' => array($l, 'id', 'name', 0),
					'suggestion' => strip_tags($i->description),
					'extra' => 'class="large"'
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
		}
		
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
		
		// content
		$view = new X4View_core('editor');
		$view->title = _EDIT_PRIV.': '.$a->name._TRAIT_.ucfirst($table);
		
		// form builder
		$view->form = '<div id="scrolled">'.X4Form_helper::doform('detpriv', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'detpriv\');"').'</div>';
		
		$view->js = '
<script>
window.addEvent("domready", function()
{
	var myScroll = new Scrollable($("scrolled"));
});
</script>';
		
		$view->render(TRUE);
	}
	
	/**
	 * Update user permissions on table records
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function detailing($_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'users', $_post['id_user'], 3);
		
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
					$post[] = array('id' => $_post['id_'.$c], 'value' => $_post['value_'.$c]);
				$c++;
			}
			
			if(!empty($post))
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
				$msg->update[] = array(
					'element' => 'modal', 
					'url' => BASE_URL.'users/perm/'.$_post['id_user'].'/'.$_post['id_area'].'/1',
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Delete User form (use Ajax)
	 *
	 * @param   integer $id User ID
	 * @return  void
	 */
	public function delete($id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'users'));
		
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
			$this->deleting($_POST);
			die;
		}
		
		// get object
		$user = new User_model();
		$obj = $user->get_by_id($id, 'users', 'username');
		
		// contents
		$view = new X4View_core('delete');
		$view->title = _DELETE_USER;
		$view->item = $obj->username;
		
		// form builder
		$view->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '', 
			'onclick="setForm(\'delete\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Delete user
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function deleting($_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'users', $_post['id'], 4);
		
		if (is_null($msg))
		{
			// do action
			$mod = new User_model();
			$result = $mod->delete($_post['id']);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// clear useless permissions
			if ($result[1]) 
			{
				$perm = new Permission_model();
				
				// clean permission on user
				$perm->deleting_by_what('users', $_post['id']);
				
				// clean user permissions
				$perm->deleting_by_user($_post['id']);
				
				// set what update
				$msg->update[] = array(
					'element' => 'tdown', 
					'url' => BASE_URL.'users',
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
}
