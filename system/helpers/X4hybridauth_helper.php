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
 * Helper for hybridauth
 * 
 * @package X4WEBAPP
 */
class X4hybridauth_helper 
{
	/**
	 * auth
	 *
	 * @static
	 * @param string	$provider Provider name
	 * @param string	$status Status text
	 * @return boolean
	 */
	public static function auth($provider, $status)
	{
		$chk = 0;
		// load library
		X4Core_core::auto_load('hybridauth_library');
		
		try{
			// hybridauth EP
			$hybridauth = new Hybrid_Auth(SPATH.'libraries/hybridauth/config.php');
		
			// auth
			$adapter = $hybridauth->authenticate($provider);
			
			if($hybridauth->isConnectedWith($provider))
			{ 
				// status
				$adapter->setUserStatus($status);
				/*
				// get the user profile 
				$user_profile = $adapter->getUserProfile();
				echo "<pre>" . print_r( $user_profile, true ) . "</pre><br />";
				*/
				// logout
				$adapter->logout();
				$chk = 1;
			}
		}
		catch( Exception $e )
		{
			if (DEBUG)
			{
				// Display the recived error, 
				// to know more please refer to Exceptions handling section on the userguide
				switch( $e->getCode() ){ 
					case 0 : echo "Unspecified error."; break;
					case 1 : echo "Hybridauth configuration error."; break;
					case 2 : echo "Provider not properly configured."; break;
					case 3 : echo "Unknown or disabled provider."; break;
					case 4 : echo "Missing provider application credentials."; break;
					case 5 : echo "Authentication failed. " 
							  . "The user has canceled the authentication or the provider refused the connection."; 
						   break;
					case 6 : echo "User profile request failed. Most likely the user is not connected "
							  . "to the provider and he should to authenticate again."; 
						   $twitter->logout();
						   break;
					case 7 : echo "User not connected to the provider."; 
						   $twitter->logout();
						   break;
					case 8 : echo "Provider does not support this feature."; break;
				} 
		
				// well, basically your should not display this to the end user, just give him a hint and move on..
				echo "<br /><br /><b>Original error message:</b> " . $e->getMessage();
		
				echo "<hr /><h3>Trace</h3> <pre>" . $e->getTraceAsString() . "</pre>";
			}
		}
		return $chk;
	}
	
	/**
	 * end point
	 *
	 * @static
	 * @return void
	 */
	public static function end_point()
	{
		// load library
		X4Core_core::auto_load('hybridauth_end_library');
	}
	
}
