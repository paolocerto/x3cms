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
	 * Groups filter
	 *
	 * @return  void
	 */
	public function filter()
	{
		// load the dictionary
		$this->dict->get_wordarray(array('groups'));
		
		echo '<a class="btf" href="'.BASE_URL.'groups/edit" title="'._NEW_GROUP.'"><i class="fa fa-plus fa-lg"></i></a>
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
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'groups', $id, $val);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// do action
			$group = new Group_model();
			$result = $group->update($id, array($what => $value));
			
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
	 * New / Edit group form (use Ajax)
	 *
	 * @param   integer  $id item ID (if 0 then is a new item)
	 * @return  void
	 */
	public function edit($id = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'groups'));
		
		// get object
		$group = new Group_model();
		$g = ($id) 
			? $group->get_by_id($id)
			: new Group_obj();
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id,
			'name' => 'id'
		);
		
		$amod = new Area_model();
		if ($id) {
			// update a group
			$area = $amod->get_by_id($g->id_area, 'areas', 'title');
			$fields[] = array(
				'label' => null,
				'type' => 'html',
				'value' => '<h4>'._AREA.': '.$area->title.'</h4>'
			);
			$fields[] = array(
				'label' => null,
				'type' => 'hidden', 
				'value' => $g->id_area,
				'name' => 'id_area'
			);
		}
		else 
			$fields[] = array(
			'label' => _AREA,
			'type' => 'select',
			'value' => '',
			'options' => array($amod->get_areas(), 'id', 'title'),
			'name' =>'id_area',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => _NAME,
			'type' => 'text',
			'value' => $g->name,
			'name' => 'name',
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		$fields[] = array(
			'label' => _DESCRIPTION,
			'type' => 'textarea', 
			'value' => $g->description,
			'name' => 'description',
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		
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
		
		// contents
		$view = new X4View_core('editor');
		$view->title = ($id) 
			? _EDIT_GROUP 
			: _ADD_GROUP;
		
		// form builder
		$view->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'editor\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Register Edit / New group form data
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing($_post)
	{
		$msg = null;
		// check permission
		$msg = ($_post['id']) 
			? AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'menus', $_post['id'], 2)
			: AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_group_creation', 0, 4);
		
		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'name' => $_post['name'],
				'id_area' => $_post['id_area'],
				'description' => $_post['description']
			);
			
			// update or insert
			$group = new Group_model();
			if ($_post['id']) 
				$result = $group->update($_post['id'], $post);
			else 
			{
				$result = $group->insert($post);
				
				// add permission
				if ($result[1]) {
					$perm = new Permission_model();
					$array[] = array(
							'action' => 'insert', 
							'id_what' => $result[0], 
							'id_user' => $_SESSION['xuid'], 
							'level' => 4);
					$res = $perm->pexec('groups', $array, $_post['id_area']);
				}
			}
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// set what update
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => 'tdown', 
					'url' => BASE_URL.'users',
					'title' => null
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
	public function gperm($id_group)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'groups'));
		
		// get objects (group permissions)
		$mod = new Permission_model();
		$gp = X4Utils_helper::obj2array($mod->get_gprivs($id_group), 'what', 'level');
		
		// get area data
		$g = $mod->get_by_id($id_group, 'groups', 'id_area');
		$a = $mod->get_by_id($g->id_area, 'areas', 'private');
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id_group,
			'name' => 'id'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $a->private,
			'name' => 'xrif'
		);
		
		// available permission levels
		$l = $mod->get_levels();
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div class="band inner-pad clearfix">'
		);
		
		// registered group permissions
		$types = $mod->get_privtypes($a->private);
		foreach($types as $i) 
		{
			$fields[] = array(
				'label' => null,
				'type' => 'html', 
				'value' => '<div class="one-half xs-one-whole">'
			);
			
			// actual permission level
			$value = (isset($gp[$i->name])) 
				? $gp[$i->name] 
				: 0;
				
			$fields[] = array(
				'label' => constant($i->description),
				'type' => 'select',
				'value' => $value,
				'name' => $i->name,
				'options' => array($l, 'id', 'name', 0),
				'extra' => 'class="large"'
			);
			$fields[] = array(
				'label' => null,
				'type' => 'hidden',
				'value' => $value,
				'name' => 'old_'.$i->name
			);
			
			$fields[] = array(
				'label' => null,
				'type' => 'html', 
				'value' => '</div>'
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
			$e = X4Validation_helper::form($fields, 'gprivs');
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
		$view->title = _GROUP_PERMISSION;
		
		// form builder
		$view->form = '<div id="scrolled">'.X4Form_helper::doform('gprivs', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'gprivs\');"').'</div>';
		
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
	 * Register edited group permissions
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function permitting($_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'groups', $_post['id'], 4);
		
		if (is_null($msg))
		{
			// get all available permissions
			$perm = new Permission_model();
			$types = $perm->get_privtypes($_post['xrif']);
			
			// build action arrays
			$insert = $update = $delete = array();
			foreach($types as $i)
			{
				if (isset($_post[$i->name]) && $_post[$i->name] != $_post['old_'.$i->name]) 
				{
					if ($_post[$i->name]) 
					{
						// insert or update
						if ($_post['old_'.$i->name]) 
							$update[$i->name] = $_post[$i->name];
						else 
							$insert[$i->name] = $_post[$i->name];
					}
					else 
						$delete[] = $i->name;
				}
			}
			
			// update privs
			$result = $perm->update_gprivs($_post['id'], $insert, $update, $delete);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// set what update
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => 'tdown', 
					'url' => BASE_URL.'users',
					'title' => null
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
	public function delete($id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'groups'));
		
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
		$group = new Group_model();
		$obj = $group->get_by_id($id, 'groups', 'name');
		
		// contents
		$view = new X4View_core('delete');
		$view->title = _DELETE_GROUP;
		$view->item = $obj->name;
		
		// form builder
		$view->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '', 
			'onclick="setForm(\'delete\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Delete Group
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function deleting($_post)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'groups', $_post['id'], 4);
		
		if (is_null($msg))
		{
			// action
			$group = new Group_model();
			$result = $group->delete($_post['id']);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// clear useless permissions
			if ($result[1])
			{
				$perm = new Permission_model();
				$perm->deleting_by_what('groups', $_post['id']);
				
				// set what update
				$msg->update[] = array(
					'element' => 'topic', 
					'url' => BASE_URL.'users',
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
}
