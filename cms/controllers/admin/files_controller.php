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
 * Controller for Files items
 * 
 * @package X3CMS
 */
class Files_controller extends X3ui_controller 
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
	 * Show files
	 *
	 * @return  void
	 */
	public function _default()
	{
		$this->index(2);
	}
	
	/**
	 * Show files (table view)
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $category category
	 * @param   string  $subcategory subcategory
	 * @param   integer	$type type index
	 * @param   integer	$pp pagination index
	 * @return  void
	 */
	public function index($id_area = 2, $category = '-', $subcategory = '-', $xtype = -1, $pp = 0, $str = '')
	{
		// load dictionary
		$this->dict->get_wordarray(array('files'));
		
		$amod = new Area_model();
	    list($id_area, $areas) = $amod->get_my_areas($id_area);
	    
		// get page
		$page = $this->get_page('files');
		$navbar = array($this->site->get_bredcrumb($page));
		
		$category = urldecode($category);
		$subcategory = urldecode($subcategory);
		
		// content
		$view = new X4View_core('container');
		
		$view->content = new X4View_core('files/file_list');
		$view->content->page = $page;
		$view->content->navbar = $navbar;
		$view->content->id_area = $id_area;
		$view->content->xtype = $xtype;
		$view->content->category = $category;
		$view->content->subcategory = $subcategory;
		$view->content->str = $str;
		
		$mod = new File_model();
		$view->content->items = X4Pagination_helper::paginate($mod->get_files($id_area, $category, $subcategory, $xtype, $str), $pp);
		
		$view->content->file_path = $mod->file_path;
		
		// area switcher
		$view->content->areas = $areas;
		// type switcher
		$view->content->types = $mod->get_types();
		
		// files category switcher
		$view->content->categories = $mod->get_cat($id_area);
		// files subcategory switcher
		$view->content->subcategories = $mod->get_subcat($id_area, $category);
		
		$view->render(TRUE);
	}
	
	/**
	 * Show files (tree view)
	 *
	 * @param   integer $id_area Area ID
	 * @return  void
	 */
	public function tree($id_area)
	{
		// load dictionary
		$this->dict->get_wordarray(array('files'));
		
		// left
		$view = new X4View_core('files/tree');
		$view->id_area = $id_area;
		
		$file = new File_model();
		$view->items = $file->get_tree();
		
		$view->render(TRUE);
	}
	
	/**
	 * Files filter
	 *
	 * @param   integer $id_area Area ID
	 * @param   string $category Files category
	 * @param   string $subcategory Files subcategory
	 * @param   string $str Search string
	 * @return  void
	 */
	public function filter($id_area, $category = '', $subcategory = '', $str = '')
	{
		if ($id_area)
		{
			// load the dictionary
			$this->dict->get_wordarray(array('files'));
			
			if (X4Route_core::$post)
			{
				// set message
				$msg = AdmUtils_helper::set_msg(array(0,1));
				$msg->update[] = array(
						'element' => 'topic', 
						'url' => BASE_URL.'files/index/'.$id_area.'/'.$category.'/'.$subcategory.'/-1/0/'.urlencode(trim($_POST['search'])),
						'title' => null
					);
				$this->response($msg);
			}
			else
			{
				// build the URL
				$tokens = array();
				if (!empty($category))
					$tokens[] = $category;
				if (!empty($subcategory))
					$tokens[] = $subcategory;
				$url = (empty($tokens))
					? ''
					: '/'.implode('/', $tokens);
				
				echo '<button type="button" name="bulk" id="bulk" class="button" onclick="setForm(\'bulk_action\');">'._DELETE_BULK.'</button>
				<input type="checkbox" class="bulker vmiddle" name="bulk_selector" id="bulk_selector"  title="'._SELECT_ALL.'" />
				<form id="searchfile" name="searchfile" action="'.BASE_URL.'files/filter/'.$id_area.$url.'" method="post" onsubmit="return false;">
				<input type="text" name="search" id="search" value="'.urldecode($str).'" />
				<button type="button" name="searcher" class="button" onclick="setForm(\'searchfile\');">'._FIND.'</button>
				</form>
				<a class="btf" href="'.BASE_URL.'files/add/'.$id_area.$url.'" title="'._NEW_FILE.'"><i class="fas fa-plus fa-lg"></i></a>
				
		<script>
		window.addEvent("domready", function()
		{
			buttonize("filters", "btf", "modal");
			bulkize("bulk_selector", "bulkable", "bulk");
		});
		</script>';
			}
		}
		else
		{
			echo '';
		}
	}
	
	/**
	 * Article bulk action
	 *
	 * @param   integer $id_area Area ID
	 * @param   string $category Files category
	 * @param   string $subcategory Files subcategory
	 * @param   string $xtype Type of file
	 * @return  void
	 */
	public function bulk($id_area, $category, $subcategory, $xtype = '')
	{
		$msg = null;
		if (X4Route_core::$post) 
		{
			if (isset($_POST['bulk']))
			{
				if (is_array($_POST['bulk']) && !empty($_POST['bulk']))
				{
					$mod = new File_model();
					$perm = new Permission_model();
					foreach($_POST['bulk'] as $i)
					{
						$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'files', $i, 4);
						if (is_null($msg))
						{
							$result = $mod->delete_file($i);
							if ($result[1])
							{
								$perm->deleting_by_what('files', $i);
							}
						}
					}
					
					// set message
					$this->dict->get_words();
					$msg = AdmUtils_helper::set_msg($result);
					
					// set update
					if ($result[1])
					{
						$msg->update[] = array(
							'element' => 'topic',
							'url' => BASE_URL.'files/index/'.$id_area.'/'.$category.'/'.$subcategory.'/'.$xtype,
							'title' => null
						);
					}
				}
			}
		}
		$this->response($msg);
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
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'files', $id, $val);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// do action 
			$files = new File_model();
			$result = $files->update($id, array($what => $value));
			$file = $files->get_by_id($id);
			
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
	 * New files form
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string  $category Category name
	 * @param   string  $subcategory Subcategory name
	 * @return  void
	 */
	public function add($id_area = 0, $category = '', $subcategory = '')
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'files'));
		
		// build the form
		$fields = array();
		$mod = new File_model();
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _AREA,
			'type' => 'select', 
			'value' => $id_area,
			'name' => 'id_area',
			'options' => array($mod->get_areas(), 'id', 'title'),
			'multiple' => 4,
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => 'files',
			'name' => 'link',
		);
		$fields[] = array(
			'label' => _CATEGORY,
			'type' => 'text', 
			'value' => ($category == '-') ? '' : $category,
			'name' => 'category',
			'extra' => 'class="large"',
			'rule' => 'required'
		);
		
		$fields[] = array(
			'label' => _SUBCATEGORY,
			'type' => 'text', 
			'value' => ($subcategory == '-') ? '' : $subcategory,
			'name' => 'subcategory',
			'extra' => 'class="large"',
			'rule' => 'required'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div></div>'
		);
		
		$fields[] = array(
			'label' => _FILE,
			'type' => 'file', 
			'value' => '',
			'name' => 'xname',
			'rule' => 'required',
			'multiple' => 5,
			'extra' => 'class="large"'
		);
		
		// to handle file's label
		$file_array = array(
			'xname' => _FILE
		);
		
		// if submitted
		if (X4Route_core::$post)
		{			
			$e = X4Validation_helper::form($fields, 'upload');
			if ($e) 
			{
				$this->adding($_POST, $file_array);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
		
		// content
		$view = new X4View_core('files/upload_file');
		
		$view->id_area = $id_area;
		$view->category = $category;
		$view->subcategory = $subcategory;
		
		// form builder
		$view->form = X4Form_helper::doform('upload', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', 'enctype="multipart/form-data"',
			'onclick="setUploadForm(\'upload\', \'xname\');"');
		
		$view->render(TRUE);
	}
	
	/**
	 * Register New files form data
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @param   array 	$file_array Files labels array
	 * @return  void
	 */
	private function adding($_post, $file_array)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_file_upload', 0, 4);
		if (is_null($msg))
		{
			$error = array();
			
			$mod = new File_model();
			$path = $mod->file_path;
			$filename = X4Files_helper::upload('xname', $path);
			
			// check for errors
			if ($filename[1])
			{
				$post = array();
				$files = $filename[0];
				$n = sizeof($files);
				
				// handle _post for each area
				for($i = 0; $i < $n; $i++)
				{
					$xtype = X4Files_helper::get_type_by_name($files[$i]);
					$areas = array();
					foreach($_post['id_area'] as $ii)
					{
						$areas[] = $ii;
						$post[] = array(
							'id_area' => $ii,
							'xtype' => $xtype,
							'category' => X4Utils_helper::unspace($_post['category']),
							'subcategory' => X4Utils_helper::unspace($_post['subcategory']),
							'name' => $files[$i],
							'alt' => $_POST['dida_'.($i+1)],
							'xon' => 1
						);
					}
				}
				
				// insert new files
				$result = $mod->insert_file($post);
				if ($result[1]) 
				{
					// add permission
					$perm = new Permission_model();
					foreach($areas as $i)
					{
						$perm->refactory_table($_SESSION['xuid'], array($i), 'files');
					}
				}
				
				// set message
				$msg = AdmUtils_helper::set_msg($result);
				
				// set what update
				if ($result[1])
				{
					$msg->update[] = array(
						'element' => 'topic', 
						'url' => BASE_URL.'files/index/'.$post[0]['id_area'].'/'.urlencode($post[0]['category']).'/'.urlencode($post[0]['subcategory']),
						'title' => null
					);
				}
			}
			else
			{
				// build msg
				$str = array();
				foreach($filename[0] as $k => $v)
				{
					// each field
					foreach($v as $i)
					{
						// each error
						$str[] = $file_array[$k]._TRAIT_.$this->dict->get_word(strtoupper($i), 'msg');
					}
				}
				$msg = AdmUtils_helper::set_msg(false, '', implode('<br />', $str));
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Return file categories by Area ID in JSON format
	 * Used with autocompleter
	 *
	 * @param   integer $id Area ID
	 * @return  void
	 */
	public function categories($id)
	{
		$files = new File_model();
		$view = new X4View_core('json');
		$view->result = $files->get_cat($id, $_POST['category']);
		$view->render(TRUE);
	}
	
	/**
	 * Return file subcategories by Area ID in JSON format
	 * Used with autocompleter
	 *
	 * @param   integer $id Area ID
	 * @return  void
	 */
	public function subcategories($id)
	{
		$files = new File_model();
		$view = new X4View_core('json');
		$view->result = $files->get_subcat($id, $_POST['subcategory']);
		$view->render(TRUE);
	}
	
	/**
	 * Edit file form (use Ajax)
	 *
	 * @param   integer $id File id
	 * @return  void
	 */
	public function edit($id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'files'));
		
		// get object
		$mod = new File_model();
		$file = $mod->get_by_id($id);
		
		// builde the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id,
			'name' => 'id'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $file->id_area,
			'name' => 'id_area'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $file->xtype,
			'name' => 'xtype'
		);
		
		$area = $mod->get_by_id($file->id_area, 'areas');
		switch ($file->xtype)
		{
			case 0:
				// image
				$folder = X4Files_helper::get_type_by_name($file->name, true);
				$action = (file_exists(APATH.'files/filemanager/img/'.$file->name))
					? '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">
							<img class="thumb" src="'.FPATH.$folder.'/'.$file->name.'?t='.time().'" alt="'.$file->alt.'" /> 
						</div><div class="one-half xs-one-whole">
							<a class="btb" href="'.BASE_URL.'files/editor/'.$file->id.'" title="'._IMAGE_EDIT.'"><i class="fas fa-file-image-o fa-lg"></i> '._IMAGE_EDIT.'</a>
						</div></div>'
					: '';
				break;
			case 2:
				// video
				$action = (file_exists(APATH.'files/filemanager/media/'.$file->name))
					? '<div class="band inner-pad clearfix"><div class="one-whole">
							<a class="btb" href="'.BASE_URL.'files/editor/'.$file->id.'" title="'._VIDEO_EDIT.'"><i class="fas fa-file-video-o fa-lg"></i> '._VIDEO_EDIT.'</a>
						</div></div>'
					: '';
				break;
			case 3:
				// template
				$action = (file_exists(APATH.'files/filemanager/template/'.$file->name))
					? '<div class="band inner-pad clearfix"><div class="one-whole">
						<a class="btb" href="'.BASE_URL.'files/editor/'.$file->id.'" title="'._TEMPLATE_EDIT.'"><i class="fas fa-file-code-o fa-lg"></i> '._TEMPLATE_EDIT.'</a>
					</div></div>'
					: '';
				break;
			default:
				// generic files
				$ext = pathinfo(APATH.'files/filemanager/files/'.$file->name, PATHINFO_EXTENSION);
				if ($ext == 'txt' || $ext == 'csv')
				{
					$action = '<p><a class="btb" href="'.BASE_URL.'files/editor/'.$file->id.'" title="'._TEXT_EDIT.'"><i class="fas fa-file-text-o fa-lg"></i> '._TEXT_EDIT.'</a></p>';
				}
				else
				{
					$action = '';
				}
				break;
		}
			
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<p><b>'.$area->title.'</b>: '.$file->name.'</p>'.$action
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _CATEGORY,
			'type' => 'text', 
			'value' => $file->category,
			'name' => 'category',
			'extra' => 'class="large"',
			'rule' => 'required'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _SUBCATEGORY,
			'type' => 'text', 
			'value' => $file->subcategory,
			'name' => 'subcategory',
			'extra' => 'class="large"',
			'rule' => 'required'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div></div>'
		);
		
		$fields[] = array(
			'label' => _COMMENT,
			'type' => 'textarea', 
			'value' => $file->alt,
			'name' => 'alt'
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
		$view->title = _EDIT_FILE;
		
		// form builder
		$view->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'editor\');"');
		
		if (!empty($action))
		{
			$view->js = '
<script>
window.addEvent("domready", function()
{
	buttonize("simple-modal", "btb", "topic");
});
</script>';
		}
		
		$view->render(TRUE);
	}
	
	/**
	 * Register Edit file form data
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing($_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'files', $_post['id'], 2);
		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'category' => X4Utils_helper::unspace($_post['category']),
				'subcategory' => X4Utils_helper::unspace($_post['subcategory']),
				'alt' => $_post['alt']
				);
			
			// do action
			$mod = new File_model();
			$result = $mod->update($_post['id'], $post);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// set what update
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => 'topic', 
					'url' => BASE_URL.'files/index/'.$_post['id_area'].'/'.$post['category'].'/'.$post['subcategory'],
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Delete File form (use Ajax)
	 *
	 * @param   integer $id File ID
	 * @return  void
	 */
	public function delete($id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'files'));
		
		// get object
		$mod = new File_model();
		$obj = $mod->get_by_id($id);
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $obj->id_area,
			'name' => 'id_area'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $obj->xtype,
			'name' => 'xtype'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $obj->category,
			'name' => 'category'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $obj->subcategory,
			'name' => 'subcategory'
		);
		
		// if submitted
		if (X4Route_core::$post)
		{
			$this->deleting($id, $_POST);
			die;
		}
		
		// contents
		$view = new X4View_core('delete');
		$view->title = _DELETE_FILE;
		$view->item = $obj->name;
		
		// form builder
		$view->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '', 
			'onclick="setForm(\'delete\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Delete file
	 *
	 * @access	private
	 * @param   integer	$id File ID
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function deleting($id, $_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'files', $id, 4);
		
		if (is_null($msg))
		{
			// action
			$mod = new File_model();
			$result = $mod->delete_file($id);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			if ($result[1]) 
			{
				// clear useless permissions
				$perm = new Permission_model();
				$perm->deleting_by_what('files', $id);
				
				// set what update
				$msg->update[] = array(
					'element' => 'topic', 
					'url' => BASE_URL.'files/index/'.$_post['id_area'].'/'.$_post['category'].'/'.$_post['subcategory'],
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Generate Js files for TinyMCE
	 *
	 * @param	integer	$id_area	Area ID
	 * @param   string	$type		List type
	 * @return  string
	 */
	public function js($id_area, $type)
	{
		$mod = new File_model();
		$js = $mod->get_js_list($id_area, $type);
		
		// output
		header('Content-Type: ext/javascript');
		echo $js;
	}
	
	/**
	 * Editor file
	 *
	 * @param integer	$id_file	File ID
	 * @return  void
	 */
	public function editor($id_file)
	{
		$this->dict->get_wordarray(array('files', 'form'));
		
		// get page
		$page = $this->get_page('files/editor');
		$navbar = array($this->site->get_bredcrumb($page));
		
		// content
		$view = new X4View_core('container_two');
		// right
		$view->right = new X4View_core('editor');
		$view->right->close = false;
		
		// left
		$view->content = new X4View_core('files/file_editor');
		$view->content->page = $page;
		
		
		$mod = new File_model();
		$file = $mod->get_by_id($id_file);
		
		if ($file)
		{
			// if the file exists
			$view->content->navbar = $navbar;
			$view->content->id_area = $file->id_area;
			$view->content->file = $file;
			$view->content->file_path = $mod->file_path;
			
			// switch to set where display the form
			$form = 'right';
			$tinymce = false;
			
			$reset = _RESET;
			$submit = _SUBMIT;
			
			// build the form
			$fields = array();
			
			// switch by type
			switch($file->xtype)
			{
			case 0:
				// images

				// image size
				$size = (file_exists($mod->file_path.'img/'.$file->name)) 
					? getimagesize($mod->file_path.'img/'.$file->name) 
					: '';
					
				$view->content->width = $size[0];
				$view->content->height = $size[1];
			
				// editor form
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '<h3> Zoom 1:<span id="zoom_label">1</span></h3>'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'hidden', 
					'value' => $id_file,
					'name' => 'id'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'hidden', 
					'value' => 1,
					'name' => 'zoom'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
				);
				
				$fields[] = array(
					'label' => _IMAGE_XCOORD,
					'type' => 'text',
					'value' => 0,
					'name' => 'xcoord',
					'rule' => 'numeric',
					'extra' => 'class="aright large"'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '</div><div class="one-half xs-one-whole">'
				);
				
				$fields[] = array(
					'label' => _IMAGE_YCOORD,
					'type' => 'text',
					'value' => 0,
					'name' => 'ycoord',
					'rule' => 'numeric',
					'extra' => 'class="aright large"'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '</div></div>'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
				);
				
				$fields[] = array(
					'label' => _IMAGE_WIDTH,
					'type' => 'text',
					'value' => $size[0],
					'name' => 'width',
					'rule' => 'numeric',
					'extra' => 'class="aright large"'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '</div><div class="one-half xs-one-whole">'
				);
				
				$fields[] = array(
					'label' => _IMAGE_HEIGHT,
					'type' => 'text',
					'value' => $size[1],
					'name' => 'height',
					'rule' => 'numeric',
					'extra' => 'class="aright large"'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '</div></div>'
				);
				
				$fields[] = array(
					'label' => _IMAGE_LOCK_RATIO,
					'type' => 'checkbox',
					'value' => 1,
					'name' => 'ratio'
				);
				
				$fields[] = array(
					'label' => _IMAGE_ROTATE,
					'type' => 'slider',
					'value' => 0,
					'name' => 'slider'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'text', 
					'value' => 0,
					'name' => 'rotate',
					'extra' => 'readonly class="large acenter noborder"'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '<div class="acenter" style="overflow:hidden;"><img id="imagethumb" src="'.FPATH.'img/'.$file->name.'?t='.time().'" style="max-width:250px" /></div>'
				);
				
				$fields[] = array(
					'label' => _IMAGE_AS_NEW,
					'type' => 'checkbox',
					'value' => 1,
					'name' => 'asnew',
					'checked' => 1
				);
				
				break;
			case 1:
				// generic file
				
				// template
				$form = 'left';
				$view->right = '';
				
				$fields[] = array(
					'label' => null,
					'type' => 'hidden', 
					'value' => $id_file,
					'name' => 'id'
				);
				
				$content = file_get_contents(APATH.'files/filemanager/files/'.$file->name);
				
				$fields[] = array(
					'label' => _TEMPLATE_EDIT,
					'type' => 'textarea', 
					'value' => $content,
					'name' => 'content'
				);
				break;
				
			case 2:
				// media files
				
				$mime = X4Files_helper::get_mime(APATH.'files/filemanager/media/'.$file->name);
				$data = X4getid3_helper::analyze(APATH.'files/filemanager/media/'.$file->name);
				
				$view->content->mime = $mime;
				$view->content->width = $data['video']['resolution_x'];
				$view->content->height = $data['video']['resolution_y'];
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '<h3> Filesize: '.number_format($data['filesize']/(1024*1024), 2, '.', ',').' MB</h3><p>'._VIDEO_FORMAT_MSG.'</p>'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'hidden', 
					'value' => $id_file,
					'name' => 'id'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'hidden', 
					'value' => $mime,
					'name' => 'old_format'
				);
				
				if ($mime != 'video/x-flv' && $mime != 'application/vnd.adobe.flash.movie' && $mime != 'application/x-shockwave-flash')
				{
					$fields[] = array(
						'label' => _VIDEO_GET_IMAGE,
						'type' => 'checkbox',
						'value' => 1,
						'name' => 'capture'
					);
				}
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '<div id="video_section"><h4>'._VIDEO_EDIT.'</h4>'
				);
				
				$options = array(
					array('value' => 'video/quicktime', 'option' => 'MOV'),
					array('value' => 'video/mp4', 'option' => 'MP4'),
					array('value' => 'video/webm', 'option' => 'WEBM'),
					array('value' => 'video/ogg', 'option' => 'OGV mime 1'),
					array('value' => 'application/ogg', 'option' => 'OGV mime 2'),
					array('value' => 'video/x-flv', 'option' => 'FLV'),
					array('value' => 'video/avi', 'option' => 'AVI'),
					array('value' => 'application/vnd.adobe.flash.movie', 'option' => 'SWF flash-movie'),
					array('value' => 'application/x-shockwave-flash', 'option' => 'SWF shockwave-flash')
				);
				
				$fields[] = array(
					'label' => _VIDEO_FORMAT,
					'type' => 'select',
					'value' => $mime,
					'options' => array(X4Utils_helper::array2obj($options, 'value', 'option'), 'value', 'option'),
					'name' => 'format',
					'extra' => 'class="large"'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'hidden', 
					'value' => $data['video']['resolution_x'],
					'name' => 'old_width'
				);
				
				$fields[] = array(
					'label' => _IMAGE_WIDTH,
					'type' => 'text',
					'value' => $data['video']['resolution_x'],
					'name' => 'width',
					'rule' => 'numeric|min§1',
					'extra' => 'class="aright large"'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '</div><div class="one-half xs-one-whole">'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'hidden', 
					'value' => $data['video']['resolution_y'],
					'name' => 'old_height'
				);
				
				$fields[] = array(
					'label' => _IMAGE_HEIGHT,
					'type' => 'text',
					'value' => $data['video']['resolution_y'],
					'name' => 'height',
					'rule' => 'numeric|min§1',
					'extra' => 'class="aright large"'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '</div></div>'
				);
				
				if ($mime != 'application/vnd.adobe.flash.movie' && $mime != 'application/x-shockwave-flash')
				{
					$fields[] = array(
						'label' => _IMAGE_LOCK_RATIO,
						'type' => 'checkbox',
						'value' => 1,
						'name' => 'ratio'
					);
					
					$fields[] = array(
						'label' => _IMAGE_AS_NEW,
						'type' => 'checkbox',
						'value' => 1,
						'name' => 'asnew',
						'checked' => 1
					);
				}
				else
				{
					$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '<h4>'._VIDEO_SWF_MSG.'</h4>'
				);
					
					$reset = null;
					$submit = null;
				}
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '</div><div id="image_section"><h4>'._VIDEO_GET_IMAGE.'</h4>'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
				);
				
				$fields[] = array(
					'label' => _IMAGE_WIDTH,
					'type' => 'text',
					'value' => $data['video']['resolution_x'],
					'name' => 'iwidth',
					'rule' => 'numeric|min§1',
					'extra' => 'class="aright large"'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '</div><div class="one-half xs-one-whole">'
				);
				
				$fields[] = array(
					'label' => _IMAGE_HEIGHT,
					'type' => 'text',
					'value' => $data['video']['resolution_y'],
					'name' => 'iheight',
					'rule' => 'numeric|min§1',
					'extra' => 'class="aright large"'
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '</div></div>'
				);
				
				$fields[] = array(
					'label' => _VIDEO_SEC,
					'type' => 'text',
					'value' => 0,
					'name' => 'sec',
					'rule' => 'numeric',
					'extra' => 'class="large aright" readonly',
					'suggestion' => _VIDEO_SEC_MSG
				);
				
				$fields[] = array(
					'label' => null,
					'type' => 'html', 
					'value' => '</div>'
				);
				break;
				
			case 3:
				// template
				$form = 'left';
				$tinymce = true;
				
				$fields[] = array(
					'label' => null,
					'type' => 'hidden', 
					'value' => $id_file,
					'name' => 'id'
				);
				
				$content = file_get_contents(APATH.'files/filemanager/template/'.$file->name);
				
				$fields[] = array(
					'label' => _TEMPLATE_EDIT,
					'type' => 'textarea', 
					'value' => $content,
					'name' => 'content'
				);
				break;
				
			}
			
			if ($form == 'right')
			{
				$view->right->title = $file->name;
				$view->right->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array($reset, $submit, 'buttons'), 'post',  '',
				'onclick="setForm(\'editor\')";', 'onclick="reset_editor()"');
			}
			else
			{
				if ($tinymce)
				{
					// edit template
					$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array($reset, $submit, 'buttons'), 'post',  '',
					'onclick="setForm(\'editor\', \'content\')";', 'onclick="reset_editor()"');
					
					$view->content->tinymce = new X4View_core('tinymce');
					$view->content->tinymce->id_area = $file->id_area;
				}
				else
				{
					// edit generic text file 
					$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array($reset, $submit, 'buttons'), 'post',  '',
					'onclick="setForm(\'editor\')";', 'onclick="reset_editor()"');
				}
			}
			
			// if submitted
			if (X4Route_core::$post)
			{
				$e = X4Validation_helper::form($fields, 'editor');
				if ($e) 
				{
					$this->saving($id_file, $_POST);
				}
				else
				{
					$this->notice($fields);
				}
				die;
			}
			
			$view->render(TRUE);
		}
		else
		{
			header('Location: '.BASE_URL.'files');
		}
	}
	
	/**
	 * Register Edited image
	 *
	 * @access	private
	 * @param   integer $id File ID (if 0 then is a new item)
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function saving($id_file, $_post)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'files', $id_file, 2);
			
		if (is_null($msg))
		{
			$ko = _MSG_ERROR;
			
			// check if set asnew
			$asnew = intval(isset($_post['asnew']));
			
			$mod = new File_model();
			$file = $mod->get_by_id($id_file);
			
			if ($file)
			{
				switch($file->xtype)
				{
				case 0:
					// images
					$path = APATH.'files/filemanager/img/';
					
					$rotation = intval($_post['rotate']);
					$rotation = ($rotation)
						? 360 - $rotation
						: 0;
					
					if ($asnew)
					{
						// save a new file
						
						// set the new name
						$final_name = X4Files_helper::get_final_name($path, $file->name);
						
						$chk = X4Files_helper::create_cropped($path.$file->name, $path.$final_name, array($_post['width'], $_post['height']), array($_post['xcoord'], $_post['ycoord']), true);
						
						if ($chk)
						{
							$post = array();
							$post[] = array(
								'id_area' => $file->id_area,
								'xtype' => $file->xtype,
								'category' => $file->category,
								'subcategory' => $file->subcategory,
								'name' => $final_name,
								'alt' => $file->alt,
								'xon' => 1
							);
							
							// insert
							$result = $mod->insert_file($post);
							
							// create permissions
							if ($result[1]) 
							{
								$id = $result[0];
								$perm = new Permission_model();
								// privs permissions
								$array[] = array(
										'action' => 'insert', 
										'id_what' => $id, 
										'id_user' => $_SESSION['xuid'], 
										'level' => 4);
								$res = $perm->pexec('files', $array, $file->id_area);
								
								if ($rotation)
								{
									sleep(1);
									$res = X4Files_helper::rotate($path.$final_name, $path.$final_name, $rotation);
								}
							}
						}
						else
						{
							$result = array($_post['id'], intval($chk));	
						}
					}
					else
					{
						// replace old
						$chk = X4Files_helper::create_cropped($path.$file->name, $path.$file->name, array($_post['width'], $_post['height']), array($_post['xcoord'], $_post['ycoord']), true);
						
						if ($chk && $rotation)
						{
							sleep(1);
							$res = X4Files_helper::rotate($path.$file->name, $path.$file->name, $rotation);
						}
						
						$result = array($_post['id'], intval($chk));
						$id = $file->id;
					}
					break;
					
				case 1:
					// generic text file
					$path = APATH.'files/filemanager/files/';
					
					$txt = $_post['content'];
					
					$res = file_put_contents($path.$file->name, $txt);
					$id = $id_file;
					$result = array($id, intval($res));
					break;
					
				case 2:
					// video file
					
					// get the command, if exists
					$ffmpeg = str_replace(NL, '', $this->command_exist('ffmpeg')); 
					if (!empty($ffmpeg))
					{
						$file_name = $file->name;
						
						$mimes = array(
							'video/quicktime' => 'mov',
							'video/mp4' => 'mp4',
							'video/webm'=> 'webm',
							'video/ogg' => 'ogv',
							'application/ogg' => 'ogv',
							'video/x-flv' => 'flv',
							'video/avi' => 'avi',
							'application/vnd.adobe.flash.movie' => 'swf', 
							'application/x-shockwave-flash' => 'swf'
						);
						
						if (isset($_post['capture']))
						{
							// we have to extract a frame
							$vpath = APATH.'files/filemanager/media/';
							$ipath = APATH.'files/filemanager/img/';
							
							$file_name = str_replace($mimes[$_post['old_format']], 'jpg', $file_name);
							
							// set the new name
							$final_name = X4Files_helper::get_final_name($ipath, $file_name);
							
							//ffmpeg -i video_file -an -ss 27.888237 -vframes 1 -s 320x240 -f image2 image_file
							$chk = shell_exec($ffmpeg.' -i '.$vpath.$file->name.' -an -ss '.$_post['sec'].' -vframes 1 -s '.$_post['iwidth'].'x'.$_post['iheight'].' -f image2 '.$ipath.$final_name.' 2>&1');
							
							if ($chk && file_exists($ipath.$final_name))
							{
								chmod($ipath.$final_name, 0777);
								
								$post = array();
								$post[] = array(
									'id_area' => $file->id_area,
									'xtype' => 0,
									'category' => $file->category,
									'subcategory' => $file->subcategory,
									'name' => $final_name,
									'alt' => $file->alt,
									'xon' => 1
								);
								
								// insert
								$result = $mod->insert_file($post);
								
								// create permissions
								if ($result[1]) 
								{
									$id = $result[0];
									$perm = new Permission_model();
									// privs permissions
									$array[] = array(
											'action' => 'insert', 
											'id_what' => $id, 
											'id_user' => $_SESSION['xuid'], 
											'level' => 4);
									$res = $perm->pexec('files', $array, $file->id_area);
								}
							}
						}
						else
						{
							// is a video conversion
							$path = APATH.'files/filemanager/media/';
							
							$new_format = $new_size = 0;
							
							if ($_post['old_width'] != $_post['width'] || $_post['old_height'] != $_post['height'])
							{
								$new_size = 1;
							}
							
							// if new format is a new file
							if ($_post['old_format'] != $_post['format'])
							{
								$new_format = 1;
								$file_name = str_replace($mimes[$_post['old_format']], $mimes[$_post['format']], $file_name);
							}
						
						
							if ($asnew || $new_format)
							{
								// save a new file
								
								// set the new name
								$final_name = X4Files_helper::get_final_name($path, $file_name);
		
								if ($new_size)
								{
									$chk = shell_exec($ffmpeg.' -i '.$path.$file->name.' -vf scale='.$_post['width'].':'.$_post['height'].' '.$path.$final_name.' 2>&1');
								}
								else
								{
									// -c:a copy 
									$chk = shell_exec($ffmpeg.' -i '.$path.$file->name.' '.$path.$final_name.' 2>&1');
								}
								
								if ($chk)
								{
									chmod($path.$final_name, 0777);
									
									$post = array();
									$post[] = array(
										'id_area' => $file->id_area,
										'xtype' => $file->xtype,
										'category' => $file->category,
										'subcategory' => $file->subcategory,
										'name' => $final_name,
										'alt' => $file->alt,
										'xon' => 1
									);
									
									// insert
									$result = $mod->insert_file($post);
									
									// create permissions
									if ($result[1]) 
									{
										$id = $result[0];
										$perm = new Permission_model();
										// privs permissions
										$array[] = array(
												'action' => 'insert', 
												'id_what' => $id, 
												'id_user' => $_SESSION['xuid'], 
												'level' => 4);
										$res = $perm->pexec('files', $array, $file->id_area);
									}
								}
							}
							else
							{
								// replace old
								if ($new_size)
								{
									$chk = shell_exec($ffmpeg.' -i '.$path.$file->name.' -vf scale='.$_post['width'].':'.$_post['height'].' '.$path.$file->name.' 2>&1');
								}
								else
								{
									$chk = 1;
								}
								
								$result = array($_post['id'], intval($chk));
								$id = $result[0];
							}
						} 
					}
					else
					{
						// ffmpeg not available
						$result = array(0, 0);
						$ko = _FFMPEG_NOT_FOUND;
					}
					break;
				case 3:
					// template
					$path = APATH.'files/filemanager/template/';
					
					if (extension_loaded('php5-tidy')) 
					{
						// clean the code
						$tidy = tidy_parse_string($_post['content']);
						$tidy->cleanRepair();
						$html = $tidy->html();
					}
					else
					{
						$html = $_post['content'];
					}
					
					$res = file_put_contents($path.$file->name, $html);
					$id = $id_file;
					$result = array($id, intval($res));
					break;
					
				}
				
				// set message
				$msg = AdmUtils_helper::set_msg($result, _MSG_OK, $ko);
				
				// set what update
				if ($result[1])
				{
					$msg->update[] = array(
						'element' => 'topic', 
						'url' => BASE_URL.'files/editor/'.$id,
						'title' => null
					);
				}
			}
			else
			{
				// file not found
				// set message
				$msg = AdmUtils_helper::set_msg(array(0, 0));
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Check if a shell command is available for PHP
	 *
	 * @access	private
	 * @param	string $cmd	Command name
	 * @return	string
	 */
	private function command_exist($cmd) 
	{
		return shell_exec("which $cmd");
	}
}
