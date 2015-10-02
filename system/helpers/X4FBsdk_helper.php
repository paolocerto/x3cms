<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */
 
/**
 * Helper for Facebook
 * 
 * @package X4WEBAPP
 */
class X4FBsdk_helper 
{
	/**
	 * Get Facebook user data 
	 *
	 * @static
	 * @return mixed
	 */
	public static function user()
	{
		X4Core_core::auto_load('fbsdk_library');
		
		$facebook = new Facebook(array(
			'appId'  => FACEBOOK_APP_ID,
			'secret' => FACEBOOK_SECRET
		));
		
		$facebook->setAccessToken(null);
		$user = $facebook->getUser();
		//$access_token = $facebook->getAccessToken();
		
		if ($user) 
		{
			// check if current user is authenticated
			try 
			{
				return $facebook->api('/me');
			}
			catch(Exception $e)
			{
				return false;
			}
			return false;
		}
	}
	
	/**
	 * Post to wall user on Facebook 
	 *
	 * @static
	 * @param string	$msg Message to post
	 * @return mixed
	 */
	public static function post($msg)
	{
		X4Core_core::auto_load('fbsdk_library');
		
		$facebook = new Facebook(array(
			'appId'  => FACEBOOK_APP_ID,
			'secret' => FACEBOOK_SECRET
		));
		
		$facebook->setAccessToken(null);
		$user = $facebook->getUser();
		$res = false;
		if ($user) 
		{
			// set post
			$post =  array(
				'link' => 'www.sistemalitalia.it',	
				'message' => $msg
			);
			try
			{
				$res =  $facebook->api('/me/feed', 'POST', $post);
			}
			catch(Exception $e)
			{
				if (DEBUG)
					print_r($e);
			}
		}
		return $res;
	}
	
}
