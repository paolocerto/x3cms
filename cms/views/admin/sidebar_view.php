<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// fake icon for pages without icon
$fake_icon = '<i class="fa-solid fa-lg fa-fw fa-circle-question"></i>';

$xdata = '{
        open:false,
        settings: false,
        plugins: false,
        toggle() {
            if (this.settings || this.plugins) this.open = true;
        },
        close() {
            if (!this.open) {
                this.settings = false;
                this.plugins = false;;
            }
        }
	}';

?>
<sidebar id="sidebar" class="flex-none w-18 bg-gray-300 px-3 pt-3 leading-8" x-data="<?php echo $xdata ?>">
    <a href="javascript:void(0)" x-cloak  class="pointer" @click="open=!open;close()" title="Show/hide text">
        <i x-show="!open" class="fa-solid fa-lg fa-fw fa-circle-chevron-right"></i>
        <i x-show="open" class="fa-solid fa-lg fa-fw fa-circle-chevron-left"></i>
    </a>

    <br><br>

    <!--a @click="$dispatch('pager', '<?php echo BASE_URL ?>home/dashboard')"
        title="<?php echo _HOME_PAGE ?>"><i class="fa-solid fa-lg fa-fw fa-house-chimney"></i> <span x-show="open"><?php echo _HOME_PAGE ?></span></a>
        <br -->

<?php
//print_r($menus);
//die;

$open = false;
foreach ($menus['sidebar'] as $k => $v)
{
    if ($v->xfrom == 'home')
    {
        if ($open)
        {
            // close open submenu
            echo '</ul></div>';
            $open = false;
        }

        $icon = !empty($v->icon)
            ? $v->icon
            : $fake_icon;

        // first level links
        switch ($v->url)
        {
            case 'sites':
                echo '<a @click="settings=!settings;toggle()"
                    title="'._SETTINGS.'">'.$icon.' <span x-show="open">'._SETTINGS.'</span></a><br>
                    <div x-show="settings" class="pl-3 text-sm leading-5 mb-4"><ul style="list-style:none">';
                $open = true;
                break;
            case 'modules':
                // plugins links
                if (!isset($menus['sidebar'][$k+1]) || $menus['sidebar'][$k+1]->xfrom == 'home')
                {
                    // link to all plugins
                    echo '<a
                        @click="$dispatch(\'pager\', \''.BASE_URL.'modules\')"
                        @contextmenu="$dispatch(\'blank\', \''.BASE_URL.'modules\')"
                        title="'._PLUGINS.'"
                    >
                        '.$icon.' <span x-show="open">'._PLUGINS.'</span>
                    </a><br>';
                }
                else
                {
                    echo '<a @click="plugins=!plugins;toggle()" title="'._PLUGINS.'">'.$icon.' <span x-show="open">'._PLUGINS.'</span></a><br>
                        <div x-show="plugins" class="pl-3 text-sm leading-5 mb-4"><ul style="list-style:none">
                            <li><a @click="$dispatch(\'pager\', \''.BASE_URL.'modules\');" title="'._ALL_PLUGINS.'">'._ALL_PLUGINS.'</a></li>';
                        $open = true;
                }
                break;
            case 'login/logout':
                echo '<a href="'.BASE_URL.'login/logout" title="'.$v->name.'">'.$icon.' <span x-show="open">'.$v->name.'</span></a><br>';
                break;
            default:
                echo '<a
                    @click="$dispatch(\'pager\', \''.BASE_URL.$v->url.'\')"
                    @contextmenu="$dispatch(\'blank\', \''.BASE_URL.$v->url.'\')"
                    title="'.$v->name.'"
                >
                    '.$icon.' <span x-show="open">'.$v->name.'</span>
                </a><br>';
                break;
        }
    }
    else
    {
        echo '<li><a
            @click="$dispatch(\'pager\', \''.BASE_URL.$v->url.'\');"
            @contextmenu="$dispatch(\'blank\', \''.BASE_URL.$v->url.'\')"
            title="'.$v->name.'">'.$v->name.'</a></li>';
    }
}

if ($open)
{
    // close open submenu
    echo '</ul></div>';
    $open = false;
}

// user menu
foreach ($menus['user_menu'] as $k => $v)
{
    $icon = !empty($v->icon)
        ? $v->icon
        : $fake_icon;

    $action = ($v->url == 'login/logout')
        ? 'href="'.BASE_URL.'login/logout"'
        : '@click="$dispatch(\'pager\', \''.BASE_URL.$v->url.'\')" @contextmenu="$dispatch(\'blank\', \''.BASE_URL.$v->url.'\')"';

    echo '<a '.$action.'
        title="'.$v->name.'">'.$icon.' <span x-show="open">'.$v->name.'</span></a><br>';
}
?>
    &nbsp;<i class="fa-solid fa-fw fa-ellipsis"></i><br>
    <a @click="$dispatch('popup', '<?php echo BASE_URL ?>languages/selector')" title="<?php echo _LANGUAGE ?>"><i class="fa-solid fa-lg fa-fw fa-earth-americas"></i> <span x-show="open"><?php echo strtoupper($lang) ?></span></a><br>
<?php
if ($_SESSION['level'] > 4)
{
    $debug = (DEBUG)
        ? 'on'
        : '';

    $devel = (DEVEL)
        ? 'on'
        : '';
?>
    <a @click="$dispatch('pager', '<?php echo BASE_URL ?>info')" @contextmenu="$dispatch('blank', '<?php echo BASE_URL ?>info')"
        title="<?php echo _ABOUT ?>"><?php echo $icon ?> <span x-show="open"><?php echo _ABOUT ?></span></a><br>

    <a @click="$dispatch('setter', '<?php echo BASE_URL.'sites/set/debug/'.((DEBUG+1)%2) ?>')" title="<?php echo _DEBUG_MODE ?>">
        <i class="fa-solid fa-lg fa-fw fa-bug <?php echo $debug ?>"></i> <span x-show="open"><?php echo _DEBUG_MODE ?></span>
    </a><br>
    <a @click="$dispatch('setter', '<?php echo BASE_URL.'sites/set/devel/'.((DEVEL+1)%2) ?>')" title="<?php echo _DEVEL_MODE ?>"><i class="fa-solid fa-lg fa-fw fa-code <?php echo $devel ?>"></i> <span x-show="open"><?php echo _DEVEL_MODE ?></span></a><br>
<?php
}
?>
    <br>
</sidebar>
