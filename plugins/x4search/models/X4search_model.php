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
 * x4search model
 *
 * @package		X3CMS
 */
class X4search_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 */
	public function __construct()
	{
        // we use as base matches table
		parent::__construct('matches');
	}

	/**
	 * Build the form array required to set the parameter
	 * This method have to be updated with the plugin options
	 */
	public function configurator(int $id_area, string $lang, int $id_page, string $param) : array
	{
	    $fields = array();

        $fields[] = array(
            'label' => null,
            'type' => 'html',
            'value' => '<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">'
        );

	    $fields[] = array(
			'label' => null,
			'type' => 'html',
			'value' => '<p>'._ARTICLE_PARAM_SETTING_NOT_REQUIRED.'</p>'
		);

		// comment this if you have options for param
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => 1,
			'name' => 'no_options'
		);

        $fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => '',
			'name' => 'param1'
		);

        $fields[] = array(
            'label' => null,
            'type' => 'html',
            'value' => '</div>'
        );

		return $fields;
	}

}