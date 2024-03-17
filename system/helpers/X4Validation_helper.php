<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X4WEBAPP
 */

/**
 * Helper for form validation
 *
 * @package X4WEBAPP
 */
class X4Validation_helper
{
	/**
	 * Array of available validation rules
	 * Used to build forms
	 *
	 * param is an array where the first value is a boolean that is set to true if the rule has a relation with other fields in the same form
	 * and the second and further values are comparason values
	 */
	public static $rules = array(
		array('value' => 'required', 	'option' => 'required: set a field as mandatory', 														'param' => array(0, 0)),
		array('value' => 'hidden_req', 	'option' => 'hidden_req: set a field as mandatory but don\'t show the *', 								'param' => array(0, 0)),
		array('value' => 'requiredif', 	'option' => 'requiredif: set a field as mandatory if another field has a specific value (more than one field separated with :)', 				'param' => array(1, 'text')),
		array('value' => 'ifempty', 	'option' => 'ifempty: set a field as mandatory if another field is empty (more than one field separated with :)', 'param' => array(1, 0)),
		array('value' => 'requiredifempty', 	'option' => 'requiredifempty: set a field as mandatory if another field has a specific value and another is_a empty', 				'param' => array(1, 'text')),
        array('value' => 'checkif', 	'option' => 'checkif: set a rule on a field if another field  has a specific value',                    'param' => array(1, 'text')),
		array('value' => 'depends', 	'option' => 'depends: set a field as mandatory if another field is not empty', 							'param' => array(1, 0)),
		array('value' => 'contains', 	'option' => 'contains: check if a text field contains a specific string', 								'param' => array(0, 'text', 'integer')), // (eg. substr_to_contain, minimum_number_of_time (1 if not set))
		array('value' => 'inarray', 	'option' => 'inarray: check if a selected value is in the selected values in a multiple select field', 	'param' => array(1, 'text')),
        array('value' => 'selected', 	'option' => 'selected: check if selected in a multiple select field are less, equal or more than required',	'param' => array(0, 'text', 'integer')),    // (eg. <-5 or ==-3 or >-2)
		array('value' => 'mail', 		'option' => 'mail: check if a value is a valid email address', 											'param' => array(0, 0)),
		array('value' => 'url', 		'option' => 'url: check if a value is a valid URL', 													'param' => array(0, 0)),
		array('value' => 'phone', 		'option' => 'phone: check if a value is a valid phone number', 											'param' => array(0, 0)),
		array('value' => 'length', 		'option' => 'length: check the exact value length', 													'param' => array(0, 'integer')),
		array('value' => 'minlength', 	'option' => 'minlength: check the minimum value length', 												'param' => array(0, 'integer')),
		array('value' => 'maxlength', 	'option' => 'maxlength: check the maximum value length', 												'param' => array(0, 'integer')),
		array('value' => 'equal', 		'option' => 'equal: check if a value is equal to another field',										'param' => array(1, 0)),
		array('value' => 'different', 	'option' => 'different: check if a value is different to another field', 								'param' => array(1, 0)),
		array('value' => 'alpha',       'option' => 'alpha: check if a value contains only alphabetic chars', 						            'param' => array(0, 'text')),
		array('value' => 'alphanumeric', 'option' => 'alphanumeric: check if a value contains only alphanumeric chars', 						'param' => array(0, 0)),
		array('value' => 'numeric', 	'option' => 'numeric: check if a value contains only numbers', 											'param' => array(0, 0)),
		array('value' => 'greater', 	'option' => 'greater: check if a value is greater than a value in another field', 						'param' => array(1, 0)),
		array('value' => 'less', 		'option' => 'less: check if a value is less than a value in another field', 							'param' => array(1, 0)),
		array('value' => 'max', 		'option' => 'max: check if a value is too big', 														'param' => array(0, 'integer')),
		array('value' => 'min', 		'option' => 'min: check if a value is too small', 														'param' => array(0, 'integer')),
		array('value' => 'date', 		'option' => 'date: check if a value is a valid date', 													'param' => array(0, 0)),
		array('value' => 'time', 		'option' => 'time: check if a value is a valid time', 													'param' => array(0, 0)),
		array('value' => 'timer', 		'option' => 'timer: check if a value is a valid timer (hours:mm:ss)', 									'param' => array(0, 0)),
		array('value' => 'datetime', 	'option' => 'datetime: check if a value is a valid datetime', 											'param' => array(0, 0)),
		array('value' => 'after', 		'option' => 'after: check if a date is after another date in the same form', 							'param' => array(1, 0)),
		array('value' => 'afterequal', 	'option' => 'afterequal: check if a date is after or equal another date in the same form',				'param' => array(1, 0)),
		array('value' => 'before', 		'option' => 'before: check if a date is before another date in the same form', 							'param' => array(1, 0)),
		array('value' => 'beforeequal', 'option' => 'beforequal: check if a date is before or equal another date in the same form',				'param' => array(1, 0)),
		array('value' => 'periodical', 	'option' => 'periodical: check if a value is a strtotime compatible string (1 year)', 					'param' => array(0, 0)),
		array('value' => 'captcha', 	'option' => 'captcha: check if value is equal to session captcha value', 								'param' => array(0, 0)),
		array('value' => 'fiscalit', 	'option' => 'fiscalit: check if value is a valid italian Fiscal ID', 									'param' => array(0, 0)),	// (if length = 16 personal ID, if length = 11 company ID)
		array('value' => 'sizes', 		'option' => 'sizes: check if image sizes are too big', 													'param' => array(0, 'integer', 'integer')),	// (eg. sizes-width_pixels-height_pixels)
		array('value' => 'small', 		'option' => 'small: check if image sizes are too small', 												'param' => array(0, 'integer', 'integer')),	// (eg. sizes-width_pixels-height_pixels)
		array('value' => 'weight', 		'option' => 'weight: check if file weight is too big in KiloBytes', 									'param' => array(0, 'integer')),
		array('value' => 'color', 		'option' => 'color: check if value is a valid color (HEX format #00aaFF or #0aF)', 						'param' => array(0, 0)),
		array('value' => 'iban', 		'option' => 'iban: check if value is a valid IBAN', 													'param' => array(0, 0)),
		array('value' => 'ean', 		'option' => 'ean: check if value is a valid EAN 13 code', 												'param' => array(0, 0)),
		array('value' => 'isdir', 		'option' => 'isdir: check if value is a valid directory in the server', 								'param' => array(0, 0)),
		array('value' => 'password', 	'option' => 'password: check if a value contains only alphanumeric chars plus symbols', 				'param' => array(0, 0)),
	);

	/**
	 * Array of special validation rules to check when the field is empty or not
	 */
	private static $special_rules = array(
		'_required',
		'_hidden_req',
		'_requiredif',
		'_ifempty',
		'_requiredifempty',
		'_equal',
		'_different',
		'_sizes',
		'_small',
		'_weight',
		'_captcha'
	);

	/**
	 * Fields array
	 */
	private static $fields = array();

	/**
	 * Data array
	 */
	private static $data = array();

	/**
	 * Validate an external form
	 * Used to validate data submitted on remote (Mobile app)
	 */
	public static function validate(array &$fields, array $data) : array
	{
		// share the array of fields in the class
		self::$fields = $fields;

		// share the array of data
		self::$data = $data;

		$errors = [];
		$n = sizeof($fields);
		for ($i = 0; $i < $n; $i++)
		{
            $e = true;
            self::validateField($fields[$i], $e);

            if (!$e)
            {
                $last = end($fields[$i]['error']);
                $errors[$fields[$i]['name']] = $last['msg'];
            }
		}
		return $errors;
	}

	/**
	 * Validate a form
	 * on each field you can mix many rules (with |) and some rules can contains parameter (with § as separator)
	 */
	public static function form(array &$fields, string $form_name, string $method = 'post', array $data = []) : bool
	{
		// share the array of fields in the class
		self::$fields = $fields;

		// share the array of data
        if (empty($data))
        {
            self::$data = ($method == 'post')
                ? $_POST
                : $_GET;
        }
        else
        {
            self::$data = $data;
        }

		$e = true;

		// check x4token
		if (!isset(self::$data['x4token']) || self::$data['x4token'] != md5($_SESSION['token'].$form_name))
		{
			$e = false;
            $fields[] = [
                'label' => '',
                'name' => ' ',
                'type' => 'html',
                'value' => '',
                'error' => [['msg' => '_session_expired']]
            ];
            $_SESSION['token'] = uniqid(rand(), true);
		}
		else
		{
			$n = sizeof($fields);
			for ($i = 0; $i < $n; $i++)
			{
                self::validateField($fields[$i], $e);

				// assign the value
				if (!in_array($fields[$i]['type'], X4Form_helper::$exclude) &&
					(
						isset($fields[$i]['name']) &&
						isset(self::$data[$fields[$i]['name']]) &&
						(
							self::$data[$fields[$i]['name']] >= 0 ||
							strlen(self::$data[$fields[$i]['name']]) > 0
						)
					)
				)
				{
                    self::assignValue($fields[$i]);
				}
			}
		}
		return $e;
	}

    /**
     * Validate field
     */
    private static function validateField(array &$field, bool &$e)
    {
        if (isset($field['rule']))
		{
            $token = explode('|', $field['rule']);
            foreach ($token as $ii)
            {
                // get parameters
                $tok = explode('§', $ii);

                // set rule function name
                $rule = '_'.$tok[0];

                // if we have this validation function
                if(method_exists(__CLASS__, $rule))
                {
                    if (
                        in_array($rule, self::$special_rules) ||        // special rules
                        !self::is_empty($field['name'])                 // checked only if the field value is not empty
                    )
                    {
                        self::$rule($field, $tok, $e, self::$data, $_FILES);
                    }
                }
            }
        }
    }

    /**
     * Assign value
     */
    private static function assignValue(array &$field)
    {
        switch($field['type'])
        {
            case 'checkbox':
                $field['value'] = 1;
                $field['checked'] = intval(isset(self::$data[$field['name']]));
                break;

            case 'mcheckbox':
                $field['checked'] = (isset(self::$data[$field['name']]))
                    ? self::$data[$field['name']]
                    : array();
                break;
            case 'radio':
                if (isset(self::$data[$field['name']]))
                {
                    $field['checked'] = self::$data[$field['name']];
                }
                break;

            default:
                // handle 0
                if (is_array(self::$data[$field['name']]))
                {
                    $tmp_value = self::$data[$field['name']];
                }
                else
                {
                    $tmp_value = (self::$data[$field['name']] === '0' || strval(self::$data[$field['name']]) == '0')
                        ? '0'
                        : self::$data[$field['name']];
                }
                // check for sanitize
                $field['value'] = (isset($field['sanitize']))
                    ? self::sanitize($tmp_value, $field['sanitize'])
                    : $tmp_value;
                break;
        }
    }

	/**
	 * Sanitize input
	 */
	public static function sanitize(string $string, string $type) : mixed
	{
		switch($type)
		{
		case 'numeric':
			return floatval($string);
			break;
		case 'string':
			return htmlentities(strip_tags($string), ENT_QUOTES, 'UTF-8', false);
			break;
		case 'html':
			return htmlspecialchars($string);
			break;
		default:
			return $string;
			break;
		}
	}

	/**
	 * Avoid resubmissions
	 */
	public static function no_duplicate(string $form_name) : bool
	{
		$str = md5(serialize(self::$data));
		if (isset($_SESSION['x4'.$form_name]) && $_SESSION['x4'.$form_name] == $str)
        {
			return false;
        }
		else
		{
			$_SESSION['x4'.$form_name] = $str;
			return true;
		}
	}

	/**
	 * Rebuild form fields after validation
	 */
	public static function get_form(array $fields = array()) : array
	{
		$elements = array();
		foreach ($fields as $i)
		{
            $lbl = '';
			if (!is_null($i['label'])) 	{
				$req = (isset($i['rule']) && strstr($i['rule'], 'required') != '') ? ' *' : '';
				$err = (isset($i['error'])) ? ' class="error"' : '';
				$lbl = '
				<label for="'.$i['name'].'" '.$err.'>'.$i['label'].$req;
			}

			switch ($i['type'])
			{
				case 'select':
					$opt = '';
					// empty option
					if (isset($i['options'][3])) {
						$opt .= '
						<option value="'.$i['options'][3][0].'">'.$i['options'][3][1].'</option>';
					}
					// other options
					if (!empty($i['options'][0])) {
						foreach ($i['options'][0] as $ii)
						{
							$sel = ($i['value'] == $ii->$i['options'][1]) ? 'selected="selected"' : '';
							$opt .= '
							<option value="'.$ii->$i['options'][1].'" '.$sel.'>'.$ii->$i['options'][2].'</option>';
						}
					}
					$elements[$i['name']] = array(
						'label' => $lbl,
						'value' => $i['value'],
						'options' => $opt
						);
					break;
				default:
					$elements[$i['name']] = array(
						'label' => $lbl,
						'value' => $i['value']
						);
					break;
			}
		}
		return $elements;
	}

	/* VALIDATION FUNCTIONS */

	/**
	 * Required rule
	 */
	private static function _required(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if (!isset($field['name']) || self::is_empty($field['name']))
		{
			$field['error'][] = array('msg' => '_required');
			$e = false;
		}
	}

	/**
	 * Hidden Required rule
	 */
	private static function _hidden_req(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		self::_required($field, $tok, $e,  $_post, $_files);
	}

	/**
	 * Required if rule
	 * set a field as mandatory if another field has a specific value (more than one field separated with :)
     * Added more options to value field (rule = requiredif§related_field§related_value)
     * Related value can have a prefix (!, gt_, lt_ and bt_) for (different, greater than, lower than and between two values saparated with #)
     * This rule for more options works by default like an AND, if only a rule is false the field is not required
	 */
	private static function _requiredif(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if (self::is_empty($field['name']))
		{
			// there are multiple fields in tok[1] and tok[2]?
			$toks1 = explode(':', $tok[1]);
			$toks2 = explode(':', $tok[2]);

			$fields = array();
            // if only a rule is false the field is not required
            $fired = true;

			foreach ($toks1 as $index => $name)
			{
				// check for relations
                $relations = array('>', '<', '#', '!', '[');
                // replace prefixes
                $tmp = str_replace(
                    array('gt_', 'lt_', 'bt_', '!', 'sb_'),
                    $relations,
                    $toks2[$index]
                );

                // relations available are none, !, > and <
                $relation = (strlen($tmp) == 0)
                    ? ''
                    : substr($tmp, 0, 1);

                // get tok2 value
                if ($relation == '')
                {
                    $tok2 = '';
                }
                else
                {
                    $tok2 =  (in_array($relation, $relations))
                        ? substr($tmp, 1)
                        : $tmp;
                }

                $value = self::get_value($name);

                switch($relation)
                {
                    case '#':
                        list($t1, $t2) = explode('#', $tok2);
                        $check = ($value > $t1 && $value < $t2);
                        break;
                    case '!':
                        $check = ($value != $tok2);
                        break;
                    case '>':
                        $check = ($value > $tok2);
                        break;
                    case '<':
                        $check = ($value < $tok2);
                        break;
                    case '[':
                        // substring
                        $check = strpos($value, $tok2);
                        break;
                    default:
                        // default is equal
                        $check = ($value == $tok2);
                        break;
                }

				if ($fired && !$check)
				{
                    // one false turn off the rule
					$fired = false;
                }

				// store data for related
				$fields[$name] = $value;
			}

			// the required condition is fired?
			if ($fired)
			{
				foreach ($fields as $name => $value)
				{
					$field['error'][] = array(
						'msg' => '_requiredif',
						'related' => $name,
						'relatedvalue' => self::get_related_value($name, $value)	// $value
					);
					$e = false;
				}
			}
		}
	}

	/**
	 * If empty rule
	 * if the field defined in tok[1] is empty (or not set) then catch an error
	 */
	private static function _ifempty(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
	    // there are multiple fields in tok[1]?
		$toks = explode(':', $tok[1]);

		if (self::is_empty($field['name']))
		{
			// check the others
			foreach ($toks as $i)
			{
				// at least one not empty
				if (!empty($i) && self::is_empty($i))
				{
					$field['error'][] = array(
						'msg' => '_ifempty',
						'related' => $toks[0]
					);
					$e = false;
				}
			}
		}
	}

	/**
	 * Required if empty rule
	 * if tok[1] field as a specific value stored in tok[2] then check if tok[3] is empty
	 * tok[] = rule name, tok[1] = field that triggers the check, tok[2] = value that triggers, tok[3] = field that if empty triggers the required
	 */
	private static function _requiredifempty(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		// only if isset the field defined in $tok[1]
		if (self::is_empty($field['name']))
		{
			$tok1 = self::get_field($tok[1]);

			// we can have different cases
			switch (sizeof($tok))
			{
				case 4:
					//tok[] = rule name, tok[1] = field that triggers the check, tok[2] = value that triggers, tok[3] = field that if empty triggers the required
					// we have a value to check

					// check for not
					$tok2 = str_replace('!', '', $tok[2]);

					// check the value
					if ($tok1['type'] == 'file')
					{
						// options are only empty and not empty
						$check = ($tok2 != $tok[2])
							? self::check_file_upload($tok[1])		// required if file is not empty
							: !self::check_file_upload($tok[1]);	// required if file is empty
					}
					elseif ($tok1['type'] == 'checkbox')
					{
						$check = ($tok2 != $tok[2])
							? !isset($_post[$tok[1]])		// §!1 required if no set
							: isset($_post[$tok[1]]);		// §1 required if is set

					}
					else
					{
						$check = ($tok2 != $tok[2])
							? $_post[$tok[1]] != $tok2		// required if post is different from tok2
							: $_post[$tok[1]] == $tok[2];	// required if post is equal tok2
					}

					// related fields
					$toks = explode(':', $tok[3]);
					break;

				case 3:
					//tok[] = rule name, tok[1] = field that triggers the check, tok[2] = field that if empty triggers the required
					// we have only to check if the field tok[1] is set or not

					// check the value
					$check = !self::is_empty($tok[1]);

					// related fields
					$toks = explode(':', $tok[2]);
					break;

				default:
					// not enough data
					$check = false;
			}

			// the required condition is fired?
			if ($check)
			{
				// we have to check related fields
				$one_field_is_not_empty = false;
				// check if there are empty fields
				foreach ($toks as $i)
				{
					if (!empty($i))
					{
						if (!$one_field_is_not_empty && self::is_empty($i))
						{
							/*
							// hidden for simplicity
							$field['error'][] = array(
								'msg' => '_requiredif',
								'related' => $tok[1],
								'relatedvalue' => self::$data[$tok[1]]
							);
							*/
							$e = false;
						}
						else
						{
							$one_field_is_not_empty = true;
							$e = true;
						}
					}
				}

				if (!$e)
				{
					// just one for all
					$field['error'][] = array(
						'msg' => '_ifempty',
						'related' => $i
					);
				}
			}
		}
	}

    /**
	 * Check if
     * Set a rule on a field if another field  has a specific value
	 * tok[] = rule name, tok[1] = field that triggers the check, tok[2] = value that triggers, tok[3] = is the validation rule to apply on tok
	 */
	private static function _checkif(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
        // check for not
		$tok2 = str_replace('!', '', $tok[2]);

        $check = $tok2 == $tok[2]
            ? $_post[$tok[1]] == $tok2
            : $_post[$tok[1]] != $tok2;
        if ($check)
        {
            $rule = '_'.$tok[3];
            self::$rule($field, $tok, $e, self::$data, []);
        }
    }

	/**
	 * Equal rule
	 * if the field value and the field value defined in tok[1] are differents then catch an error
	 */
	private static function _equal(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if ($_post[$field['name']] != $_post[$tok[1]])
		{
			$field['error'][] = array(
			    'msg' => '_must_be_equal',
			    'related' => $tok[1]
			);
			$e = false;
		}
	}

	/**
	 * Different rule
	 * if the field value and the field value defined in tok[1] are equals then catch an error
	 */
	private static function _different(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if ($_post[$field['name']] == $_post[$tok[1]])
		{
			$field['error'][] = array(
			    'msg' => '_must_be_different',
			    'related' => $tok[1]
			);
			$e = false;
		}
	}

	/**
	 * Sizes rule
	 * if the sizes of the image uploaded are greater than defined in tok[1] (width) and tok[2] (height) then catch an error
	 */
	private static function _sizes(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if (isset($_files[$field['name']]) && is_uploaded_file($_files[$field['name']]['tmp_name']))
		{
			$sizes = getImageSize($_files[$field['name']]['tmp_name']);
			if ($sizes[0] > intval($tok[1]) || $sizes[1] > $tok[2])
			{
				$field['error'][] = array('msg' => '_image_size_is_too_big');
				$e = false;
			}
		}
	}

	/**
	 * Small rule
	 * if the sizes of the image uploaded are lower than defined in tok[1] (width) and tok[2] (height) then catch an error
	 */
	private static function _small(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if (isset($_files[$field['name']]) && is_uploaded_file($_files[$field['name']]['tmp_name']))
		{
			$sizes = getImageSize($_files[$field['name']]['tmp_name']);
			if ($sizes[0] < intval($tok[1]) || $sizes[1] < $tok[2])
			{
				$field['error'][] = array('msg' => '_image_size_is_too_small');
				$e = false;
			}
		}
	}

	/**
	 * Weight rule
	 * if the weight of the file uploaded are greater than defined in tok[1] (file size in Kilobytes) then catch an error
	 */
	private static function _weight(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if (isset($_files[$field['name']]) && is_uploaded_file($_files[$field['name']]['tmp_name']))
		{
			if ($_files[$field['name']]['size'] > intval($tok[1]))
			{
				$field['error'][] = array('msg' => '_file_weight_is_too_big');
				$e = false;
			}
		}
	}

	/**
	 * Captcha rule
	 * if the value is different from the session stored captcha value then catch an error
	 */
	private static function _captcha(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if (!isset($_SESSION['captcha']) || strtolower($_post[$field['name']]) != strtolower($_SESSION['captcha']))
		{
			$field['error'][] = array('msg' => '_captcha_error');
			$e = false;
		}
	}

	/**
	 * Mail rule
	 * if the value is not a valid email address then catch an error
	 */
	private static function _mail(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		$mail = explode('|', strtolower(trim($_post[$field['name']])));

		foreach ($mail as $m)
		{
			if (!X4Checker_helper::check_email($m))
			{
				$field['error'][] = array('msg' => '_invalid_mail');
				$e = false;
			}
		}
	}

	/**
	 * URL rule
	 * if the value is not a valid URL then catch an error
	 */
	private static function _url(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		$url = trim($_post[$field['name']]);
		if (!X4Checker_helper::check_url($url))
		{
			$field['error'][] = array('msg' => '_invalid_url');
			$e = false;
		}
	}

	/**
	 * Phone rule
	 * if the value is not a valid phone number then catch an error
	 */
	private static function _phone(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		$val = str_replace(array(' ', '-', '/', '+'), '', $_post[$field['name']]);
		if (!preg_match('/^([0-9])*?$/', $val))
		{
			$field['error'][] = array('msg' => '_must_contain_only_numbers');
			$e = false;
		}
	}

	/**
	 * Depends rule
	 * if the value in the field defined in tok[1] is not set or empty then catch an error
	 */
	private static function _depends(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if (
            !isset($_post[$tok[1]]) || (
                (is_array($_post[$tok[1]]) && empty($_post[$tok[1]])) ||
                (!is_array($_post[$tok[1]]) && strlen($_post[$tok[1]]) == 0)
            ))
		{
			$field['error'][] = array(
			    'msg' => '_depends',
			    'related' => $tok[1]
			);
			$e = false;
		}
	}

	/**
	 * Contains rule
	 * check if the value of the field desn't contain the string in tok[1] then catch an error
	 */
	private static function _contains(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		// get minimum limit
		$n = (isset($tok[2]))
			? intval($tok[2])
			: 1;
		if (substr_count($_post[$field['name']], $tok[1]) < $n)
		{
			$field['error'][] = array(
			    'msg' => '_contains',
				'related' => $tok[1],
				'relatedvalue' => $n
			);
			$e = false;
		}
	}

	/**
	 * In array rule (or not in array if tok[2] == '!')
	 * if the value is not in an array of selections (multiple select) then catch an error
	 */
	private static function _inarray(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
        if (
                isset($tok[2]) &&
                $tok[2] == '!' &&
                is_array(self::$data[$tok[1]]) &&
                in_array($_post[$field['name']], self::$data[$tok[1]])
            )
        {
            // check not in array
            $field['error'][] = array(
                'msg' => '_notinarray',
                'related' => $tok[1],
                'relatedvalue' => $_post[$field['name']]
            );
            $e = false;
        }
        elseif ((
                !isset($tok[2]) &&
                isset(self::$data[$tok[1]]) &&
                (
                    !is_array(self::$data[$tok[1]]) ||
                    !in_array($_post[$field['name']], self::$data[$tok[1]])
                )) ||
                !isset($_post[$tok[1]]) ||
                !is_array($_post[$tok[1]])
            )
        {
            $field['error'][] = array('msg' => '_inarray');
            $e = false;
        }
	}

    /**
	 * Selected rule
	 * check if selected values in a multiple select are less, equal or more than required then catch an error
	 */
	private static function _selected(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
        if (!is_array($_post[$field['name']]))
		{
			$e = false;
		}
        else
        {
            eval('$e = '.sizeof($_post[$field['name']]).$tok[1].$tok[2].';');
        }

        if (!$e)
        {
            $field['error'][] = array(
                'msg' => '_selected',
                'related' => $tok[1],
                'relatedvalue' => $tok[2]
            );
        }
	}

	/**
	 * Length rule
	 * if the value length is different from tok[1] then catch an error
	 */
	private static function _length(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		$len = strlen($_post[$field['name']]);
		if ($len != $tok[1])
		{
			$field['error'][] = array(
			    'msg' => '_wrong_length',
			    'related' => $tok[1]
			);
			$e = false;
		}
	}

	/**
	 * Min length rule
	 * if the value length is lower than tok[1] then catch an error
	 */
	private static function _minlength(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		$len = strlen($_post[$field['name']]);
		if ($len < $tok[1])
		{
			$field['error'][] = array(
			    'msg' => '_too_short',
			    'related' => $tok[1]
			);
			$e = false;
		}
	}

	/**
	 * Max length rule
	 * if the value length is greater than tok[1] then catch an error
	 */
	private static function _maxlength(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		$len = strlen($_post[$field['name']]);
		if ($len > $tok[1])
		{
			$field['error'][] = array(
			    'msg' => '_too_long',
			    'related' => $tok[1]
			);
			$e = false;
		}
	}

	/**
	 * Alphabetic rule
	 * if the value contains not alphabetic chars then catch an error
	 */
	private static function _alpha(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if (sizeof($tok) > 1)
		{
		    if (!preg_match('/^(['.$tok[1].']*)$/', $_post[$field['name']]))
            {
                $field['error'][] = array(
                    'msg' => '_must_contain_only',
                    'related' => $tok[1]
                );
                $e = false;
            }
		}
		else
		{
            if (!preg_match('/^([a-zA-Z]*)$/', $_post[$field['name']]))
            {
                $field['error'][] = array('msg' => '_must_be_alphabetic');
                $e = false;
            }
        }
	}

	/**
	 * Alphanumeric rule
	 * if the value contains not alphanumeric chars then catch an error
	 */
	private static function _alphanumeric(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if (!preg_match('/^([a-zA-Z0-9._-]*)$/', $_post[$field['name']]))
		{
			$field['error'][] = array('msg' => '_must_be_alphanumeric');
			$e = false;
		}
	}

	/**
	 * Password rule
	 * if the value contains not alphanumeric chars or symbols then catch an error
	 */
	private static function _password(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if (!strpbrk($_post[$field['name']], 'ABCDEFGHIJKLMNOPQRSTUVXYWZ'))
		{
			$field['error'][] = array('msg' => '_must_contain_uppercase');
			$e = false;
		}
		elseif (!strpbrk($_post[$field['name']], 'abcdefghijklmnopqrstuvxywz'))
		{
			$field['error'][] = array('msg' => '_must_contain_lowercase');
			$e = false;
		}
		elseif (!strpbrk($_post[$field['name']], '0123456789'))
		{
			$field['error'][] = array('msg' => '_must_contain_number');
			$e = false;
		}
		elseif (!strpbrk($_post[$field['name']], '!"#$%&()*+,\-./:;<=>?@[]^_{|}~'))
		{
			$field['error'][] = array('msg' => '_must_contain_symbol');
			$e = false;
		}
		elseif (preg_match('/^(.*)([.\s]+)(.*)$/', $_post[$field['name']]))
		{
			$field['error'][] = array('msg' => '_must_be_alphanumeric_plus_symbols');
			$e = false;
		}
	}

	/**
	 * Numeric rule
	 * if the value contains not numeric chars then catch an error
	 */
	private static function _numeric(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		$_post[$field['name']] = str_replace(',', '.', $_post[$field['name']]);
		if (!is_numeric($_post[$field['name']]))
		{
			$field['error'][] = array('msg' => '_must_be_numeric');
			$e = false;
		}
	}

	/**
	 * Color rule
	 * if the value is not a valide HEX color then catch an error
	 */
	private static function _color(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if (!preg_match('/^\#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $_post[$field['name']]))
		{
			$field['error'][] = array('msg' => '_is_not_a_valid_color');
			$e = false;
		}
	}

	/**
	 * Greater rule
	 * if the value is lower than the value of the field defined in tok[1] then catch an error
	 */
	private static function _greater(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		$_post[$field['name']] = str_replace(',', '.', $_post[$field['name']]);
		$_post[$tok[1]] = str_replace(',', '.', $_post[$tok[1]]);
		if ($_post[$field['name']] <= $_post[$tok[1]])
		{
			$field['error'][] = array(
			    'msg' => '_greater_than',
			    'related' => $tok[1]
			);
			$e = false;
		}
	}

	/**
	 * Less rule
	 * if the value is greater than the value of the field defined in tok[1] then catch an error
	 */
	private static function _less(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		$_post[$field['name']] = str_replace(',', '.', $_post[$field['name']]);
		$_post[$tok[1]] = str_replace(',', '.', $_post[$tok[1]]);
		if ($_post[$field['name']] > $_post[$tok[1]])
		{
			$field['error'][] = array(
			    'msg' => '_lower_than',
			    'related' => $tok[1]
			);
			$e = false;
		}
	}

	/**
	 * Max rule
	 * if the value is greater than the value defined in tok[1] then catch an error
	 */
	private static function _max(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		$_post[$field['name']] = str_replace(',', '.', $_post[$field['name']]);
		$tok[1] = str_replace(',', '.', $tok[1]);
		if (is_numeric($_post[$field['name']]) && $_post[$field['name']] > floatval($tok[1]))
		{
			$field['error'][] = array(
			    'msg' => '_lower_than',
			    'related' => $tok[1]
			);
			$e = false;
		}
	}

	/**
	 * Min rule
	 * if the value is lower than the value defined in tok[1] then catch an error
	 */
	private static function _min(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		$_post[$field['name']] = str_replace(',', '.', $_post[$field['name']]);
		$tok[1] = str_replace(',', '.', $tok[1]);
		if (is_numeric($_post[$field['name']]) && $_post[$field['name']] < floatval($tok[1]))
		{
			$field['error'][] = array(
			    'msg' => '_greater_than',
			    'related' => $tok[1]
			);
			$e = false;
		}
	}

	/**
	 * Date rule
	 * if the value is not a valid date then catch an error
	 */
	private static function _date(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if ($_post[$field['name']] != '0000-00-00')
		{
			$val = str_replace('/', '-', $_post[$field['name']]);
			// check if an alternative date_format is defined in the form
			$date_format = (isset($_post['date_format']))
				? str_replace('/', '-', $_post['date_format'])
				: 'Y-m-d';

			$res = X4Checker_helper::isDateTime($val, 'date', $date_format);
			if(!$res)
			{
				$format = str_replace(
					array('d', 'm', 'Y', '-'),
					array('gg', 'mm', 'aaaa', '/'),
					$date_format
				);

				$field['error'][] = array(
				    'msg' => '_must_be_a_date',
				    'relatedvalue' => '['.$format.']'
				);
				$e = false;
			}
			else
			{
				$field['value'] = $res;
			}
		}
	}

	/**
	 * Time rule
	 * if the value is not a valid time then catch an error
	 */
	private static function _time(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		$value = (isset($_post['time_format']))
			? X4Time_helper::reformat($_post[$field['name']], $_post['time_format'], 'H:i')
			: $_post[$field['name']];

		if(!preg_match('/^([01][0-9]|2[0-3]):([0-5][0-9])$/', $value))
		{
			$field['error'][] = array('msg' => '_must_be_a_time');
			$e = false;
		}
	}

	/**
	 * Timer rule
	 * if the value is not a valid timer then catch an error
	 */
	private static function _timer(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if(!preg_match('/^(([0-9])*):([0-5][0-9])((:([0-5][0-9]))*)$/', $_post[$field['name']]))
		{
			$field['error'][] = array('msg' => '_must_be_a_timer');
			$e = false;
		}
	}

	/**
	 * Datetime rule
	 * if the value is not a valid datetime then catch an error
	 */
	private static function _datetime(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if ($_post[$field['name']] != '0000-00-00 00:00:00')
		{
			$val = $_post[$field['name']];

			// check if an alternative date_format is defined in the form
			$datetime_format = (isset($_post['datetime_format']))
				? $_post['datetime_format']
				: 'Y-m-d H:i:s';

			$res = X4Checker_helper::isDateTime($val, 'datetime', $datetime_format);
			if(!$res)
			{
				$format = str_replace(
					array('d', 'm', 'Y', '-', 'H', 'i', 's'),
					array('gg', 'mm', 'aaaa', '/', 'hh', 'mm', 'ss'),
					$datetime_format
				);

				$field['error'][] = array(
					'msg' => '_must_be_a_datetime',
					'relatedvalue' => '['.$format.']'
				);
				$e = false;
			}
			else
			{
				$field['value'] = $res;
			}
		}
	}

	/**
	 * Handle dates for date comparason
	 */
	private static function handle_dates(array $field, array $tok, array $_post) : array
	{
		$len = strlen($_post[$field['name']]);
        if (
            ($len == 10 && isset($_post['date_format'])) ||
            ($len == 16 && isset($_post['datetime_format'])) ||
            ($len == 19 && isset($_post['datetime_format'])) ||
            ($len == 5 && isset($_post['time_format']))
        )
        {
            // exists an alternative date_format
            $val = str_replace('/', '-', $_post[$field['name']]);
            switch($len)
            {
            case 10:
                // date
                $value = X4Time_helper::reformat($val, $_post['date_format'], 'Y-m-d');
                $related = isset($_post[$tok[1]])
                    ? X4Time_helper::reformat($_post[$tok[1]], $_post['date_format'], 'Y-m-d')
                    : X4Time_helper::reformat($tok[1], $_post['date_format'], 'Y-m-d');
                break;
            case 16:
            case 19:
                // datetime
                $value = X4Time_helper::reformat($val, $_post['datetime_format'], 'Y-m-d H:i:s');
                $related = isset($_post[$tok[1]])
                    ? X4Time_helper::reformat($_post[$tok[1]], $_post['datetime_format'], 'Y-m-d H:i:s')
                    : X4Time_helper::reformat($tok[1], $_post['date_format'], 'Y-m-d H:i:s');
                break;
            case 5:
                // time
                $value = X4Time_helper::reformat($val, $_post['time_format'], 'H:i');
                $related = isset($_post[$tok[1]])
                    ? X4Time_helper::reformat($_post[$tok[1]], $_post['time_format'], 'H:i')
                    : X4Time_helper::reformat($tok[1], $_post['time_format'], 'H:i');
                break;
            }
        }
        else
        {
            $value = $_post[$field['name']];
            $related = isset($_post[$tok[1]])
                ? $_post[$tok[1]]
                : $tok[1];
        }
		return array($value, $related);
	}

	/**
	 * After rule
	 * if the value is not a date after another field date then catch an error
	 */
	private static function _after(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		list($value, $related) = self::handle_dates($field, $tok, $_post);
		if (strtotime($value) <= strtotime($related))
		{
			$field['error'][] = array(
				'msg' => '_must_be_after',
				'related' => $_post[$tok[1]],
			);
			$e = false;
		}
	}

	/**
	 * After or equal rule
	 * if the value is a date before another field date then catch an error
	 */
	private static function _afterequal(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		list($value, $related) = self::handle_dates($field, $tok, $_post);
		if (strtotime($value) < strtotime($related))
		{
			$field['error'][] = array(
				'msg' => '_must_be_after_or_equal',
				'related' => $_post[$tok[1]],
			);
			$e = false;
		}
	}

	/**
	 * Before rule
	 * if the value is not a date before another field date then catch an error
	 */
	private static function _before(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		list($value, $related) = self::handle_dates($field, $tok, $_post);
		if (strtotime($value) >= strtotime($related))
		{
			$field['error'][] = array(
				'msg' => '_must_be_before',
				'related' => $_post[$tok[1]]
			);
			$e = false;
		}
	}

	/**
	 * Before or equal rule
	 * if the value is a date before another field date then catch an error
	 */
	private static function _beforeequal(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		list($value, $related) = self::handle_dates($field, $tok, $_post);
		if (strtotime($value) > strtotime($related))
		{
			$field['error'][] = array(
				'msg' => '_must_be_before_or_equal',
				'related' => $_post[$tok[1]]
			);
			$e = false;
		}
	}

	/**
	 * Periodical rule
	 * if the value is not a periodical date then catch an error
	 */
	private static function _periodical(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if (!preg_match('/^([0-9]) (year|month|week|day)$/', $_post[$field['name']]))
		{
			$field['error'][] = array('msg' => '_must_be_a_periodical');
			$e = false;
		}
	}

	/**
	 * Fiscal IT rule
	 * if the value is not a valid italian Fiscal ID then catch an error
	 */
	private static function _fiscalit(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		$tmp = trim($_post[$field['name']]);
		switch (strlen($tmp))
		{
		case 16:
			if (!X4Checker_helper::isCF($tmp))
			{
				$field['error'][] = array('msg' => '_invalid_cf');
				$e = false;
			}
			break;
		default:
			if (!X4Checker_helper::isPIVA($tmp))
			{
				$field['error'][] = array('msg' => '_invalid_fiscal_id');
				$e = false;
			}
			break;
		}
	}

	/**
	 * Iban rule
	 * if the value is not a valid IBAN then catch an error
	 */
	private static function _iban(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
        $val = str_replace(array(' '), '', $_post[$field['name']]);
		if (!X4Checker_helper::verify_iban($val))
		{
			$field['error'][] = array('msg' => '_invalid_iban');
			$e = false;
		}
	}

	/**
	 * Ean rule
	 * if the value is not a valid EAN then catch an error
	 */
	private static function _ean(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if (!X4Checker_helper::isEAN(trim($_post[$field['name']])))
		{
			$field['error'][] = array('msg' => '_invalid_ean');
			$e = false;
		}
	}

	/**
	 * Isdir rule
	 * if the value is not a valid directory in the server then catch an error
	 */
	private static function _isdir(array &$field, array $tok, bool &$e, array $_post, array $_files)
	{
		if (!is_dir(trim($_post[$field['name']])))
		{
			$field['error'][] = array('msg' => '_invalid_directory');
			$e = false;
		}
	}

	/**
	 * Get name, also for multiple select
	 */
	public static function get_name(array $field) : string
	{
		return (isset($field['multiple']))
			? $field['name'].'[]'
			: $field['name'];
	}

	/**
	 * Check if a field is empty
	 * Used for related fields
	 */
	public static function is_empty(string $name) : bool
	{
		$res = true;
		$i = self::get_field($name);
		if ($i)
		{
            $name = self::get_name($i);
            switch($i['type'])
            {
                case 'file':
					$res = !self::check_file_upload($i);
					break;
				case 'checkbox':
					$res = !isset(self::$data[$name]);
					break;
                default:
                    if (isset(self::$data[$name]))
                    {
                        if (isset($i['multiple']))
                        {
                            // not set if array is empty or the first value is empty
                            $res = empty(self::$data[$name]) || empty(self::$data[$name][0]);
                        }
                        else
                        {
                            // empty value (zero is a value)
                            $res = empty(self::$data[$name]) && self::$data[$name] !== '0';
                        }
                    }
                    else
                    {
                        if (isset($i['multiple']))
                        {
                            // multiple
                            // not set if array is empty or the first value is empty
                            $res = empty(self::$data[$i['name']]) || empty(self::$data[$i['name']][0]);
                        }
                        else
                        {
                            $res = empty(self::$data[$name]);
                        }
                    }
                    break;
            }
        }
		return $res;
	}

	/**
	 * Check file upload
	 * return true if file is uploaded
	 */
	private static function check_file_upload(array $field) : bool
	{
        $uploaded = true;
		if (!isset($field['old']) || empty($field['old']))
		{
            if (
                empty($_FILES) ||
                !isset($_FILES[$field['name']])||
                !is_array($_FILES[$field['name']]) ||
                !isset($_FILES[$field['name']]['tmp_name']) ||
                (
                    is_array($_FILES[$field['name']]['tmp_name']) &&
                    strlen($_FILES[$field['name']]['tmp_name'][0]) == 0
                ) ||
                $_FILES[$field['name']]['tmp_name'] == '' ||
                (
                    is_array($_FILES[$field['name']]['name']) &&
                    strlen($_FILES[$field['name']]['name'][0]) == 0
                ) ||
                (
                    !is_array($_FILES[$field['name']]['name']) &&
                    strlen($_FILES[$field['name']]['name']) == 0
                )
            )
            {
                $uploaded = false;
            }
		}
		return $uploaded;
	}

	/**
	 * Get the value of a field
	 * Used for requiredif fields
	 */
	public static function get_value(string $name) : mixed
	{
		$i = self::get_field($name);
		if (!$i)
		{
            return '';
        }
        $res = '';
        $name = self::get_name($i);
        switch ($i['type'])
        {
            case 'file':
                $res = 'file';
                break;
            case 'checkbox':
                $res = (isset(self::$data[$name]) && self::$data[$name])
                    ? $i['value']
                    : '';
                break;
            case 'select':
                // fields with multiple
                if (isset(self::$data[$name]))
                {
                    if (isset($i['multiple']))
                    {
                        $res = (!empty(self::$data[$name]) && isset(self::$data[$name][0]))
                            ? self::$data[$name][0]
                            : '';
                    }
                    else
                    {
                        // empty value (zero is a value)
                        $res = self::$data[$name];
                    }
                }
                else
                {
                    if (isset($i['multiple']))
                    {
                        // multiple
                        $res = (!empty(self::$data[$i['name']]) && isset(self::$data[$i['name']][0]))
                            ? self::$data[$i['name']][0]
                            : '';
                    }
                    else
                    {
                        $res = empty(self::$data[$name]);
                    }
                }
                break;
            default:
                $res = self::$data[$name];
                break;
        }
		return $res;
	}

	/**
	 * Get a field from fields array by name
	 * Used for related fields
	 */
	public static function get_field(string $name) : mixed
	{
	    foreach(self::$fields as $i)
		{
			if (isset($i['name']) && $i['name'] == $name)
			{
				return $i;
			}
		}
		return false;
	}

	/**
	 * Get options
	 * Used for related fields
	 */
	public static function get_options(array $options) : array
	{
	    $a = array();
	    $k = $options[1];
	    $v = $options[2];
		foreach ($options[0] as $i)
		{
		    $a[$i->$k] = $i->$v;
		}
		return $a;
	}

	/**
	 * Get the related value of a field
	 * Used for requiredif fields
	 */
	public static function get_related_value(string $name, mixed $value)
	{
		$i = self::get_field($name);
		if ($i)
		{
			if (isset($i['options']))
			{
				// we have to ge the related value
				$options = self::get_options($i['options']);
				if (isset($options[$value]))
				{
					return $options[$value];
				}
			}

			if ($i['type'] == 'checkbox')
			{
				return $value
					? _YES
					: _NO;
			}
		}
		// we already have the value
		return $value;
	}
}
