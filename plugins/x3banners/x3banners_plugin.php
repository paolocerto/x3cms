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
 * X3banners plugin
 *
 * @package		X3CMS
 */
class X3banners_plugin extends X4Plugin_core implements X3plugin
{

	/**
	 * Constructor
	 *
	 * @param	object	$site, site object
	 * @return	void
	 */
	public function __construct(X4Site_model $site)
	{
		parent::__construct($site);
        $this->dict = new X4Dict_model(X4Route_core::$area, X4Route_core::$lang);
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
            case 'banner_top':
                return $this->banner_top($page, $args);
                break;
            default:
                return '';
                break;
		}
	}

    /**
	 * banner_top
	 *
	 * @param   object	Page
	 * @param   array	Array of args
	 * @return  string
	 */
	private function banner_top($page, $args)
	{
		// get banner
        $mod = new X3banners_model($this->site->data->xdatabase);
		$banner = $mod->get_banner_by_id_page($page->id);

		if ($banner)
		{
            $xdata = '';
            if ($banner->auto_hide)
            {
                $xdata = 'x-data=\'{
                    seconds:0,
                    setup(t) {
                        this.seconds = t;
                        var obj = this;
                        tmx = setInterval(function(){obj.updateTimer()},1000);
                    },
                    updateTimer() {
                        var obj = this;
                        this.seconds--;
                        if (this.seconds <= 0) {
                            clearInterval(tmx);
                        }
                    }
                }\' x-init="setup('.$banner->auto_hide.')" x-show="seconds > 0" x-transition.opacity.duration.500ms';
            }

			return '
<script>var tmx;</script>
<style>
#banner_top a {color:'.$banner->link_color.'}
#topic .section:first-of-type {padding-top:8em !important;}
</style>
<div id="banner_top" '.$xdata.' class="w-full z-10 shadow-lg" style="background:'.$banner->bg_color.';color:'.$banner->fg_color.'">
    <div class="max-w-screen-lg pt-8 pb-4 mx-auto">'.$banner->description.'</div>
</div>';
		}
		else
        {
            return '';
        }
	}

	/**
	 * call plugin actions
	 *
	 * @param   string	$control action name
	 * @param   mixed	$a
	 * @param   mixed	$b
	 * @param   mixed	$c
	 * @param   mixed	$d
	 * @return  void
	 */
	public function plugin(string $control, string $a, string $b, string $c, string $d)
	{
	 	switch ($control)
		{

		// put here others calls

		/* SAMPLE
		// call private method
		case 'test':
			$this->test($a, $b);
			break;
		*/

		default:
			return '';
			break;
		}
	}

    /**
	 * SAMPLE method
	 *
	 * @param   integer $id_area Area ID
	 * @param   mixed	$a
	 * @param   mixed	$b
	 * @return  mixed
	 */
	private function test($a, $b)
	{
		// TO DO
		/*
		Here you can execute an action or you can get data to display
		*/
	}
}
