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
 * X4site_map plugin
 *
 * @package		X3CMS
 */
class X4site_map_plugin extends X4Plugin_core implements X3plugin
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
	 * Displays the tree structure of the area
	 */
	public function get_module(stdClass $page, array $args, string $param = '') : mixed
	{
		// if param can be exploded
		$p = explode('|', $param);

		switch($p[0])
		{
            case 'area_map':
                // show the site map
                return $this->area_map($page, $args);
                break;
            default:
                return '';
                break;
		}
	}

	/**
	 * Area map
	 */
	public function area_map(stdClass $page, array $args) : string
	{
        $map = $this->site->get_map($page, true);
		$out = '<div id="sitemap" class="mt-6">';

		$len = 0;
		$openul = $openli = 0;
		foreach ($map as $i)
		{
			$ilen = strlen($i->ordinal)/4;
			$class = '';
			if ($ilen > $len)
			{
				// change subpages
				$out .= '<ul class="ml-6">';
				$openul++;
			}
			elseif ($ilen < $len)
			{
				$out .= '</li>';
				$openli--;

				$n = $len - $ilen;
				for ($l = 0; $l < $n; $l++)
				{
					$out .= '</ul>';
					$openul--;
					$out .= '</li>';
					$openli--;
				}
			}
			else
			{
				// if home page
				if ($i->ordinal == 'A')
					$out .= '<ul>';
				else
				{
					// normal subpage
					$out .= '</li>';
					$openli--;
				}
			}

			// menus
			if ($ilen == 2 && $i->id_menu)
            {
				$class = 'class="map"';
            }

			$len = $ilen;
			$url = ($i->url == 'home')
				? ''
				: $i->url;

			$description = stripslashes($i->description);
			$out .= ($i->fake)
                ? '<li '.$class.'>'.stripslashes($i->name)._TRAIT_.$description
                : '<li '.$class.'><a href="'.BASE_URL.$url.'" title="'.$description.'">'.stripslashes($i->name).'</a>'._TRAIT_.$description;
			$openli++;
		}

		// close open li and ul
		while ($openli > 0)
		{
			$out .= '</li>';
			$openli--;
			if ($openul > 0)
			{
				$out .= '</ul>';
				$openul--;
			}
		}
		$out .= '</div>';
		return $out;
	}

	/**
	 * call plugin actions
	 */
	 public function plugin(string $control, mixed $a, mixed $b, mixed $c, mixed $d) : void
	 {
	 	 // none
	 }
}
