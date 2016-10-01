<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// X3CMS - admin theme - login view
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge,chrome=1');


$title = $xkeys = $css = '';
$description = stripslashes($this->site->site->description);
if (isset($page)) {
	$title = stripslashes($page->title).' | ';
	$description = (empty($page->description)) ? $description : stripslashes($page->description);
	$xkeys = stripslashes($page->xkeys);
	$css = $page->css;
} 

?>
<!doctype html>
<html lang="<?php echo X4Route_core::$lang ?>">
<head>
<meta charset="utf-8">

<title><?php echo $title.$this->site->site->title ?></title>
<meta name="description" content="<?php echo $description ?>">
<meta name="keywords" content="<?php echo $this->site->site->keywords.','.$xkeys ?>">
<meta name="author" content="cblu.net">
<meta name="robots" content="all">

<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="shortcut icon" href="<?php echo THEME_URL ?>favicon.ico" type="images/x-icon">
<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css">
<link rel="stylesheet" href="<?php echo THEME_URL ?>css/normalize.css">
<?php
echo (!DEVEL && file_exists(PATH.'themes/'.$this->site->area->theme.'/css/'.$css.'.min.css'))
	? '<link rel="stylesheet" href="'.THEME_URL.'css/'.$css.'.min.css">'
	: '<link rel="stylesheet" href="'.THEME_URL.'css/'.$css.'.css">'
?>
<?php
if (RTL) 
{
	echo '<link title="normal" rel="stylesheet" type="text/css" href="'.THEME_URL.'/css/rtl.css" media="all" />';
}
?>

<script src="<?php echo ROOT ?>files/js/mootools/MooTools-Core-1.6.0-compat-compressed.js" ></script>
<script src="<?php echo ROOT ?>files/js/mootools/MooTools-More-1.6.0-compat-compressed.js" ></script>
<script>var root = "<?php echo $this->site->site->domain ?>";</script>
<!-- datepicker -->
<script src="<?php echo ROOT ?>files/js/mootools/datepicker.js"></script>
<script src="<?php echo THEME_URL ?>js/domready.js" ></script>
</head>

<body>

	<div class="band bwhite clearfix">
		<div id="login_logo" class="one-fifth md-one-third sx-one-whole push-two-fifth md-push-one-third sx-push-one align-center"><a href="<?php echo BASE_URL ?>" title="Home">
			<img src="<?php echo THEME_URL ?>img/x3cms_white.png" alt="X3CMS" width="200" /></a>
		</div>
	</div>
	<div class="band padded clearfix">
		<div class="one-fifth md-one-third xs-one-whole push-two-fifth md-push-one-third xs-push-none lightgray">
			<div id="logger-box" >
<?php
// check user agent
$browser = $_SERVER['HTTP_USER_AGENT'];
if (strstr($browser, 'MSIE') == '' || strstr($browser, 'MSIE 10.0;') != '')
{
	// msg
	if (isset($_SESSION['msg']) && !empty($_SESSION['msg']))
	{
		echo '<div id="msg"><div><p>'.$_SESSION['msg'].'</p></div></div>';
		unset($_SESSION['msg']);
	}
	
	// content
	if (isset($content)) 
	{
		echo $content;
	}
	elseif (!empty($sections[1])) 
	{
		// section 1
		foreach($sections[1] as $i) 
		{
			echo '<article class="block">'.X4Utils_helper::reset_url(stripslashes($i->content));
			if (!empty($i->module)) 
			{
				echo stripslashes(X4Utils_helper::module($this->site, $page, $args, $i->module, $i->param));
			}
			echo '</article>';
		}
	}
	else 
	{
		echo '<div class="block"><h1>'._WARNING.'</h1>',
				'<p>'._GLOBAL_PAGE_NOT_FOUND.'</p>',
				'</div>';
	}
}
else
{
	echo '<div id="msg"><div><p>'._UNSUPPORTED_BROWSER.'</p></div></div>';
	
	echo '<div class="block"><h4>'._SUPPORTED_BROWSER.'</h4>',
		'<a href="http://www.google.com/chrome" title="Google Chrome"><img src="'.THEME_URL.'img/chrome.png" alt="Google Chrome" /></a>',
		'<a href="http://www.mozilla.org/firefox/new/" title="Mozilla Firefox"><img src="'.THEME_URL.'img/firefox.png" alt="Mozilla Firefox" /></a>',
		'<a href="http://www.opera.com/download/" title="Opera"><img src="'.THEME_URL.'img/opera.png" alt="Opera" /></a>';
		
	// Windows users and Mac OSX users
	if (strstr($browser, 'Windows') != '')
	{
		echo '<a href="http://support.apple.com/it_IT/downloads/#internet" title="Safari"><img src="'.THEME_URL.'img/safari.png" alt="Safari" /></a>',
			 '<a href="http://windows.microsoft.com/it-IT/internet-explorer/products/ie/home" title="Internet Explorer"><img src="'.THEME_URL.'img/ie.png" alt="Internet Explorer" /></a>';
	}
	else if (strstr($browser, 'Macintosh') != '' || strstr($browser, 'Mac_PowerPC') != '')
	{
		echo '<a href="http://support.apple.com/it_IT/downloads/#internet" title="Safari"><img src="'.THEME_URL.'img/safari.png" alt="Safari" /></a>';
	}
	else
	{
		echo '<img src="'.THEME_URL.'img/safari_gray.png" alt="Safari" />',
			 '<img src="'.THEME_URL.'img/ie_gray.png" alt="Internet Explorer" />';
	}	
	echo '</div>';
}
?>
			</div>
		</div>
	</div>
	<div class="band padded clearfix">
		<footer class="one-half sm-one-whole push-one-fourth sm-push-none small gray double-padded">
			<p class="small acenter"><a href="http://www.x3cms.net" title="X3 your next Content Management System">X3 CMS</a> &copy; <a href="http://www.cblu.net" title="Cblu.net - Software &amp; Web design">Cblu.net</a></p>
<?php
echo '<p class="xs-hidden xsmall">'.X4Bench_core::info('X4WebApp v. {x4wa_version} &copy; Cblu.net - execution time: {execution_time} - memory usage: {memory_usage} - queries: {queries} - included files: {included_files}');
echo '<br />'.$browser.'</p>';
?>
		</div>
	</div>

</body>
</html>
