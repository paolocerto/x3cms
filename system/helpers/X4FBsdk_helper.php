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
		
		$fb = new Facebook\Facebook(array(
		    'app_id' => FACEBOOK_APP_ID,
		    'app_secret' => FACEBOOK_SECRET,
		    'default_graph_version' => 'v2.2',
		));
		
        // already logged with JS
        $helper = $fb->getJavaScriptHelper();
        
        try 
        {
            // get the access token from a Cookie
            $accessToken = $helper->getAccessToken();
            //$_SESSION['fb_access_token'] = (string) $accessToken;
            
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get('/me?fields=id,first_name,last_name,email,location', $accessToken);
            
            $user = $response->getGraphUser();
        } 
        catch(Exception $e) 
        {
            // user not found
            //echo 'User not found';
            //die;
            return false;
        }
        
        return $user;
	}
	
	/**
	 * Post to wall user on Facebook 
	 *
	 * @static
	 * @param string	$msg Message to post
	 * @return mixed
	 * /
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
				'link' => 'DOMAIN NAME',	
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
	*/
}
