<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

/**
 * X3flags plugin
 *
 * @package		X3CMS
 */
class X3flags_plugin extends X4Plugin_core implements X3plugin
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
	 * Build a div with languages link to switch from all enabled languages
	 *
	 * @param object	$page object
	 * @param array		$args array of args
	 * @param string	$param parameter (the link separator)
	 * @return string
	 */
	public function get_module(stdClass $page, array $args, string $param = '')
	{
		$langs = $this->site->get_alang();

		// config
		$conf = $this->site->get_module_param('x3flags', $page->id_area);
		extract($conf, EXTR_OVERWRITE);

		$out = array();
		foreach ($langs as $i)
		{
			if ($show_all || $i->code != $page->lang)
			{
				// selected flag
				$flag = ($i->code == $page->lang)
					? 'class="on"'
					: '';

				if ($flags)
				{
					$code = '<img src="'.ROOT.'files/files/'.$i->code.'.jpg" alt="'.$i->language.'" />';
				}
				else
				{
					$code = ($short_text) ? $i->code : $i->language;
				}
				$out[] = '<li><a '.$flag.' href="'.ROOT.$i->code.'/'.X4Route_core::$area.'" title="'.$i->language.'">'.$code.'</a></li>';
			}
		}
		$output = '<ul class="inline-list xsmall">'.implode($param, $out).'</ul>';
		return '<div id="x3flags">'.$output.'</div>';
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
