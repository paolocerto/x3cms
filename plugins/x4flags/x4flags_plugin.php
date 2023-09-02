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
	 *
	 * @param	object	$site, site object
	 * @return	void
	 */
	public function __construct($site)
	{
		parent::__construct($site);
	}

    /**
	 * Default method
	 *
	 * @param object	$page object
	 * @param array		$args array of args
	 * @param string	$param plugin parameter
	 * @return string
	 */
	public function get_module(stdClass $page, array $args, string $param = '')
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
	 *
	 * @param object	$page object
	 * @param array		$args array of args
	 * @return array
	 */
	public function active_flag(stdClass $page, array $args)
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

        if ($flags && file_exists(PATH.'files/files/'.$page->lang.'.jpg'))
        {
            $active_flag = '<img class="md:mt-2" src="'.ROOT.'files/files/'.$page->lang.'.png" alt="'.$languages[$page->lang]->language.'" />';
        }

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

                if ($flags && file_exists(PATH.'files/files/'.$v->code.'.jpg'))
                {
                    $flag = '<img class="md:mt-2" src="'.ROOT.'files/files/'.$v->code.'.png" alt="'.$languages[$v->code]->language.'" />';
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
    x-data="{dropdown:false}"
    @click.away="dropdown = false"
>
    <button
        @click="dropdown = !dropdown"
        class="px-3 items-center focus:outline-none menu_item"
    >
        '.$active_flag.'
    </button>

    <div
        x-show="dropdown"
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
        $res['mobile'] = str_replace('XXXHIDDENXXX', 'inline-flex md:hidden', $btn);
        $res['screen'] = str_replace('XXXHIDDENXXX', 'hidden md:inline-flex', $btn);

		return $res;
	}

	/**
	 * call plugin actions
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$control action name
	 * @param   mixed	$a
	 * @param   mixed	$b
	 * @param   mixed	$c
	 * @param   mixed	$d
	 * @return  void
	 */
	public function plugin(int $id_area, string $control, string $a, string $b, string $c, string $d)
	{
		// none
	}
}
