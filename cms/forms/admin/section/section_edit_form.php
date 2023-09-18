<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

$xdata = '{
    open: true,
    nc: parseInt(document.getElementById("old_cols_num").value),
    setup() {
        this.checkSize();
        var bg = new JSColor("#bgcolor");
        var fg = new JSColor("#fgcolor");

        for (var i = 0; i < this.nc; i++) {
            eval("var bg"+i+"= new JSColor(\"#bg"+i+"\");");
            eval("var fg"+i+"= new JSColor(\"#fg"+i+"\");");
        }
    },
    checkSize() {
        // using x-model values do not update
        var columns = document.getElementById("columns").value;
        var sizes = document.getElementById("col_sizes").value;

        document.getElementById("col_sizes").classList.remove("softwarn");
        document.getElementById("rotide").disabled = false;
        // this.cleanSizes();
        let tmp = sizes.replace(/[^1-5+]/gi, "");
        let lastChar = tmp.slice(-1);
        if (lastChar === "+") {
            tmp = tmp.substring(0, tmp.length - 1);
        }
        sizes = tmp;
        // end clean sizes
        if (sizes != "") {
            eval("n=parseInt("+sizes+");");
            if (n != parseInt(columns)) {
                document.getElementById("col_sizes").classList.add("softwarn");
                document.getElementById("rotide").disabled = true;
            }
        }
    }
}';

// build the form
$fields = array();
$file_array = array();
$js_array = array();

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $id,
    'name' => 'id'
);

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $item->id_area,
    'name' => 'id_area'
);

$fields[] = array(
    'label' => null,
    'type' => 'hidden',
    'value' => $item->id_page,
    'name' => 'id_page'
);

$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
);

$fields[] = array(
    'label' => _NAME,
    'type' => 'text',
    'value' => $item->name,
    'name' => 'name',
    'rule' => 'required',
    'extra' => 'class="w-full mb-4"'
);

// decode settings for this particular section
$settings = json_decode($item->settings, true);

// translations
$options = array(
    'bgcolor' => _SECTION_BACKGROUND,
    'fgcolor' => _SECTION_FOREGROUND,
    'columns' => _SECTION_COLUMNS,
    'col_sizes' => _SECTION_COL_SIZES,
    'img_h' => _SECTION_IMG_H,
    'img_v' => _SECTION_IMG_V,
    'width' => _SECTION_WIDTH,
    'height' => _SECTION_HEIGHT
);

// if user can't edit
if ($_SESSION['level'] < 3)
{
    // display settings
    $tmp = array();
    foreach ($settings as $k => $v)
    {
        $tmp[] = '<li><b>'.$options[$k].'</b>: '.$v.'</li>';
    }

    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '<ul>'.implode('', $tmp).'</ul>'
    );
}
else
{
    // edit settings

    // tpl settings could be ordered in the wrong way
    if (!empty($settings))
    {
        foreach ($settings as $k => $v)
        {
            $mod_settings[$k] = $v;
        }
    }

    // sort by model to grant order
    foreach ($mod_settings as $k => $v)
    {
        switch($k)
        {
            case 'columns':

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '<br><div class="relative w-full mx-auto overflow-hidden">'
                );

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '<div x-data=\''.$xdata.'\' x-init="setup()">
                    <button @click="open = !open" class="cursor-pointer bg2 rounded flex items-center justify-between w-full p-4 text-left select-none mb-1">
                        <span>'._SECTION_SETTINGS.': '._SECTION_COL_SIZES.'</span>
                        <svg class="w-4 h-4 duration-200 ease-out" :class="{ \'rotate-180\': open }" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div x-show="open" x-transition:enter.duration.300ms x-transition:leave.duration.50ms x-cloak>
                        <div class="p-4 pt-0">'
                );

                $nc = sizeof(explode('+', $settings['col_sizes']));
                $fields[] = array(
                    'label' => null,
                    'type' => 'hidden',
                    'value' => $nc,
                    'name' => 'old_cols_num'
                );

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">'
                );

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '<div>'
                );

                // available number of columns
                $opts = array(1, 2, 3, 4, 5, 6);

                $fields[] = array(
                    'label' => _SECTION_COLUMNS,
                    'type' => 'select',
                    'value' => $settings['columns'],
                    'options' => array(X4Array_helper::simplearray2obj($opts), 'value', 'option'),
                    'name' => 'columns',
                    'extra' => 'class="w-full text-right" @change="checkSize()"'
                );

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '</div>'
                );

            break;

            case 'col_sizes':

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '<div>'
                );

                // to handle old pages
                $settings['col_sizes'] = (isset($settings['col_sizes']) && !empty($settings['col_sizes']))
                    ? $settings['col_sizes']
                    : implode('+', array_fill(0, $settings['columns'], 1));

                $fields[] = array(
                    'label' => _SECTION_COL_SIZES,
                    'type' => 'text',
                    'value' => $settings['col_sizes'],
                    'name' => 'col_sizes',
                    'extra' => 'class="w-full" @change="checkSize()"',
                    'suggestion' => _SECTION_COL_SIZES_MSG
                );

                // close grid and accordion item
                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '</div></div>
                        </div></div></div>'
                );

            break;

            case 'bgcolor':

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '<div x-data="{ open: false }">
                    <button @click="open = !open" class="cursor-pointer bg2 rounded flex items-center justify-between w-full p-4 text-left select-none mb-1">
                        <span>'._SECTION_SETTINGS.': '._SECTION_SIZES_AND_COLORS.'</span>
                        <svg class="w-4 h-4 duration-200 ease-out" :class="{ \'rotate-180\': open }" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div x-show="open" x-transition:enter.duration.300ms x-transition:leave.duration.50ms x-cloak>
                        <div class="p-4 pt-0">'
                );

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>'
                );

                $fields[] = array(
                    'label' => _SECTION_BACKGROUND,
                    'type' => 'text',
                    'value' => $settings['bgcolor'],
                    'name' => 'bgcolor',
                    'extra' => 'class="w-full"'
                );

                $fields[] = array(
                    'label' => _SECTION_BACKGROUND.' (default)&nbsp;',
                    'type' => 'checkbox',
                    'value' => 1,
                    'name' => 'bgcolor_default',
                    'checked' => intval($settings['bgcolor'] == 'default'),
                    //'extra' => 'inline'
                );

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '</div>'
                );

            break;

            case 'fgcolor':
                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '<div>'
                );

                $fields[] = array(
                    'label' => _SECTION_FOREGROUND,
                    'type' => 'text',
                    'value' => $settings['fgcolor'],
                    'name' => 'fgcolor',
                    'extra' => 'class="w-full"'
                );

                $fields[] = array(
                    'label' => _SECTION_FOREGROUND.' (default)&nbsp;',
                    'type' => 'checkbox',
                    'value' => 1,
                    'name' => 'fgcolor_default',
                    'checked' => intval($settings['fgcolor'] == 'default'),
                    //'extra' => 'inline'
                );

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '</div>'
                );
            break;

            case 'img_h':
                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '<div>'
                );

                $file_array['img_h'] = _SECTION_IMG_H;

                $fields[] = array(
                    'label' => _SECTION_IMG_H,
                    'type' => 'file',
                    'value' => '',
                    'name' => 'img_h',
                    'rule' => '',
                    'suggestion' => 'Min 1920x1080 px',
                    'extra' => 'class="w-full" @change="selectFile(\'img_h\', Object.values($event.target.files))"',
                    'folder' => 'img',
                    'old' => (isset($settings['img_h'])) ? $settings['img_h'] : '',
                    'delete' => _DELETE
                );

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '</div>'
                );
            break;

            case 'img_v':
                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '<div>'
                );

                $file_array['img_v'] = _SECTION_IMG_V;

                $fields[] = array(
                    'label' => _SECTION_IMG_V,
                    'type' => 'file',
                    'value' => '',
                    'name' => 'img_v',
                    'rule' => '',
                    'suggestion' => 'Min 1080x1920 px',
                    'extra' => 'class="w-full" @change="selectFile(\'img_v\', Object.values($event.target.files))"',
                    'folder' => 'img',
                    'old' => (isset($settings['img_v'])) ? $settings['img_v'] : '',
                    'delete' => _DELETE
                );

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '</div>'
                );

            break;

            case 'width':
                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '<div>'
                );

                $opts = array(
                    'fullwidth' => _SECTION_FULL_WIDTH,
                    'container mx-auto' => _SECTION_FIXED_WIDTH,
                    'small_container mx-auto' => 'Max 1024px',
                    'tiny_container mx-auto' => 'Max 800px',
                );

                $fields[] = array(
                    'label' => _SECTION_WIDTH,
                    'type' => 'select',
                    'value' => $settings['width'],
                    'options' => array(X4Array_helper::array2obj($opts, null, null, true), 'value', 'option'),
                    'name' => 'width',
                    'extra' => 'class="w-full"'
                );

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '</div>'
                );
            break;

            case 'height':
                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '<div>'
                );

                $opts = array('free', 'fullscreen');
                // fix for missing height
                if (!isset($settings['height']))
                {
                    $settings['height'] = 'free';
                }

                $fields[] = array(
                    'label' => _SECTION_HEIGHT,
                    'type' => 'select',
                    'value' => $settings['height'],
                    'options' => array(X4Array_helper::simplearray2obj($opts), 'value', 'option'),
                    'name' => 'height',
                    'extra' => 'class="w-full"'
                );

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '</div>'
                );

            break;

            case 'style':
                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '<div>'
                );

                // fix for missing class
                if (!isset($settings['style']))
                {
                    $settings['style'] = '';
                }
                $fields[] = array(
                    'label' => _SECTION_STYLE,
                    'type' => 'select',
                    'value' => $settings['style'],
                    'name' => 'stylex',
                    'options' => array(X4Array_helper::array2obj($theme_styles['sections'], null, null, true), 'value', 'option', ''),
                    'extra' => 'class="w-full"',
                    'suggestion' => _SECTION_STYLE_MSG
                );

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '</div>'
                );
            break;

            case 'class':
                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '<div>'
                );

                // fix for missing class
                if (!isset($settings['class']))
                {
                    $settings['class'] = '';
                }
                $fields[] = array(
                    'label' => _SECTION_CLASS,
                    'type' => 'text',
                    'value' => $settings['class'],
                    'name' => 'classx',
                    'extra' => 'class="w-full"',
                    'suggestion' => _SECTION_CLASS_MSG
                );

                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '</div></div>
                        </div></div></div>'
                );
            break;

            case 'col_settings':


                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '<div x-data="{ open : false }">
                    <button @click="open = !open" class="cursor-pointer bg2 rounded flex items-center justify-between w-full p-4 text-left select-none mb-1">
                        <span>'._SECTION_SETTINGS.': '._SECTION_COLUMNS_STYLE.'</span>
                        <svg class="w-4 h-4 duration-200 ease-out" :class="{ \'rotate-180\': open }" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition:enter.duration.300ms x-transition:leave.duration.50ms x-cloak>
                        <div class="p-4 pt-0">'
                );

                // number of columns
                $nc = sizeof(explode('+', $settings['col_sizes']));

                for ($i = 0; $i < $nc; $i++)
                {
                    // exists col_class set for this column?
                    if (!isset($settings['col_settings']) || !isset($settings['col_settings']['bg'.$i]))
                    {
                        $settings['col_settings']['bg'.$i] = '';
                        $settings['col_settings']['fg'.$i] = '#444444';
                        $settings['col_settings']['style'.$i] = '';
                        $settings['col_settings']['class'.$i] = '';
                    }

                    $fields[] = array(
                        'label' => null,
                        'type' => 'html',
                        'value' => '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>'
                    );
                    $fields[] = array(
                        'label' => _SECTION_BACKGROUND.' col '.($i+1),
                        'type' => 'text',
                        'value' => $settings['col_settings']['bg'.$i],
                        'name' => 'bg'.$i,
                        'extra' => 'class="w-full"'
                    );

                    $fields[] = array(
                        'label' => _SECTION_BACKGROUND.' '._RESET,
                        'type' => 'checkbox',
                        'value' => 1,
                        'name' => 'bg'.$i.'_reset',
                        'checked' => intval(empty($settings['col_settings']['bg'.$i]))
                    );

                    $fields[] = array(
                        'label' => null,
                        'type' => 'html',
                        'value' => '</div><div>'
                    );

                    $fields[] = array(
                        'label' => _SECTION_FOREGROUND.' col '.($i+1),
                        'type' => 'text',
                        'value' => $settings['col_settings']['fg'.$i],
                        'name' => 'fg'.$i,
                        'extra' => 'class="w-full"'
                    );

                    $fields[] = array(
                        'label' => _SECTION_FOREGROUND.' '._RESET,
                        'type' => 'checkbox',
                        'value' => 1,
                        'name' => 'fg'.$i.'_reset',
                        'checked' => intval(empty($settings['col_settings']['fg'.$i]))
                    );

                    $fields[] = array(
                        'label' => null,
                        'type' => 'html',
                        'value' => '</div><div>'
                    );

                    if (!isset($settings['col_settings']['style'.$i]))
                    {
                        $settings['col_settings']['style'.$i] = '';;
                    }

                    $fields[] = array(
                        'label' => _SECTION_STYLE.' col '.($i+1),
                        'type' => 'select',
                        'value' => $settings['col_settings']['style'.$i],
                        'name' => 'style'.$i,
                        'options' => array(X4Array_helper::array2obj($theme_styles['articles'], null, null, true), 'value', 'option', ''),
                        'extra' => 'class="w-full"',
                        'suggestion' => _SECTION_STYLE_MSG
                    );

                    $fields[] = array(
                        'label' => null,
                        'type' => 'html',
                        'value' => '</div><div>'
                    );
                    $fields[] = array(
                        'label' => _SECTION_CLASS.' col '.($i+1),
                        'type' => 'text',
                        'value' => $settings['col_settings']['class'.$i],
                        'name' => 'class'.$i,
                        'extra' => 'class="w-full"',
                        'suggestion' => _SECTION_CLASS_MSG
                    );

                    // close grid
                    $fields[] = array(
                        'label' => null,
                        'type' => 'html',
                        'value' => '</div></div>'
                    );
                }
                // close accordion item
                $fields[] = array(
                    'label' => null,
                    'type' => 'html',
                    'value' => '</div></div></div>'
                );
            break;
        }
    }

    // close accordion
    $fields[] = array(
        'label' => null,
        'type' => 'html',
        'value' => '</div>'
    );
}

// close
$fields[] = array(
    'label' => null,
    'type' => 'html',
    'value' => '</div>'
);
