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
	 */
	public function __construct(X4Site_model $site)
	{
		parent::__construct($site);
        $this->dict = new X4Dict_model(X4Route_core::$area, X4Route_core::$lang);
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
            case 'banner_top':
                return $this->banner_top($page);
                break;
            default:
                return '';
                break;
		}
	}

    /**
	 * banner_top
	 */
	private function banner_top(stdClass $page) : string
	{
		$mod = new X3banners_model($this->site->data->db);
		$banner = $mod->get_banner_by_id_page($page->id);

		if ($banner)
		{
            $view = new X4View_core('public/x3banners_bar', 'x3banners');
            $view->banner = $banner;
            $view->gradient = $banner->gradient
                ? 'background: linear-gradient(90deg, '.$this->hex2rgba($banner->bg_color1).' 0%, '.$this->hex2rgba($banner->bg_color2).' 100%);'
                : '';

            return $view->render(false);
		}
		else
        {
            return '';
        }
	}

    /**
     * From HEX to RGBA
     */
    private function hex2rgba(string $hex) : string
    {
        list($r, $g, $b) = array_map(
            function ($c) {
              return hexdec(str_pad($c, 2, $c));
            },
            str_split(ltrim($hex, '#'), strlen($hex) > 4 ? 2 : 1)
        );
        return 'rgba('.$r.','.$g.','.$b.',1)';
    }


	/**
	 * call plugin actions
	 */
	public function plugin(string $control, mixed $a, mixed $b, mixed $c, mixed $d) : void
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
			echo '';
			break;
		}
	}

    /**
	 * SAMPLE method
	 */
	private function test(mixed $a, mixed $b)
	{
		// TO DO
		/*
		Here you can execute an action or you can get data to display
		*/
	}
}
