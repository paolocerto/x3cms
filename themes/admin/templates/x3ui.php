<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// X3CMS - admin theme - base view
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
<!DOCTYPE html>
<html lang="<?php echo X4Route_core::$lang ?>">
<head>
<meta charset="utf-8">

<title><?php echo $title.$this->site->site->title ?></title>
<meta name="description" content="<?php echo $description ?>">
<meta name="keywords" content="<?php echo $this->site->site->keywords.','.$xkeys ?>">
<meta name="robots" content="all">

<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
<link rel="shortcut icon" href="<?php echo THEME_URL ?>favicon.ico" type="images/x-icon" />
<link rel="stylesheet" href="<?php echo THEME_URL ?>css/font-awesome.css">
<link rel="stylesheet" href="<?php echo THEME_URL ?>css/normalize.css">
<?php
echo (!DEVEL && file_exists(PATH.'themes/'.$this->site->area->theme.'/css/'.$css.'.min.css'))
	? '<link rel="stylesheet" href="'.THEME_URL.'css/'.$css.'.min.css">'
	: '<link rel="stylesheet" href="'.THEME_URL.'css/'.$css.'.css">'
?>

<?php
if (RTL) 
{
	echo '<link title="normal" rel="stylesheet" href="'.THEME_URL.'/css/rtl.css" media="all" />';
}
?>
<script src="<?php echo ROOT ?>files/js/modernizr-2.8.2.min.js"></script>
</head>
<body>
<div class="band bdarkgray clearfix">
	<div id="logo" class="one-fifth md-one-fourth sm-one-third xs-one-whole">
		<a class="no_link" href="<?php echo BASE_URL ?>" title="<?php echo _HOME_PAGE ?>">
			<img src="<?php echo THEME_URL ?>img/x3cms_black.png" alt="X3 CMS logo" />
		</a>
	</div>
	<div id="menu" class="three-fifth md-one-half sm-two-third xs-one-whole"></div>
	<div id="public" class="one-fifth md-one-fourth sm-one-third sm-hidden aright xsmall pad-right half-pad-top lightgray">
		<?php echo _PUBLIC_SIDE ?>: <a href="<?php echo $this->site->site->domain ?>" title="<?php echo _PUBLIC_SIDE ?>"><?php echo $this->site->site->domain ?></a><br />
		<?php echo _LOGGED_AS ?>: <b><?php echo $_SESSION['username'] ?></b>
	</div>
</div>
<div class="band bwhite dtable">
	<aside id="sidebar">
		<i id="spinner" class="fa fa-circle-o-notch"></i>
		<div>
			<a class="no_link" href="<?php echo BASE_URL ?>" title="<?php echo _HOME_PAGE ?>"><i class="fa fa-home fa-lg"></i></a>
<?php
// sidebar menu
$sbm = array(
	'sites' => '<a href="'.BASE_URL.'sites/show/1" title="'._SETTINGS.'"><i class="fa fa-cog fa-lg"></i></a>'
);

if (!empty($menus['sidebar']))
{
	foreach($menus['sidebar'] as $i)
	{
		if (isset($sbm[$i->url]))
		{
			echo $sbm[$i->url];
		}
	}
}

// user menu
$um = array(
	'widgets' 	=> '<a href="'.BASE_URL.'widgets" title="'._WIDGETS.'"><i class="fa fa-paperclip fa-lg"></i></a>',
	'help' 		=> '<a href="'.BASE_URL.'help" title="'._HELP_ON_LINE.'"><i class="fa fa-question fa-lg"></i></a>',
	'profile' 	=> '<a href="'.BASE_URL.'profile" title="'.ucfirst($_SESSION['username']).' '._PROFILE.'"><i class="fa fa-user fa-lg"></i></a>',
	'info' 		=> '<a href="'.BASE_URL.'info" title="'._ABOUT.'"><i class="fa fa-info fa-lg"></i></a>'
);	
			
if (!empty($menus['admin_user']))
{
	foreach($menus['admin_user'] as $i)
	{
		if (isset($um[$i->url]))
		{
			echo $um[$i->url];
		}
	}
}
?>
			<a class="no_link" href="<?php echo BASE_URL ?>login/logout" title="<?php echo _LOGOUT ?>"><i class="fa fa-power-off fa-lg"></i></a>
			...
<?php
// languages
if ($langs)
{
	foreach($langs as $i)
	{
		echo '<a class="no_link small" href="'.ROOT.$i->code.'/admin/" title="'.$i->language.'">'.$i->code.'</a>';
	}
}
?>
		</div>
	</aside>
	
	<aside id="workarea">
		<div id="toolbar" class="band small">
			<div id="page-title" class="two-fifth pad-left xs-hidden hide-x">Home</div>
			<div id="filters" class="three-fifth xs-one-whole aright pad-right"></div>
		</div>
		<div id="topic" class="double-pad-left double-pad-right" role="main"></div>
	</aside>
</div>
<footer id="foot" class="xsmall lightgray double-padded acenter">
	<p><a href="http://www.x3cms.net" title="X3 CMS">X3 CMS</a> powered by <a href="http://www.cblu.net" title="Cblu.net - Web solutions">Cblu.net</a></p>
<?php			
echo X4Bench_core::info('<p class="xs-hidden xsmall">X4WebApp v. {x4wa_version} &copy; Cblu.net - execution time: {execution_time} - memory usage: {memory_usage} - queries: {queries} - included files: {included_files}</p>');
?>

</footer>

<div id="modal"></div>

<script src="<?php echo ROOT ?>files/js/mootools/MooTools-Core-1.6.0-compat-compressed.js" charset="UTF-8"></script>
<script src="<?php echo ROOT ?>files/js/mootools/MooTools-More-1.6.0-compat-compressed.js" charset="UTF-8"></script>
<script src="<?php echo ROOT ?>files/js/mootools/MooDropMenu.js" charset="UTF-8"></script>
<script src="<?php echo ROOT ?>files/js/mootools/simple-modal.js" charset="UTF-8"></script>
<script>
var domain = "<?php echo $this->site->site->domain ?>",
	root = "<?php echo BASE_URL ?>",
	theme = "",
	warning = "<?php echo _WARNING ?>",
	start_page = "<?php echo $start_page ?>",
	start_title = "<?php echo $start_title ?>";
</script>
<script src="<?php echo ROOT ?>files/js/mootools/datepicker.js" charset="UTF-8"></script>
<script src="<?php echo ROOT ?>files/js/mootools/Lasso.js"></script>
<script src="<?php echo ROOT ?>files/js/mootools/Lasso.Crop.js"></script>
<script src="<?php echo ROOT ?>files/js/swfobject.js"></script>
<?php
echo (!DEVEL && file_exists(PATH.'themes/'.$this->site->area->theme.'/js/x3ui.min.js'))
	? '<script src="'.THEME_URL.'js/x3ui.min.js"></script>'
	: '<script src="'.THEME_URL.'js/x3ui.js"></script>'
?>
<script src="<?php echo ROOT ?>files/js/tinymce/tinymce.min.js"></script>
<script src="<?php echo ROOT ?>files/js/mootools/color-picker.js"></script>
<script src="<?php echo ROOT ?>files/js/mootools/Request.File.js" ></script>
<script src="<?php echo ROOT ?>files/js/mootools/Form.MultipleFileInput.js" ></script>
<script src="<?php echo ROOT ?>files/js/mootools/Form.Upload.js" ></script>
<script src="<?php echo ROOT ?>files/js/mootools/Autocompleter.js"></script>
<script src="<?php echo ROOT ?>files/js/mootools/Autocompleter.Request.js"></script>
<script src="<?php echo ROOT ?>files/js/mootools/Observer.js"></script>
<script src="<?php echo ROOT ?>files/js/mootools/Scrollable.js"></script>
</body>
</html>