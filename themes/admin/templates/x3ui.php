<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// X3CMS - admin theme - base view
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge');

$title = $xkeys = $css = '';
$description = stripslashes($this->site->data->description);
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <title><?php echo $title.$this->site->data->title ?></title>
    <meta name="description" content="<?php echo $description ?>">
    <meta name="keywords" content="<?php echo $this->site->data->keywords.','.$xkeys ?>">
    <meta name="robots" content="all">

    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo ROOT ?>apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo ROOT ?>favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo ROOT ?>favicon-16x16.png">

<?php
if (file_exists(PATH.'files/css/tailwind.css'))
{
    echo '<link rel="stylesheet" href="'.ROOT.'files/css/tailwind.css">';
}
else
{
    echo '<script src="https://cdn.tailwindcss.com"></script>';
}
?>
    <link rel="stylesheet" href="<?php echo THEME_URL ?>css/fontawesome-all.min.css">
    <link rel="stylesheet" href="<?php echo THEME_URL ?>css/dragula.min.css">

<?php
echo (!DEVEL && file_exists(PATH.'themes/'.$this->site->area->theme.'/css/'.$css.'.min.css'))
	? '<link rel="stylesheet" href="'.THEME_URL.'css/'.$css.'.min.css?v=2">'
	: '<link rel="stylesheet" href="'.THEME_URL.'css/'.$css.'.css?v=4">';

if (RTL)
{
	echo '<link title="normal" rel="stylesheet" href="'.THEME_URL.'/css/rtl.css" media="all" />';
}
?>
    <script src="https://kit.fontawesome.com/2e7ce67797.js" crossorigin="anonymous"></script>

    <script defer src="<?php echo THEME_URL ?>js/tinymce/tinymce.min.js"></script>
    <script defer src="<?php echo THEME_URL ?>js/dragula.min.js"></script>
    <script defer src="<?php echo THEME_URL ?>js/alpine.min.js"></script>
    <script defer src="<?php echo ROOT ?>files/js/jscolor.js"></script>

    <script>
    var domain = "<?php echo $this->site->data->domain ?>",
        root = "<?php echo BASE_URL ?>",
        theme = "<?php echo $this->site->area->theme ?>",
        area_id = <?php echo $page->id_area ?>,
        lang = "<?php echo X4Route_core::$lang.'-'.strtoupper(X4Route_core::$lang) ?>",
        completed = "<?php echo _MSG_OK ?>";
        warning = "<?php echo _WARNING ?>",
        error = "<?php echo _MSG_ERROR ?>";
        start_page = "<?php echo $start_page ?>",
        xmaps = [];
    </script>

<?php
echo (!DEVEL && file_exists(PATH.'themes/'.$this->site->area->theme.'/js/x3ui.min.js'))
	? '<script src="'.THEME_URL.'js/x3ui.min.js"></script>'
	: '<script src="'.THEME_URL.'js/x3ui.js?v=1"></script>'
?>

    <link rel="stylesheet" href="<?php echo ROOT ?>files/js/croppie.css">
    <script src="<?php echo ROOT ?>files/js/croppie.js"></script>

</head>
<body class="w-full h-screen">

    <div id="working" x-data="spinner_box()" @mouseover="menu()" class="py-3">
        <i
            class="fa-solid fa-lg fa-slash text-gray-600"
            :class="{'fa-spin text-amber-500': working}"
            x-on:working.window="run($event.detail)"
            x-cloak
        ></i>
    </div>

    <header id="head" class="relative flex bg-gray-700 px-3 py-2">
        <div class="flex-none w-18">
            <a href="<?php echo BASE_URL ?>" title="Home page">
                <img src="<?php echo THEME_URL ?>img/x3cms_light_small.png" class="max-h-8 inline-block align-bottom " alt="X3 CMS"/> <span class="text-gray-100">&nbsp;CMS</span>
            </a>
        </div>

        <div class="flex-auto text-right text-gray-100 text-xs pr-4">
            <?php echo _PUBLIC_SIDE ?>: <a class="link" target="_blank" href="<?php echo $this->site->data->domain ?>" title="<?php echo _PUBLIC_SIDE ?>"><?php echo $this->site->data->domain ?></a><br />
            <?php echo _LOGGED_AS ?>: <b><?php echo $_SESSION['username'] ?></b>
        </div>
    </header>
<?php
if (!$this->site->data->xon)
{
    // maintenance alert
    echo '<div x-data="{
        bannerVisible: false,
        bannerVisibleAfter: 500,
    }"
    x-show="bannerVisible"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="-translate-y-10"
    x-transition:enter-end="translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="translate-y-0"
    x-transition:leave-end="-translate-y-10"
    x-init="
        setTimeout(()=>{ bannerVisible = true }, bannerVisibleAfter);
    "
    class="h-auto duration-300 ease-out w-full failed text-right px-4 py-2 text-sm" x-cloak>'._MAINTENANCE_MODE.'</div>';
}
?>
    <main id="main" class="flex flex-row items-stretch bg-gray-300 text-gray-600">
<?php
// sidebar
$view = new X4View_core('sidebar');
$view->lang = X4Route_core::$lang;
$view->menus = $menus;
echo $view->render(false);
?>
        <div id="page"
            class="flex-auto"
            x-data="page_box()"
            x-init="pager(start_page)"
            x-on:pager.window="pager($event.detail)"
            x-on:blank.window="blank($event.detail)"
            x-on:setter.window="setter($event.detail)"
            x-html="content"
            @scroll.window="go_top = (window.pageYOffset > 50) ? true : false"
            x-cloak
        >
            <div>
                <div class="px-4 py-3 text-sm">
                    &nbsp;
                </div>

                <div
                    id="topic"
                    class="px-4 md:px-8 py-3 md:py-5 bg-white rounded-l"
                >
                    &nbsp;
                </div>
        </div>
    </main>
    <footer class="text-center py-4 text-gray-200 text-xs z-10">
        <p><a href="https://www.x3cms.net" title="X3 CMS">X3 CMS</a> powered by <a href="https://www.cblu.net" title="Cblu.net - Web solutions">Cblu.net</a></p>
    </footer>

    <div
        role="dialog"
        tabindex="-1"
        class="block fixed top-0 left-0 h-full w-full bg-gray-900 bg-opacity-20 backdrop-blur z-50 xmodal overflow-y-auto overflow-x-hidden outline-none"
        x-data="xmodal()"
        x-show="modal"

        x-on:popup.window="popup($event.detail)"
        x-on:menu.window="menu($event.detail)"
        x-on:completed.window="completed($event.detail)"
        x-on:failed.window="failed($event.detail)"
        x-cloak
        x-transition:enter.duration.600ms
        x-transition:leave.duration.200ms
    >
        <div x-html="html_modal" class="max-h-[calc(100%-1rem)]"></div>
    </div>


</body>
</html>
