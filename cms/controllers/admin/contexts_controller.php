<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
 
/**
 * Controller for Contexts
 * 
 * @package X3CMS
 */
class Contexts_controller extends X3ui_controller
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
	 * Show contexts
	 *
	 * @return  void
	 */
	public function _default()
	{
		$this->index(2, X4Route_core::$lang);
	}
	
	/**
	 * Show contexts
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @return  void
	 */
	public function index($id_area, $lang)
	{
		// load dictionary
		$this->dict->get_wordarray(array('contexts', 'articles'));
		
		// get page
		$page = $this->get_page('contexts');
		$navbar = array($this->site->get_bredcrumb($page), array('articles' => 'index/'.$id_area.'/'.$lang));
		
		$view = new X4View_core('container');
		
		// content
		$mod = new Context_model();
		$view->content = new X4View_core('articles/context_list');
		$view->content->page = $page;
		$view->content->navbar = $navbar;
		$view->content->items = $mod->get_contexts($id_area, $lang);
		
		// area switcher
		$view->content->id_area = $id_area;
		$area = new Area_model();
		$view->content->areas = $area->get_areas();
		
		// language switcher
		$view->content->lang = $lang;
		$lang = new Language_model();
		$view->content->langs = $lang->get_languages();
		
		$view->render(TRUE);
	}
	
	/**
	 * Contexts filter
	 *
	 * @return  void
	 */
	public function filter($id_area, $lang)
	{
		// load the dictionary
		$this->dict->get_wordarray(array('contexts'));
		
		echo '<a class="btf" href="'.BASE_URL.'contexts/edit/'.$id_area.'/'.$lang.'/-1" title="'._NEW_CONTEXT.'"><i class="fa fa-plus fa-lg"></i></a>
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
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'contexts', $id, $val);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// do action
			$mod = new Context_model();
			$obj = $mod->get_by_id($id);
			
			// default contexts cannot change status
			$result = ($obj->code > 100)
				? $mod->update($id, array($what => $value))
				: false;
			
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
	 * New / Edit context form (use Ajax)
	 *
	 * @param   integer	$id Context ID
	 * @return  void
	 */
	public function edit($id_area, $lang, $id = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'contexts'));
		
		// to switch render at the end of this method
		$chk = false;
		if ($id < 0)
		{
			$id = 0;
			$chk = true;
		}
		
		// get object
		$mod = new Context_model();
		$obj = ($id) 
			? $mod->get_by_id($id)
			: new Context_obj($id_area, $lang);
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id,
			'name' => 'id'
		);
		$amod = new Area_model();
		$fields[] = array(
			'label' => _AREA,
			'type' => 'select',
			'value' => $obj->id_area,
			'options' => array($amod->get_areas(), 'id', 'name'),
			'name' => 'id_area',
			'extra' => 'class="large"'
		);
		
		$lmod = new Language_model();
		$fields[] = array(
			'label' => _LANGUAGE,
			'type' => 'select',
			'value' => $obj->lang,
			'options' => array($lmod->get_languages(), 'code', 'language'),
			'name' => 'lang',
			'extra' => 'class="large"'
		);
		$fields[] = array(
			'label' => _NAME,
			'type' => 'text', 
			'value' => $obj->name,
			'name' => 'name',
			'rule' => 'required',
			'extra' => 'class="large"'
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
		
		// content
		$view = new X4View_core('editor');
		$view->title = ($id) 
			? _EDIT_CONTEXT 
			: _ADD_CONTEXT;
		
		// form builder
		$view->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
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
	 * Register Edit / New Context form data
	 *
	 * @access	private
	 * @param   integer $id item ID (if 0 then is a new item)
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing($id, $_post)
	{
		$msg = null;
		// check permission
		$msg = ($id) 
			? AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'contexts', $id, 3)
			: AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_context_creation', 0, 4);
		
		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'id_area' => $_post['id_area'],
				'lang' => $_post['lang'],
				'name' => strtolower($_post['name']),
				'xkey' => X4Utils_helper::unspace($_post['name'])
			);
			
			$mod = new Context_model();
			
			// check if context already exists
			$check = $mod->exists($post, $id);
			if ($check) 
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_CONTEXT_ALREADY_EXISTS', 'msg'));
			else 
			{
				// update or insert
				if ($id)
				{
					$result = $mod->update($id, $post);
					
					// check if dictionary name for the context already exists
					if ($result[1]) 
						$mod->check_dictionary($post);
				}
				else 
				{
					// get the code of the new context
					$code = $mod->get_max_code($post['id_area'], $post['lang']);
					
					// this implies that the site can't have more than 33 languages
					// you have 3 default contexts (draft, page, multipages) for each language and for each area
					$post['code'] = ($code > 100) ? ($code+1) : 101;
					
					$result = $mod->insert($post);
					if ($result[1]) 
					{
						// add item into dictionary
						$mod->check_dictionary($post, 1);
						
						// create permission
						$perm = new Permission_model();
						$array[] = array(
								'action' => 'insert', 
								'id_what' => $result[0], 
								'id_user' => $_SESSION['xuid'], 
								'level' => 4);
						$res = $perm->pexec('contexts', $array, $post['id_area']);
					}
				}
				
				// set message
				$msg = AdmUtils_helper::set_msg($result);
				
				// set what update
				if ($result[1])
				{
					$msg->update[] = array(
						'element' => 'topic', 
						'url' => BASE_URL.'contexts/index/'.$post['id_area'].'/'.$post['lang'],
						'title' => null
					);
				}
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Delete context form (use Ajax)
	 *
	 * @param   integer $id Context ID
	 * @return  void
	 */
	public function delete($id)
	{
		// get object
		$mod = new Context_model();
		$obj = $mod->get_by_id($id, 'contexts', 'id_area, lang, name, code');
		
		// only added context can be deleted
		if ($obj->code > 100) 
		{
			// load dictionaries
			$this->dict->get_wordarray(array('form', 'contexts'));
			
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
				$this->deleting($id, $obj);
				die;
			}
			
			// contents
			$view = new X4View_core('delete');
			$view->title = _DELETE_CONTEXT;
			$view->item = $obj->name;
			
			// form builder
			$view->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '', 
				'onclick="setForm(\'delete\');"');
			$view->render(TRUE);
		}
	}
	
	/**
	 * Delete context
	 *
	 * @access	private
	 * @param   integer	$id Context ID
	 * @param   object	$obj Context Obj
	 * @return  void
	 */
	private function deleting($id, $obj)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'contexts', $id, 4);
		
		if (is_null($msg))
		{
			// do action
			$mod = new Context_model();
			$result = $mod->delete($id);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// clear useless permissions
			if ($result[1]) {
				$perm = new Permission_model();
				$perm->deleting_by_what('contexts', $id);
				
				// set what update
				$msg->update[] = array(
					'element' => 'topic', 
					'url' => BASE_URL.'contexts/index/'.$obj->id_area.'/'.$obj->lang,
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
}
