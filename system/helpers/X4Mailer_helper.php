<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
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
	 */
	public static function mailto(
        mixed $from,            // sender email or array like to
        bool $html,
        string $subject,
        string $body,
        array $recipients,      // 'to' => ['name' => xxx, 'mail' => yyy], 'cc' => [], 'bcc' => [], 'replyto' => []
        array $attached = [],   // ['file' => xxx, 'filename' => optional]
    )
	{
        if (DEVEL)
        {
            return true;    // if application isn't production STATE
        }

		require_once PATH . 'vendor/autoload.php';

        $transport = new Swift_SendmailTransport('/usr/sbin/sendmail -bs');

		// Create the Mailer using your created Transport
		$mailer = new Swift_Mailer($transport);
		$body = stripslashes($body);
		// Force conversion to utf-8
		$body = iconv(mb_detect_encoding($body, mb_detect_order(), true), "UTF-8//translit", $body);

		// build mail obj
		try
		{
			// create an empty mail
			$mail = new Swift_Message();

			// set charset
			$mail->setCharset('utf-8');

			// Set the From address with an associative array
			if (is_array($from))
			{
				$mail->setFrom(array($from['mail'] => $from['name']));
			}
			else
			{
				$mail->setFrom(array($from => SERVICE));
			}

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
            self::attachments($mail, $attached);

            // add recipients
            self::recipients($mail, $recipients);

			$check = $mailer->send($mail);
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
			    $mod = new Log_model();
			    $mod->logger(1, 1, 'maildebug', 'error: +++'.$e->getMessage().'+++');
			}
			return DEVEL;
		}
		return $check;
	}

    /**
     * Add attachments
     */
    private static function attachments(Swift_Message &$mail, array $attached)
    {
        if (!empty($attached))
        {
            foreach ($attached as $i)
            {
                $data = file_get_contents($i['file']);
                // set filename if exists
                if (isset($i['filename']))
                {
                    // NOTE we attach only PDF files
                    $attachment = new Swift_Attachment($data, $i['filename'], 'application/pdf');
                }
                else
                {
                    $attachment = (new Swift_Attachment())->setBody($data);
                }

                $mail->attach($attachment);
            }
        }
    }

    /**
     * Add recipients
     */
    private static function recipients(Swift_Message &$mail, array $recipients)
    {
        foreach ($recipients['to'] as $i)
        {
            $mail->addTo(self::sanitize(strtolower($i['mail'])), self::sanitize($i['name']));
        }

        if (isset($recipients['cc']))
        {
            foreach ($recipients['cc'] as $i)
            {
                $mail->addCc(self::sanitize(strtolower($i['mail'])), self::sanitize($i['name']));
            }
        }

        if (isset($recipients['bcc']))
        {
            foreach ($recipients['bcc'] as $i)
            {
                $mail->addBcc(self::sanitize(strtolower($i['mail'])), self::sanitize($i['name']));
            }
        }

        if (isset($recipients['replyto']) && !empty($recipients['replyto']))
        {
            $mail->setReplyTo($recipients['replyto']['mail'], $recipients['replyto']['name']);
        }
    }

	/**
	 * Sanitize string
	 */
	private static function sanitize(string $str) : string
	{
		return str_replace(array("\r", "\n", "%0a", "%0d"), '', stripslashes($str));
	}
}
