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
 * X3Form Builder plugin
 *
 * @package X3CMS
 */
class X3form_builder_plugin extends X4Plugin_core implements X3plugin
{
	/**
	 * Constructor
	 */
	public function __construct(X4Site_model $site)
	{
		parent::__construct($site);
		$this->dict = new X4Dict_model(X4Route_core::$area, X4Route_core::$lang);
	}

	/**
	 * Default plugin's method
	 */
	public function get_module(stdClass $page, array $args, string $param = '') : mixed
	{
        $this->dict->get_wordarray(array('form', 'x3form_builder'));
        $mod = new X3form_builder_model($this->site->data->db);
        // get fields
        $items = $mod->get_fields_by_form($page->id_area, $page->lang, $param);

		if (empty($param) || empty($items))
		{
            return '';
        }

        // build the form
        list($fields, $file_array) = $this->build_data($mod, $items);

        $enctype = empty($file_array)
            ? ''
            : 'enctype="multipart/form-data"';

        $form = $mod->get_by_id($items[0]->id_form);

        if (X4Route_core::$post && array_key_exists(strrev($param),$_POST))
        {
            $e = X4Validation_helper::form($fields, $param);
            if ($e)
            {
                $this->form_result($page, $form, $_POST, $fields, $file_array);
            }
            else
            {
                X4Utils_helper::set_error($fields);
            }
        }

        $out = '<a name="anchor'.$param.'"></a>';
        // msg
        if (isset($_SESSION['msg']) && !empty($_SESSION['msg']))
        {
            $out .= '<div id="msg" class="warning p-6 my-6 text-white rounded"><p>'.$_SESSION['msg'].'</p></div>';
            unset($_SESSION['msg']);
        }

        $reset = (empty($form->reset_button))
            ? null
            : $form->reset_button;

        $submit = (empty($form->submit_button))
            ? _SUBMIT
            : $form->submit_button;

        $out .= X4Form_helper::doform($param, $_SERVER['REQUEST_URI'].'#anchor'.$param, $fields, array($reset, $submit, 'buttons'), 'post', $enctype);
		return $out;
	}

    /**
	 * build data for form
	 */
	private function build_data(X3form_builder_model $mod, array $items) : array
	{
        $c = 0;
        $fields = $file_array = [];
        foreach ($items as $i)
        {
            $fields[$c] = array(
                'label' => (empty($i->label)) ? null : $i->label,
                'type' => $i->xtype,
                'name' => $i->name,
                'sanitize' => 'string',
                'suggestion' => nl2br($i->suggestion),
                'extra' => $i->extra,
            );

            // handle label and alabel (alternative label)
            if (substr($i->label, 0, 7) == 'alabel-')
            {
                $fields[$c]['alabel'] = substr($i->label, 7);
                $fields[$c]['label'] = null;
            }

            // handle value
            if (empty($i->value) || $i->xtype == 'checkbox')
            {
                $fields[$c]['value'] = ($i->xtype == 'checkbox') ? _YES : '';
            }
            else
            {
                switch ($i->xtype)
                {
                case 'file':
                    $file_array[$i->name] = (is_null($fields[$c]['label']) && isset($fields[$c]['alabel']))
                        ? $fields[$c]['alabel']
                        : $fields[$c]['label'];
                    break;
                case 'hidden':
                    if (X4Utils_helper::slugify($i->value) == $i->value)
                    {
                        eval('$value = (isset($page->'.$i->value.')) ? $page->'.$i->value.' : \''.$i->value.'\';');
                    }
                    else
                    {
                        $value = $i->value;
                    }
                    break;
                case 'radio':
                case 'select':
                    $opts = [];
                    $opt = explode('|', $i->value);
                    foreach ($opt as $ii)
                    {
                        $opts[] = array('v' => $ii, 'o' => $ii);
                    }
                    $fields[$c]['options'] = array(X4Array_helper::array2obj($opts, 'v', 'o'), 'value', 'option');
                    $value = $opt[0];
                    if ($i->xtype == 'radio')
                    {
                        $fields[$c]['checked'] = $value;
                    }
                    if (strpos($i->extra, 'multiple'))
                    {
                        $fields[$c]['multiple'] = 8;
                    }
                    break;
                default:
                    // text and textarea
                    $value = $i->value;
                    break;
                }
                $fields[$c]['value'] = $value;
            }

            // rules
            if (!empty($i->rule))
            {
                $rules = json_decode($i->rule);
                $tmp = [];
                foreach ($rules as $rule)
                {
                    $tmp[] = $mod->build_rule($rule);
                }
                $fields[$c]['rule'] = implode('|', $tmp);
            }
            $c++;
        }
        return [$fields, $file_array];
    }

	/**
	 * register form
	 */
	private function form_result(stdClass $page, stdClass $form, array $_post, array $fields, array $file_array) : mixed
	{
		list($_files, $error) = $this->upload_files($page->id_area, $fields);

		if (!empty($error))
        {
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
            $_SESSION['msg'] = implode('<br />', $str);
            return 1;
        }

        $result = [0, 0];

        // clean unused field
        unset($_post[strrev($form->name)]);
        unset($_post['x4token']);

        $mod = new X3form_builder_model($this->site->data->db);

        // check mail
        $msg_spam = $mod->messagize($page->id_area, $form->name, $_post, $_files);
        if (!empty($form->mailto) && $msg_spam == 0)
        {
            $this->send_email($form, $_post, $_files);
        }

        // it seems spam but we are unsure
        if ($msg_spam > -2)
        {
            // checkbox checking
            $this->checkbox_checking($_post, $fields);

            // register data
            $post = array(
                'id_area' => $page->id_area,
                'lang' => $page->lang,
                'id_form' => $form->id,
                'result' => json_encode($_post + $_files),
                'xlock' => is_numeric($msg_spam) ? 1 : 0
            );
            $result = $mod->insert($post, 'x3_forms_results');
        }

        $msg = ($result[1] && $msg_spam == 0)
            ? $form->msg_ok
            : $form->msg_failed;

		header('Location: '.BASE_URL.'msg/message/'.urlencode($form->title).'/'.urlencode($msg).'?ok='.intval($result[1] && $msg_spam == 0));
		die;
	}

    /**
     * Send email
     */
    private function send_email(stdClass $form, array $_post, array $_files) : void
    {
        $from = MAIL;
        $recipients = [];
        if (isset($_post['email']) && !empty($_post['email']))
        {
            $recipients['replyto'] = ['mail' => $_post['email'], 'name' => $_post['email']];
        }

        $mails = explode('|', $form->mailto);
        $attachments = array();
        foreach ($mails as $i)
        {
            $recipients['to'][] = array('mail' => $i, 'name' => $i);
        }

        if (!empty($_files))
        {
            $conf = $this->site->get_module_param('x3form_builder', $form->id_area);
            $attachments = $this->attach_files($_files, $conf);
        }

        // we send email only if not spam
        X4Mailer_helper::mailto($from, true, $form->title, 0, $recipients, $attachments);

        // delete file sfrom the server
        if (!empty($attachments) && $conf['delete'])
        {
            $this->delete_files($id_area, $attachments);
        }
    }

	/**
	 * attach files
	 */
	private function attach_files(array $files, array $conf) : array
	{
		$path = (!empty($conf['folder']) && is_dir(PPATH.$conf['folder']))
			? PPATH.$conf['folder'].'/'
			: PPATH.'tmp/';

		$a = array();
		foreach ($files as $v)
		{
			if (!empty($v))
			{
				$f = explode('/', $v);
				$ff = array_pop($f);
				$a[] = array('filename' => $ff, 'file' => $path.$ff);
			}
		}
		return $a;
	}

	/**
	 * delete files
	 */
	private function delete_files(int $id_area, array $files) : void
	{
		$conf = $this->site->get_module_param('x3form_builder', $id_area);
		$path = (!empty($conf['folder']) && is_dir(PPATH.$conf['folder']))
			? PPATH.$conf['folder'].'/'
			: PPATH.'tmp/';

		foreach ($files as $i)
		{
			$f = explode('/', $v);
			$ff = array_pop($f);
			unlink($path.$ff);
		}
	}

	/**
	 * replace boolean with string in checkbox values
	 */
	private function checkbox_checking(array &$_post, array $fields) : void
	{
		foreach ($fields as $i)
		{
			if ($i['type'] == 'checkbox')
			{
				$_post[$i['name']] = (isset($_post[$i['name']]))
					? _YES
					: _NO;
			}
		}
	}

	/**
	 * upload files
	 */
	private function upload_files($id_area, $fields) : array
	{
		$files = $error = array();
		foreach ($fields as $i)
		{
			if ($i['type'] == 'file')
			{
			    $files[$i['name']] = '';
			}
		}

		if (!empty($files))
		{
			// conf
			$conf = $this->site->get_module_param('x3form_builder', $id_area);
			$path = (!empty($conf['folder']) && is_dir(PPATH.$conf['folder']))
				? PPATH.$conf['folder'].'/'
				: PPATH.'tmp/';

			foreach ($files as $k => $v)
			{
			    if (is_uploaded_file($_FILES[$k]['tmp_name']))
			    {
                    $name = X4Files_helper::upload($k, $path, '__secret', 1);

                    if (is_array($name))
                    {
                        $error = $name;
                    }
                    else
                    {
                        $files[$k] = $this->site->data->domain.'/cms/files/'.$path.'/'.$name;
                    }
                }
			}
		}
		return array($files, $error);
	}

	/**
	 * call plugin actions
	 */
	public function plugin(string $control, mixed $a, mixed $b, mixed $c, mixed $d) : void
	{
		switch ($control)
		{

		// put here others calls

		default:
			echo '';
			break;
		}
	}
}

