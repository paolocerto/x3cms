<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
 
/**
 * Login Controller for private area
 * As default this controller use the users table
 * If you want to use another table you must change the controller behaviour
 * 
 * @package X3CMS
 */
class Login_controller extends X4Cms_controller
{
	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		//X4Utils_helper::logged(2, 'home');
	}
	
	/**
	 * Login form
	 *
	 * @return  void
	 */
	public function _default()
	{
		// initialize failure counter
		if (!isset($_SESSION['failed'])) 
			$_SESSION['failed'] = 0;
		
		// load dictionary
		$this->dict->get_wordarray(array('login', 'form'));
		
		// get page
		$page = $this->get_page('login');
		$view = new X4View_core(X4Utils_helper::set_tpl($page->tpl));
		$view->page = $page;
		$view->sections = array();;
		
		// get menus
		$view->menus = array();
		
		// check if user have used remember me
		if (isset($_COOKIE[COOKIE.'_login'])) 
		{
			list($usr, $hidden_pwd) = explode('-', $_COOKIE[COOKIE.'_login']);
			$pwd = '12345678';
			$chk = true;
		}
		else 
		{
			$usr = $pwd = '';
			$chk = false;
		}
		
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
			'label' => _USERNAME,
			'type' => 'text', 
			'value' => $usr,
			'name' => 'username',
			'rule' => 'required',
			'sanitize' => 'string'
		);
		$fields[] = array(
			'label' => _PASSWORD,
			'type' => 'password', 
			'value' => $pwd,
			'name' => 'password',
			'rule' => 'required|password|minlength§6',
			'sanitize' => 'string'
		);
		
		$fields[] = array(
			'label' => _REMEMBER_ME,
			'type' => 'checkbox', 
			'value' => '1',
			'name' => 'remember_me',
			'checked' => $chk
		);
		
		// if site is on line add captcha
		if (!$chk && $this->site->site->xon) 
		{
			$fields[] = array(
				'label' => null,
				'type' => 'html', 
				'value' => '<div id="cha"><img id="captcha_img" src="'.BASE_URL.'captcha" alt="captcha" /></div>',
			);
			$fields[] = array(
				'label' => null,
				'type' => 'html', 
				'value' => '<p><a href="'.BASE_URL.'captcha" title="reload" id="reload_captcha">'._RELOAD_CAPTCHA.'</a></p>'
			);
			$fields[] = array(
				'label' => _CAPTCHA,
				'type' => 'text', 
				'value' => '',
				'name' => 'captcha',
				'rule' => 'required|captcha',
				'suggestion' => _CASE_SENSITIVE
			);
		}
		
		// if submitted, check control field
		if (X4Route_core::$post && array_key_exists(strrev('formlogin'), $_POST))
		{
			$e = X4Validation_helper::form($fields, 'formlogin');
			if ($e && !isset($_POST['antispam']))
				$this->do_login($_POST, $page->id_area);
			else 
				X4Utils_helper::set_error($fields);
		}
		
		// content
		$view->content = new X4View_core('login');
		
		if ($_SESSION['failed']) 
		{
			$view->content->msg = ($_SESSION['failed'] < 5) 
				? str_replace('XXX', $_SESSION['failed'], _FAILED_X_TIMES) 
				: _FAILED_TOO_TIMES;
		}
		
		// msg
		$msg = '';
		if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) 
		{
			$msg = '<div id="msg"><p>'.$_SESSION['msg'].'</p></div>';
			unset($_SESSION['msg']);
		}
		
		// form builder
		$view->content->form = '<div style="max-width:340px;">'.$msg.X4Form_helper::doform('formlogin', $_SERVER['REQUEST_URI'], $fields, array(null, _LOGIN, 'buttons')).'</div>';
		$view->render(TRUE);
	}
	
	/**
	 * Perform login
	 *
	 * @param   array 	$_post _POST array
	 * @param	integer	$id_area Area ID
	 * @return  void
	 */
	public function do_login($_post, $id_area)
	{
		if ($_SESSION['failed'] < 5)
		{
			// fields to set in sessions
			$fields = array(
				'mail' => 'mail',
				'username' => 'username',
				'id' => 'uid',
				'last_in' => 'last_in'
			);
			
			// conditions
			$conditions = array('id_area' => $id_area, 'username' => $_post['username']);
			
			// remember me
			$conditions['password'] = (isset($_post['hpwd']) && $_post['password'] == '12345678')
				? $_post['hpwd']
				: X4Utils_helper::hashing($_post['password']);
			
			// log in
			$login = X4Auth_helper::log_in(
				'users', 
				$conditions, 
				$fields
			);
			
			if ($login)
			{
				$_SESSION['failed'] = 0;
				// post login operations
				$_SESSION['site'] = SITE;
				$_SESSION['id_area'] = $id_area;
				
				// set cookie for remember me
				if (isset($_post['remember_me'])) 
					setcookie(COOKIE.'_login', $conditions['username'].'-'.$conditions['password'], time() + 2592000, '/', $_SERVER['HTTP_HOST']);
				
				$mod = new X4Auth_model('users');
				
				// log
				if (LOGS)
				{
					$mod->logger($_SESSION['uid'], $id_area, 'users', 'log in');
				}
				
				$area = $mod->get_by_id($id_area, 'areas', 'name');
				
				// redirect
				header('Location: '.BASE_URL.$area->name);
				die;
			}
			else
			{
				// increase failure counter
				$_SESSION['failed']++;
				
				if (LOGS)
				{
					$mod = new X4Auth_model('users');
					$mod->logger(0, $id_area, 'users', 'log in failed for '.$_post['username']);
				}
			}
		}
		header('Location: '.BASE_URL.'login');
		die;
	}
	
	/**
	 * Perform logout
	 *
	 * @return  void
	 */
	public function logout()
	{
		// log
		if (LOGS)
		{
			$mod = new X4Auth_model('users');
			$mod->logger($_SESSION['uid'], $_SESSION['id_area'], 'users', 'log out');
		}
		
		X4Auth_helper::log_out();
		
		// redirect
		header('Location: '.ROOT);
		die;
	}
	
	/**
	 * Recovery password
	 *
	 * @return  void
	 */
	public function recovery()
	{
		// load dictionary
		$this->dict->get_wordarray(array('login', 'form', 'pwd_recovery'));
		
		// get page
		$page = $this->get_page('login');
		$view = new X4View_core(X4Utils_helper::set_tpl($page->tpl));
		$view->page = $page;
		
		// get menus
		$view->menus = array();
		
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
			'rule' => 'required|mail'
		);
		
		// if submitted, check control field
		if (X4Route_core::$post && array_key_exists(strrev('formrecovery'),$_POST))
		{
			$e = X4Validation_helper::form($fields, 'formrecovery');
			
			if ($e && !isset($_POST['antispam'])) 
			{
				$this->do_recovery($_POST);
				die;
			}
			else 
				X4Utils_helper::set_error($fields);
		}
		
		// content
		$view->content = new X4View_core('recovery');
		
		// form builder
		$view->content->form = X4Form_helper::doform('formrecovery', $_SERVER['REQUEST_URI'], $fields, array(null, _SEND, 'xcenter'));
		
		$view->render(TRUE);
	}
	
	/**
	 * Recovery password action
	 * send an email with a code for reset
	 *
	 * @access private
	 * @param   array	$_post POST array
	 * @return  void
	 */
	private function do_recovery($_post)
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
				$link = $this->site->site->domain.'/private/login/reset/'.$user->id.'/'.$md5;
				
				// send a resetting mail
				$src = array('XXXLINKXXX', 'XXXDOMAINXXX');
				$rpl = array($link, $this->site->site->domain);
				
				$view = new X4View_core(X4Utils_helper::set_tpl('mail'));
				$view->subject = SERVICE.' - '._RECOVERY_SUBJECT;
				$view->message = str_replace($src, $rpl, _RECOVERY_BODY_CONFIRM);
				
				// build msg
				$body = $view->__toString();
				$msg = mb_convert_encoding($body, 'ISO-8859-1', 'auto');
						
				// recipients
				$to = array(array('mail' => $user->mail, 'name' => $user->username));
				
				$check = X4Mailer_helper::mailto(MAIL, true, $view->subject, $msg, $to, array());
				
				X4Utils_helper::set_msg($check, _RESET_MSG, _MSG_ERROR);
				
				header('Location: '.BASE_URL.'login/recovery');
				die;
			}
			
			// log
			if (LOGS)
				$mod->logger($user->id, $_post['id_area'], 'users', 'recovery password request from '.$_post['email']);
		}
		else if (LOGS)
			$mod->logger(0, $_post['id_area'], 'users', 'recovery password request from unknown '.$_post['email']);
		
		X4Utils_helper::set_msg(false, '', _RECOVERY_PWD_ERROR);
		header('Location: '.BASE_URL.'login/recovery');
		die;
	}
	
	/**
	 * Reset password
	 * send an email with new credentials
	 *
	 * @param   integer	$id User ID
	 * @param   string	$md5 Encrypted verification code
	 * @return  void
	 */
	public function reset($id, $md5)
	{
		$mod = new X4Auth_model('users');
		$user = $mod->get_by_id($id, 'users', 'last_in, password, mail, username');
		
		$id_area = X4Route_core::get_id_area();
		
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
					
					$view = new X4View_core(X4Utils_helper::set_tpl('mail'));
					$view->subject = SERVICE.' - '._RECOVERY_SUBJECT;
					$view->message = str_replace($src, $rpl, _RECOVERY_BODY_RESET);
					
					// build msg
					$body = $view->__toString();
					$msg = mb_convert_encoding($body, 'ISO-8859-1', 'auto');
					
					// recipients
					$to = array(array('mail' => $user->mail, 'name' => $user->username));
					
					$check = X4Mailer_helper::mailto(MAIL, true, $view->subject, $msg, $to, array());
					
					X4Utils_helper::set_msg($check, _RECOVERY_PWD_OK, _MSG_ERROR);
					header('Location: '.BASE_URL.'login/recovery');
					die;
				}
				
				// log
				if (LOGS)
					$mod->logger($user->id, $id_area, 'users', 'recovery password completed for '.$user->mail);
			}
			else if (LOGS)
				$mod->logger($user->id, $id_area, 'users', 'recovery password failed for '.$user->mail);
		}
		else if (LOGS)
			$mod->logger($user->id, $id_area, 'users', 'recovery password attempt from unknown id '.$id);
		
		X4Utils_helper::set_msg(false, '', _RECOVERY_PWD_ERROR);
		header('Location: '.BASE_URL.'login/recovery');
		die;
	}
	
}
