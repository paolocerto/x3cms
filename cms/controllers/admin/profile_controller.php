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
		$mod = new Language_model();

        $form_fields = new X4Form_core('user/profile');
        $form_fields->user = $mod->get_by_id($_SESSION['xuid'], 'users', 'id, lang, username, mail, phone, description');
        $form_fields->languages = $mod->get_alanguages(1);

        // get the fields array
        $fields = $form_fields->render();

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

		// contents
		$view = new X4View_core('page');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = '';
		$view->content = new X4View_core('editor');

		// form builder
		$view->content->super_title = _EDIT_PROFILE;
		$view->content->form = X4Form_helper::doform('profile', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'profile\')"');

		$view->render(true);
	}

	/**
	 * Register User profile
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function profiling(array $_post)
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
        {
			$post['password'] = X4Utils_helper::hashing($_post['password']);
        }
		$user = new User_model();

		// check if username or email address are already used by another user
		$check = (boolean) $user->exists($post['username'], $post['mail'], $_SESSION['xuid']);
		if ($check)
        {
			$msg = AdmUtils_helper::set_msg($false, '', $this->dict->get_word('_USER_ALREADY_EXISTS', 'msg'));
        }
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
            {
				$msg->update = array(
					'element' => 'page',
					'url' => BASE_URL.'profile'
				);
            }
		}
		$this->response($msg);
	}
}
