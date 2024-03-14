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
 * X4search plugin
 *
 * @package		X3CMS
 */
class X4search_plugin extends X4Plugin_core implements X3plugin
{
	/**
	 * Constructor
	 * Initialize dict
	 */
	public function __construct(X4Site_model $site)
	{
		parent::__construct($site);
		$this->dict = new X4Dict_model(X4Route_core::$area, X4Route_core::$lang);
	}

	/**
	 * Default method
	 * Display a search form
	 */
	public function get_module(stdClass $page, array $args, string $param = '') : mixed
	{
		// load dictionary
		$this->dict->get_wordarray(array('x4search'));

		// get plugin configuration
		$conf = $this->site->get_module_param('x4search', $page->id_area);

		// set label
		$label = ($conf['label'])
			? _X4SEARCH_LABEL
			: '<i class="fa-solid fa-magnifying-glass"></i>';

		// set placeholder
		$placeholder = ($conf['placeholder'])
			? 'placeholder="'._X4SEARCH_PLACEHOLDER.'"'
			: '';

		$out = '<form name="fsearch" method="post" action="'.BASE_URL.'search" >
					<div x-data=\'{disabled: true}\' class="w-full flex flex-row gap-4">
						<div class="flex-1">
							<input type="text" class="w-full" x-on:input="disabled=$el.value.length < 3" name="search" id="search" autocomplete="off" '.$placeholder.' />
						</div>
						<div class="flex-none">
							<button class="btn link small" type="submit" x-bind:disabled="disabled">'.$label.'</button>
						</div>
					</div>
				</form>';

		return '<div id="x4search" class="max-w-3xl text-sm">'.$out.'</div>';
	}

	/**
	 * plugin actions
	 */
	public function plugin(string $control, mixed $a, mixed $b, mixed $c, mixed $d) : void
	{
		// none
	}
}
