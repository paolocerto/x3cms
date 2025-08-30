<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// check for redirect
X4Theme_helper::redirect();

// check cookie policy
$cookie = (isset($_COOKIE[COOKIE.'_policy']));

// X3CMS - default theme - base view
header('X-UA-Compatible: IE=edge');

$title = $css = $xkeys = '';
$robots = 'index,follow';
$this->site->data->keywords;
$description = stripslashes($this->site->data->description);
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

    if (!empty($page->robot))
    {
        $robots = $page->robot;
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

// links for buttons
$cart_page = $this->site->get_page_to($page->id_area, $page->lang, 'x3cart', 'cart');
?>
<!DOCTYPE html>
<html lang="<?php echo X4Route_core::$lang ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <title><?php echo $title.$this->site->data->title ?></title>
    <meta name="description" content="<?php echo $description ?>">
    <meta name="robots" content="<?php echo $robots ?>">

<?php
if (!empty($xkeys))
{
    echo '<meta name="keywords" content="'.$xkeys.'">';
}

if ($this->site->area->private == 0)
{
    echo '<link rel="sitemap" type="application/xml" title="Sitemap" href="/'.$this->site->area->lang.'/sitemap.xml">';
}
?>
    <link rel="canonical" href="<?php echo $this->site->data->domain ?>">
    <link rel="shortcut icon" href="<?php echo THEME_URL ?>favicon.png" type="images/x-png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap" rel="stylesheet">

    <!-- css -->
<?php

echo (file_exists($_SERVER['DOCUMENT_ROOT'].'/themes/pix/css/tailwind_wt.css'))
    ? '<link rel="stylesheet" href="'.ROOT.'themes/pix/css/tailwind_wt.css?v=1">'
    : '<script src="https://cdn.tailwindcss.com"></script>';
?>

    <link rel="stylesheet" href="<?php echo THEME_URL ?>css/fa.all.min.css" >
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" >

<?php
echo (!DEVEL && file_exists(PATH.'themes/'.$this->site->area->theme.'/css/'.$css.'.min.css'))
	? '<link rel="stylesheet" href="'.THEME_URL.'css/'.$css.'.min.css?v=0">'
	: '<link rel="stylesheet" href="'.THEME_URL.'css/'.$css.'.css?v=2">';

?>

    <!-- js -->
    <script defer src="<?php echo THEME_URL ?>js/dragula.min.js"></script>

    <!-- Alpine Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/gh/hankhank10/alpine-fetch@main/alpine-fetch.js"></script>

    <script defer src="<?php echo THEME_URL ?>js/alpine.min.js"></script>
    <script defer src="<?php echo ROOT ?>files/js/jscolor.js"></script>
    <script>
        const domain = "<?php echo $this->site->data->domain ?>",
            root = domain+"<?php echo BASE_URL ?>",
            theme = "<?php echo $this->site->area->theme ?>",
            area_id = <?php echo $page->id_area ?>,
            lang = "<?php echo $page->lang ?>",
            area = "<?php echo X4Route_core::$area ?>",
            completed = "<?php echo _MSG_OK ?>";
            warning = "<?php echo _WARNING ?>",
            error = "<?php echo _MSG_ERROR ?>";
    </script>

<?php
echo (!DEVEL && file_exists(PATH.'themes/'.$this->site->area->theme.'/js/script.min.js'))
	? '<script src="'.THEME_URL.'js/script.min.js?v=0"></script>'
	: '<script src="'.THEME_URL.'js/script.js?v=1"></script>';

?>

</head>

<body class="w-full h-screen" onload="targetBlank()" onresize="resizer()">

    <!-- modal -->
    <div
        id="modal"
        class="block fixed top-0 left-0 h-full w-full bg-gray-100 bg-opacity-60 z-50 overflow-y-auto overflow-x-hidden outline-none" style="backdrop-filter: blur(3px);"
        x-data="xmodal()"
        x-show="modal"
        x-on:close.window="modal = false"
        x-on:reload.window="reload($event.detail)"
        x-on:popup.window="popup($event.detail)"
        x-on:failed.window="failed($event.detail)"
        x-on:completed.window="completed($event.detail)"
        x-transition:enter.duration.600ms
        x-transition:leave.duration.200ms
        x-cloak
    >
        <div class="block fixed top-0 left-0 h-dvh w-full" x-html="html_modal"></div>
    </div>
    <!-- end modal -->

<?php
if (isset($_SESSION['checker']) && sizeof($_SESSION['checker']) > 0)
{
    echo stripslashes(X4Theme_helper::module($this->site, $page, [], 'x3users', 'checker'));
}
elseif(isset($_SESSION['pop']) && sizeof($_SESSION['pop']) > 0)
{
    echo stripslashes(X4Theme_helper::module($this->site, $page, [], 'x3events', 'setup'));
}
$cart_tot = stripslashes(X4Theme_helper::module($this->site, $page, ['number'], 'x3cart', 'tot'));

// menu items in top menu
$menu_items = '';
if (!empty($menus['menu_top']))
{
    list($menu, $dropdowns) = X4Theme_helper::build_tailwind_menu($page->ordinal, $menus['menu_top'], 0, '', '', '', '');
    $menu_items = X4Theme_helper::tailwind_navbar($menu, $dropdowns,
        'menu_item', // item style
        '', // active status
        ''  // not active status
    );
}
?>
    <nav
        id="navbar"
        class="fixed w-full z-40"
        x-data="navBar()"
        x-init="setUp('<?php echo $cart_tot ?>')"
        x-on:carting.window="updateCart($event.detail)"
        x-on:refreshing.window="checkMsg()"
    >
<?php
echo stripslashes(X4Theme_helper::module($this->site, $page, [], 'x3banners', 'banner_top'));
?>

        <!-- container -->
        <div class="container mx-auto px-2">

            <div class="flex flex-col md:items-center md:justify-between md:flex-row">

                <!-- logo -->
                <div class="flex flex-row justify-between items-center">

                    <a
                        id="logo"
                        href="<?php echo BASE_URL ?>"
                        class="flex flex-row gap-x-2 items-center pt-2 ml-2">
                        <img class="flex-none" src="<?php echo THEME_URL ?>img/chebeo_long.png" width="140"  alt="<?php echo _HOME_PAGE ?>" />
                    </a>

                    <div class="mobile flex flex-row-reverse justify-right gap-4 md:hidden pt-2 pr-2">

                        <button
                            type="button"
                            class="animate-pulse"
                            x-show="cart_amount!=''"
                            onclick="window.location.href='<?php echo BASE_URL ?>cart';"
                        >
                            <i class="fa-solid fa-cart-shopping"></i>
                        </button>

                        <button
                            type="button"
                            @click="open = !open"
                        >
                            <i class="fa-solid fa-bars fa-lg"></i>
                        </button>

<?php
// flags
echo $flags['mobile'];
?>
                    </div>

                </div>

                <!-- screen -->
                <div
                    :class="{'flex': open, 'hidden': !open}"
                    class="screen flex-col flex-grow hidden md:flex md:justify-end md:flex-row items-center md:space-x-3 mt-2"
                >

<?php
// menu items
echo $menu_items;

// flags
echo $flags['screen'];
?>

                <button
                    type="button"
                    class="animate-pulse"
                    x-show="cart_amount!=''"
                    onclick="window.location.href='<?php echo BASE_URL ?>cart';"
                    x-cloak
                >
                    <i class="fa-solid fa-cart-shopping fa-lg"></i>
                    <span class="btn_info" :class="{'pl-2': cart_amount != ''}" x-text="cart_amount"></span>
                </button>
            </div>

        </div>
    </div>

<?php
echo stripslashes(X4Theme_helper::module($this->site, $page, $args, 'x3shop', 'search'));
?>
</nav>
