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
 * Controller for Categories
 *
 * @package X3CMS
 */
class Categories_controller extends X3ui_controller
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
	 * Show categories
	 *
	 * @return  void
	 */
	public function _default()
	{
		$this->index(2, X4Route_core::$lang);
	}

	/**
	 * Show categories
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string 	$tag
	 * @return  void
	 */
	public function index(int $id_area, string $lang, string $tag = '')
	{
		// load dictionary
		$this->dict->get_wordarray(array('categories', 'articles'));

		$area = new Area_model();
		list($id_area, $areas) = $area->get_my_areas($id_area);

		$lang = (empty($lang))
			? X4Route_core::$lang
			: $lang;

		// get page
		$page = $this->get_page('categories');
		$navbar = array($this->site->get_bredcrumb($page), array('articles' => 'index/'.$id_area.'/'.$lang));

		$mod = new Category_model();

		$tags = $mod->get_tags($id_area, $lang);
		// if empty get the first available
		$tag = (empty($tag) && !empty($tags))
		    ? $tags[0]->tag
		    : $tag;

        	// contents
		$view = new X4View_core('articles/category_list');
		$view->page = $page;
		$view->navbar = $navbar;
		$view->items = $mod->get_categories($id_area, $lang, $tag);

		// tag switcher
		$view->tag = $tag;
		$view->tags = $tags;

		// area switcher
		$view->id_area = $id_area;
		$view->areas = $areas;

		// language switcher
		$view->lang = $lang;
		$lang = new Language_model();
		$view->langs = $lang->get_languages();

		$view->render(TRUE);
	}

	/**
	 * Categories filter
	 *
	 * @return  void
	 */
	public function filter(int $id_area, string $lang, string $tag = '')
	{
		// load the dictionary
		$this->dict->get_wordarray(array('categories'));

		echo '<a class="btf" href="'.BASE_URL.'categories/edit/'.$id_area.'/'.$lang.'/'.$tag.'/-1" title="'._NEW_CATEGORY.'"><i class="fas fa-plus fa-lg"></i></a>
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
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'categories', $id, $val);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();

			// do action
			$mod = new Category_model();
			$result = $mod->update($id, array($what => $value));

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
	 * New / Edit category form (use Ajax)
	 *
	 * @param   integer	$id_area Area ID
	 * @param   integer	$id Category ID
	 * @return  void
	 */
	public function edit(int $id_area, string $lang, string $tag = '', int $id = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'categories'));

		// handle id
		$chk = false;
		if ($id < 0)
		{
			$id = 0;
			$chk = true;
		}

		// get object
		$mod = new Category_model();
		$m = ($id)
			? $mod->get_by_id($id)
			: new Category_obj($id_area, $lang, $tag);

		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $id,
			'name' => 'id'
		);

		if (MULTIAREA)
		{
            $amod = new Area_model();
            $fields[] = array(
                'label' => _AREA,
                'type' => 'select',
                'value' => $m->id_area,
                'options' => array($amod->get_areas(), 'id', 'name'),
                'name' => 'id_area',
                'extra' => 'class="large"'
            );
        }
        else
        {
            $fields[] = array(
                'label' => null,
                'type' => 'hidden',
                'value' => $m->id_area,
                'name' => 'id_area'
            );
        }

		if (MULTILANGUAGE)
			{
		    $lmod = new Language_model();
		    $fields[] = array(
		        'label' => _LANGUAGE,
		        'type' => 'select',
		        'value' => $m->lang,
		        'options' => array($lmod->get_languages(), 'code', 'language'),
		        'name' => 'lang',
		        'extra' => 'class="large"'
		    );
		}
		else
		{
		    $fields[] = array(
		        'label' => null,
		        'type' => 'hidden',
		        'value' => $m->lang,
		        'name' => 'lang'
		    );
		}
	
		$fields[] = array(
			'label' => _TITLE,
			'type' => 'text',
			'value' => $m->title,
			'name' => 'title',
			'rule' => 'required',
			'extra' => 'class="large"'
		);

		$fields[] = array(
			'label' => _CATEGORY_TAG,
			'type' => 'text',
			'value' => $m->tag,
			'name' => 'tag',
			'extra' => 'class="large"',
			'suggestion' => _CATEGORY_TAG_MSG
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
			? _EDIT_CATEGORY
			: _ADD_CATEGORY;

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
	 * Register Edit / New Category form data
	 *
	 * @access	private
	 * @param   integer $id item ID (if 0 then is a new item)
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing(int $id, array $_post)
	{
		$msg = null;
		// check permission
		$msg = ($id)
			? AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'categories', $_post['id'], 3)
			: AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_category_creation', 0, 4);

		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'id_area' => $_post['id_area'],
				'lang' => $_post['lang'],
				'title' => $_post['title'],
				'name' => X4Utils_helper::slugify($_post['title']),
				'tag' => X4Utils_helper::slugify($_post['tag'])
			);

			$mod = new Category_model();

			// check if category already exists
			$check = $mod->exists($post, $id);
			if ($check)
            {
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_CATEGORY_ALREADY_EXISTS', 'msg'));
            }
			else
			{
				// update or insert
				$result = ($id)
					? $mod->update($_post['id'], $post)
                    			: $mod->insert($post);

				// set message
				$msg = AdmUtils_helper::set_msg($result);

				// set what update
				if ($result[1])
				{
					$msg->update[] = array(
						'element' => 'topic',
						'url' => BASE_URL.'categories/index/'.$post['id_area'].'/'.$post['lang'].'/'.$post['tag'],
						'title' => null
					);
				}
			}
		}
		$this->response($msg);
	}

	/**
	 * Delete category form (use Ajax)
	 *
	 * @param   integer $id Category ID
	 * @return  void
	 */
	public function delete(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'categories'));

		// get object
		$mod = new Category_model();
		$obj = $mod->get_by_id($id, 'categories', 'id_area, lang, tag, title');

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
		$view->title = _DELETE_CATEGORY;
		$view->item = $obj->title;

		// form builder
		$view->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
			'onclick="setForm(\'delete\');"');

		$view->render(TRUE);
	}

	/**
	 * Delete category
	 *
	 * @access	private
	 * @param   integer	$id Category ID
	 * @param   stdClass	$obj Category Obj
	 * @return  void
	 */
	private function deleting(int $id, stdClass $obj)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'categories', $id, 4);

		if (is_null($msg))
		{
			// do action
			$mod = new Category_model();
			$result = $mod->delete($id);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// clear useless permissions
			if ($result[1]) {
				$perm = new Permission_model();
				$perm->deleting_by_what('categories', $id);

				// set what update
				$msg->update[] = array(
					'element' => 'topic',
					'url' => BASE_URL.'categories/index/'.$obj->id_area.'/'.$obj->lang.'/'.$obj->tag,
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
}
