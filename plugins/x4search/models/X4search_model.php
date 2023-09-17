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
	 *
	 * @return  void
	 */
	public function __construct()
	{
        // we use as base matches table
		parent::__construct('matches');
	}

    /**** THIS IS THE DEFAULT CONFIGURATOR ****/
	// If you need only this you don't need to uncomment or edit it
	// Else you can use it as base for your needs

	/**
	 * Build the form array required to set the parameter
	 * This method have to be updated with the plugin options
	 *
	 * @param	integer $id_area Area ID
	 * @param	string	$lang Language code
	 * @param	integer $id_page
	 * @param	string	$param Parameter
	 * @return	array
	 */
	public function configurator(int $id_area, string $lang, int $id_page, string $param)
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

		// options field store all possible cases and parts
		// cases are separated by §
		// parts are separated by |
/*
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => 'param1',
			'name' => 'options'
		);

		// this is for simple cases with param equal to get_modeule key
		$options = array(
		    array('value' => 'banner_top', 'option' => 'banner_top: Banner at the top of the page'),
		);

		$p = (empty($param))
	        ? array('', '', '')
	        : explode('|', urldecode($param));

		// plugin option
		$fields[] = array(
			'label' => _ARTICLE_PARAM_OPTIONS,
			'type' => 'select',
			'value' => $p[0],
			'options' => array(X4Array_helper::array2obj($options, 'value', 'option'), 'value', 'option', ''),
			'name' => 'param1',
			'rule' => 'required',
			'extra' => 'class="w-full"'
		);
*/
        $fields[] = array(
            'label' => null,
            'type' => 'html',
            'value' => '</div>'
        );

		return $fields;
	}

}
