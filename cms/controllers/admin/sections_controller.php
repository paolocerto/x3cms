<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

/**
 * Controller for Sections
 *
 * @package X3CMS
 */
class Sections_controller extends X3ui_controller
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
	 * Show sections by page
	 *
     * @param   integer  $id_area
     * @param   integer  $id_page
	 * @return  void
	 */
	public function index(int $id_area, int $id_page)
	{
		// load the dictionary
		$this->dict->get_wordarray(array('sections'));

		// get page
		$page = $this->get_page('sections');
		$navbar = array($this->site->get_bredcrumb($page));

		// contents
		$view = new X4View_core('sections/sections');
		$view->page = $page;
		$view->navbar = $navbar;

		$mod = new Section_model();
		$view->xpage = $mod->get_by_id($id_page, 'pages', 'id, id_area, lang, name, url, xfrom');

		// get page template sections
		$tpl_sections = $mod->get_template_data($id_page, 'sections');
		$view->sections = $tpl_sections;

		$items = $mod->get_items($id_page);

		$n = $mod->count_default_sections($id_page);
		if ($n != $tpl_sections)
		{
			// create default sections
			$mod->initialize($id_area, $id_page);

			$items = $mod->get_items($id_page);
		}
		$view->items = $items;

		$view->render(TRUE);
	}

	/**
	 * Compose filter
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   integer $id_page Page id
	 * @param	string	$page
	 * @return  string
	 */
	public function filter(int $id_area, string $lang, int $id_page, string $page = 'compose')
	{
		if ($page == 'compose')
		{
			// load the dictionary
			$this->dict->get_wordarray(array('articles'));
			echo '<a class="btf" href="'.BASE_URL.'articles/edit/'.$id_area.'/'.$lang.'/1/x3/'.$id_page.'" title="'._NEW_ARTICLE.'"><i class="fas fa-plus fa-lg"></i></a>
<script>
window.addEvent("domready", function()
{
	buttonize("filters", "btf", "topic", "'.urlencode('sections/compose/'.$id_page).'");
});
</script>';
		}
		else
		{
			// load the dictionary
			$this->dict->get_wordarray(array('sections'));
			echo '<a class="btf" href="'.BASE_URL.'sections/edit/'.$id_area.'/'.$id_page.'" title="'._SECTION_NEW.'"><i class="fas fa-plus fa-lg"></i></a>
<script>
window.addEvent("domready", function()
{
	buttonize("filters", "btf", "modal");
});
</script>';
		}
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
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'sections', $id, $val);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();

			// do action
			$mod = new Section_model();
			$obj = $mod->get_by_id($id);

			$result = $mod->update($id, array($what => $value));

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
	 * New / Edit section form (use Ajax)
	 *
	 * @param   integer	$id area
	 * @param   integer	$id_page
	 * @param   integer	$id Section ID
	 * @return  void
	 */
	public function edit(int $id_area, int $id_page, int $id = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'sections'));

		// get object
		$mod = new Section_model();

		if ($id)
		{
			$item = $mod->get_by_id($id);
		}
		else
		{
			// get page template settings
			$tpl_settings = $mod->get_template_data($id_page, 'settings');

			$settings = json_encode($mod->settings);
			if (!empty($tpl_settings))
			{
				$tps = json_decode($tpl_settings, true);

				if (isset($tps['sn']) && !empty($tps['sn']))
				{
					// template could have less or more or different keys of model settings
					$settings = json_encode($tps['sn']);
				}
			}
			$item = new Section_obj($id_area, $id_page, $settings);
		}

		// build the form
		$form_fields = new X4Form_core('section_edit', '', array('fields' => array()));
		$form_fields->id = $id;
		$form_fields->item = $item;
		$form_fields->mod_settings = $mod->settings;
		// get the fields array
		$fields = $form_fields->render();

		// get the file_array
		$file_array = $form_fields->__get('file_array');

        // get js array
        $js_array = $form_fields->__get('js_array');

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'upload');
			if ($e)
			{
				$this->editing($id, $_POST, $file_array);
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
			? _SECTION_EDIT.' - #'.$item->progressive
			: _SECTION_NEW;

        $view->msg = _SECTION_EDIT_MSG;

		// form builder
		$view->form = '<div id="scrolled">'.X4Form_helper::doform('upload', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', 'enctype="multipart/form-data"',
			'onclick="let n=0;let c=$(\'columns\').get(\'value\');let cs=$(\'col_sizes\').get(\'value\');if(cs!=\'\'){eval(\'n=\'+cs+\';\');if(n!=c){$(\'col_sizes\').addClass(\'softwarn\');return false;}};setUploadForm(\'upload\', \'img_h|img_v\', \'topic\', \''.BASE_URL.'sections/index/'.$id_area.'/'.$id_page.'\');"').'</div>';

		$view->js = '
<script>
var picker1 = new JSColor("#bgcolor");
var picker2 = new JSColor("#fgcolor");
'.implode(NL, $js_array).'

window.addEvent("domready", function()
{
	var myScroll = new Scrollable($("scrolled"));
    saccordion("accordion", "#accordion h4", "#accordion .section");

	X3.single_upload("editor", "img_h");
	X3.single_upload("editor", "img_v");

    $("col_sizes").addEvent("keyup", function(e){
		e.stop();
		this.removeClass("softwarn");
	});
});
</script>';

		$view->render(TRUE);
	}

	/**
	 * Register Edit / New Context form data
	 *
	 * @access	private
	 * @param   integer $id item ID (if 0 then is a new item)
	 * @param   array 	$_post _POST array
	 * @param   array 	$_file_array
	 * @return  void
	 */
	private function editing($id, $_post, $file_array)
	{
		$msg = null;
		// check permission
		$msg = ($id)
			? AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'sections', $id, 3)
			: AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_section_creation', 0, 4);

		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'id_area' => $_post['id_area'],
				'name' => strtolower($_post['name']),
				'id_page' => $_post['id_page']
			);

			// used only with files
			$error = array();

			$mod = new Section_model();

            // get the previuos
            if ($id)
            {
                $settings = json_decode($mod->get_var($id, 'sections', 'settings'), true);
            }
            else
            {
                // do we have settings?
                // get the settings from the template
                $settings = $mod->get_template_data($post['id_page'], 'settings');
                if (!is_null($settings))
                {
                    // set template settings
                    $settings = json_decode($settings, true);
                    $settings = isset($settings['sn'])
                        ? $settings['sn']
                        : $settings['s1'];
                }
                else
                {
                    // set default settings from model
                    $settings = $mod->settings;
                }
            }

			// get data for settings
			if (!$_post['locked'])
			{
				$settings = array(
					'locked' => $_post['locked'],
					'columns' => $_post['columns'],
                    'col_sizes' => $_post['col_sizes'],
					'bgcolor' => $_post['bgcolor'],
					'fgcolor' => $_post['fgcolor'],
					'width' => $_post['width'],
					'height' => $_post['height'],
					'class' => $_post['class'],
				);

                // col_sizes
                if (empty($settings['col_sizes']))
                {
                    $cs = array_fill(0, $settings['columns'], '1');
                    $settings['col_sizes'] = implode('+', $cs);
                }

                // handle column settings
                $nc = sizeof(explode('+', $settings['col_sizes']));
                $tmp = array();
                for ($i = 0; $i < $nc; $i++)
                {
                    if (isset($_post['bg'.$i]))
                    {
                        // store data
                        $tmp['bg'.$i] = $_post['bg'.$i];
                        $tmp['fg'.$i] = $_post['fg'.$i];
                        $tmp['class'.$i] = $_post['class'.$i];
                    }
                }
                $settings['col_settings'] = $tmp;

				// check for default colors
				if (empty($settings['bgcolor']) || isset($_post['bgcolor_default']))
				{
					$settings['bgcolor'] = 'default';
				}

				if (empty($settings['fgcolor']) || isset($_post['fgcolor_default']))
				{
					$settings['fgcolor'] = 'default';
				}

				// there are files?
				$w4k = 3840;
				$h4k = 2160;

				$path = APATH.'files/'.SPREFIX.'/filemanager/';
				$sizes = array($w4k, $h4k, 'RESIZE', $w4k, $h4k);
				$mimes = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');

				// delete previous
				if (isset($_post['delete_img_h']))
				{
					if (!empty($settings['img_h'])) unlink($path.'img/'.$settings['img_h']);
					$settings['img_h'] = '';
				}

				// handle img_h
				if (!empty($_FILES['img_h']['name']))
				{
					// upload file
					$filename = X4Files_helper::upload('img_h', $path, '', 0, $sizes, $mimes);

					// check for errors
					if (!is_array($filename))
					{
						$settings['img_h'] = $filename;
					}
					else
					{
						$error = array_merge($error, $filename[0]);
					}
				}

				// delete previous
				if (isset($_post['delete_img_v']))
				{
					if (!empty($settings['img_v'])) unlink($path.'img/'.$settings['img_v']);
					$settings['img_v'] = '';
				}

				if (!empty($_FILES['img_v']['name']))
				{
					// upload file
					$filename = X4Files_helper::upload('img_v', $path, '', 0, $sizes, $mimes);

					// check for errors
					if (!is_array($filename))
					{
						$settings['img_v'] = $filename;
					}
					else
					{
						$error = array_merge($error, $filename[0]);
					}
				}
				$post['settings'] = json_encode($settings);
			}

			if (empty($error))
			{
				// update or insert
				if ($id)
				{
					$result = $mod->update($id, $post);
				}
				else
				{
					// get the progressive of the new section
					$post['progressive'] = $mod->get_max_pos($post['id_area'], $post['id_page']) + 1;

                    // set template settings
					$post['settings'] = json_encode($settings);
					$result = $mod->insert($post);
				}

				// set message
				$msg = AdmUtils_helper::set_msg($result);

				// set what update
				if ($result[1])
				{
					$msg->update[] = array(
						'element' => 'topic',
						'url' => BASE_URL.'sections/index/'.$post['id_area'].'/'.$post['id_page'],
						'title' => null
					);
				}
			}
			else
			{
				// build msg
				$str = array();
				foreach ($error as $k => $v)
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
	 * Delete section form (use Ajax)
	 *
	 * @param   integer $id Section ID
	 * @return  void
	 */
	public function delete(int $id)
	{
		// get object
		$mod = new Section_model();
		$obj = $mod->get_by_id($id, 'sections', 'id_area, name, progressive, id_page');

		// load dictionaries
		$this->dict->get_wordarray(array('form', 'sections'));

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
		$view->title = _SECTION_DELETE;
		$view->item = '#'.$obj->progressive.' - '.$obj->name;

		// form builder
		$view->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
			'onclick="setForm(\'delete\');"');
		$view->render(TRUE);
	}

	/**
	 * Delete section
	 *
	 * @access	private
	 * @param   integer     $id Section ID
	 * @param   stdClass	$obj Section Obj
	 * @return  void
	 */
	private function deleting(int $id, stdClass $obj)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'sections', $id, 4);

		if (is_null($msg))
		{
			// do action
			$mod = new Section_model();
			$result = $mod->delete($id);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// clear useless permissions
			if ($result[1])
            {
				$perm = new Permission_model();
				$perm->deleting_by_what('sections', $id);

				// set what update
				$msg->update[] = array(
					'element' => 'topic',
					'url' => BASE_URL.'sections/index/'.$obj->id_area.'/'.$obj->id_page,
					'title' => null
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Move item
	 *
	 * @param   integer $id_page
	 * @return  void
	 */
	public function ordering(int $id_page)
	{
		$msg = null;
		if (is_null($msg) && X4Route_core::$post)
		{
		    $mod = new Section_model();

			// handle post
			if (isset($_POST['sort_order']))
			{
			    $elements = explode(',', '0,'.$_POST['sort_order']);
			}

			$items = $mod->get_items($id_page);

			// get fixed sections
			$n = $mod->get_template_data($id_page, 'sections');

			$result = array(0, 1);
			if ($items && !empty($elements))
			{
				foreach ($items as $i)
				{
					$p = array_search($i->id, $elements);
					if ($i->progressive > $n && $i->progressive != $p+$n)
					{
						$res = $mod->update($i->id, array('progressive' => $p+$n));
						if ($result[1] == 1)
						{
							$result = $res;
						}
					}
				}
			}

			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);
		}
		$this->response($msg);
	}

	/**
	 * Page compositing
	 *
	 * @param   integer $id_page Page ID
	 * @param   string  $by sort key
	 * @return  void
	 */
	public function compose(int $id_page, string $by = 'name')
	{
		// load dictionaries
		$this->dict->get_wordarray(array('sections', 'form', 'articles'));

		// get object
		$mod = new Page_model(2, X4Route_core::$lang);
		$page_to_edit = $mod->get_page_by_id($id_page);

		// get page
		$page = $this->get_page('sections/compose');
		$navbar = array($this->site->get_bredcrumb($page), array('pages' => 'index/'.$page_to_edit->id_area.'/'.$page_to_edit->lang));

		// content
		$view = new X4View_core('left');

		// left
		$view->left = new X4View_core('sections/compose');
		$view->left->navbar = $navbar;
		$view->left->pagetoedit = $page_to_edit;
		$smod = new Section_model();
		$view->left->mod = $smod;

		// get contexts
		$view->left->dict = $this->dict;
		$view->left->codes = $smod->get_contexts($page_to_edit->id_area, $page_to_edit->lang);

		// get articles in area/language
		$view->left->articles = $smod->get_articles_to_publish($page_to_edit, $by);

		// get sections
		$sections = $smod->get_sections($id_page);
		if (empty($sections))
		{
		    // initialize
		    //$n = $smod->count_default_sections($id_page);
		    // create default sections
			$smod->initialize($page_to_edit->id_area, $id_page);
		    $sections = $smod->get_sections($id_page);
		}
		$view->left->sections = $sections;
		$view->left->referer = urlencode('sections/compose/'.$id_page);

		// template image
		$theme = $mod->get_theme($page_to_edit->id_area);
		$view->left->layout = (file_exists(PATH.'themes/'.$theme->name.'/img/'.$page_to_edit->tpl.'.png'))
			? ROOT.'themes/'.$theme->name.'/img/'.$page_to_edit->tpl.'.png'
			: '';

		$view->render(TRUE);
	}

    /**
     * Initialize sections
     *
     * @param integer   $id_page
     * @return array    Array of objects
     * /
    private function initialize($method, $id_page)
    {
        //$items = $mod->get_items($id_page);

		$n = $mod->count_default_sections($id_page);
		if ($n != $tpl_sections)
		{
			// create default sections
			$mod->initialize($id_area, $id_page);

			$items = $mod->get_items($id_page);
		}
    }
    */

	/**
	 * Update article settings (context and Page ID) and return article
	 * Called via Ajax
	 * During composition of page contents, user drag and drop articles from context list to page sections and vice versa
	 * When an article move from contexts to sections the system calls this method
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang Language code
	 * @param   integer $id_page Page ID
	 * @param   string  $bid, article unique ID
	 * @return  string
	 */
	public function get_article(int $id_area, string $lang, int $id_page, string $bid)
	{
		// load dictionary
		$this->dict->get_wordarray(array('articles'));

		// get object
		$mod = new Section_model();
		$art = $mod->get_by_bid($id_area, $lang, $bid);

		// set context and id page
		$this->recode_article($id_area, $lang, 'pages', $bid, $id_page);

		// plugin info
		$m = (empty($art->module))
			? _TRAIT_
			: $art->module;

		// parameter info
		$p = (empty($art->param))
			? _TRAIT_
			: $art->param;

		// return article
		echo '<div class="sbox"><b>'.stripslashes($art->name).'</b>'._TRAIT_.'<a class="btm" href="'.BASE_URL.'articles/edit/'.$id_area.'/'.$lang.'/'.$art->code_context.'/'.$art->bid.'" title="'._EDIT.'">'._EDIT.'</a></div>
			'.$art->content.'
			<div class="tbox">'._MODULE.': '.$m.'&nbsp;&nbsp;|&nbsp;&nbsp;'._PARAM.': '.$p.'</div>';
	}

	/**
	 * Recode an article, set context code and page ID
	 * This method is called from get_article and via Ajax
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang Language code
	 * @param   string  $holder context name
	 * @param   string  $bid, article unique ID
	 * @param   integer $id_page Page ID
	 * @return  void
	 */
	public function recode_article(int $id_area, string $lang, string $holder, string $bid, int $id_page = 0)
	{
		// set context and id_page
		$mod = new Section_model();
		$mod->recode($id_area, $lang, $holder, $bid, $id_page);
	}

	/**
	 * Return article's title
	 * Called via Ajax
	 * During composition of page contents, user drag and drop articles from context list to page sections and vice versa
	 * When an article move from sections to contexts the system calls this method
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang Language code
	 * @param   string  $bid, article unique ID
	 * @return  string
	 */
	public function get_title(int $id_area, string $lang, string $bid)
	{
		// get article object
		$mod = new Section_model();
		$art = $mod->get_by_bid($id_area, $lang, $bid);

		// return article's title
		echo '<strong>'.stripslashes($art->name).'</strong>';
	}

	/**
	 * Register page's composition
	 * Use _POST data
	 *
	 * @param   integer item id (if 0 then is a new item)
	 * @param   array 	_POST array
	 * @return  void
	 */
	public function compositing()
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'pages', $_POST['id_page'], 3);

		if (is_null($msg))
		{
			// handle _POST
			$sections = array();
			$post = array(
				'id_area' => $_POST['id_area'],
				'id_page' => $_POST['id_page'],
				'xon' => 1
			);

			// handle _POST for each section
			for($i = 1; $i <= $_POST['snum']; $i++)
			{
				$sort = $_POST['sort'.$i];
				$post['progressive'] = $i;
			    // disable strange behaviour
			    if (strlen($sort) >= 32)
			    {
                    // delete first comma
                    $articles = (substr($sort, 0, 1) == ',')
                        ? substr($_POST['sort'.$i], 1)
                        : $_POST['sort'.$i];

                    $post['articles'] = str_replace(',', '|', $articles);
                    $sections[] = $post;
				}
				else
				{
					// empty sections
					$post['articles'] = '';
					$sections[] = $post;
				}
			}

			// register composition
			$mod = new Section_model();
			$result = $mod->compose($sections);
			APC && apcu_delete(SITE.'sections'.$post['id_page']);

			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);

			// add permissions on new sections
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => 'topic',
					'url' => BASE_URL.'sections/compose/'.$post['id_page'],
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
}
