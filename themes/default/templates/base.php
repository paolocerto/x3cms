<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */


$view = new X4View_core('default/head_menu');
$view->site = $this->site;
$view->menus = $menus;
$view->page = $page;
$view->args = $args;
echo $view->render(false);
?>
    <!-- topic -->
    <div id="topic" x-data="page_box()" x-cloak>

<?php
// banner
echo stripslashes(X4Theme_helper::module($this->site, $page, [], 'x3banners', 'banner_top'));
?>

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
    $css = [];

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
    <footer>

        <div class="container mx-auto p-4 text-white">
            <p class="text-center text-sm mt-4">
                <a href="https://www.x3cms.net" title="X3 your next Content Management System">X3 CMS</a> powered by <a href="https://www.cblu.net" title="Cblu.net - Software &amp; Web design">Cblu.net</a>
            </p>
        </div>


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

// check cookie policy
echo stripslashes(X4Theme_helper::module($this->site, $page, [], 'x4cookie', 'alert'));

// script view
$view = new X4View_core('script');
echo $view->render(false);

?>
</body>
</html>
