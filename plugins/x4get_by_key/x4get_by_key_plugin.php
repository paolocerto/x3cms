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
 * x4get_by_key plugin
 *
 * @package		X3CMS
 */
class X4get_by_key_plugin extends X4Plugin_core implements X3plugin
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
        $this->dict = new X4Dict_model(X4Route_core::$area, X4Route_core::$lang);
	}

	/**
	 * Default method
	 * Display paginated articles with specified key
	 *
	 * @param object	$page object
	 * @param array		$args array of args
	 * @param string	$param parameter (the key)
	 * @return string
	 */
	public function get_module(stdClass $page, array $args, string $param = '')
	{
		$out = '';

		// pagination index
		$pp = (isset($args[0]))
			? intval($args[0])
			: 0;

		// tag index
		$tag = (isset($args[1]) && $args[1] == 'tag')
			? urldecode($args[2])
			: false;

        $params = explode('|', $param);
		if (!empty($param))
		{
            $this->dict->get_wordarray(array('x4get_by_key'));

			if ($params[1] == 'with_tags' && $tag)
			{
				$mod = new X4get_by_key_model();
				$items = X4Pagination_helper::paginate($mod->get_articles_by_key_and_tag($page->id_area, $page->lang, $params[0], $tag), $pp);
				$out .= '<h3 class="mt-6">'._TAG.': '.htmlentities($tag).'  <a class="text-sm" href="'.BASE_URL.$page->url.'" title="'._X4GET_BY_KEY_UNFILTER.'">'._X4GET_BY_KEY_UNFILTER.'</a></h3>';
			}
			else
			{
				$items = X4Pagination_helper::paginate($this->site->get_articles_by_key($page->id_area, $page->lang, $params[0]), $pp);
			}

			// use pagination
			if ($items[0])
			{
                // get tailwind classes for columns
                $grid = X4Theme_helper::tw_grid(sizeof($items[0]%4));
                // open grid
                $out .= '<div class="mt-4 '.$grid.' gap-4">';

				foreach ($items[0] as $i)
				{
                    // open box
					$out .= '<div class="gbkey_box">';

					if (!empty($i->content))
					{
						// check excerpt
						if ($i->excerpt)
						{
							$text = X4Theme_helper::excerpt($i->content);
							$out .= X4Theme_helper::reset_url(stripslashes($text[0]));
						}
						else
                        {
							$out .= X4Theme_helper::reset_url(stripslashes($i->content));
                        }

						// display tags
						if ($params[1] == 'with_tags' && !empty($i->tags))
						{
							$out .= '<div class="gbkey_tags text-sm pt-2 flex justify-start space-x-4"><div>'._TAGS.'</div>: ';
							$tt = explode(',', $i->tags);
							foreach ($tt as $t)
							{
								$t = trim($t);
								$out .= '<div><a href="'.BASE_URL.$page->url.'/0/tag/'.urlencode($t).'" title="'._X4GET_BY_KEY_FILTER.'">'.$t.'</a></div>';
							}
							$out .= '</div>';
						}

					}

					// module
					if (!empty($i->module))
					{
						$out .= X4Theme_helper::module($this->site, $page, $args, $i->module, $i->param);
					}

                    // close box
					$out .= '</div>';
				}
                // close grid
				$out .= '</div>';

				// pager
				if ($items[1][0] > 1)
				{
				    $out .= '<div id="pager">'.X4Pagination_helper::tw_admin_pager(BASE_URL.$page->url.'/', $items[1]).'</div>';
				}
			}
		}
		return $out;
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
