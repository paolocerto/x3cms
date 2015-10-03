<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
 
/**
 * Controller for User profile 
 * 
 * @package X3CMS
 */
class Profile_controller extends X3ui_controller
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
	 * Edit User profile
	 *
	 * @return  void
	 */
	public function _default()
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'login', 'users', 'profile'));
		
		// get object
		$user = new User_model();
		$u = $user->get_by_id($_SESSION['xuid']);
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $u->id,
			'name' => 'id'
		);
		
		$lmod = new Language_model();
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _LANGUAGE,
			'type' => 'select',
			'value' => $u->lang,
			'options' => array($lmod->get_alanguages(1), 'code', 'language'),
			'name' => 'lang',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _USERNAME,
			'type' => 'text',
			'value' => $u->username,
			'name' => 'username',
			'suggestion' => _USERNAME_RULE,
			'rule' => 'required|alphanumeric|minlength§5',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div></div>'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html',
			'value' => '<h4 class="acenter">'._PASSWORD_CHANGE_MSG.'</h4>'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _PASSWORD,
			'type' => 'password',
			'value' => '',
			'name' => 'password',
			'suggestion' => _PASSWORD_RULE,
			'rule' => 'alphanumeric|minlength§5',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' =>  _REPEAT_PASSWORD,
			'type' => 'password',
			'value' => '',
			'name' => 'password2',
			'rule' => 'equal-password',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div></div>'
		);
		
		$fields[] = array(
			'label' => _DESCRIPTION,
			'type' => 'textarea', 
			'value' => $u->description,
			'name' => 'description',
			'sanitize' => 'string',
			'rule' => 'required'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _EMAIL,
			'type' => 'text',
			'value' => $u->mail,
			'name' => 'mail',
			'rule' => 'required|mail',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _PHONE,
			'type' => 'text',
			'value' => $u->phone,
			'name' => 'phone',
			'rule' => 'phone',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div></div>'
		);
		
		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'profile');
			if ($e) 
			{
				$this->profiling($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
		
		// get page
		$page = $this->get_page('profile');
		
		// content
		$view = new X4View_core('container');
		
		$view->content = new X4View_core('editor');
		$view->content->close = false;
		$view->content->page = $page;
		
		// form builder
		$view->content->title = _EDIT_PROFILE;
		$view->content->form = '<div class="band"><div class="one-third push-one-third sm-one-whole sm-push-none">'.X4Form_helper::doform('profile', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'profile\');"').'</div></div>';
		 
		$view->render(TRUE);
	}
	
	/**
	 * Register User profile
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function profiling($_post)
	{
		$msg = null;
		
		// ther'is no permission check because each user can only change his profile
		
		// handle _post
		$post = array(
			'lang' => $_post['lang'],
			'username' => $_post['username'],
			'description' => strip_tags($_post['description']),
			'mail' => $_post['mail'],
			'phone' => $_post['phone']
		);
		
		// check for password update
		if (!empty($_post['password'])) 
			$post['password'] = X4Utils_helper::hashing($_post['password']);
		
		$user = new User_model();
		
		// check if username or email address are already used by another user
		$check = (boolean) $user->exists($post['username'], $post['mail'], $_SESSION['xuid']);
		if ($check) 
			$msg = AdmUtils_helper::set_msg($false, '', $this->dict->get_word('_USER_ALREADY_EXISTS', 'msg'));
		else 
		{
			// update profile
			$result = $user->update($_SESSION['xuid'], $post);
			
			// if user changes his password then send a reminder
			if ($result[1] && !empty($_post['password'])) 
			{
				// build subject and message
				$s = array('DOMAIN', 'USERNAME', 'PASSWORD');
				$r = array($this->site->site->domain, $_post['username'], $_post['password']);
				$subject = str_replace($s, $r, _SUBJECT_PROFILE);
				$msg = str_replace($s, $r, _MSG_PROFILE);
				$to = array(array('mail' => $_post['mail'], 'name' => $_post['username']));
				// send
				X4Mailer_helper::mailto(MAIL, false, $subject, $msg, $to);
			}
			
			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);
			
			// set update
			if ($result[1])
				$msg->update[] = array(
					'element' => 'topic',
					'url' => urldecode(BASE_URL.'profile'),
					'title' => null
				);
		}
		$this->response($msg);
	}
}
