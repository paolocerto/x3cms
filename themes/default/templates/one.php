<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// X3CMS - default theme - base view
header('X-UA-Compatible: IE=edge,chrome=1');

$title = $xkeys = $css = '';
$description = stripslashes($this->site->site->description);
if (isset($page)) 
{
	$title = stripslashes($page->title).' | ';
	$description = (empty($page->description)) 
		? $description 
		: stripslashes($page->description);
	$xkeys = stripslashes($page->xkeys);
	$css = $page->css;
} 

?>
<!DOCTYPE html>
<html lang="<?php echo X4Route_core::$lang ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">

<title><?php echo $title.$this->site->site->title ?></title>
<meta name="description" content="<?php echo $description ?>">
<meta name="keywords" content="<?php echo $this->site->site->keywords.','.$xkeys ?>">

<link rel="shortcut icon" href="<?php echo THEME_URL ?>favicon.ico" type="images/x-icon">
<link rel="stylesheet" href="<?php echo THEME_URL ?>css/bootstrap.min.css">
<?php
echo (!DEVEL && file_exists(PATH.'themes/'.$this->site->area->theme.'/css/'.$css.'.min.css'))
	? '<link rel="stylesheet" href="'.THEME_URL.'css/'.$css.'.min.css">'
	: '<link rel="stylesheet" href="'.THEME_URL.'css/'.$css.'.css">';

// if you have to display right-to-left languages
if (RTL) 
{
	echo '<link title="normal" rel="stylesheet" href="'.THEME_URL.'css/rtl.css" media="all">';
}
?>

<link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml">

<script src="<?php echo ROOT ?>files/js/jquery/jquery.min.js"></script>
<script src="<?php echo ROOT ?>files/js/jquery/jquery.cycle2.js"></script>
<script src="<?php echo THEME_URL ?>js/jquery-ui.min.js"></script>
<script src="<?php echo THEME_URL ?>js/bootstrap.min.js"></script>
<script>
var root = "<?php echo $this->site->site->domain ?>";
</script>
<script src="<?php echo THEME_URL ?>js/jqready.js"></script>
</head>

<body>
<div class="container-fluid bwhite">
	<div id="main" class="container">
		<div class="row pad-bottom">
<?php
// Language switcher
echo X4Utils_helper::module($this->site, $page, array(), 'x3flags');
?>
			<div id="logo">
				<a href="<?php echo BASE_URL ?>" title="<?php echo _X3CMS_SLOGAN ?>"><img src="<?php echo THEME_URL ?>/img/x3cms.png" /></a>
			</div>
		</div>
<?php
// top menu
if (!empty($menus['menu_top']))
{	
	// <div class="container-fluid">
	// </div>
	echo '<div class="row">
            <nav id="navbar" class="navbar navbar-default">
                <div class="navbar-header hidden-md">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu_top">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                </div>
                <div class="collapse navbar-collapse" id="menu_top">
                    '.stripslashes(X4Utils_helper::build_menu($page->ordinal, $menus['menu_top'], 1, 'ALL', 1, true, 'class="nav navbar-nav"', true)).'
                </div>
            </nav>
        </div>';
}

// the banner only in home page
if ($page->url == 'home')
{
?>
	<div data-cycle-fx="scrollHorz" data-cycle-pause-on-hover="true" class="cycle-slideshow row">
		<img src="http://placehold.it/1200x300/546E7A/ffffff/&amp;text=X3+CMS" alt="">
		<img src="http://placehold.it/1200x300/ff7700/ffffff/&amp;text=...your+next+Content+Management+System..." alt="">
		<img src="http://placehold.it/1200x300/ffffff/ff7700/&amp;text=...or+a+base+for+your+web+application..." alt="">
		<img src="http://placehold.it/1200x300/C0CA33/ffffff/&amp;text=...because+Simple+is+better+:)" alt="">
	</div>
<?php 
}
?>

<div id="topic">

<?php
// Empty pages
if (X4Utils_helper::empty_sections($sections))
{
	// content
	if (isset($content)) 
	{
		echo $content;
	}
	else 
	{
		echo '
            <div class="row clearfix">
                <div class="block">
                    <h1>'._WARNING.'</h1>
                    <p>'._GLOBAL_PAGE_NOT_FOUND.'</p>
                </div>
            </div>';
	}
}
else
{
	// section 1
	if (!empty($sections[1])) 
	{
		echo '<div class="row clearfix">';
		
		foreach($sections[1] as $i) 
		{
			if (!empty($i->content))
			{
				echo '<div class="block clearfix">'.X4Utils_helper::inline_edit($i, 1);
				// options
				echo X4Utils_helper::get_block_options($i);
				echo X4Utils_helper::reset_url(stripslashes($i->content));
				echo '</div>';
			}
			if (!empty($i->module)) 
			{
				echo stripslashes(X4Utils_helper::module($this->site, $page, $args, $i->module, $i->param));
			}
		}
		
		echo '</div>';
	}
}
?>

</div>

<?php
// section 2
if (!empty($sections[2])) 
{
	echo '<div id="x3row">
			<div class="row">';
	$c = 0;
	$x = 4; // max number of columns
	$n = sizeof($sections[2]);
	
	$widths = array('', '12', '6', '4', '3');
	
	foreach($sections[2] as $i) 
	{
		if (!empty($i->content) || !empty($i->module)) 
		{
			$class = ($n >= $x) 
				? $widths[$x] 
				: $widths[$n];
				
			if ($c > 0 && $c%$x == 0) 
			{
				$n = $n - $x;
				$class = ($n >= $x) 
					? $widths[$x] 
					: $widths[$n];
				echo '</div><div class="row">';
			}
			echo '<div class="col-xs-12 col-sm-6 col-md-'.$class.'">'.X4Utils_helper::inline_edit($i, 2).X4Utils_helper::get_block_options($i);
			echo X4Utils_helper::reset_url(stripslashes($i->content));
			// module
			if (!empty($i->module))
			{
				echo stripslashes(X4Utils_helper::module($this->site, $page, $args, $i->module, $i->param));
			}
			echo '</div>';
		}
		$c++;
	}
	
	echo '</div>
		</div>';
}
?>
	</div>
</div>

<div id="footer">
	<p class="text-center small">
        <a href="http://www.x3cms.net" title="X3 your next Content Management System">X3 CMS</a> powered by <a href="http://www.cblu.net" title="Cblu.net - Software &amp; Web design">Cblu.net</a><br />
        <a href="http://www.x3cms.net/en/x3_cms_legal_notices" title="X3 CMS Legal Notices">X3 CMS Legal Notices</a>
    </p>
<?php
if (DEBUG)
{
	// display some info
	echo X4Bench_core::info('<p class="text-center small">X4WebApp v. {x4wa_version} - execution time: {execution_time} - memory usage: {memory_usage} - queries: {queries} - included files: {included_files}</p>');
}
?>
</div>

<?php
if (!DEBUG)
{
?>
<script>
/*
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-XXXXXXX']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
*/
</script>
<?php 
}
?>

</body>
</html>
