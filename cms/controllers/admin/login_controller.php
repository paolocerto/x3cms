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
 * Login Controller
 *
 * @package X3CMS
 */
class Login_controller extends X4Cms_controller
{
	/*
	 * List of admitted IP addresses
	 * If you want to permit the login to a set of IP addresses
	 */
	protected $admitted = array(); // array('168.192.0.1', '192.168.0.3');

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// check for locked login
		if (!empty($this->admitted))
		{
			$ip = $this->getRealIpAddr();
			if (ROOT == '/' && !in_array($ip, $this->admitted))
			{
				header('Location: '.$this->site->data->domain);
				die;
			}
		}
	}

	/**
	 * Login form
	 */
	public function _default() : void
	{
		if (X4Utils_helper::is_ajax())
		{
			$view = new X4View_core('empty');
			$view->location = BASE_URL.'login';
		}
		else
		{
			// initialize failure counter
			if (!isset($_SESSION['failed']))
			{
				$_SESSION['failed'] = 0;
			}

			// load dictionary
			$this->dict->get_wordarray(array('login'));

			// get page
			$page = $this->get_page('login');

			// contents
			$view = new X4View_core(X4Theme_helper::set_tpl($page->tpl));
			$view->page = $page;
			$view->content = new X4View_core('login');

			$form_fields = new X4Form_core('login');
            $form_fields->site = $this->site->data;

            // get the fields array
            $fields = $form_fields->render();

			// if submitted, check control field
			if (X4Route_core::$post && array_key_exists(strrev('formlogin'), $_POST))
			{
				$e = X4Validation_helper::form($fields, 'formlogin');
				if ($e) // && !isset($_POST['antispam']))
				{
					$this->do_login($_POST);
					die;
				}
				else
				{
					X4Utils_helper::set_error($fields);
				}
			}

			// msg
			if (isset($_SESSION['msg']) && !empty($_SESSION['msg']))
			{
				$view->content->msg = $_SESSION['msg'];
				unset($_SESSION['msg']);
			}

			// failure message
			if ($_SESSION['failed'])
			{
				$view->content->msg = ($_SESSION['failed'] < 5)
					? str_replace('XXX', $_SESSION['failed'], _FAILED_X_TIMES)
					: _FAILED_TOO_TIMES;
			}

			// form builder
			$view->content->form = X4Form_helper::doform('formlogin', $_SERVER['REQUEST_URI'], $fields, array(null, _LOGIN, 'buttons'));
		}
		$view->render(true);
	}

	/**
	 * Perform login
	 */
	private function do_login(array $_post) : void
	{
		// check failure counter
		if ($_SESSION['failed'] < 5)
		{
			// fields to set in sessions
			$fields = array(
				'mail' => 'mail',
				'username' => 'username',
				'id' => 'xuid',
				'lang' => 'lang',
                'id_group' => 'id_group',
				'last_in' => 'last_in',
				'level' => 'level'
			);

			// conditions
			$conditions = array('id_area' => 1, 'username' => $_post['username']);

			// remember me
			$conditions['password'] = (isset($_post['hpwd']) && $_post['password'] == '12345678')
				? $_post['hpwd']
				: X4Utils_helper::hashing($_post['password']);

			// log in
			$login = X4Auth_helper::log_in(
				'users',
				$conditions,
				$fields,
				true,   // last login
				true    // haskey
			);

			if ($login)
			{
				// post login operations
				$_SESSION['site'] = SITE;
				$_SESSION['id_area'] = 1;	// admin AREA ID

				// refactory permissions
				$mod = new Permission_model();
				$mod->refactory($_SESSION['xuid']);

				// log
				if (LOGS)
				{
					$mod = new X4Auth_model('users');
					$mod->logger($_SESSION['xuid'], 1, 'users', 'log in', X4Utils_helper::get_ip());
				}

				// redirect
				header('Location: '.$this->site->data->domain.'/'.$_SESSION['lang'].'/admin');
				die;
			}
			else
			{
				// increase failure counter
				$_SESSION['failed']++;

				if (LOGS)
				{
					$mod = new X4Auth_model('users');
					$mod->logger(0, 1, 'users', 'log in failed for '.$_post['username'], '', 1, X4Utils_helper::get_ip());
				}
			}
		}
		header('Location: '.BASE_URL.'login');
		die;
	}

	/**
	 * Perform logout
	 */
	public function logout() : void
	{
		unset($_COOKIE[COOKIE.'_hash']);
		setcookie(COOKIE.'_hash', '', time() - 3600, '/', $_SERVER['HTTP_HOST']);

		// log
		if (LOGS)
		{
			if (isset($_SESSION['xuid']))
			{
				$mod = new X4Auth_model('users');
				$mod->logger($_SESSION['xuid'], 1, 'users', 'log out');
			}
		}

		X4Auth_helper::log_out();
        header('Location: '.ROOT);
		die;
	}

	/**
	 * Recovery password
	 */
	public function recovery() : void
	{
		// load dictionary
		$this->dict->get_wordarray(array('login', 'form', 'pwd_recovery'));

		// get page
		$page = $this->get_page('login/recovery');
		$view = new X4View_core(X4Theme_helper::set_tpl($page->tpl));
		$view->page = $page;

		// get menus
		$view->menus = array();
		$view->navbar = array($this->site->get_bredcrumb($page));

		// build the form
		$fields = array();
		// antispam control
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => time(),
			'name' => 'antispam'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $page->id_area,
			'name' => 'id_area'
		);
		$fields[] = array(
			'label' => _MAIL,
			'type' => 'text',
			'value' => '',
			'name' => 'email',
			'rule' => 'required|mail',
			'sanitize' => 'string',
			'extra' => 'class="w-full"'
		);

		// if submitted, check control field
		if (X4Route_core::$post && array_key_exists(strrev('formrecovery'), $_POST))
		{
			$e = X4Validation_helper::form($fields, 'formrecovery');

			if ($e && !isset($_POST['antispam']))
			{
				$this->do_recovery($_POST);
				die;
			}
			else
			{
				X4Utils_helper::set_error($fields);
			}
		}

		// content
		$view->content = new X4View_core('recovery');

		// msg
		if (isset($_SESSION['msg']) && !empty($_SESSION['msg']))
		{
			$view->content->msg = $_SESSION['msg'];
			unset($_SESSION['msg']);
		}

		// form builder
		$view->content->form = X4Form_helper::doform('formrecovery', $_SERVER['REQUEST_URI'], $fields, array(null, _SEND, 'buttons'));
		$view->render(true);
	}

	/**
	 * Recovery password action
	 * send an email with a code for reset
	 */
	private function do_recovery(array $_post) : void
	{
		// check if users exists
		$mod = new X4Auth_model('users');
		$user = $mod->get_user_by_email($_post['id_area'], strtolower($_post['email']));

		if ($user)
		{
			if ($user->xon && $user->xlock == 0)
			{
				// load dictionary
				$this->dict->get_wordarray(array('login', 'pwd_recovery'));

				// create resetting key
				$md5 = md5($user->last_in.SITE.$user->password);
				$link = $this->site->data->domain.'/admin/login/reset/'.$user->id.'/'.$md5;

				// send a resetting mail
				$src = array('XXXLINKXXX', 'XXXDOMAINXXX');
				$rpl = array($link, $this->site->data->domain);

				$view = new X4View_core(X4Theme_helper::set_tpl('mail'));
				$view->subject = SERVICE.' - '._RECOVERY_SUBJECT;
				$view->message = str_replace($src, $rpl, _RECOVERY_BODY_CONFIRM);

				// build msg
				$body = $view->__toString();
				$msg = mb_convert_encoding($body, 'ISO-8859-1', 'auto');

				// recipients
				$to = ['mail' => $user->mail, 'name' => $user->username];

				$check = X4Mailer_helper::mailto(MAIL, true, $view->subject, $msg, ['to' => [$to]]);

				X4Utils_helper::set_msg($check, _RESET_MSG, _MSG_ERROR);

				header('Location: '.BASE_URL.'login/recovery');
				die;
			}

			// log
			if (LOGS)
			{
				$mod->logger($user->id, 1, 'users', 'recovery password request from '.$_post['email']);
			}
		}
		elseif (LOGS)
		{
			$mod->logger(0, 1, 'users', 'recovery password request from unknown '.$_post['email']);
		}

		X4Utils_helper::set_msg(false, '', _RECOVERY_PWD_ERROR);
		header('Location: '.BASE_URL.'login/recovery');
		die;
	}

	/**
	 * Reset password
	 * send an email with new credentials
	 */
	public function reset(
        int $id,
        string $md5     // Encrypted verification code
    ) : void
	{
		$mod = new X4Auth_model('users');
		$user = $mod->get_by_id($id, 'users', 'last_in, password, mail, username');
		if ($user)
		{
			// user exists
			if (md5($user->last_in.SITE.$user->password) == $md5 && (time() - strtotime($user->last_in)) < 604800)
			{
				$new_pwd = X4Text_helper::random_string(6);
				$result = $mod->reset($user->mail, $new_pwd);

				if ($result)
				{
					// load dictionary
					$this->dict->get_wordarray(array('login', 'pwd_recovery'));

					$src = array('XXXUSERNAMEXXX', 'XXXPASSWORDXXX');
					$rpl = array($user->username, $new_pwd);

					$view = new X4View_core(X4Theme_helper::set_tpl('mail'));
					$view->subject = SERVICE.' - '._RECOVERY_SUBJECT;
					$view->message = str_replace($src, $rpl, _RECOVERY_BODY_RESET);

					// build msg
					$body = $view->__toString();
					$msg = mb_convert_encoding($body, 'ISO-8859-1', 'auto');

					// recipients
					$to = ['mail' => $user->mail, 'name' => $user->username];

					$check = X4Mailer_helper::mailto(MAIL, true, $view->subject, $msg, ['to' => [$to]]);

					X4Utils_helper::set_msg($check, _RECOVERY_PWD_OK, _MSG_ERROR);
					header('Location: '.BASE_URL.'login/recovery');
					die;
				}

				// log
				if (LOGS)
				{
					$mod->logger($user->id, 1, 'users', 'recovery password completed for '.$user->mail);
				}
			}
			else if (LOGS)
			{
				$mod->logger($user->id, 1, 'users', 'recovery password failed for '.$user->mail);
			}
		}
		else if (LOGS)
		{
			$mod->logger($user->id, 1, 'users', 'recovery password attempt from unknown id '.$id);
		}

		X4Utils_helper::set_msg(false, '', _RECOVERY_PWD_ERROR);
		header('Location: '.BASE_URL.'login/recovery');
		die;
	}
}
