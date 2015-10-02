<?php
/*
	NOTE:
	Complete your configutation with data in files received from OpenInviter.com
	
	message_body and message_subject are USELESS
	you need to load 'x4openinviter' dictionary
*/
$openinviter_settings=array(
	"username"=>"your_username",
	"private_key"=>"your_private_key",
	"cookie_path"=>'/tmp',
	"message_body"=>"You are invited to XXX", // Useless in X3 CMS
	"message_subject"=>" is inviting you to XXX", // Useless in X3 CMS
	"transport"=>"curl", //Replace "curl" with "wget" if you would like to use wget instead
	"local_debug"=>"on_error", //Available options: on_error => log only requests containing errors; always => log all requests; false => don`t log anything
	"remote_debug"=>FALSE, //When set to TRUE OpenInviter sends debug information to our servers. Set it to FALSE to disable this feature
	"hosted"=>FALSE, //When set to TRUE OpenInviter uses the OpenInviter Hosted Solution servers to import the contacts.
	"proxies"=>array(), //If you want to use a proxy in OpenInviter by adding another key to the array. Example: "proxy_1"=>array("host"=>"1.2.3.4","port"=>"8080","user"=>"user","password"=>"pass")
					   //You can add as many proxies as you want and OpenInviter will randomly choose which one to use on each import.
	"stats"=>TRUE,
	"plugins_cache_time"=>1800,
	"plugins_cache_file"=>"oi_plugins.php",
	"update_files"=>true,
	"stats_user"=>"", //Required to access the stats
	"stats_password"=>"" //Required to access the stats
);
?>