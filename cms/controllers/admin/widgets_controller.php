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
 * Controller for Widgets
 *
 * @package X3CMS
 */
class Widgets_controller extends X3ui_controller
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

		// content
		$view = new X4View_core('widgets/widgets_list');
		$view->page = $page;

		$mod = new Widget_model();
		$view->items = $mod->get_my_widgets();
		$view->render(TRUE);
	}

	/**
	 * Widget filter
	 *
	 * @return  void
	 */
	public function filter()
	{
		// load the dictionary
		$this->dict->get_wordarray(array('widgets'));

		echo '<a class="btf" href="'.BASE_URL.'widgets/edit" title="'._WIDGETS_NEW.'"><i class="fas fa-plus fa-lg"></i></a>
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
	public function set(string $what, int $id, int $value = 0)
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
	 * Edit widget
	 *
	 * @return  void
	 */
	public function edit()
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'widgets'));

		// get available widgets
		$mod = new Widget_model();
		$items = $mod->get_available_widgets($_SESSION['xuid']);

		$view = new X4View_core('editor');
		$view->title = _WIDGETS_NEW;

		if ($items)
		{
			// build the form
			$fields = array();

			$fields[] = array(
				'label' => _WIDGETS_AVAILABLE,
				'type' => 'select',
				'value' => '',
				'name' => 'id',
				'options' => array($items, 'id', 'what', ''),
				'disabled' => 'wid',
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
			$view->msg = _WIDGETS_NEW_MSG;

			// form builder
			$view->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
				'onclick="setForm(\'editor\');"');
		}
		else
		{
			$view->form = '<p>'._NO_WIDGETS_TO_SET.'</p>';
		}
		$view->render(TRUE);
	}

	/**
	 * Edit widget
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing(array $_post)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'modules', $_post['id'], 1);

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
			$msg = AdmUtils_helper::set_msg($result);

			// set what update
			if ($result[1])
			{
				$perm = new Permission_model();
				$array[] = array(
						'action' => 'insert',
						'id_what' => $result[0],
						'id_user' => $_SESSION['xuid'],
						'level' => 4);
				$perm->pexec('widgets', $array, $post['id_area']);

				$msg->update[] = array(
					'element' => 'topic',
					'url' => BASE_URL.'widgets',
					'title' => null
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Move widgets
	 *
	 * @return  void
	 */
	public function ordering()
	{
		$msg = null;
		if (X4Route_core::$post)
		{
			// handle post
			$elements = explode(',', $_POST['sort_order']);

			// do action
			$mod = new Widget_model();
			$items = $mod->get_my_widgets();

			$result = array(0, 1);
			if ($items)
			{
				foreach ($items as $i)
				{
					$p = array_search($i->id, $elements) + 1;
					if ($p && $i->xpos != $p)
					{
						$res = $mod->update($i->id, array('xpos' => $p), 'widgets');
						if ($result[1] == 1)
							$result = $res;
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
	 * Delete widget form (use Ajax)
	 *
	 * @param   integer $id Bookmark ID
	 * @return  void
	 */
	public function delete(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'widgets'));

		// get object
		$mod = new Widget_model();
		$obj = $mod->get_by_id($id, 'widgets', 'name');

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

		// contents
		$view = new X4View_core('delete');
		$view->title = _WIDGETS_DELETE;
		$view->item = $obj->name;

		// form builder
		$view->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
			'onclick="setForm(\'delete\');"');
		$view->render(TRUE);
	}

	/**
	 * Delete bookmark
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function deleting(array $_post)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'widgets', $_post['id'], 4);

		if (is_null($msg))
		{
			// do action
			$mod = new Widget_model();
			$result = $mod->my_delete($_post['id']);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// clear useless permissions
			if ($result[1])
			{
				$perm = new Permission_model();
				$perm->deleting_by_what('widgets', $_post['id']);

				// set what update
				$msg->update[] = array(
					'element' => 'topic',
					'url' => BASE_URL.'widgets',
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
}
