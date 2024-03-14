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
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::logged();
	}

	/**
	 * Default method
	 * This method is required
	 */
	public function mod(int $id_area = 2, string $lang = '', int $pp = 0) : void
	{
		$this->dict->get_wordarray(array('x3form_builder'));

        $mod = new Area_model();
		list($id_area, $areas) = $mod->get_my_areas($this->site->data->id, $id_area);

		$lang = (empty($lang))
			? X4Route_core::$lang
			: $lang;

		$page = $this->get_page('x3form-builder/mod');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page), array('modules' => 'index/'.$id_area));
		$view->actions = $this->actions($id_area, $lang);
        $view->content = new X4View_core('x3form_list', 'x3form_builder');
		$view->pp = $pp;

        $mod = new X3form_builder_model($this->site->data->db);
		$view->content->items = X4Pagination_helper::paginate($mod->get_forms($id_area, $lang, 2), $pp);

		$view->content->id_area = $id_area;
		$view->content->areas = $areas;

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
	 */
	private function actions(int $id_area, string $lang, int $id_form = 0) : string
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
                $js_url = $this->site->data->domain.'/admin/x3form_builder/encoded_rules';

                return '<a class="link"
                    @click="popup({url: \''.$url.'\', js: \''.$js_url.'\'})" title="'._X3FB_NEW_FIELD.'">
                    <i class="fa-solid fa-lg fa-circle-plus"></i>
                </a>';
                break;
		}
	}

	/**
	 * Form fields
	 */
	public function fields(int $id_area, string $lang, int $id_form) : void
	{
		$this->dict->get_wordarray(array('x3form_builder'));

		$lang = (empty($lang))
			? X4Route_core::$lang
			: $lang;

		$page = $this->get_page('x3form_builder/fields');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page), array('modules' => 'index/'.$id_area, 'x3form-builder/mod' => $id_area.'/'.$lang));
		$view->actions = $this->actions($id_area, $lang, $id_form);

		$view->content = new X4View_core('x3form_fields', 'x3form_builder');
		$view->content->id_area = $id_area;
		$view->content->lang = $lang;
		$view->content->id_form = $id_form;
        $view->content->domain = $this->site->data->domain;

		$mod = new X3form_builder_model($this->site->data->db);

		$view->content->form = $mod->get_by_id($id_form);
		$view->content->items = $mod->get_form_fields($id_area, $lang, $id_form);

		$view->render(true);
	}

	/**
	 * Form results
	 */
	public function results(int $id_area, string $lang, int $id_form, $pp = 0) : void
	{
		$this->dict->get_wordarray(array('x3form_builder'));

		$lang = (empty($lang))
			? X4Route_core::$lang
			: $lang;

		$page = $this->get_page('x3form_builder/results');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page), array('modules' => 'index/'.$id_area, 'x3form-builder/mod' => $id_area.'/'.$lang));
		$view->actions = $this->actions($id_area, $lang);

        $mod = new X3form_builder_model($this->site->data->db);
		$form = $mod->get_by_id($id_form, 'x3_forms', 'name');

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
	 */
	public function blacklist(int $id_area, string $lang, $pp = 0): void
	{
		$this->dict->get_wordarray(array('x3form_builder'));

		$lang = (empty($lang))
			? X4Route_core::$lang
			: $lang;

        $mod = new Area_model();
        list($id_area, $areas) = $mod->get_my_areas($id_area);

		$page = $this->get_page('x3form-builder/blacklist');

        $view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page), array('modules' => 'index/'.$id_area, 'x3form-builder/mod' => $id_area.'/'.$lang));
		$view->actions = $this->actions($id_area, $lang, -1);

        $mod = new X3form_builder_model($this->site->data->db);

		$view->content = new X4View_core('x3form_blacklist', 'x3form_builder');
        $view->content->id_area = $id_area;
		$view->content->lang = $lang;
		$view->content->pp = $pp;

		$view->content->items = X4Pagination_helper::paginate($mod->get_blacklist($id_area, $lang), $pp);

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
	 */
	public function set(string $table, string $what, int $id_area, int $id, int $value) : void
	{
		$msg = null;
		$table = ($table == 'forms')
            ? ''
            : '_'.$table;
		$msg = AdmUtils_helper::chk_priv_level($id_area, 'x3_forms'.$table, $id, $what);
		if (is_null($msg))
		{
			$mod = new X3form_builder_model($this->site->data->db);
			$result = $mod->update($id, array($what => $value), 'x3_forms'.$table);

			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);

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
	 */
	public function edit(int $id_area, string $lang, int $id = 0) : void
	{
		$this->dict->get_wordarray(array('form', 'x3form_builder'));

		$mod = new X3form_builder_model($this->site->data->db);
		$item = ($id)
			? $mod->get_by_id($id)
			: new Obj_form($id_area, $lang);

		$form_fields = new X4Form_core('x3form_builder', 'x3form_builder');
		$form_fields->id = $id;
		$form_fields->item = $item;
        $fields = $form_fields->render();

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
		$view->content = new X4View_core('editor');
        $submit = AdmUtils_helper::submit_btn($item->id_area, 'x3_forms', $id, $item->xlock);
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, $submit, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

		$view->render(true);
	}

	/**
	 * Editing form
	 */
	private function editing(array $_post) : void
	{
		$msg = null;
		$msg = ($_post['id'])
			? AdmUtils_helper::chk_priv_level($_post['id_area'], 'x3_forms', $_post['id'], 'edit')
			: AdmUtils_helper::chk_priv_level($_post['id_area'], '_x3form_creation', 0, 'create');

		if (is_null($msg))
		{
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

			$mod = new X3form_builder_model($this->site->data->db);

			$check = (boolean) $mod->form_exists($post['id_area'], $post['lang'], $post['name'], $_post['id']);
			if ($check)
			{
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_X3FB_FORM_ALREADY_EXISTS', 'msg'));
			}
			else
			{
				$result = ($_post['id'])
                    ? $mod->update($_post['id'], $post)
                    : $mod->insert($post);

				$msg = AdmUtils_helper::set_msg($result);

				if ($result[1])
				{
                    if (!$_post['id'])
                    {
                        $perm = new Permission_model($this->site->data->db);
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
	 */
	public function duplicate(int $id_area, string $lang, int $id_form) : void
	{
		$this->dict->get_wordarray(array('form', 'x3form_builder'));

		$mod = new X3form_builder_model($this->site->data->db);
		$item = $mod->get_by_id($id_form);

		$form_fields = new X4Form_core('x3form_builder_duplicate', 'x3form_builder');
		$form_fields->id_form = $id_form;
		$form_fields->item = $item;
        $form_fields->mod = $mod;
        $fields = $form_fields->render();

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
		$view->content = new X4View_core('editor');

		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

		$view->render(true);
	}

	/**
	 * Duplicating form
	 */
	private function duplicating(array $_post) : void
	{
		$msg = null;
		$msg = AdmUtils_helper::chk_priv_level($_post['id_area'], '_x3form_creation', 0, 'create');
		if (is_null($msg))
		{
			$mod = new X3form_builder_model($this->site->data->db);
			$item = $mod->get_by_id($_post['id_form']);

			if ($item)
			{
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

				$check = (boolean) $mod->form_exists($post['id_area'], $post['lang'], $post['name']);
				if ($check)
                {
					$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_X3FB_FORM_ALREADY_EXISTS', 'msg'));
                }
                else
				{
					$result = $mod->insert($post);

					if ($result[1])
					{
                        $perm = new Permission_model($this->site->data->db);
                        $array[] = array(
                            'action' => 'insert',
                            'id_what' => $result[0],
                            'id_user' => $_SESSION['xuid'],
                            'level' => 4
                        );
                        $perm->pexec('x3_forms', $array, $post['id_area']);

						$id_form = $result[0];

						$items = $mod->get_form_fields($item->id_area, $item->lang, $item->id);

						foreach ($items as $i)
						{
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

					$msg = AdmUtils_helper::set_msg($result);

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
	 */
	public function delete(int $id_area, int $id_form) : void
	{
		$this->dict->get_wordarray(array('form', 'x3form_builder'));

		$mod = new X3form_builder_model($this->site->data->db);
		$item = $mod->get_by_id($id_form, 'x3_forms', 'id, id_area, name');

		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $id_form,
			'name' => 'id'
		);

		if (X4Route_core::$post)
		{
			$this->deleting('forms', $item);
			die;
		}

        $view = new X4View_core('modal');
        $view->title = _X3FB_DELETE_FORM;

		$view->content = new X4View_core('delete');
		$view->content->item = $item->name;

		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

	// fields

	/**
	 * Edit field
	 */
	public function edit_field(int $id_area, string $lang, int $id_form, int $id = 0) : void
	{
		$this->dict->get_wordarray(array('form', 'x3form_builder'));

		$mod = new X3form_builder_model($this->site->data->db);
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

        $fields = $form_fields->render();

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

		$view->content = new X4View_core('editor');
		$view->content->id = $id;
        $submit = AdmUtils_helper::submit_btn($item->id_area, 'x3_forms_fields', $id, $item->xlock);
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
	 */
	public function decompose(string $str = '', string $fields = '', int $move = 0, int $echo = 0) : mixed
	{
        $this->dict->get_words();
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
	 */
	public function encoded_rules() : void
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
	 */
	private function editing_field(array $_post) : void
	{
		$msg = null;
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

			$mod = new X3form_builder_model($this->site->data->db);

			$check = (boolean) $mod->field_exists($post['id_form'], $post['name'], $_post['id']);
			if ($check)
            {
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_X3FB_FIELD_ALREADY_EXISTS', 'msg'));
            }
			else
			{
				if ($_post['id'])
                {
					$result = $mod->update($_post['id'], $post, 'x3_forms_fields');
                }
				else
				{
					$xpos = intval($mod->get_max_pos('fields', 'id_form', $_post['id_form']));
					$xpos++;
					$post['xpos'] = $xpos;
					$result = $mod->insert($post, 'x3_forms_fields');
				}
				$msg = AdmUtils_helper::set_msg($result);

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
	 */
	public function ordering(int $id_area, string $lang, int $id_form) : void
	{
		$msg = null;
		$msg = AdmUtils_helper::chk_priv_level($id_area, 'x3_forms', $id_form, 'manage');
		if (is_null($msg) && X4Route_core::$input)
		{
		    $_post = X4Route_core::$input;
			$elements = $_post['sort_order'];

		    $mod = new X3form_builder_model($this->site->data->db);
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

			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);
		}
		$this->response($msg);
	}

	/**
	 * Delete field
	 */
	public function delete_field(int $id_area, int $id_field) : void
	{
		$this->dict->get_wordarray(array('form', 'x3form_builder', 'msg'));

		$mod = new X3form_builder_model($this->site->data->db);
		$item = $mod->get_by_id($id_field, 'x3_forms_fields', 'id, id_area, name');

		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $id_field,
			'name' => 'id'
		);

		if (X4Route_core::$post)
		{
			$this->deleting('fields', $item);
			die;
		}

        $view = new X4View_core('modal');
        $view->title = _X3FB_DELETE_FIELD;

		$view->content = new X4View_core('delete');
		$view->content->item = $item->name;

		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

	// form result

	/**
	 * Export results
	 */
	public function export(int $id_area, string $lang, int $id_form) : void
	{
        $this->dict->get_wordarray(array('x3form_builder'));
        $level = AdmUtils_helper::chk_priv_level($id_area, 'x3_forms', $id_form, 'delete');
		if (is_null($level))
        {
            $mod = new X3form_builder_model($this->site->data->db);
            $form = $mod->get_by_id($id_form, 'x3_forms', 'name');

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
                    elseif ($k != 'x4token')
                    {
                        $str[] = $v;
                    }
                    $tmp[] = implode('-', $str);
                }
                $c++;
                $list[] = '"'.implode('";"', $tmp).'"';
            }

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
	 */
	public function delete_result(int $id_area, int $id) : void
	{
		$this->dict->get_wordarray(array('form', 'x3form_builder'));

        $mod = new X3form_builder_model($this->site->data->db);
		$item = $mod->get_by_id($id, 'x3_forms_results', 'id, id_area, updated');

		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $id,
			'name' => 'id'
		);

		if (X4Route_core::$post)
		{
			$this->deleting('results', $item);
			die;
		}
        $view = new X4View_core('modal');
        $view->title = _X3FB_DELETE_RESULTS;

		$view->content = new X4View_core('delete');
		$view->content->item = $item->updated;
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

    /**
	 * Result bulk action
	 */
	public function bulk(int $id_area, string $lang) : void
	{
		$msg = null;
        $_post = X4Route_core::$input;
		if (!empty($_post) && isset($_post['bulk']) && is_array($_post['bulk']) && !empty($_post['bulk']))
		{
            $mod = new X3form_builder_model($this->site->data->db);
            $perm = new Permission_model($this->site->data->db);

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

            $this->dict->get_words();
            $msg = AdmUtils_helper::set_msg($result);

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
	 */
	public function edit_black(int $id_area, string $lang, int $id = 0) : void
	{
		$this->dict->get_wordarray(array('form', 'x3form_builder'));

		$mod = new X3form_builder_model($this->site->data->db);
		$item =  ($id)
			? $mod->get_by_id($id, 'x3_forms_blacklist')
			: new Obj_blackitem($id_area, $lang);

		$form_fields = new X4Form_core('x3form_builder_blackitem', 'x3form_builder');
		$form_fields->id = $id;
		$form_fields->item = $item;
        $fields = $form_fields->render();

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
        $view->title = ($id)
			? _X3FB_BLACKLIST_EDIT
			: _X3FB_BLACKLIST_NEW;

		$view->content = new X4View_core('editor');
		$submit = AdmUtils_helper::submit_btn($item->id_area, 'x3_forms_blacklist', $id, $item->xlock);
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, $submit, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

		$view->render(true);
	}

    /**
	 * Editing blacklist item
	 */
	private function editing_blackitem(array $_post) : void
	{
		$msg = null;
		$msg = ($_post['id'])
			? AdmUtils_helper::chk_priv_level($_post['id_area'], 'x3_forms_blacklist', $_post['id'], 'edit')
			: AdmUtils_helper::chk_priv_level($_post['id_area'], '_x3form_blacklist_creation', 0, 'create');

		if (is_null($msg))
		{
			$post = array(
				'id_area' => $_post['id_area'],
				'lang' => $_post['lang'],
                'xon' => 1  // enabled by default
            );

            // how many items?
            // we could have more than one word to add to the blacklist separated new lines
            $items = explode(NL, str_replace("\r", '', $_post['name']));

			$mod = new X3form_builder_model($this->site->data->db);

            $n = sizeof($items);
            $counter = 0;
            foreach ($items as $i)
            {
                $check = (boolean) $mod->blackitem_exists($i, $post, $_post['id']);
                if ($check)
                {
                    $counter++;
                }
                else
                {
                    $post['name'] = strtolower($i);
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
				$msg = AdmUtils_helper::set_msg($result);

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
	 */
	public function delete_blacklist(int $id_area, int $id) : void
	{
		$this->dict->get_wordarray(array('form', 'x3form_builder'));

        $mod = new X3form_builder_model($this->site->data->db);
		$item = $mod->get_by_id($id, 'x3_forms_blacklist', 'id, id_area, name');

		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $id,
			'name' => 'id'
		);

		if (X4Route_core::$post)
		{
			$this->deleting('blacklist', $item);
			die;
		}
        $view = new X4View_core('modal');
        $view->title = _X3FB_BLACKLIST_DELETE;

		$view->content = new X4View_core('delete');
		$view->content->item = $item->name;
		$view->content->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '',
            '@click="submitForm(\'delete\')"');
		$view->render(true);
	}

	// form, field, result or blacklist item

	/**
	 * Deleting item
	 */
	private function deleting($table, $item) : void
	{
		$msg = null;
        $table = ($table == 'forms')
            ? ''
            : '_'.$table;
		$msg = AdmUtils_helper::chk_priv_level($item->id_area, 'x3_forms'.$table, $item->id, 'delete');
		if (is_null($msg))
		{
			$mod = new X3form_builder_model($this->site->data->db);
			$result = $mod->delete($item->id, 'x3_forms'.$table);

			$msg = AdmUtils_helper::set_msg($result);

			if ($result[1])
			{
				$perm = new Permission_model($this->site->data->db);
				$perm->deleting_by_what('x3_forms'.$table, $item->id);

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
	 */
	public function helper(int $id_area, string $lang, string $xtype) : void
	{
		$this->dict->get_wordarray(array('x3form_builder'));
		echo constant('_X3FB_HELP_'.strtoupper($xtype));
	}

	/* widget */

	/**
	 * Rebuild the widget
	 */
	public function rewidget(string $title, int $id_area, string $area) : string
	{
        $mod = new X3form_builder_model($this->site->data->db);
		echo $mod->get_widget(urldecode($title), $id_area, urldecode($area), false);
	}

    /**
	 * Test
	 */
	public function test()
	{
        // insert here your code
	}
}
