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
 * Controller for In-line Editor
 * 
 * @package X3CMS
 */
class X3admin_controller extends X4Cms_controller
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
	 * Redirect to REFERER
	 *
	 * @return  void
	 */
	public function _default()
	{
		header('Location: '.$_SERVER["HTTP_REFERER"]);
		die;
	}
	
	/**
	 * Edit article by ID
	 *
	 * @param	integer	$id Article ID
	 * @return  void
	 */
	public function edit($id = 0)
	{
		if ($id == 0) 
		{
			$this->_default();
		}
		else 
		{
			// load dictionaries
			$this->dict->get_wordarray(array('form', 'articles'));
			
			// get object
			$mod = new Article_model();
			$i = $mod->get_by_id($id);
			
			// cannot edit locked items
			if ($i->xlock == 1) 
				$this->_default();
			
			// switch editor
			// default use Tiny MCE
			if (empty($i->xschema)) 
			{
				// tinymce
				$fields = array();
				$fields[] = array(
					'label' => null,
					'type' => 'hidden', 
					'value' => 0,
					'name' => 'schema'
				);
				$fields[] = array(
					'label' => null,
					'type' => 'hidden', 
					'value' => $_SERVER["HTTP_REFERER"],
					'name' => 'from'
				);
				$fields[] = array(
					'label' => null,
					'type' => 'hidden', 
					'value' => $i->bid,
					'name' => 'bid'
				);
				$fields[] = array(
					'label' => null,
					'type' => 'hidden',
					'value' => $i->id_area,
					'name' => 'id_area'
				);
				$fields[] = array(
					'label' => null,
					'type' => 'hidden',
					'value' => $i->lang,
					'name' => 'lang'
				);
				$fields[] = array(
					'label' => null,
					'type' => 'hidden',
					'value' => $i->code_context,
					'name' => 'code_context'
				);
				$fields[] = array(
					'label' => null,
					'type' => 'hidden',
					'value' => $i->id_page,
					'name' => 'id_page'
				);
				$fields[] = array(
					'label' => null,
					'type' => 'hidden', 
					'value' => $i->xkeys,
					'name' => 'xkeys'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'hidden', 
					'value' => stripslashes($i->name),
					'name' => 'name'
				);
				$fields[] = array(
					'label' => null,
					'type' => 'hidden',
					'value' => $i->module,
					'name' => 'module'
				);
				$fields[] = array(
					'label' => null,
					'type' => 'hidden',
					'value' => $i->param,
					'name' => 'param'
				);
				
				// the only field not hidden
				$fields[] = array(
					'label' => '',
					'type' => 'textarea', 
					'value' => $i->content,
					'name' => 'content'
				);
			
			}
			else 
			{
				// TODO: schema editor
			}
		}
		
		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e) 
			{
				$this->editing($id, $_POST);
				die;
			}
			else 
			{
				X4Utils_helper::set_error($fields);
			}
		}
		
		// get page
		$page = $this->get_page('x3admin');
		$view = new X4View_core(X4Utils_helper::set_tpl($page->tpl));
		$view->page = $page;
		
		// get menus
		$view->menus = $this->site->get_menus($page->id_area);
		$view->navbar = array($this->site->get_bredcrumb($page));
		
		// sections
		$view->args = array();
		$view->sections = array('', '');
		
		// content
		$view->content = new X4View_core('editor');
		$view->content->title = _EDIT_ARTICLE;
		
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'));
		
		if (empty($i->xschema)) 
		{
			$view->content->tinymce = new X4View_core('tinymce');
			$view->content->tinymce->id_area = $page->id_area;
			$view->content->tinymce->tinybrowser = true;
		}
		$view->render(TRUE);
	}
	
	/**
	 * Register article
	 *
	 * @access	private
	 * @param   integer $id article ID
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing($id, $_post)
	{
		// check permission
		AdmUtils_helper::chklevel($_SESSION['xuid'], 'articles', $id, 2);
		
		// check editor
		if ($_post['schema']) 
		{
			// schema
			// TODO: build post array using schema
		}
		else 
		{
			// tinymce
			$post = array(
				'bid' => $_post['bid'],
				'id_area' => $_post['id_area'],
				'lang' => $_post['lang'],
				'code_context' => $_post['code_context'],
				'id_page' => $_post['id_page'],
				'date_in' => time(),
				'xkeys' => strtolower($_post['xkeys']),
				'name' => $_post['name'],
				'content' => $_post['content'],
				'excerpt' => (strstr($_post['content'], '<!--pagebreak-->') !== false) ? 1 : 0,
				'author' => $_SESSION['mail'],
				'module' => $_post['module'],
				'param' => $_post['param'],
				'id_editor' => $_SESSION['xuid'],
				'xon' => AUTOREFRESH
			);
		}
		
		// insert new article's version
		$mod = new Article_model();
		$result = $mod->insert($post);
		
		if ($result[1]) 
		{
			// add permission
			$perm = new Permission_model();
			// privs permissions
			$array[] = array(
					'action' => 'insert', 
					'id_what' => $result[0], 
					'id_user' => $_SESSION['xuid'], 
					'level' => 4);
			$res = $perm->pexec('articles', $array, $_post['id_area']);
		}
		
		// set message
		X4Utils_helper::set_msg($result);
		
		// redirect
		header('Location: '.$_post['from']);
		die;
	}
	
	/**
	 * Save article
	 *
	 * @param   string	$bid
	 * @return  void
	 */
	public function update($bid)
	{
	    // load dictionaries
		$this->dict->get_words();
		
	    // get article id
	    $mod = new Article_model();
	    $item = $mod->get_by_bid($bid);
	    
		// check permission
		AdmUtils_helper::chklevel($_SESSION['xuid'], 'articles', $item->id, 2);
		
		// only if there are differences
		if ($item->content != $_POST['content'])
		{
		    // tinymce
            $post = array(
                'bid' => $bid,
                'id_area' => $item->id_area,
                'lang' => $item->lang,
                'code_context' => $item->code_context,
                'id_page' => $item->id_page,
                'date_in' => time(),
                'xkeys' => $item->xkeys,
                'name' => $item->name,
                'content' => $_POST['content'],
                'excerpt' => 0,
                'author' => $_SESSION['mail'],
                'module' => $item->module,
                'param' => $item->param,
                'id_editor' => $_SESSION['xuid'],
                'xon' => AUTOREFRESH
            );
            
            // insert new article's version
            $result = $mod->insert($post);
            
            if ($result[1]) 
            {
                // add permission
                $perm = new Permission_model();
                // privs permissions
                $array[] = array(
                        'action' => 'insert', 
                        'id_what' => $result[0], 
                        'id_user' => $_SESSION['xuid'], 
                        'level' => 4);
                $res = $perm->pexec('articles', $array, $item->id_area);
            }
            
            // set message
            X4Utils_helper::set_msg($result);
            
            echo $_SESSION['msg'];
            unset($_SESSION['msg']);
        }
        else
        {
            echo '';
        }
	}
}
