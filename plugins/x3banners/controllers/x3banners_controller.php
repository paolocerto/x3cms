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
 * x3banners controller
 * This controller work only on the admin side
 *
 * @package		X3CMS
 */
class X3banners_controller extends X3ui_controller implements X3plugin_controller
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
	 * Default method
	 * This method is required
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang Language code
	 * @param   integer $pp Pagination index
	 * @return  void
	 */
	public function mod(int $id_area = 2, string $lang = '', int $pp = 0)
	{
		// load dictionary
		$this->dict->get_wordarray(array('x3banners'));

		// initialize lang
		$lang = (empty($lang))
			? X4Route_core::$lang
			: $lang;

        // get query string from filter
        $qs = X4Route_core::get_query_string();

        // handle filters
        $qs['xstr'] = $qs['xstr'] ?? '';
        $qs['xid_page'] = $qs['xid_page'] ?? 0;

		// get page
		$page = $this->get_page('x3banners/mod');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page), array('modules' => 'index/'.$id_area));
		$view->actions = $this->actions($id_area, $lang);

		// content
		$view->content = new X4View_core('x3banners_list', 'x3banners');
		$view->content->pp = $pp;
        $view->content->qs = $qs;

		$mod = new X3banners_model();

		$view->content->items = X4Pagination_helper::paginate($mod->get_items($id_area, $lang, $qs), $pp);
        $view->content->pages = $mod->get_pages($id_area, $lang);

		// language switcher
		$view->content->lang = $lang;
        if (MULTILANGUAGE)
        {
            $lang = new Language_model();
            $view->content->langs = $lang->get_languages();
        }

		// area switcher
		$view->content->id_area = $id_area;
		$area = new Area_model();
		$view->content->areas = $area->get_areas();
		$view->render(true);
	}

	/**
	 * x3banners actions
	 *
     * @param   integer $id_area
	 * @param	string	$lang
	 * @return  void
	 */
	public function actions(int $id_area, string $lang)
	{
		return '<a class="link" @click="popup(\''.BASE_URL.'x3banners/edit/'.$id_area.'/'.$lang.'\')" title="'._X3BANNERS_ADD.'">
                <i class="fa-solid fa-lg fa-circle-plus"></i>
            </a>';
	}

	/**
	 * Change status
	 *
	 * @param   string	$what field to change
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
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'x3_banners', $id, $val);
		if (is_null($msg))
		{
			// do action
			$mod = new X3banners_model();
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
	 * Edit item
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   integer $id Item ID
	 * @return  void
	 */
	public function edit(int $id_area, string $lang, int $id = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'x3banners'));

		// get object
		$mod = new X3banners_model();
		$item = ($id)
			? $mod->get_by_id($id)
			: new Obj_x3banners($id_area, $lang);

        // build the form
        $form_fields = new X4Form_core('x3banners_edit', 'x3banners');
        $form_fields->id = $id;
        $form_fields->item = $item;
        $form_fields->pages = $mod->get_pages($id_area, $lang);

        // get the fields array
        $fields = $form_fields->render();

        // get the file_array
		$file_array = $form_fields->__get('file_array');

        // get js array
        $js_array = $form_fields->__get('js_array');

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields);
			if ($e)
			{
				$this->editing($_POST, $file_array);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
        $view = new X4View_core('modal');
        $view->wide = 'md:w-2/3 lg:w-2/3';
        $view->title = ($id)
			? _X3BANNERS_EDIT
			: _X3BANNERS_ADD;
		// content
		$view->content = new X4View_core('editor');

		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

		$view->render(true);
	}

	/**
	 * Register Edit / New item
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
     * @param   array 	$file_array File\'s labels array
	 * @return  void
	 */
	private function editing(array $_post, array $file_array)
	{
		$msg = null;
		// check permission
		$msg = ($_post['id'])
			? AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'x3_banners', $_post['id'], 2)
			: AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_x3banners_creation', 0, 4);
		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'id_area' => $_post['id_area'],
				'lang' => $_post['lang'],
				'title' => $_post['title'],
				'description' => $_post['description'],
                'id_page' => $_post['id_page'],
                'start_date' => $_post['start_date'].':00',
                'end_date' => $_post['end_date'].':00',
                'bg_color' => $_post['bg_color'],
                'fg_color' => $_post['fg_color'],
                'link_color' => $_post['link_color'],
                'auto_hide' => $_post['auto_hide']
			);

            $mod = new X3banners_model();

            // update or insert
            if ($_post['id'])
            {
                $result = $mod->update($_post['id'], $post);
            }
            else
            {
                $result = $mod->insert($post);
            }

            // set message
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
	 * Delete item
	 *
	 * @param   integer $id Item ID
	 * @return  void
	 */
	public function delete(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'x3banners'));

		// get object
		$mod = new X3banners_model();
		$item = $mod->get_by_id($id, 'x3_banners', 'id, title');
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
        $view->title = _X3BANNERS_DELETE;
		// contents
		$view->content = new X4View_core('delete');
		$view->content->item = $item->title;
		//$view->content->msg = _X3BANNERS_DELETE_MSG;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

	/**
	 * Deleting item
	 *
	 * @access	private
	 * @param   object 	$item Item object
	 * @return  void
	 */
	private function deleting(stdClass $item)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'x3_banners', $item->id, 4);
		if (is_null($msg))
		{
			// action
			$mod = new X3banners_model();
			$result = $mod->delete($item->id, 'x3_banners');

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// clear useless permissions
			if ($result[1])
			{
				$perm = new Permission_model();
				$perm->deleting_by_what('x3_banners', $item->id);

				// set what update
				$msg->update = array(
					'element' => 'page',
					'url' => $_SERVER['HTTP_REFERER']
				);
			}
		}
		$this->response($msg);
	}
}
