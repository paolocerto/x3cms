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
 * Controller for Widgets
 *
 * @package X3CMS
 */
class Widgets_controller extends X3ui_controller
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
	 * Widget manager
	 *
	 * @return  void
	 */
	public function _default()
	{
		// load dictionaries
		$this->dict->get_wordarray(array('widgets'));

		// get page
		$page = $this->get_page('widgets');

		// contents
		$view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = AdminUtils_helper::link(
                'memo',
                'widgets:'.$page->lang,
                [],
                _MEMO
            ).$this->actions();

		$view->content = new X4View_core('widgets/widgets_list');
		$view->content->page = $page;

		$mod = new Widget_model();
		$view->content->items = $mod->get_my_widgets();
		$view->render(true);
	}

	/**
	 * Widget actions
	 */
	private function actions() : string
	{
		return '<a class="link" @click="popup(\''.BASE_URL.'widgets/edit\')" title="'._WIDGETS_NEW.'"><i class="fa-solid fa-lg fa-circle-plus"></i></a>';
	}

	/**
	 * Change status
	 */
	public function set(string $what, int $id, int $value = 0) : void
	{
		$msg = null;
		$mod = new Widget_model();
		$id_user = $mod->get_var($id, 'widgets', 'id_user');
		if ($id_user != $_SESSION['xuid'])
        {
			$msg = AdminUtils::set_msg(false, '', $this->dict->get_word('_NOT_PERMITTED', 'msg'));
        }
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();

			// do action
			$mod = new Widget_model();
			$result = $mod->update($id, array($what => $value));

			// set message
			$this->dict->get_words();
			$msg = AdminUtils_helper::set_msg($result);

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
	 * Edit widget
	 */
	public function edit() : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'widgets'));

		// get available widgets
		$mod = new Widget_model();
		$items = $mod->get_available_widgets($_SESSION['xuid']);

		$view = new X4View_core('modal');
		$view->title = _WIDGETS_NEW;

		if ($items)
		{
			// build the form
			$fields = array();

            $fields[] = array(
                'label' => null,
                'type' => 'html',
                'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
            );

			$fields[] = array(
				'label' => _WIDGETS_AVAILABLE,
				'type' => 'select',
				'value' => '',
				'name' => 'id',
				'options' => array($items, 'id', 'what', ''),
				'disabled' => 'wid',
				'rule' => 'required',
				'extra' => 'class="w-full"'
			);

            $fields[] = array(
                'label' => null,
                'type' => 'html',
                'value' => '</div>'
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
            $view->content = new X4View_core('editor');
			$view->content->msg = _WIDGETS_NEW_MSG;

			// form builder
			$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
                '@click="submitForm(\'editor\')"');
		}
		else
		{
			$view->content = '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">
                <p>'._NO_WIDGETS_TO_SET.'</p>
            </div>';
		}
		$view->render(true);
	}

	/**
	 * Edit widget
	 */
	private function editing(array $_post) : void
	{
		$msg = null;
		// check permissions
		$msg = AdminUtils_helper::chk_priv_level(1, 'modules', $_post['id'], 'edit');
		if (is_null($msg))
		{
			// get obj
			$mod = new Widget_model();
			$obj = $mod->get_by_id($_post['id'], 'modules', 'id_area, name, title');

			// handle post
			$post = array(
				'id_area' => $obj->id_area,
				'id_user' => $_SESSION['xuid'],
				'id_module' => $_post['id'],
				'name' => $obj->name,
				'description' => $obj->title
			);

			// xpos
			$xpos = $mod->get_max_pos($_SESSION['xuid']);
			$post['xpos'] = $xpos;

			$result = $mod->insert($post);

			// set message
			$msg = AdminUtils_helper::set_msg($result);

			// set what update
			if ($result[1])
			{
                AdminUtils_helper::set_priv($_SESSION['xuid'], $result[0], 'widgets', $post['id_area']);

				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'widgets'
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Move widgets
	 */
	public function ordering() : void
	{
		$msg = null;
		if (X4Route_core::$input)
		{
			// handle post
            $_post = X4Route_core::$input;
			$elements = $_post['sort_order'];

			// do action
			$mod = new Widget_model();
			$items = $mod->get_my_widgets();

			$result = array(0, 1);
			if ($items && !empty($elements))
			{
				foreach ($items as $i)
				{
					$p = array_search($i->id, $elements) + 1;
					if ($p && $i->xpos != $p)
					{
						$res = $mod->update($i->id, array('xpos' => $p), 'widgets');
						if ($res[1])
                        {
							$result = $res;
                        }
					}
				}
			}

			// set message
			$this->dict->get_words();
			$msg = AdminUtils_helper::set_msg($result);
		}
		$this->response($msg);
	}

	/**
	 * Delete widget form
	 */
	public function delete(int $id) : void
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'widgets'));

		// get object
		$mod = new Widget_model();
		$item = $mod->get_by_id($id, 'widgets', 'description AS name');

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
        $view = new X4View_core('modal');
        $view->title = _WIDGETS_DELETE;
		// contents
		$view->content = new X4View_core('delete');

		$view->content->item = $item->name;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

	/**
	 * Delete widget
	 */
	private function deleting(array $_post) : void
	{
		$msg = null;
		$msg = AdminUtils_helper::chk_priv_level(1, 'widgets', $_post['id'], 'delete');

		if (is_null($msg))
		{
			$mod = new Widget_model();
			$result = $mod->my_delete($_post['id']);

			$msg = AdminUtils_helper::set_msg($result);

			if ($result[1])
			{
				AdminUtils_helper::delete_priv('widgets', $_post['id']);

				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'widgets'
				);
			}
		}
		$this->response($msg);
	}
}
