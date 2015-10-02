<?php defined('ROOT') or die('No direct script access.');
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

return 
	array(
		"base_url" => "http://www.your_site_url/endpoint_url", 

		"providers" => array ( 
			"Facebook" => array ( 
				"enabled" => true,
				"keys"    => array ( "id" => "fbid", "secret" => "xxx" ), 
				"scope"	  => "email, user_about_me, user_birthday, user_hometown, publish_actions",
				"display" => "popup"
			),

			"Twitter" => array ( 
				"enabled" => true,
				"keys"    => array ( "key" => "xxx", "secret" => "xxx" ) 
			)
			/*,
			"LinkedIn" => array ( 
				"enabled" => true,
				"keys"    => array ( "key" => "", "secret" => "" ) 
			)
			*/
		),

		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode" => false,

		"debug_file" => "",
	);
