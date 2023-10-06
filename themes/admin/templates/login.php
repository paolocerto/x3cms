<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
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
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />
        <title><?php echo $title.$this->site->site->title ?></title>
        <meta name="description" content="<?php echo $description ?>">
        <meta name="keywords" content="<?php echo $this->site->site->keywords.','.$xkeys ?>">
        <meta name="author" content="cblu.net">
        <meta name="robots" content="all">

        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo ROOT ?>apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo ROOT ?>favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo ROOT ?>favicon-16x16.png">

        <link rel="stylesheet" href="<?php echo THEME_URL ?>css/fontawesome-all.min.css">

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
        <script src="https://kit.fontawesome.com/2e7ce67797.js" crossorigin="anonymous"></script>
        <script src="https://cdn.tailwindcss.com"></script>

        <script defer src="<?php echo THEME_URL ?>js/alpine.min.js"></script>
        <script src="<?php echo THEME_URL ?>js/x3ui.js"></script>

        <script>var root = "<?php echo $this->site->site->domain ?>";</script>
</head>

<body>
    <header id="head" class="bg-white w-full px-6 py-4">
        <img src="<?php echo THEME_URL ?>img/x3cms_extended_dark.png" alt="X3 CMS" class="w-60 m-auto mb-4" />
    </header>
    <div id="page" class="py-4">
        <div class="w-full md:w-96 px-4 md:m-auto text-gray-300">

<?php
// check user agent
$browser = $_SERVER['HTTP_USER_AGENT'];
if (strstr($browser, 'MSIE') == '' || strstr($browser, 'MSIE 10.0;') != '')
{
	// msg
	if (isset($_SESSION['msg']) && !empty($_SESSION['msg']))
	{
		echo '<div id="msg"><p class="failed px-4 py-4 rounded">'.$_SESSION['msg'].'</p></div>';
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
		foreach ($sections[1] as $i)
		{
			echo '<article class="block">'.X4Theme_helper::reset_url(stripslashes($i->content));
			if (!empty($i->module))
			{
				echo stripslashes(X4Theme_helper::module($this->site, $page, $args, $i->module, $i->param));
			}
			echo '</article>';
		}
	}
	else
	{
		echo '<div class="failed px-4 py-4 rounded">
                <h1>'._WARNING.'</h1>
				<p>'._GLOBAL_PAGE_NOT_FOUND.'</p>
			</div>';
	}
}
else
{
	echo '<div id="msg"><p class="failed px-4 py-4 rounded">'._UNSUPPORTED_BROWSER.'</p></div>';

	echo '<div class="block"><h4>'._SUPPORTED_BROWSER.'</h4>',
		'<a href="https://www.google.com/chrome" title="Google Chrome"><img src="'.THEME_URL.'img/chrome.png" alt="Google Chrome" /></a>',
		'<a href="https://www.mozilla.org/firefox/new/" title="Mozilla Firefox"><img src="'.THEME_URL.'img/firefox.png" alt="Mozilla Firefox" /></a>',
		'<a href="https://www.opera.com/download/" title="Opera"><img src="'.THEME_URL.'img/opera.png" alt="Opera" /></a>';

	// Windows users and Mac OSX users
	if (strstr($browser, 'Windows') != '')
	{
		echo '<a href="https://support.apple.com/it_IT/downloads/#internet" title="Safari"><img src="'.THEME_URL.'img/safari.png" alt="Safari" /></a>',
			 '<a href="https://windows.microsoft.com/it-IT/internet-explorer/products/ie/home" title="Internet Explorer"><img src="'.THEME_URL.'img/ie.png" alt="Internet Explorer" /></a>';
	}
	else if (strstr($browser, 'Macintosh') != '' || strstr($browser, 'Mac_PowerPC') != '')
	{
		echo '<a href="https://support.apple.com/it_IT/downloads/#internet" title="Safari"><img src="'.THEME_URL.'img/safari.png" alt="Safari" /></a>';
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
	<footer class="text-white text-center text-xs py-6">
	    <a href="https://www.x3cms.net" title="X3 your next Content Management System">X3 CMS</a> &copy; <a href="https://www.cblu.net" title="CBlu.net - Freelance PHP Developer">CBlu.net</a>
    </footer>

</body>
</html>
