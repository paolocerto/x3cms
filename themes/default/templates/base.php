<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

 // check for redirect
X4Theme_helper::redirect();

// X3CMS - default theme - base view
header('X-UA-Compatible: IE=edge');

$title = $css = '';
$robots = 'index,follow';
$xkeys = $this->site->site->keywords;
$description = stripslashes($this->site->site->description);
if (isset($page))
{
	$title = stripslashes($page->title).' | ';
	$description = (empty($page->description))
		? $description
		: stripslashes($page->description);
	if (!empty($page->xkeys))
	{
		$xkeys .= ','.stripslashes($page->xkeys);
	}
	$css = $page->css;
}

// handle flags
$flags = X4Theme_helper::module($this->site, $page, [], 'x4flags', 'active_flag');
if (!is_array($flags) || !isset($flags['mobile']) || !isset($flags['screen']))
{
    $flags = [
        'mobile' => '',
        'screen' => ''
    ];
}

?>
<!DOCTYPE html>
<html lang="<?php echo X4Route_core::$lang ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title><?php echo $title.$this->site->site->title ?></title>
    <meta name="description" content="<?php echo $description ?>">
    <meta name="robots" content="<?php echo $robots ?>">
<?php
if (!empty($xkeys))
{
    echo '<meta name="keywords" content="'.$xkeys.'">';
}
?>
    <link rel="canonical" href="<?php echo $this->site->site->domain ?>">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml">

    <link rel="shortcut icon" href="<?php echo THEME_URL ?>favicon.ico" type="images/x-icon">
    <link rel="stylesheet" href="<?php echo THEME_URL ?>css/fontawesome-all.min.css" >
<?php
echo (!DEVEL && file_exists(PATH.'themes/'.$this->site->area->theme.'/css/'.$css.'.min.css'))
	? '<link rel="stylesheet" href="'.THEME_URL.'css/'.$css.'.min.css?v=0">'
	: '<link rel="stylesheet" href="'.THEME_URL.'css/'.$css.'.css?v=1">';

// if you have to display right-to-left languages
if (RTL)
{
	echo '<link title="normal" rel="stylesheet" href="'.THEME_URL.'css/rtl.css" media="all">';
}
?>
    <script src="https://kit.fontawesome.com/2e7ce67797.js" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <script defer src="<?php echo THEME_URL ?>js/dragula.min.js"></script>
    <script defer src="<?php echo THEME_URL ?>js/alpine.min.js"></script>

    <script>
        var domain = "<?php echo $this->site->site->domain ?>",
            root = "<?php echo BASE_URL ?>",
            theme = "<?php echo $this->site->area->theme ?>",
            area_id = <?php echo $page->id_area ?>,
            lang = "<?php echo X4Route_core::$lang.'-'.strtoupper(X4Route_core::$lang) ?>",
            completed = "<?php echo _MSG_OK ?>";
            warning = "<?php echo _WARNING ?>",
            error = "<?php echo _MSG_ERROR ?>";
    </script>
<?php
echo (!DEVEL && file_exists(PATH.'themes/'.$this->site->area->theme.'/js/script.min.js'))
	? '<script src="'.THEME_URL.'js/script.min.js?v=0"></script>'
	: '<script src="'.THEME_URL.'js/script.js?v=0"></script>';
?>
</head>

<body class="w-full h-screen" onload="targetBlank()">

    <!-- modal -->
    <div

        class="fixed top-0 left-0 h-full w-full bg-gray-900 bg-opacity-60 z-50"
        x-show="modal"
        x-on:close.window="modal = false"
        x-data="xmodal()"
        x-cloak
    >
        <div x-html="html_modal"></div>
    </div>
    <!-- end modal -->

<?php
// menu items in top menu
$menu_items = '';
if (!empty($menus['menu_top']))
{
    list($menu, $dropdowns) = X4Theme_helper::build_tailwind_menu($page->ordinal, $menus['menu_top'], 0, '', '', '', '');
    $menu_items = X4Theme_helper::tailwind_navbar($menu, $dropdowns,
        'menu_item', // item style
        'active',    // active status
        ''           // not active status
    );
}
?>
    <!-- navbar -->
    <nav
        id="navbar"
        class="fixed bg-white text-neutral-700 z-10 w-full"
        x-data="{ isOpen: false, showModal: false }"
    >

<?php
// banner
echo stripslashes(X4Theme_helper::module($this->site, $page, [], 'x3banners', 'banner_top'));
?>

        <!-- container -->
        <div class="container mx-auto px-4">

            <div class="flex flex-col md:items-center md:justify-between md:flex-row">

                <!-- logo -->
                <div class="flex flex-row justify-between items-center">

                    <a
                        href="<?php echo BASE_URL ?>"
                        class="py-4 ml-2">
                        <img src="<?php echo THEME_URL ?>img/x3cms_extended_dark_small.png" alt="<?php echo _HOME_PAGE ?>" />
                    </a>

                    <div class="flex flex-row justify-right gap-4">
                        <button
                            class="md:hidden items-center focus:outline-none"
                            @click="isOpen = !isOpen">
                            <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

<?php
// flags
echo $flags['mobile'];
?>
                    </div>

                </div>

                <!-- buttons -->
                <div
                    :class="{'flex': isOpen, 'hidden': !isOpen}"
                    class="flex-col flex-grow py-2 md:py-4 hidden md:flex md:justify-end md:flex-row md:space-x-5"
                >
<?php
echo $menu_items;

// flags
echo $flags['screen'];
?>
                </div>

            </div>
        </div>
    </nav>
    <!-- end navbar -->

    <!-- topic -->
    <div id="topic">

<?php
// Empty pages
if (X4Theme_helper::empty_sections($sections))
{
    // NO SECTION ALERT
	echo '<div class="section">
		    <div class="text-center py-20">
				<h1>'._WARNING.'</h1>
				<p>'._GLOBAL_PAGE_NOT_FOUND.'</p>
			</div>
		</div>';
}
else
{
    // section index
    $index = 0;

    // extra css rules
    $css = array();

	// NOTE: put here special

	// build sections
    foreach ($sections as $k => $v)
    {
        $index++;
        // sectionize(&$css, $site, $page, $args, $index, $section, $grid = '')
        echo X4Theme_helper::sectionize($css, $this->site, $page, $args, $index, $v, 'tailwind');
    }

    if (!empty($css))
    {
        echo '
<style>
'.implode(NL, $css).'
</style>';
    }
}
?>
    </div>
    <!-- end topic -->

    <!-- footer -->
    <footer class="py-8">
        <p class="text-center text-sm">
            <a href="http://www.x3cms.net" title="X3 your next Content Management System">X3 CMS</a> powered by <a href="http://www.cblu.net" title="Cblu.net - Software &amp; Web design">Cblu.net</a><br />
            <a href="http://www.x3cms.net/en/x3_cms_legal_notices" title="X3 CMS Legal Notices">X3 CMS Legal Notices</a>
        </p>
<?php
if (DEBUG)
{
    // display some info
    echo X4Bench_core::info('<p class="text-center pt-4 text-xs">X4WebApp v. {x4wa_version} - execution time: {execution_time} - memory usage: {memory_usage} - queries: {queries} - included files: {included_files}</p>');
}
?>
    </footer>

<?php
if (!DEBUG)
{
    // put here extra scripts
}
?>

</body>
</html>
