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
 * x3form_builder controller
 * This controller work only on the admin side
 *
 * @package		X3CMS
 */
class X3form_builder_controller extends X3ui_controller implements X3plugin_controller
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
		$this->dict->get_wordarray(array('x3form_builder'));

        $mod = new Area_model();
		list($id_area, $areas) = $mod->get_my_areas($id_area);

		// initialize lang
		$lang = (empty($lang))
			? X4Route_core::$lang
			: $lang;

		// get page
		$page = $this->get_page('x3form-builder/mod');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page), array('modules' => 'index/'.$id_area));
		$view->actions = $this->actions($id_area, $lang);

		// content
		$view->content = new X4View_core('x3form_list', 'x3form_builder');
		$view->pp = $pp;

        $mod = new X3form_builder_model();
		$view->content->items = X4Pagination_helper::paginate($mod->get_forms($id_area, $lang, 2), $pp);

		// area switcher
		$view->content->id_area = $id_area;
		$view->content->areas = $areas;

		// language switcher
		$view->content->lang = $lang;
        if (MULTILANGUAGE)
        {
            $mod = new Language_model();
            $view->content->langs = $mod->get_languages();
        }
		$view->render(true);
	}

	/**
	 * Form builder actions
	 *
     * @access	private
	 * @param   integer $id_area Area ID
	 * @param   string  $lang Language code
	 * @param   integer $id_form Form ID
	 * @return  void
	 */
	private function actions(int $id_area, string $lang, int $id_form = 0)
	{
		switch ($id_form)
        {
            case -1;
                return '<a class="link" @click="popup(\''.BASE_URL.'x3form_builder/edit_black/'.$id_area.'/'.$lang.'\')" title="'._X3FB_BLACKLIST_ADD.'">
                    <i class="fa-solid fa-lg fa-circle-plus"></i>
                </a>';
                break;
            case 0;
                return '
                <a class="link" @click="pager(\''.BASE_URL.'x3form_builder/blacklist/'.$id_area.'/'.$lang.'\')" title="'._X3FB_BLACKLIST_MANAGE.'">
                    <i class="fa-solid fa-lg fa-tags"></i>
                </a>
                <a class="link" @click="popup(\''.BASE_URL.'x3form_builder/edit/'.$id_area.'/'.$lang.'\')" title="'._X3FB_NEW_FORM.'">
                    <i class="fa-solid fa-lg fa-circle-plus"></i>
                </a>';
                break;
            default:
                $url = BASE_URL.'x3form_builder/edit_field/'.$id_area.'/'.$lang.'/'.$id_form;
                $js_url = $this->site->site->domain.'/admin/x3form_builder/encoded_rules';

                return '<a class="link"
                    @click="popup({url: \''.$url.'\', js: \''.$js_url.'\'})" title="'._X3FB_NEW_FIELD.'">
                    <i class="fa-solid fa-lg fa-circle-plus"></i>
                </a>';
                break;
		}
	}

	/**
	 * Form fields
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang Language code
	 * @param   integer $id_form Form ID
	 * @return  void
	 */
	public function fields(int $id_area, string $lang, int $id_form)
	{
		// load dictionary
		$this->dict->get_wordarray(array('x3form_builder'));

		// initialize lang
		$lang = (empty($lang))
			? X4Route_core::$lang
			: $lang;

		// get page
		$page = $this->get_page('x3form_builder/fields');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page), array('modules' => 'index/'.$id_area, 'x3form-builder/mod' => $id_area.'/'.$lang));
		$view->actions = $this->actions($id_area, $lang, $id_form);

		// content
		$view->content = new X4View_core('x3form_fields', 'x3form_builder');
		$view->content->id_area = $id_area;
		$view->content->lang = $lang;
		$view->content->id_form = $id_form;
        $view->content->domain = $this->site->site->domain;

		$mod = new X3form_builder_model();

		$view->content->form = $mod->get_by_id($id_form);
		$view->content->items = $mod->get_form_fields($id_area, $lang, $id_form);

		$view->render(true);
	}

	/**
	 * Form results
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang Language code
	 * @param   integer $id_form Form ID
	 * @param   integer $pp Pager Index
	 * @return  void
	 */
	public function results(int $id_area, string $lang, int $id_form, $pp = 0)
	{
		// load dictionary
		$this->dict->get_wordarray(array('x3form_builder'));

		// initialize lang
		$lang = (empty($lang))
			? X4Route_core::$lang
			: $lang;

		// get page
		$page = $this->get_page('x3form_builder/results');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page), array('modules' => 'index/'.$id_area, 'x3form-builder/mod' => $id_area.'/'.$lang));
		$view->actions = $this->actions($id_area, $lang);

        $mod = new X3form_builder_model();
		$form = $mod->get_by_id($id_form, 'x3_forms', 'name');

		// content
		$view->content = new X4View_core('x3form_results', 'x3form_builder');
        $view->content->form = $form;
		$view->content->id_area = $id_area;
		$view->content->lang = $lang;
		$view->content->id_form = $id_form;
		$view->content->pp = $pp;
		$view->content->mod = $mod;

		$view->content->items = X4Pagination_helper::paginate($mod->get_form_results($id_area, $lang, $id_form), $pp);
		$view->render(true);
	}

    /**
	 * Form blacklist
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang Language code
	 * @param   integer $pp Pager Index
	 * @return  void
	 */
	public function blacklist(int $id_area, string $lang, $pp = 0)
	{
		// load dictionary
		$this->dict->get_wordarray(array('x3form_builder'));

		// initialize lang
		$lang = (empty($lang))
			? X4Route_core::$lang
			: $lang;

        $mod = new Area_model();
        list($id_area, $areas) = $mod->get_my_areas($id_area);

		// get page
		$page = $this->get_page('x3form-builder/blacklist');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page), array('modules' => 'index/'.$id_area, 'x3form-builder/mod' => $id_area.'/'.$lang));
		$view->actions = $this->actions($id_area, $lang, -1);

        $mod = new X3form_builder_model();

		// content
		$view->content = new X4View_core('x3form_blacklist', 'x3form_builder');
        $view->content->id_area = $id_area;
		$view->content->lang = $lang;
		$view->content->pp = $pp;

		$view->content->items = X4Pagination_helper::paginate($mod->get_blacklist($id_area, $lang), $pp);

        // language switcher
		$view->content->lang = $lang;
        if (MULTILANGUAGE)
        {
            $mod = new Language_model();
            $view->content->langs = $mod->get_languages();
        }
        $view->content->areas = $areas;
		$view->render(true);
	}

	/**
	 * Change status
	 *
	 * @param   string	$table Table name
     * @param   integer $id_area
	 * @param   string	$what field to change
	 * @param   integer $id ID of the item to change
	 * @param   integer $value value to set (0 = off, 1 = on)
	 * @return  void
	 */
	public function set(string $table, string $what, int $id_area, int $id, int $value)
	{
		$msg = null;
		// check permission
		$table = ($table == 'forms')
            ? ''
            : '_'.$table;
		$msg = AdmUtils_helper::chk_priv_level($id_area, 'x3_forms'.$table, $id, $what);
		if (is_null($msg))
		{
			// do action
            $mod = new X3form_builder_model();
			$result = $mod->update($id, array($what => $value), 'x3_forms'.$table);

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

	// form

	/**
	 * Edit form
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   integer $id Form ID
	 * @return  void
	 */
	public function edit(int $id_area, string $lang, int $id = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'x3form_builder'));

		// get object
		$mod = new X3form_builder_model();
		$item = ($id)
			? $mod->get_by_id($id)
			: new Obj_form($id_area, $lang);

		// build the form
		$form_fields = new X4Form_core('x3form_builder', 'x3form_builder');
		$form_fields->id = $id;
		$form_fields->item = $item;

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
        $view->title = ($id)
			? _X3FB_EDIT_FORM
			: _X3FB_NEW_FORM;
		// contents
		$view->content = new X4View_core('editor');
        // can user edit?
        $submit = AdmUtils_helper::submit_btn($item->id_area, 'x3_forms', $id, $item->xlock);
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, $submit, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

		$view->render(true);
	}

	/**
	 * Editing form
	 *
	 * @access	private
	 * @param   array $_post _POST array
	 * @return  void
	 */
	private function editing(array $_post)
	{
		$msg = null;
		// check permission
		$msg = ($_post['id'])
			? AdmUtils_helper::chk_priv_level($_post['id_area'], 'x3_forms', $_post['id'], 'edit')
			: AdmUtils_helper::chk_priv_level($_post['id_area'], '_x3form_creation', 0, 'create');

		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'id_area' => $_post['id_area'],
				'lang' => $_post['lang'],
				'name' => X4Utils_helper::slugify($_post['name']),
				'title' => $_post['title'],
				'description' => $_post['description'],
				'msg_ok' => $_post['msg_ok'],
				'msg_failed' => $_post['msg_failed'],
				'submit_button' => $_post['submit_button'],
				'reset_button' => $_post['reset_button']
			);

			$n = $_post['mailto_num']+1;
			$mailto = [];
			for($i = 0; $i < $n; $i++)
			{
				if (!empty($_post['mailto'.$i]))
				{
					$mailto[] = $_post['mailto'.$i];
				}
			}
			$post['mailto'] = implode('|', $mailto);

			$mod = new X3form_builder_model();

			// check if exists
			$check = (boolean) $mod->form_exists($post['id_area'], $post['lang'], $post['name'], $_post['id']);
			if ($check)
			{
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_X3FB_FORM_ALREADY_EXISTS', 'msg'));
			}
			else
			{
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
                    if (!$_post['id'])
                    {
                        // permissions
                        $perm = new Permission_model();
                        $array[] = array(
                            'action' => 'insert',
                            'id_what' => $result[0],
                            'id_user' => $_SESSION['xuid'],
                            'level' => 4
                        );
                        $perm->pexec('x3_forms', $array, $post['id_area']);
                    }

					$msg->update = array(
						'element' => 'page',
						'url' => $_SERVER['HTTP_REFERER']
					);
				}
			}
		}
		$this->response($msg);
	}


	/**
	 * duplicate form
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   integer $id Form ID
	 * @return  void
	 */
	public function duplicate(int $id_area, string $lang, int $id_form)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'x3form_builder'));

		// get object
		$mod = new X3form_builder_model();
		$item = $mod->get_by_id($id_form);

		// build the form
		$form_fields = new X4Form_core('x3form_builder_duplicate', 'x3form_builder');
		$form_fields->id_form = $id_form;
		$form_fields->item = $item;
        $form_fields->mod = $mod;

        // get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->duplicating($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        $view = new X4View_core('modal');
        $view->title = _X3FB_DUPLICATE_FORM;
		// contents
		$view->content = new X4View_core('editor');

		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

		$view->render(true);
	}

	/**
	 * Duplicating form
	 *
	 * @access	private
	 * @param   array $_post _POST array
	 * @return  void
	 */
	private function duplicating(array $_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_post['id_area'], '_x3form_creation', 0, 'create');
		if (is_null($msg))
		{
			$mod = new X3form_builder_model();
			// get the original
			$item = $mod->get_by_id($_post['id_form']);

			if ($item)
			{
				// handle _post
				$post = array(
					'id_area' => $_post['id_area'],
					'lang' => $_post['lang'],
					'name' => X4Utils_helper::slugify($_post['name']),
					'title' => $item->title,
					'description' => $item->description,
					'mailto' => $item->mailto,
					'msg_ok' => $item->msg_ok,
					'msg_failed' => $item->msg_failed,
					'submit_button' => $item->submit_button,
					'reset_button' => $item->reset_button
				);

				// check if exists
				$check = (boolean) $mod->form_exists($post['id_area'], $post['lang'], $post['name']);
				if ($check)
                {
					$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_X3FB_FORM_ALREADY_EXISTS', 'msg'));
                }
                else
				{
					// insert the form
					$result = $mod->insert($post);

					// add permission
					if ($result[1])
					{
                        // permissions
                        $perm = new Permission_model();
                        $array[] = array(
                            'action' => 'insert',
                            'id_what' => $result[0],
                            'id_user' => $_SESSION['xuid'],
                            'level' => 4
                        );
                        $perm->pexec('x3_forms', $array, $post['id_area']);

						$id_form = $result[0];

						// duplicate fields
						$items = $mod->get_form_fields($item->id_area, $item->lang, $item->id);

						foreach ($items as $i)
						{
							// handle _post
							$post_i = array(
								'id_area' => $post['id_area'],
								'lang' => $post['lang'],
								'id_form' => $id_form,
								'xtype' => $i->xtype,
								'label' => $i->label,
								'name' => $i->name,
								'suggestion' => $i->suggestion,
								'value' => $i->value,
								'rule' => $i->rule,
								'extra' => $i->extra,
								'xpos' => $i->xpos,
								'xon' => 1
							);
							$result = $mod->insert($post_i, 'x3_forms_fields');

                            if ($result[1])
                            {
                                $array[] = array(
                                    'action' => 'insert',
                                    'id_what' => $result[0],
                                    'id_user' => $_SESSION['xuid'],
                                    'level' => 4
                                );
                                $perm->pexec('x3_forms_fields', $array, $post['id_area']);
                            }
						}
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
			}
		}
		$this->response($msg);
	}

    /**
	 * Delete form
	 *
     * @param   integer $id_area
	 * @param   integer $id Form ID
	 * @return  void
	 */
	public function delete(int $id_area, int $id_form)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'x3form_builder'));

		// get opbject
        $mod = new X3form_builder_model();
		$item = $mod->get_by_id($id_form, 'x3_forms', 'id, id_area, name');

		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $id_form,
			'name' => 'id'
		);

		// if submitted
		if (X4Route_core::$post)
		{
			$this->deleting('forms', $item);
			die;
		}

        $view = new X4View_core('modal');
        $view->title = _X3FB_DELETE_FORM;

		// contents
		$view->content = new X4View_core('delete');
		$view->content->item = $item->name;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

	// fields

	/**
	 * Edit field
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   integer $id_form Form ID
	 * @param   integer $id Field ID
	 * @return  void
	 */
	public function edit_field(int $id_area, string $lang, int $id_form, int $id = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'x3form_builder'));

		// get object
        $mod = new X3form_builder_model();
		$item =  ($id)
			? $mod->get_by_id($id, 'x3_forms_fields')
			: new Obj_field($id_area, $lang, $id_form);

		$form_fields = new X4Form_core('x3form_builder_field', 'x3form_builder');
		$form_fields->id = $id;
		$form_fields->item = $item;

        $form_fields->mod = $mod;
        $form_fields->js_fields = $this->js_fields;
        $form_fields->tr = $this->decompose($item->rule, 'js_fields', 1);
        // for the script required by the editor
        // here can't load scripts
        // so we load it with the popup
        //$form_fields->options = $this->encoded_rules();

        // get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->editing_field($_POST);
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
			? _X3FB_EDIT_FIELD
			: _X3FB_NEW_FIELD;

		// contents
		$view->content = new X4View_core('editor');

		$view->content->id = $id;
        // can user edit?
        $submit = AdmUtils_helper::submit_btn($item->id_area, 'x3_forms_fields', $id, $item->xlock);
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, $submit, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

		$view->render(true);
	}

    // fields for rule configurator
    public $js_fields = [
            ['name' => 'rule_name', 'rule' => 'required', 'type' => 'text'],
            ['name' => 'field_value', 'rule' => '', 'type' => 'text'],
            ['name' => 'param_value', 'rule' => '', 'type' => 'text'],
        ];

    /**
	 * Return recorded selected options
	 *
	 * @param   string 	$str Encoded options
     * @param   string  $fields name of the array with configuration
	 * @param   boolean	$move With or without direction buttons
	 * @param   boolean	$echo Return or echo
	 * @return  string
	 */
	public function decompose(string $str = '', string $fields = '', int $move = 0, int $echo = 0)
	{
        // load dictionaries
		$this->dict->get_words();

        //$str = urldecode($str);

        $res = AdmUtils_helper::decompose($str, $this->$fields, $move, $echo);

		if ($echo)
		{
		    // AJAX call
		    echo $res;
		}
		else
		{
		    return $res;
		}
    }

	/**
	 * Return rules array JSON encoded
	 *
	 * @access	private
	 * @return  string
	 */
	public function encoded_rules()
	{
		$a = array();

        $rules = X4Validation_helper::$rules;
		foreach ($rules as $r)
		{
			$a[$r['value']] = $r['param'];
		}

        header('Content-Type: text/javascript');
        header("Content-Disposition: attachment; filename=encoded_rules.js");
		echo '// extra script for validation rules
var rules = '.json_encode($a).';

// extra check on configurator item for validation rules
function checkRule(item) {

    // does it require a field_value?
    if(rules[item.rule_name][0] > 0 && item.field_value.length == 0) {
        document.getElementById("field_value").classList.add("softwarn");
        return false;
    }

    // does it require a param_value?
    if(rules[item.rule_name][1] != 0) {
        if (item.param_value.length == 0) {
            document.getElementById("param_value").classList.add("softwarn");
            return false;
        } else {
            if (rules[item.rule_name][1] == "integer") {
                item.param_value = parseInt(item.param_value);
                if (isNaN(item.param_value)) {
                    document.getElementById("param_value").classList.add("softwarn");
                    return false;
                }
            }
        }
    }
    return true;
}';
	}

	/**
	 * Editing field
	 *
	 * @access	private
	 * @param   array $_post _POST array
	 * @return  void
	 */
	private function editing_field(array $_post)
	{
		$msg = null;
		// check permission
		$msg = ($_post['id'])
			? AdmUtils_helper::chk_priv_level($_post['id_area'], 'x3_forms_fields', $_post['id'], 'edit')
			: AdmUtils_helper::chk_priv_level($_post['id_area'], '_x3form_field_creation', 0, 'create');

		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'id_area' => $_post['id_area'],
				'lang' => $_post['lang'],
				'id_form' => $_post['id_form'],
				'xtype' => $_post['xtype'],
				'label' => htmlspecialchars_decode(stripslashes($_post['label'])),
				'name' => X4Utils_helper::slugify($_post['name']),
				'suggestion' => $_post['suggestion'],
				'value' => htmlspecialchars_decode(stripslashes($_post['value'])),
				'rule' => $_post['xrule'],
				'extra' => htmlspecialchars_decode(stripslashes($_post['extra']))
            );

			$mod = new X3form_builder_model();

			// check if already exists
			$check = (boolean) $mod->field_exists($post['id_form'], $post['name'], $_post['id']);
			if ($check)
            {
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_X3FB_FIELD_ALREADY_EXISTS', 'msg'));
            }
			else
			{
				// update or insert
				if ($_post['id'])
                {
					$result = $mod->update($_post['id'], $post, 'x3_forms_fields');
                }
				else
				{
					// set position
					$xpos = intval($mod->get_max_pos('fields', 'id_form', $_post['id_form']));
					$xpos++;
					$post['xpos'] = $xpos;
					$result = $mod->insert($post, 'x3_forms_fields');
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
		}
		$this->response($msg);
	}

	/**
	 * Move fields in a form
	 *
	 * @param   integer $id_area Area ID
	 * @param   string $lang Language code
	 * @param   integer $id_form Form ID
	 * @return  void
	 */
	public function ordering(int $id_area, string $lang, int $id_form)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($id_area, 'x3_forms', $id_form, 'manage');
		if (is_null($msg) && X4Route_core::$input)
		{
			// handle post
            $_post = X4Route_core::$input;
			$elements = $_post['sort_order'];

			// do action
            $mod = new X3form_builder_model();
			$items = $mod->get_form_fields($id_area, $lang, $id_form);

			$result = array(0, 1);
			if ($items)
			{
				foreach ($items as $i)
				{
					$p = array_search($i->id, $elements) + 1;
					if ($p && $i->xpos != $p)
					{
						$res = $mod->update($i->id, array('xpos' => $p), 'x3_forms_fields');
						if ($res[1])
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
	 * Delete field
	 *
     * @param   integer $id_area
	 * @param   integer $id_field Field ID
	 * @return  void
	 */
	public function delete_field(int $id_area, int $id_field)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'x3form_builder', 'msg'));

		// get object
        $mod = new X3form_builder_model();
		$item = $mod->get_by_id($id_field, 'x3_forms_fields', 'id, id_area, name');

		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $id_field,
			'name' => 'id'
		);

		// if submitted
		if (X4Route_core::$post)
		{
			$this->deleting('fields', $item);
			die;
		}

        $view = new X4View_core('modal');
        $view->title = _X3FB_DELETE_FIELD;
		// contents
		$view->content = new X4View_core('delete');
		$view->content->item = $item->name;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

	// form result

	/**
	 * Export results
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang Language code
	 * @param   integer $id Form ID
	 * @return  void
	 */
	public function export(int $id_area, string $lang, int $id_form)
	{
        $this->dict->get_wordarray(array('x3form_builder'));
        // check permission
        $level = AdmUtils_helper::chk_priv_level($id_area, 'x3_forms', $id_form, 'delete');
		if (is_null($level))
        {
            // get form
            $mod = new X3form_builder_model();
            $form = $mod->get_by_id($id_form, 'x3_forms', 'name');

            // get submissions
            $items = $mod->get_form_results($id_area, $lang, $id_form);

            $c = 0;
            $head = array('Date');
            $list = array();
            foreach ($items as $i)
            {
                $array = json_decode($i->result, true);
                $tmp = array($i->updated);
                foreach ($array as $k => $v)
                {
                    $str = array();
                    if (!$c)
                    {
                        $head[] = strtoupper($k);
                    }

                    if (is_array($v))
                    {
                        foreach ($v as $o)
                        {
                            $str[] = $o;
                        }
                    }
                    else if ($k != 'x4token')
                    {
                        $str[] = $v;
                    }
                    $tmp[] = implode('-', $str);
                }
                $c++;
                $list[] = '"'.implode('";"', $tmp).'"';
            }

            // out
            header('Content-Description: File Transfer');
            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename='.$form->name.'_'.date('d-m-Y').'.csv');
            header('Content-Transfer-Encoding: Binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');

            ob_clean();
            flush();
            echo '"'.implode('";"', $head).'"'.NL.implode(NL, $list);
            exit;
        }
        else
        {
            echo '<p>'._NOT_PERMITTED.'</p>';
        }
	}

	/**
	 * Delete result
	 *
     * @param   integer $id_area
	 * @param   integer $id Result ID
	 * @return  void
	 */
	public function delete_result(int $id_area, int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'x3form_builder'));

		// get object
        $mod = new X3form_builder_model();
		$item = $mod->get_by_id($id, 'x3_forms_results', 'id, id_area, updated');

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
			$this->deleting('results', $item);
			die;
		}
        $view = new X4View_core('modal');
        $view->title = _X3FB_DELETE_RESULTS;
		// contents
		$view->content = new X4View_core('delete');

		$view->content->item = $item->updated;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

    /**
	 * Result bulk action
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang Language code
	 * @return  void
	 */
	public function bulk(int $id_area, string $lang)
	{
		$msg = null;
        $_post = X4Route_core::$input;
		if (!empty($_post) && isset($_post['bulk']) && is_array($_post['bulk']) && !empty($_post['bulk']))
		{
            $mod = new X3form_builder_model();
            $perm = new Permission_model();

            // NOTE: we here have only bulk_action = delete
            $result = [0, 1];
            foreach ($_post['bulk'] as $i)
            {
                $msg = AdmUtils_helper::chk_priv_level($id_area, 'x3_forms_results', $i, 'delete');
                if (is_null($msg))
                {
                    $result = $mod->delete($i, 'x3_forms_results');
                    if ($result[1])
                    {
                        $perm->deleting_by_what('x3_forms_results', $i);
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
                    'url' => $_SERVER['HTTP_REFERER']
                );
            }
        }
		$this->response($msg);
	}

    // blacklist

    /**
	 * Edit blacklist item
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   integer $id Field ID
	 * @return  void
	 */
	public function edit_black(int $id_area, string $lang, int $id = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'x3form_builder'));

		// get object
        $mod = new X3form_builder_model();
		$item =  ($id)
			? $mod->get_by_id($id, 'x3_forms_blacklist')
			: new Obj_blackitem($id_area, $lang);

		$form_fields = new X4Form_core('x3form_builder_blackitem', 'x3form_builder');
		$form_fields->id = $id;
		$form_fields->item = $item;

        // get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->editing_blackitem($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

        $view = new X4View_core('modal');
        //$view->wide = 'md:w-2/3 lg:w-2/3';
        $view->title = ($id)
			? _X3FB_BLACKLIST_EDIT
			: _X3FB_BLACKLIST_NEW;

		// contents
		$view->content = new X4View_core('editor');
		// can user edit?
        $submit = AdmUtils_helper::submit_btn($item->id_area, 'x3_forms_blacklist', $id, $item->xlock);
		// form builder
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, $submit, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

		$view->render(true);
	}

    /**
	 * Editing blacklist item
	 *
	 * @access	private
	 * @param   array $_post _POST array
	 * @return  void
	 */
	private function editing_blackitem(array $_post)
	{
		$msg = null;
		// check permission
		$msg = ($_post['id'])
			? AdmUtils_helper::chk_priv_level($_post['id_area'], 'x3_forms_blacklist', $_post['id'], 'edit')
			: AdmUtils_helper::chk_priv_level($_post['id_area'], '_x3form_blacklist_creation', 0, 'create');

		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'id_area' => $_post['id_area'],
				'lang' => $_post['lang'],
                'xon' => 1  // enabled by default
            );

            // how many items?
            // we could have more than one word to add to the blacklist separated new lines
            $items = explode(NL, str_replace("\r", '', $_post['name']));

			$mod = new X3form_builder_model();

            $n = sizeof($items);
            $counter = 0;
            foreach ($items as $i)
            {
                // check if already exists
                $check = (boolean) $mod->blackitem_exists($i, $post, $_post['id']);
                if ($check)
                {
                    $counter++;
                }
                else
                {
                    $post['name'] = strtolower($i);
                    // update or insert
                    if ($_post['id'] && $counter == 0)
                    {
                        $result = $mod->update($_post['id'], $post, 'x3_forms_blacklist');
                        // reset post_id to insert following items
                        $_post['id'] = 0;
                    }
                    else
                    {
                        $result = $mod->insert($post, 'x3_forms_blacklist');
                    }
                }
            }

            if ($counter == $n)
            {
                // all items are already in
                $msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_X3FB_BLACKITEMS_ALREADY_EXISTS', 'msg'));
            }
            else
            {
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
		}
		$this->response($msg);
	}

    /**
	 * Delete blacklist item
	 *
     * @param   integer $id_area
	 * @param   integer $id Result ID
	 * @return  void
	 */
	public function delete_blaclist(int $id_area, int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'x3form_builder'));

		// get object
        $mod = new X3form_builder_model();
		$item = $mod->get_by_id($id, 'x3_forms_blacklist', 'id, id_area, name');

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
			$this->deleting('blacklist', $item);
			die;
		}
        $view = new X4View_core('modal');
        $view->title = _X3FB_BLACKLIST_DELETE;
		// contents
		$view->content = new X4View_core('delete');

		$view->content->item = $item->name;

		// form builder
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

	// form, field, result or blacklist item

	/**
	 * Deleting item
	 *
	 * @access	private
	 * @param   string 	    $table Table suffix
	 * @param   stdClass 	$item _POST array
	 * @return  void
	 */
	private function deleting($table, $item)
	{
		$msg = null;
        $table = ($table == 'forms')
            ? ''
            : '_'.$table;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($item->id_area, 'x3_forms'.$table, $item->id, 'delete');
		if (is_null($msg))
		{
			// do action
			$mod = new X3form_builder_model();
			$result = $mod->delete($item->id, 'x3_forms'.$table);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// clear useless permissions
			if ($result[1])
			{
				$perm = new Permission_model();
				$perm->deleting_by_what('x3_forms'.$table, $item->id);

				// set what update
				$msg->update = array(
					'element' => 'page',
					'url' => $_SERVER['HTTP_REFERER']
				);
			}
		}
		$this->response($msg);
	}

    /**
	 * Dictionary helper
	 *
	 * @param	integer $id_area Area ID
	 * @param	string	$lang
     * @param   string  $xtype Field type
	 * @return	string
	 */
	public function helper(int $id_area, string $lang, string $xtype)
	{
		$this->dict->get_wordarray(array('x3form_builder'));
		echo constant('_X3FB_HELP_'.strtoupper($xtype));
	}

	/* widget */

	/**
	 * Rebuild the widget
	 *
	 * @param	string	$title Widget title
	 * @param	integer $id_area Area ID
	 * @param	string	$area Area name
	 * @return	array	string
	 */
	public function rewidget(string $title, int $id_area, string $area)
	{
        $mod = new X3form_builder_model();
		echo $mod->get_widget(urldecode($title), $id_area, urldecode($area), false);
	}

    /**
	 * Test
	 *
	 * @param	integer $id_area Area ID
	 * @param	string	$area Area name
	 * @return	array	string
	 */
	public function test(int $id_area, string $area)
	{
        // insert here your code
	}
}
