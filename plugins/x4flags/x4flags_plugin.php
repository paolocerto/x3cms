<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

/**
 * X4flags plugin
 *
 * @package		X3CMS
 */
class X4flags_plugin extends X4Plugin_core implements X3plugin
{
	/**
	 * Constructor
	 */
	public function __construct(X4Site_model $site)
	{
		parent::__construct($site);
	}

    /**
	 * Default method
	 */
	public function get_module(stdClass $page, array $args, string $param = '') : mixed
	{
		// if param can be exploded
		$p = explode('|', $param);

		switch($p[0])
		{
            case 'active_flag':
                // show the active flag
                // with a dropdown to switch between active languages
                return $this->active_flag($page, $args);
                break;
            default:
                return '';
                break;
		}
	}

	/**
	 * Activeflag
	 * insert a flag in the menù with a dropdown to switch between languages
     * this call uses TailwindCSS and Alpine.js
	 */
	public function active_flag(stdClass $page, array $args) : array
	{
        // set empty response
        $res = [
            'mobile' => '',
            'screen' => ''
        ];

        // get languages for area
		$languages = X4Array_helper::indicize($this->site->get_alang(), 'code');

        // just one? No flags
        if (sizeof($languages) == 1)
        {
            return $res;
        }

		// config
		$conf = $this->site->get_module_param('x4flags', $page->id_area);
		extract($conf, EXTR_OVERWRITE);

        // set active flag
        $active_flag = ($short_text)
            ? $page->lang
            : $languages[$page->lang]->language;

        if ($flags && file_exists(PATH.'files/files/'.$page->lang.'.png'))
        {
            $active_flag = '<img src="'.ROOT.'files/files/'.$page->lang.'.png" alt="'.$languages[$page->lang]->language.'" />';
        }

        $svg = '<svg
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    :class="{\'rotate-180\': flags, \'rotate-0\': !flags}"
                    class="inline w-4 h-4 ml-1 transition-transform duration-200 transform"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd">
                    </path>
                </svg>';

        // get others flags
        $others = '';
        foreach ($languages as $k => $v)
        {
            if ($k != $page->lang)
            {
                // get flag
                $flag = ($short_text)
                    ? $v->code
                    : $languages[$v->code]->language;

                if ($flags && file_exists(PATH.'files/files/'.$v->code.'.png'))
                {
                    $flag = '<img src="'.ROOT.'files/files/'.$v->code.'.png" alt="'.$languages[$v->code]->language.'" />';
                }

                $others .= '
            <a
                href="'.ROOT.$v->code.'/'.X4Route_core::$area.'" title="'.$v->language.'"
                class="menu_item"
            >
                '.$flag.'
            </a>';
            }
        }

        $btn = '
<div
    class="relative XXXHIDDENXXX"
    x-data="{flags:false}"
    @click.away="flags = false"
>
    <a
        @click="flags = !flags"
        class="px-4 menu_item"
    >
        '.$active_flag.'
        '.$svg.'
    </a>

    <div
        x-show="flags"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 top-10 w-full pt-2 origin-top-right rounded-md shadow bg-white"
        x-cloak
    >
        <div class="px-3 py-3 flex flex-col z-30">
            '.$others.'
        </div>
    </div>
</div>';

        // build the buttons
        $res['mobile'] = str_replace('XXXHIDDENXXX', 'inline-flex md:hidden pt-2', $btn);
        $res['screen'] = str_replace('XXXHIDDENXXX', 'hidden md:inline-flex', $btn);

		return $res;
	}

	/**
	 * call plugin actions
	 */
	public function plugin(string $control, mixed $a, mixed $b, mixed $c, mixed $d) : void
	{
		// none
	}
}
