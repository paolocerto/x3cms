<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */
 
/**
 * Helper for Open Inviter
 * 
 * @package X4WEBAPP
 */
class X4Inviter_helper 
{
	/**
	 * Invite throught social
	 *
	 * @static
	 * @param string	Sender name
	 * @param boolean	HTML/TXT format
	 * @param string	Email subject
	 * @param string	Email body
	 * @param array		Associative array of recipients (name, mail)
	 * @param array		Associative array of attachments (file)
	 * @return boolean
	 */
	public static function invite()
	{
		X4Core::auto_load('inviter');
		
		$inviter = new Openinviter();
		$oi_services=$inviter->getPlugins();
		if (isset($_POST['provider_box'])) 
		{
			if (isset($oi_services['email'][$_POST['provider_box']])) $plugType='email';
			elseif (isset($oi_services['social'][$_POST['provider_box']])) $plugType='social';
			else $plugType='';
		}
		else $plugType = '';
		function ers($ers)
			{
			if (!empty($ers))
				{
				$contents = '<div id="msg"><p>';
				foreach ($ers as $key=>$error)
					$contents .= $error.'<br >';
				$contents .= '</p></div>';
				return $contents;
				}
			}
			
		function oks($oks)
			{
			if (!empty($oks))
				{
				$contents = '<div id="msg"><p>';
				foreach ($oks as $key=>$msg)
					$contents .= $msg.'<br >';
				$contents .= '</p></div>';
				return $contents;
				}
			}
		
		if (!empty($_POST['step'])) $step=$_POST['step'];
		else $step='get_contacts';
		
		$ers=array();$oks=array();$import_ok=false;$done=false;
		if ($_SERVER['REQUEST_METHOD']=='POST')
			{
			if ($step=='get_contacts')
				{
				if (empty($_POST['email_box']))
					$ers['email']=_X4OI_INVITER_MISSING;
				if (empty($_POST['password_box']))
					$ers['password']=_X4OI_PASSWORD_MISSING;
				if (empty($_POST['provider_box']))
					$ers['provider']=_X4OI_PROVIDER_EMPTY;
				if (count($ers)==0)
					{
					$inviter->startPlugin($_POST['provider_box']);
					$internal=$inviter->getInternalError();
					if ($internal)
						$ers['inviter']=$internal;
					elseif (!$inviter->login($_POST['email_box'],$_POST['password_box']))
						{
						$internal=$inviter->getInternalError();
						$ers['login']=($internal?$internal: _X4OI_LOGIN_FAILED);
						}
					elseif (false===$contacts=$inviter->getMyContacts())
						$ers['contacts']=_X4OI_UNABLE_TO_GET_CONTACTS;
					else
						{
						$import_ok=true;
						$step='send_invites';
						$_POST['oi_session_id']=$inviter->plugin->getSessionID();
						$_POST['message_box']='';
						}
					}
				}
			elseif ($step=='send_invites')
				{
				if (empty($_POST['provider_box'])) $ers['provider']=_X4OI_PROVIDER_MISSING;
				else
					{
					$inviter->startPlugin($_POST['provider_box']);
					$internal=$inviter->getInternalError();
					if ($internal) $ers['internal']=$internal;
					else
						{
						if (empty($_POST['email_box'])) $ers['inviter']=_X4OI_INVITER_MISSING;
						if (empty($_POST['oi_session_id'])) $ers['session_id']=_X4OI_NO_ACTIVE_SESSION;
						if (empty($_POST['message_box'])) $ers['message_body']=_X4OI_MSG_MISSING;
						else 
						{
							$_POST['message_box']=strip_tags($_POST['message_box']);
							$_POST['message_box']=mb_convert_encoding($_POST['message_box'], 'ISO-8859-1', 'auto');
						}
						$selected_contacts=array();$contacts=array();
						$message=array('subject'=>_X4OI_SUBJECT,'body'=>_X4OI_BODY,'attachment'=>"\n\r"._X4OI_ATTACHED_MSG.": \n\r".$_POST['message_box']);
						if ($inviter->showContacts())
							{
							foreach ($_POST as $key=>$val)
								if (strpos($key,'check_')!==false)
									$selected_contacts[$_POST['email_'.$val]]=$_POST['name_'.$val];
								elseif (strpos($key,'email_')!==false)
									{
									$temp=explode('_',$key);$counter=$temp[1];
									if (is_numeric($temp[1])) $contacts[$val]=$_POST['name_'.$temp[1]];
									}
							if (count($selected_contacts)==0) $ers['contacts']=_X4OI_NO_RECIPIENTS;
							}
						}
					}
				if (count($ers)==0)
					{
					$sendMessage=$inviter->sendMessage($_POST['oi_session_id'],$message,$selected_contacts);
					$inviter->logout();
					if ($sendMessage===-1)
						{
						$message_footer="\r\n\r\n"._X4OI_FOOTER;
						$message_subject=$_POST['email_box'].$message['subject'];
						$message_body=$message['body'].$message['attachment'].$message_footer; 
						$headers="From: {$_POST['email_box']}";
						foreach ($selected_contacts as $email=>$name)
							mail($email,$message_subject,$message_body,$headers);
						$oks['mails']=_X4OI_SENT_SUCCESSFULLY;
						}
					elseif ($sendMessage===false)
						{
						$internal=$inviter->getInternalError();
						$ers['internal']=($internal?$internal:_X4OI_ERROR);
						}
					else $oks['internal']=_X4OI_SUCCESSFULLY;
					$done=true;
					}
				}
			}
		else
			{
			$_POST['email_box']='';
			$_POST['password_box']='';
			$_POST['provider_box']='';
			}
		
		$out = '<script type="text/javascript">
			function toggleAll(element) 
			{
			var form = document.forms.openinviter, z = 0;
			for(z=0; z<form.length;z++)
				{
				if(form[z].type == \'checkbox\')
					form[z].checked = element.checked;
				}
			}
		</script>';
		
		$out .= ers($ers).oks($oks).'<form action="" method="POST" id="openinviter" name="openinviter"><fieldset>';
		
		if (!$done)
		{
			if ($step=='get_contacts')
			{
				$out .= '<label for="email_box">'._X4OI_EMAIL.'</label>
						<input class="thTextbox" type="text" name="email_box" value="'.$_POST['email_box'].'">
						<label for="password_box">'._X4OI_PASSWORD.'</label>
						<input class="thTextbox" type="password" name="password_box" value="'.$_POST['password_box'].'">
						<label for="provider_box">'._X4OI_PROVIDER.'</label>
						<select class="thSelect" name="provider_box"><option value=""></option>';
				foreach ($oi_services as $type => $providers)
				{
					$out .= '<optgroup label="'.$inviter->pluginTypes[$type].'">';
					foreach ($providers as $provider=>$details)
						$out .= '<option value="'.$provider.'" '.($_POST['provider_box']==$provider ? ' selected="selected"' : '').'>'.$details['name'].'</option>';
					$out .= '</optgroup>';
				}
				
				$out .= '</select>
					<div class="acenter"><button type="submit" name="import"></button></div>
					<input type="hidden" name="step" value="get_contacts">';
				}
			else
				$out .= '<label for="message_box">'._X4OI_MSG.'</label>
						<textarea name="message_box">'.$_POST['message_box'].'</textarea>
						<div class="acenter"><button type="submit" name="send"></button></div>';
			}
		//$contents.="<center><a href='http://openinviter.com/'><img src='http://openinviter.com/images/banners/banner_blue_1.gif?nr=56914' border='0' alt='Powered by OpenInviter.com' title='Powered by OpenInviter.com'></a></center>";
		if (!$done)
		{
			if ($step=='send_invites')
			{
				if ($inviter->showContacts())
				{
					$out .= '<br /><h2>'._X4OI_CONTACTS.'</h2>';
					if (empty($contacts))
						$out .= '<h3>'._X4OI_NO_CONTACTS.'</h3>';
					else
					{
						$out .= '<label for="toggle_all"><input class="check" type="checkbox" onChange="toggleAll(this)" name="toggle_all" title="Select/Deselect all" checked="checked" /> &nbsp;'._X4OI_SELECT_ALL.'</label>';
						$counter=0;
						foreach ($contacts as $email=>$name)
						{
							$counter++;
							$mail = ($plugType == 'email' && $email != $name)
								? $email
								: '';
								
							$out .= '<label for="check_'.$counter.'"><input name="check_'.$counter.'" value="'.$counter.'" type="checkbox" class="check" checked="checked" /> &nbsp;'.$name.' '.$mail.'
								<input type="hidden" name="email_'.$counter.'" value="'.$email.'" />
								<input type="hidden" name="name_'.$counter.'" value="'.$name.'" /></label>';
						}
						$out .= '<div class="acenter"><button type="submit" name="send"></button></div>';
					}
				}
				$out .= '<input type="hidden" name="step" value="send_invites" />
					<input type="hidden" name="provider_box" value="'.$_POST['provider_box'].'" />
					<input type="hidden" name="email_box" value="'.$_POST['email_box'].'" />
					<input type="hidden" name="oi_session_id" value="'.$_POST['oi_session_id'].'" />';
			}
		}
		$out .= '</fieldset></form>';
		
		return $out;
		
	}
}
