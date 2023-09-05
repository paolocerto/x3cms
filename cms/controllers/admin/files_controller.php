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
	 * @param   integer	$pp pagination index
	 * @return  void
	 */
	public function index(int $id_area = 2, int $pp = 0)
	{
		// load dictionary
		$this->dict->get_wordarray(array('files'));

        // get query string from filter
        $qs = X4Route_core::get_query_string();

		$amod = new Area_model();
	    list($id_area, $areas) = $amod->get_my_areas($id_area);

        // handle filters
        $qs['xstr'] = $qs['xstr'] ?? '';
        $qs['xxtype'] = $qs['xxtype'] ?? -1;
        $qs['xctg'] = $qs['xctg'] ?? '';
        $qs['xsctg'] = $qs['xsctg'] ?? '';

		// get page
		$page = $this->get_page('files');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = $this->actions($id_area, $qs);

		// contents
		$view->content = new X4View_core('files/file_list');
		$view->content->id_area = $id_area;
        $view->content->qs = $qs;

		$mod = new File_model();
		$view->content->items = X4Pagination_helper::paginate($mod->get_files($id_area, $qs), $pp);

		$view->content->file_path = $mod->file_path;

		// area switcher
		$view->content->areas = $areas;
		// type switcher
		$view->content->types = $mod->get_types();
		// files category switcher
		$view->content->categories = $mod->get_cat($id_area);
		// files subcategory switcher
		$view->content->subcategories = $mod->get_subcat($id_area, $qs['xctg']);

		$view->render(TRUE);
	}

	/**
	 * Show files (tree view)
	 *
	 * @param   integer $id_area Area ID
	 * @return  void
	 */
	public function tree(int $id_area)
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
	 * @param   array   $qs
	 * @return  void
	 */
	public function actions(int $id_area, array $qs)
	{
        return '<a class="link" @click="popup(\''.BASE_URL.'files/upload/'.$id_area.'?'.http_build_query($qs).'\')" title="'._NEW_FILE.'">
            <i class="fa-solid fa-lg fa-circle-plus"></i>
        </a>';
	}

	/**
	 * File bulk action
	 *
	 * @param   integer $id_area Area ID
	 * @return  void
	 */
	public function bulk(int $id_area)
	{
		$msg = null;
        $_post = X4Route_core::$input;
		if (!empty($_post) && isset($_post['bulk']) && is_array($_post['bulk']) && !empty($_post['bulk']))
		{
            $qs = X4Route_core::get_query_string();

            $mod = new File_model();
            $perm = new Permission_model();

            // NOTE: we here have only bulk_action = delete
            foreach ($_post['bulk'] as $i)
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
                $msg->update = array(
                    'element' => 'page',
                    'url' => BASE_URL.'files/index/'.$id_area.'?'.http_build_query($qs)
                );
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
	public function set(string $what, int $id, int $value = 0)
	{
		$msg = null;
		// check permission
		$val = ($what == 'xlock')
			? 4
			: 3;
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'files', $id, $val);
		if (is_null($msg))
		{
			// do action
			$files = new File_model();
			$result = $files->update($id, array($what => $value));

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
	 * New files form
	 *
	 * @param   integer	$id_area Area ID
	 * @return  void
	 */
	public function upload(int $id_area)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'files'));
        // get query string from filter
        $qs = X4Route_core::get_query_string();

		$mod = new File_model();

        // build the form
        $form_fields = new X4Form_core('file/file_upload');
        $form_fields->id_area = $id_area;
		$form_fields->areas = $mod->get_areas();
        $form_fields->ctg = $qs['xctg'];
        $form_fields->sctg = $qs['xsctg'];

		// get the fields array
		$fields = $form_fields->render();

		// to handle file's label
		$file_array = array(
			'filename' => _FILE
		);

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->uploading($_POST, $file_array);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
        $view = new X4View_core('modal');
        $view->title = _UPLOAD_FILE;
        $view->wide = 'md:inset-x-6 lg:w-3/4 xl:w-2/3';
		// content
		$view->content = new X4View_core('editor');

		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _UPLOAD_FILE, 'buttons'), 'post', 'enctype="multipart/form-data"',
            '@click="submitForm(\'editor\')" x-bind:disabled="files[\'filename\'] != null && !files[\'filename\'].length"');

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
	private function uploading(array $_post, array $file_array)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_file_upload', 0, 4);
		if (is_null($msg))
		{
            $mod = new File_model();
            $filename = X4Files_helper::upload('filename', $mod->file_path);

			// check for errors
			if ($filename[1])
			{
				$post = array();
				$files = $filename[0];
				$n = sizeof($files);

                // handle file desriptions
                $alt = [];
                for ($i = 0; $i < $n; $i++)
                {
                    if (isset($_post['namef_'.$i]))
                    {
                        //$tmpname = X4Utils_helper::slugify(strtolower($_post['namef_'.$i]));
                        $alt[$i] = empty($_post['altf_'.$i])
                            ? $_post['namef_'.$i]
                            : strip_tags($_post['altf_'.$i]);
                    }
                }

				// handle _post for each area
				for($i = 0; $i < $n; $i++)
				{
					$xtype = X4Files_helper::get_type_by_name($files[$i]);
					$areas = array();
					foreach ($_post['id_area'] as $ii)
					{
						$areas[] = $ii;
						$post[] = array(
							'id_area' => $ii,
							'xtype' => $xtype,
							'category' => X4Utils_helper::slugify($_post['category']),
							'subcategory' => X4Utils_helper::slugify($_post['subcategory']),
							'name' => $files[$i],
							'alt' => $alt[$i],
							'xon' => 1
						);
					}
				}

				// insert new files
				$result = $mod->insert_file($post);

				// set message
				$msg = AdmUtils_helper::set_msg($result);

				// set what update
				if ($result[1])
				{
                    $qs = [
                        'xxtype' => -1,
                        'xctg' => $post[0]['category'],
                        'xsctg' => $post[0]['subcategory']
                    ];

					$msg->update = array(
						'element' => 'page',
						'url' => BASE_URL.'files/index/'.$post[0]['id_area'].'?'.http_build_query($qs)
					);
				}
			}
			else
			{
				// build msg
				$str = array();
				foreach ($filename[0] as $k => $v)
				{
					// each field
					foreach ($v as $i)
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
	public function categories(int $id)
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
	public function subcategories(int $id)
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
	public function edit(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'files'));

		// get object
		$mod = new File_model();
		$file = $mod->get_by_id($id);

        // build the form
        $form_fields = new X4Form_core('file/file_edit');

        $form_fields->area = $mod->get_by_id($file->id_area, 'areas');;
        $form_fields->file = $file;

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
        $view->title = _EDIT_FILE;
        $view->wide = 'md:inset-x-6 lg:w-3/4 xl:w-2/3';
		// contents
		$view->content = new X4View_core('editor');

		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

		$view->render(TRUE);
	}

	/**
	 * Register Edit file form data
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing(array $_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'files', $_post['id'], 2);
		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'category' => X4Utils_helper::slugify($_post['category']),
				'subcategory' => X4Utils_helper::slugify($_post['subcategory']),
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
                $qs = [
                    'xxtype' => -1,
                    'xctg' => $post['category'],
                    'xsctg' => $post['subcategory']
                ];

                $msg->update = array(
                    'element' => 'page',
                    'url' => BASE_URL.'files/index/'.$_post['id_area'].'?'.http_build_query($qs)
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
	public function delete(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'files'));

		// get object
		$mod = new File_model();
		$item = $mod->get_by_id($id);

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
        $view->title = _DELETE_FILE;
		// contents
		$view->content = new X4View_core('delete');

		$view->content->item = $item->name;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(TRUE);
	}

	/**
	 * Delete file
	 *
	 * @access	private
	 * @param   stdClass $item
	 * @return  void
	 */
	private function deleting(stdClass $item)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'files', $item->id, 4);
		if (is_null($msg))
		{
			// action
			$mod = new File_model();
			$result = $mod->delete_file($item->id);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			if ($result[1])
			{
				// clear useless permissions
				$perm = new Permission_model();
				$perm->deleting_by_what('files', $item->id);

                $qs = [
                    'xxtype' => -1,
                    'xctg' => $item->category,
                    'xsctg' => $item->subcategory
                ];

				// set what update
				$msg->update = array(
					'element' => 'topic',
					'url' => BASE_URL.'files/index/'.$item->id_area.'?'.http_build_query($qs)
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
	public function js(int $id_area, string $type)
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
	public function editor(int $id_file)
	{
		$this->dict->get_wordarray(array('files', 'form'));

		// get page
		$page = $this->get_page('files/editor');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = '';

		// content
		$view->content = new X4View_core('files/editor_container');

        // TODO

        // left
		$view->content->left = new X4View_core('files/file_editor');
		// right
		$view->content->right = new X4View_core('editor');

		$mod = new File_model();
		$file = $mod->get_by_id($id_file);

		if ($file)
		{
			// if the file exists
			$view->content->left->id_area = $file->id_area;
			$view->content->left->file = $file;
			$view->content->left->file_path = $mod->file_path;

			// switch to set where display the form
			$form = 'right';
			$tinymce = false;

			$reset = _RESET;
			$submit = _SUBMIT;

			// build the form
			// switch by type
			switch($file->xtype)
			{
			case 0:
				// images

                // image size
				$size = (file_exists($mod->file_path.'img/'.$file->name))
                    ? getimagesize($mod->file_path.'img/'.$file->name)
                    : '';

                $view->content->left->width = $size[0];
                $view->content->left->height = $size[1];

                $form_fields = new X4Form_core('file/file_editor_image');

                $form_fields->id_file = $id_file;
                $form_fields->file = $file;
                $form_fields->size = $size;
                // get the fields array
                $fields = $form_fields->render();
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

					$content = file_get_contents(APATH.'files/'.SPREFIX.'/filemanager/files/'.$file->name);

				$fields[] = array(
					'label' => _TEMPLATE_EDIT,
					'type' => 'textarea',
					'value' => $content,
					'name' => 'content'
				);
				break;

			case 2:
				// media files

				$mime = X4Files_helper::get_mime(APATH.'files/'.SPREFIX.'/filemanager/media/'.$file->name);
				$data = X4getid3_helper::analyze(APATH.'files/'.SPREFIX.'/filemanager/media/'.$file->name);

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
					'options' => array(X4Array_helper::array2obj($options, 'value', 'option'), 'value', 'option'),
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

				$content = file_get_contents(APATH.'files/'.SPREFIX.'/filemanager/template/'.$file->name);

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
				//$view->content->right->title = $file->name;
				$view->content->right->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array($reset, $submit, 'buttons'), 'post',  '',
                    '@click="submitForm(\'editor\')"');
                //'onclick="setForm(\'editor\')";', 'onclick="reset_editor()"');
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
	private function saving(int $id_file, array $_post)
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
					$path = APATH.'files/'.SPREFIX.'/filemanager/img/';

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
					$path = APATH.'files/'.SPREFIX.'/filemanager/files/';

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
							$vpath = APATH.'files/'.SPREFIX.'/filemanager/media/';
							$ipath = APATH.'files/'.SPREFIX.'/filemanager/img/';

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
							$path = APATH.'files/'.SPREFIX.'/filemanager/media/';

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
					$path = APATH.'files/'.SPREFIX.'/filemanager/template/';

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
	private function command_exist(string $cmd)
	{
		return shell_exec("which $cmd");
	}

    /**
	 * Re-check images without thumb
	 *
	 * @return	void
	 */
	public function recheck()
	{
		// define starting folder
        $img_folder = FFPATH.SPREFIX.'/filemanager';

        $this->listFolderFiles($img_folder);
	}

    /**
	 * Get content of a folder
	 *
     * @param   string   $dir
	 * @return	array
	 */
    private function listFolderFiles(string $dir)
    {
        // get dir content
        $ffs = scandir($dir);

        // remove useless content
        unset($ffs[array_search('.', $ffs, true)]);
        unset($ffs[array_search('..', $ffs, true)]);

        // prevent empty returns
        if (count($ffs) > 0)
        {
            $this->reThumb($dir, $ffs);
        }
    }

    /**
	 * Re-thumb images without thumb
	 *
     * @param   string  $folder
     * @param   array   $files
	 * @return	string
	 */
	public function reThumb(string $folder, array $files)
	{
		// define thumb folder
        $thumb_folder = FFPATH.SPREFIX.'/thumbs/';

        // set thumb size
        $sizes = array(122, 91);

        echo '<br>FILE in  '.$folder.'<br>';

        foreach ($files as $file)
        {
            if (is_dir($folder.'/'.$file))
            {
                // recursive call
                $this->listFolderFiles($folder.'/'.$file);
            }
            else
            {
                // is it an image?
                $type = X4Files_helper::get_type_by_name($folder.'/'.$file);

                if ($type == 0 && !file_exists($thumb_folder.'/'.$file))
                {
                    // create thumb
                    $res = X4Files_helper::create_resized($folder.'/'.$file, $thumb_folder.$file, $sizes);

                    if ($res)
                    {
                        echo '<br>created '.$thumb_folder.$file;
                    }
                    else
                    {
                        echo '<br>FAILED '.$thumb_folder.$file;
                    }
                }
            }
        }
	}
}
