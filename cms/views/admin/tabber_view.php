<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// tabber view

if (isset($title))
{
    echo '<h1>'.$title.'</h1>';
}

if (!empty($tabs))
{
    $cols = sizeof($tabs);

    // buttons
    $buttons = [];
    $sections = [];
    $init = '';
    $c = 1;
    foreach ($tabs as $k => $v)
    {
        switch($v[0])
        {
            case 'view':
                $buttons[] = '<button @click="tabSelected = '.$c.'"  type="button" class="tab" :class="{ \'link\': tabSelected === '.$c.' }">'.$k.'</button>';
                // build the view
                $view = new X4View_core($v[1]);
                // get the section
                $sections[] = '<div x-show="tabSelected === '.$c.'" class="relative mt-10">
                                '.$view->render(false).'
                            </div>';
                break;
            case 'url':
                if ($c == 1)
                {
                    // we set init to load the first
                    $init = 'x-init="loadURL(\''.$v[1].'\')"';
                }
                $buttons[] = '<button @click="tabSelected = '.$c.';loadURL(\''.$v[1].'\')"  type="button" class="tab" :class="{ \'link\': tabSelected === '.$c.' }">'.$k.'</button>';
                $sections[] = '<div x-show="tabSelected === '.$c.'" class="relative mt-10" x-html="loadedSection"></div>';
                break;
        }
        $c++;
    }

    echo '<div x-data="tabs_box()" '.$init.' class="relative w-full">
            <div class="tabs flex flex-cols md:flex-row space-x-1">
                '.implode(NL, $buttons).'
            </div>

            <div class="relative w-full mt-10 content">
                '.implode(NL, $sections).'
            </div>
        </div>';
}
else
{
    echo '<p>No tabs set</p>';
}
