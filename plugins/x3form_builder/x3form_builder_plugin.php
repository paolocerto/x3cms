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
	 *
	 * @param   object Site
	 * @return  void
	 */
	public function __construct($site)
	{
		parent::__construct($site);
		$this->dict = new X4Dict_model(X4Route_core::$area, X4Route_core::$lang);
	}

	/**
	 * Default plugin's method
	 *
	 * @param   object	Page
	 * @param   array	Array of args
	 * @param   string	Parameter
	 * @return  string
	 */
	public function get_module(stdClass $page, array $args, string $param = '')
	{
		$out = '';
		if (!empty($param))
		{
		    $this->dict->get_wordarray(array('form', 'x3form_builder'));
			$mod = new X3form_builder_model();
			// get fields
			$items = $mod->get_fields_by_form($page->id_area, $page->lang, $param);

			if ($items)
			{
				$form = $mod->get_by_id($items[0]->id_form);
				$c = 0;
				$enctype = '';

				// build the form
				$fields = array();

				// to handle file's label
				$file_array = array();
				$recaptcha = false;
				foreach ($items as $i)
				{
					$opts = array();
					// handle value
					if (!empty($i->value) && !is_numeric($i->value))
					{
						switch ($i->xtype)
						{
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
							$opt = explode('|', $i->value);
							foreach ($opt as $ii)
							{
								$opts[] = array('v' => $ii, 'o' => $ii);
							}
							$value = $opt[0];
							break;
						case 'checkbox':
							$value = _YES;
							break;
						default:
							// text and textarea
							$value = $i->value;
							break;
						}
					}
					else
					{
					    switch ($i->xtype)
						{
                            default:
                                $value = ($i->xtype == 'checkbox') ? _YES : '';
                                break;
						}
					}

					$fields[$c] = array(
						'label' => (empty($i->label)) ? null : $i->label,
						'type' => $i->xtype,
						'value' => $value,
						'name' => $i->name
					);

                    // fix default value for radiobuttons
                    if ($i->xtype == 'radio')
                    {
                        $fields[$c]['checked'] = $value;
                    }

					// handle label and alabel (alternative label)
					$label = null;
					if (!empty($i->label))
					{
						if (substr($i->label, 0, 7) == 'alabel-')
						{
							$fields[$c]['alabel'] = substr($i->label, 7);
						}
						else
						{
							$label = $i->label;
						}
					}
					$fields[$c]['label'] = $label;

					// files
					if ($i->xtype == 'file')
					{
						$enctype = 'enctype="multipart/form-data"';

						// Store labels
						$file_array[$i->name] = (empty($fields[$c]['label']) && isset($fields[$c]['alabel']))
							? $fields[$c]['alabel']
							: $fields[$c]['label'];
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

					// sanitize
					$fields[$c]['sanitize'] = 'string';

					if (!empty($i->suggestion))
                    {
						$fields[$c]['suggestion'] = nl2br($i->suggestion);
                    }

					if (!empty($i->extra))
					{
						if ($i->extra == 'multiple')
						{
							$fields[$c]['multiple'] = 8;
						}
						else
						{
							$fields[$c]['extra'] = $i->extra;
						}
					}

					// if select
					if (!empty($opts))
					{
						$fields[$c]['options'] = array(X4Array_helper::array2obj($opts, 'v', 'o'), 'value', 'option');
					}
					$c++;
				}

				if (X4Route_core::$post && array_key_exists(strrev($param),$_POST))
				{
					$e = X4Validation_helper::form($fields, $param);
					if ($e)
					{
						$this->form_result($page->id_area, $page->lang, $form, $_POST, $fields, $file_array);
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

                $submit_action = ($recaptcha)
                    ? 'onclick="fsubmit()"'
                    : '';

				$out .= X4Form_helper::doform($param, $_SERVER['REQUEST_URI'].'#anchor'.$param, $fields, array($reset, $submit, 'buttons'), 'post', $enctype, $submit_action);
			}
			return $out;
		}
		else
		{
			return '';
		}
	}

	/**
	 * register form
	 *
	 * @access private
     * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   object	$form Form object
	 * @param   array	$_post _POST array
	 * @param   array	$fields fields array
	 * @param   array 	$file_array Files labels array
	 * @return  void
	 */
	private function form_result(int $id_area, string $lang, stdClass $form, array $_post, array $fields, array $file_array)
	{
		// get data
		$mod = new X3form_builder_model();

		list($_files, $error) = $this->upload_files($id_area, $fields);

		if (empty($error))
        {
            $result = [0, 0];
		    $from = MAIL;

            $replyto = (isset($_post['email']) && !empty($_post['email']))
		        ? ['mail' => $_post['email'], 'name' => $_post['email']]
		        : [];

		    // clean unused field
            unset($_post[strrev($form->name)]);
            unset($_post['x4token']);

		    // send mail
            $xlock = 0;
            $msg_spam = $mod->messagize($id_area, $form->name, $_post, $_files);
            // send email
		    if (!empty($form->mailto) && $msg_spam == 0)
            {
		        $mails = explode('|', $form->mailto);
		        $to = array();
				$attachments = array();
		        foreach ($mails as $i)
		        {
		            $to[] = array('mail' => $i, 'name' => $i);
		        }

				if (!empty($_files))
				{
					$conf = $this->site->get_module_param('x3form_builder', $id_area);
					// attachments
					$attachments = $this->attach_files($id_area, $_files, $conf);
				}

				// we send email only if not spam
				X4Mailer_helper::mailto($from, true, $form->title, $msg_spam, $to, $attachments, [], [], $replyto);
		    }

            // it seems spam but we are unsure
            if ($msg_spam > -2)
            {
                // checkbox checking
                $this->checkbox_checking($_post, $fields);

                // register data
                $post = array(
                    'id_area' => $id_area,
                    'lang' => $lang,
                    'id_form' => $form->id,
                    'result' => json_encode($_post + $_files),
                    'xlock' => is_numeric($msg_spam) ? 1 : 0
                );
                $result = $mod->insert($post, 'x3_forms_results');
            }

            // return msg
            $msg = ($result[1] && $msg_spam == 0)
                ? $form->msg_ok
                : $form->msg_failed;

			// delete file sfrom the server
			if (!empty($attachments) && $conf['delete'])
			{
				$this->delete_files($id_area, $attachments);
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
            $_SESSION['msg'] = implode('<br />', $str);
            return 1;
        }

		header('Location: '.BASE_URL.'msg/message/'.urlencode($form->title).'/'.urlencode($msg).'?ok='.intval($result[1] && $msg_spam == 0));
		die;
	}

	/**
	 * attach files
	 *
	 * @access private
	 * @param   integer	$id_area
	 * @param   array	$_files
     * @param   array	$conf
	 * @return  array
	 */
	private function attach_files(int $id_area, array $files, array $conf)
	{
		$path = (!empty($conf['folder']) && is_dir(PPATH.$conf['folder']))
			? PPATH.$conf['folder'].'/'
			: PPATH.'tmp/';

		$a = array();
		foreach ($files as $k => $v)
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
	 *
	 * @access private
	 * @param   integer	$id_area
	 * @param   array	$_files
	 * @return  void
	 */
	private function delete_files(int $id_area, array $files)
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
	 *
	 * @access private
	 * @param   array	$_post _POST array
	 * @param   array	$fields fields array
	 * @return  void
	 */
	private function checkbox_checking(array &$_post, array $fields)
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
	 *
	 * @access private
	 * @param   integer $id_area Area ID
	 * @param   array	$fields fields array
	 * @return  void
	 */
	private function upload_files($id_area, $fields)
	{
		$files = array();
		$error = array();

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
                        $files[$k] = $this->site->site->domain.'/cms/files/'.$path.'/'.$name;
                    }
                }
			}
		}
		return array($files, $error);
	}

	/**
	 * call plugin actions
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$control action name
	 * @param   mixed	$a
	 * @param   mixed	$b
	 * @param   mixed	$c
	 * @param   mixed	$d
	 * @return  void
	 */
	public function plugin(int $id_area, string $control, string $a, string $b, string $c, string $d)
	{
		switch ($control)
		{

		// put here others calls

		default:
			return '';
			break;
		}
	}
}

