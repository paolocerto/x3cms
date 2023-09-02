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
 * X4site_map model
 *
 * @package		X3CMS
 */
class X4site_map_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('matches');
	}

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
			'value' => '<p>'._ARTICLE_PARAM_DEFAULT_MSG.'</p>'
		);

		// options field store all possible cases and parts
		// cases are separated by §
		// parts are separated by |
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => 'param1',
			'name' => 'options'
		);

        // this is for simple cases with param equal to get_module key
		$options = array(
		    array('value' => 'area_map', 'option' => 'area_map: Show the Area map'),
            // add
		);

		// the form builder plugin has only one possible call
		// the parameter is the form name
		$fields[] = array(
			'label' => _ARTICLE_PARAM_OPTIONS,
			'type' => 'select',
			'value' => $param,
			'options' => array(X4Array_helper::array2obj($options, 'value', 'option'), 'value', 'option', ''),
			'name' => 'param1',
			'rule' => 'required',
			'extra' => 'class="w-full"'
		);

        $fields[] = array(
            'label' => null,
            'type' => 'html',
            'value' => '</div>'
        );

		return $fields;
	}

}