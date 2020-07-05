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
 * Helper for mailing
 * 
 * @package X4WEBAPP
 */
class X4Mailer_helper 
{
	/**
	 * Send emails through SwiftMailer
	 *
	 * @static
	 * @param string	Sender name
	 * @param boolean	HTML/TXT format
	 * @param string	Email subject
	 * @param string	Email body
	 * @param array		Associative array of recipients (name, mail)
	 * @param array		Associative array of attachments (file, filename (optional))
	 * @param array		Associative array of CC recipients (name, mail)
	 * @param array		Associative array of BCC recipients (name, mail)
	 * @param array		Associative array of replyto recipient (name, mail)
	 * @return boolean
	 */
	public static function mailto($from, $html, $subject, $body, $to, $attached = array(), $cc = array(), $bcc = array(), $replyto = array())
	{
		X4Core_core::auto_load('swiftmailer_library');
		
		// Create the Transport

		/*
		Options are:
		
		// SMTP server
		$transport = Swift_SmtpTransport::newInstance('smtp.example.org', 25)
		  ->setUsername('your username')
		  ->setPassword('your password')
		  ;
		
		// Sendmail
		$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
		
		// Mail
		$transport = Swift_MailTransport::newInstance();
		*/
		
		// as default uses Mail
		$transport = Swift_MailTransport::newInstance();
		
		// Create the Mailer using your created Transport
		$mailer = Swift_Mailer::newInstance($transport);

		$check = true;
		
		$body = stripslashes($body);

		// Force conversion to utf-8
		$body = iconv(mb_detect_encoding($body, mb_detect_order(), true), "UTF-8//translit", $body);
		
		// build mail obj
		try 
		{
			// create an empty mail
			$mail = Swift_Message::newInstance();
			
			// set charset
			$mail->setCharset('utf-8');
			
			// Set the From address with an associative array
			$mail->setFrom(array(MAIL => SERVICE));
			
			// Set the subject
			$mail->setSubject(self::sanitize($subject));
			
			// Set the body
			if ($html) 
			{	
				$mail->setBody($body, 'text/html');
			}
			else
			{
				$mail->setBody($body, 'text/plain');
			}
			
			// add attachments
			if (!empty($attached)) 
			{
				foreach($attached as $i)
				{
					$attachment = Swift_Attachment::fromPath($i['file']);
					
					// set filename if exists
					if (isset($i['filename']))
					{
						$attachment->setFilename($i['filename']);
					}
					
					$mail->attach($attachment);
				}
			}
			
			// add recipients
			foreach($to as $i) 
			{
				$mail->addTo(self::sanitize(strtolower($i['mail'])), self::sanitize($i['name']));
			}
			
			// CC recipients
			foreach($cc as $i) 
			{
				$mail->addCc(self::sanitize(strtolower($i['mail'])), self::sanitize($i['name']));
			}
			
			// BCC recipients
			foreach($bcc as $i) 
			{
				$mail->addBcc(self::sanitize(strtolower($i['mail'])), self::sanitize($i['name']));
			}
			
			if (!empty($replyto))
			{
				$mail->setReplyTo($replyto['mail'], $replyto['name']);
			}

			// if application isn't production STATE
			$check = (!DEVEL) 
				? $mailer->send($mail) 
				: true;
		}
		catch (Exception $e) 
		{
			if (DEBUG) 
			{
				echo $e->getMessage();
				die;
			}
			else
			{
				// todo log error message
			}
			return DEVEL;
		}
		return $check;
	}
	
	/**
	 * Sanitize string
	 *
	 * @access	private
	 * @param	string	String
	 * @return	string
	 */
	private static function sanitize($str)
	{
		return str_replace(array("\r", "\n", "%0a", "%0d"), '', stripslashes($str));
	}
}
