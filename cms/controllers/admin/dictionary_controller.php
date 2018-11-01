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
 * Controller for Categories
 * 
 * @package X3CMS
 */
class Dictionary_controller extends X3ui_controller
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
	 * Redirect to language list
	 *
	 * @return  void
	 */
	public function _default()
	{
		header('Location: '.BASE_URL.'languages');
		die;
	}
	
	/**
	 * Show dictionary words by Language code, Area name and key
	 *
	 * @param   string 	$code Language code
	 * @param   string 	$area Area name
	 * @param   string 	$what Dictionary key
	 * @return  void
	 */
	public function keys($code = '', $area = 'public', $what = '', $str = '')
	{
	    // load dictionary
		$this->dict->get_wordarray(array('dictionary'));
		
		$area_mod = new Area_model();
		list($id_area, $areas) = $area_mod->get_my_areas(2);
		
		if ($id_area != 2)
		{
			$area = $area_mod->get_var($id_area, 'areas', 'name');
		}
		
		if (empty($str))
		{
            $code = (empty($code))
                ? X4Route_core::$lang
                : $code;
            
            // get page
            $page = $this->get_page('dictionary/keys');
            
            // content
            $view = new X4View_core('container');
            
            $view->content = new X4View_core('languages/words');
            $view->content->page = $page;
            
            // keys
            $dict = new Dictionary_model();
            $keys = $dict->get_keys($code, $area);
            $view->content->keys = $keys;
            
            // check empty what
            if (empty($what) && !empty($keys)) 
            {
                $what = $keys[0]->what;
            }
            $view->content->items = $dict->get_words($code, $area, $what);
            $view->content->what = $what;
            $view->content->str = '';
            
            // area switcher
            $view->content->area = $area;
            
            $view->content->areas = $areas;
            
            // language switcher
            $view->content->lang = $code;
            $lang = new Language_model();
            $view->content->langs = $lang->get_languages();
            
            header('Content-Type: text/html; charset=utf-8');
            
            $view->render(TRUE);
        }
        else
        {
            $this->search($code, $area, $what, $str);
        }
	}
	
	/**
	 * Dictionary filter
	 *
	 * @param   string 	$code Language code
	 * @param   string 	$area Area name
	 * @param   string 	$what Dictionary key
	 * @param   string 	$str  Searched string
	 * @return  string
	 */
	public function filter($lang, $area, $what, $str = '')
	{
		// load the dictionary
		$this->dict->get_wordarray(array('dictionary'));
		
		if (X4Route_core::$post)
		{
		    // set message
            $msg = AdmUtils_helper::set_msg(array(0,1));
            $msg->update[] = array(
                    'element' => 'tdown', 
                    'url' => BASE_URL.'dictionary/keys/'.$lang.'/'.$area.'/'.$what.'/'.urlencode(trim($_POST['search'])),
                    'title' => null
                );
            $this->response($msg);
		}
		else
		{
		    echo '<form id="searchitems" name="searchitems" action="'.BASE_URL.'dictionary/filter/'.$lang.'/'.$area.'/'.$what.'" method="POST" onsubmit="return false;">
                <input type="text" name="search" id="search" value="'.urldecode($str).'" title="'._DICTIONARY_SEARCH_MSG.'" />
                <button type="button" name="searcher" class="button" onclick="setForm(\'searchitems\');">'._FIND.'</button>
                </form>';
                
            echo '
		<a class="btf" href="'.BASE_URL.'dictionary/import/'.$lang.'/'.$area.'" title="'._IMPORT_KEYS.'"><i class="fa fa-download fa-lg"></i></a>
		<a class="btf" href="'.BASE_URL.'dictionary/add/'.$lang.'/'.$area.'" title="'._NEW_WORD.'"><i class="fa fa-plus fa-lg"></i></a>
<script>
window.addEvent("domready", function()
{
	buttonize("filters", "btf", "modal");
});
</script>';
        }
	}
	
	/**
	 * Show search results on dictionary words
	 *
	 * @param   string 	$code Language code
	 * @param   string 	$area Area name
	 * @param   string 	$what Dictionary key
	 * @param   string 	$str  Searched string
	 * @return  void
	 */
	public function search($code, $area, $what, $str)
	{
	    // load dictionary
		$this->dict->get_wordarray(array('dictionary'));
		
        // get page
        $page = $this->get_page('dictionary/keys');
        
        // content
        $view = new X4View_core('container');
        
        $view->content = new X4View_core('languages/search');
        $view->content->page = $page;
        
        $dict = new Dictionary_model();
        $view->content->items = $dict->search_words($area, $str);
        $view->content->lang = $code;
        $view->content->what = $what;
        $view->content->str = $str;
        
        // area switcher
        $view->content->area = $area;
        $area = new Area_model();
        $view->content->areas = $area->get_areas();
        
        header('Content-Type: text/html; charset=utf-8');
        
        $view->render(TRUE);
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
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'dictionary', $id, $val);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// do action
			$dict = new Dictionary_model();
			$result = $dict->update($id, array($what => $value));
			
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
	 * Edit dictionary word form (use Ajax)
	 *
	 * @param   integer $id Dictionary ID
	 * @return  void
	 */
	public function edit($id)
	{
		// get object
		$dict = new Dictionary_model();
		$obj = $dict->get_by_id($id);
		
		// load dictionary
		$this->dict->get_wordarray(array('form', 'dictionary'));
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $id,
			'name' => 'id'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'html',
			'value' => '<h4>'.$obj->xkey.'</h4>'
		);
		$fields[] = array(
			'label' => _WORD,
			'type' => 'textarea',
			'value' => str_replace(array("\n", '<br />', '<br>', '<br/>'), array('', "\n", "\n", "\n"), $obj->xval),
			'name' => 'xval',
			'rule' => 'required'
		);
		
		// if submitted
		if (X4Route_core::$post) 
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e) 
			{
				$this->editing($obj, $_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
		
		// content
		$view = new X4View_core('editor');
		$view->title = _EDIT_WORD;
		
		// form builder
		$view->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'editor\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Register Edit dictionary word form data
	 *
	 * @access	private
	 * @param   object 	$obj word object
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing($obj, $_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'dictionary', $_post['id'], 2);
		
		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'xval' => nl2br(trim($_post['xval']))
			);
			
			// update 
			$dict = new Dictionary_model();
			$result = $dict->update($_post['id'], $post);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// set what update
			if ($result[1])
			{
				APC && apc_delete(SITE.'dict'.$obj->area.$obj->lang.$obj->what);
				
				$msg->update[] = array(
					'element' => 'tdown', 
					'url' => BASE_URL.'dictionary/keys/'.$obj->lang.'/'.$obj->area.'/'.$obj->what,
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Delete dictionary word form (use Ajax)
	 *
	 * @param   integer $id Dictionary word ID
	 * @return  void
	 */
	public function delete($id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'dictionary'));
		
		// get object
		$dict = new Dictionary_model();
		$obj = $dict->get_by_id($id, 'dictionary', 'id, xkey, lang, area, what');
		
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
			$this->deleting($obj);
			die;
		}
		
		// contents
		$view = new X4View_core('delete');
		$view->title = _DELETE_WORD;
		$view->item = $obj->xkey;
		
		// form builder
		$view->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '', 
			'onclick="setForm(\'delete\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Delete dictionary word
	 *
	 * @access	private
	 * @param   object 	$obj Word object
	 * @return  void
	 */
	private function deleting($obj)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'dictionary', $obj->id, 4);
		
		if (is_null($msg))
		{
			// do action
			$dict = new Dictionary_model();
			$result = $dict->delete($obj->id);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// clear useless permissions
			if ($result[1]) {
				$perm = new Permission_model();
				$perm->deleting_by_what('dictionary', $obj->id);
				
				// set what update
				$msg->update[] = array(
					'element' => 'tdown', 
					'url' => BASE_URL.'dictionary/keys/'.$obj->lang.'/'.$obj->area.'/'.$obj->what,
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * New dictionary word form (use Ajax)
	 *
	 * @param   string 	$lang Language code
	 * @param   string 	$area Area name
	 * @param   string	$what Key value
	 * @return  void
	 */
	public function add($lang, $area, $what = '')
	{
		// load dictionary
		$this->dict->get_wordarray(array('form', 'dictionary'));
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $lang,
			'name' => 'lang'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $area,
			'name' => 'area',
			'extra' => 'class="large"'
		);
		$fields[] = array(
			'label' => _SECTION,
			'type' => 'text',
			'value' => $what,
			'name' => 'what',
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		$fields[] = array(
			'label' => _KEY,
			'type' => 'text',
			'value' => '',
			'name' => 'xkey',
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		$fields[] = array(
			'label' => _WORD,
			'type' => 'textarea',
			'value' => '',
			'name' => 'xval',
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		
		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e) 
			{
				$this->adding($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
		
		// contents
		$view = new X4View_core('editor');
		$view->title = _ADD_WORD;
		
		// form builder
		$view->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'editor\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Register New dictionary word form data
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function adding($_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_word_creation', 0, 4);
		
		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'lang' => $_post['lang'],
				'area' => $_post['area'],
				'what' => X4Utils_helper::unspace($_post['what']),
				'xkey' => strtoupper(trim($_post['xkey'])),
				'xval' => nl2br(trim($_post['xval']))
			);
			
			$dict = new Dictionary_model();
			
			// check if words already exists
			$check = $dict->exists($post);
			if ($check) 
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_XKEY_ALREADY_EXISTS', 'msg'));
			else 
			{
				// insert
				$result = $dict->insert($post);
				
				// set message
				$msg = AdmUtils_helper::set_msg($result);
				
				// add permission
				if ($result[1])
				{
					$amod = new Area_model();
					$id_area = $amod->get_area_id($_post['area']);
					
					$perm = new Permission_model();
					$array[] = array(
							'action' => 'insert', 
							'id_what' => $result[0], 
							'id_user' => $_SESSION['xuid'], 
							'level' => 4);
					$result = $perm->pexec('dictionary', $array, $id_area);
					
					$msg->update[] = array(
						'element' => 'tdown', 
						'url' => BASE_URL.'dictionary/keys/'.$post['lang'].'/'.$post['area'].'/'.$post['what'],
						'title' => null
					);
				}
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Import all dictionary words from another area (use Ajax)
	 *
	 * @param   string 	$lang Language code
	 * @param   string 	$area Area name
	 * @return  void
	 */
	public function import($lang, $area)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'dictionary'));
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $lang,
			'name' => 'lang'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $area,
			'name' => 'area',
			'extra' => 'class="large"'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'html',
			'value' => '<h4>'._IMPORT_INTO.' '.$lang.'/'.$area.'</h4>'._IMPORT_INTO_MSG
		);
		$dict = new Dictionary_model();
		$fields[] = array(
			'label' => _SECTION,
			'type' => 'select',
			'value' => '',
			'name' => 'what',
			'options' => array($dict->get_section_options(), 'value', 'option'),
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		
		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'import');
			if ($e) 
			{
				$this->importing($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
		
		// contents
		$view = new X4View_core('editor');
		$view->title = _IMPORT_KEYS;
		
		// form builder
		$view->form = X4Form_helper::doform('import', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'import\');"');
		
		$view->render(TRUE);
	}
	
	/**
	 * Perform the importing of words
	 *
	 * @access	private
	 * @param   array	$_post _POST array
	 * @return  void
	 */
	private function importing($_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_key_import', 0, 4);
		
		if (is_null($msg))
		{
			// get key
			list($lang, $area, $what) = explode('-', $_post['what']);
			
			// handle _post
			$post = array(
				'lang' => $_post['lang'],
				'area' => $_post['area'],
				'what' => $what,
				'xon' => 1
			);
			
			// set the translator
			X4Core_core::auto_load('google_translate_library');
			$translator = new GoogleTranslate($lang, $post['lang']);
			
			// get words to import
			$dict = new Dictionary_model();
			
			if ($what == 'ALL')
			{
				// import all sections in an area
				$sections = $dict->get_sections($lang, $area);
				
				$result = true;
				
				foreach($sections as $s)
				{
					// get words in section
					$words = $dict->get_words_to_import($lang, $area, $s->what, $post['lang'], $post['area']);
					
					if (!empty($words))
					{
						$post['what'] = $s->what;
						
						// import
						foreach($words as $i) 
						{
							$post['xkey'] = $i->xkey;
							
							// try to translate
							if ($lang != $post['lang'])
							{
								$value = $translator->translate($i->xval);
							}
							else
							{
								$value = $i->xval;
							}
							
							// set the word
							$post['xval'] = $value;
							
							// insert
							$result = $dict->insert($post);
							
							// add permission
							if ($result[1]) {
								$amod = new Area_model();
								$id_area = $amod->get_area_id($_post['area']);
								$perm = new Permission_model();
								$array[] = array(
										'action' => 'insert', 
										'id_what' => $result[0], 
										'id_user' => $_SESSION['xuid'], 
										'level' => 4);
								$res = $perm->pexec('dictionary', $array, $id_area);
							}
						}
					}
				}
				
				// set what for redirect
				$what = 'global';
			}
			else
			{
				// import only one section
				$words = $dict->get_words_to_import($lang, $area, $what, $post['lang'], $post['area']);
				
				$result = true;
				
				// import
				foreach($words as $i) 
				{
					$post['xkey'] = $i->xkey;
					
					// try to translate
					if ($lang != $post['lang'])
					{
						$value = $translator->translate($i->xval);
					}
					else
					{
						$value = $i->xval;
					}
					
					// set the word
					$post['xval'] = $value;
					
					// insert
					$result = $dict->insert($post);
					
					// add permission
					if ($result[1]) {
						$amod = new Area_model();
						$id_area = $amod->get_area_id($_post['area']);
						$perm = new Permission_model();
						$array[] = array(
								'action' => 'insert', 
								'id_what' => $result[0], 
								'id_user' => $_SESSION['xuid'], 
								'level' => 4);
						$res = $perm->pexec('dictionary', $array, $id_area);
					}
				}
			}
			
			$msg = AdmUtils_helper::set_msg($result);
			
			// set what update
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => 'tdown', 
					'url' => BASE_URL.'dictionary/keys/'.$post['lang'].'/'.$post['area'].'/'.$what,
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
}
