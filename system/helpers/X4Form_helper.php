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
 * Helper for Form handling
 *
 * @package X4WEBAPP
 */
class X4Form_helper
{
	// array of items to exclude
	public static $exclude = array('html', 'clear', 'loading', 'slider', 'button');

	/**
	 * Build a form
	 * each item of fields is an associative array with informations about the field
	 * field = array(
	 *	'label' => field's label string OR null
	 *	'type' => html | hidden | text | file | password | checkbox | radio | textarea | select | clear | loading | slider | button
	 *	'name' => the name of the field
	 *	'value' => the default value of the field
	 *	// after there are optionals
	 *	'rule' => rules for the field eg. required|mail see validation method
	 *	'extra' => attribute for the field, useful for CSS style
	 *	'suggestion' => a suggestion message for the field
	 *	// only for files
	 *	'old' => the old value of the field if not empty
	 *	'folder' => if set the form show a link to the file for check
	 *	'delete' => label for removal, if set you have a checkbox so you can remove old file without add a new file
	 *	// only for checkbox
	 *	'checked' => 0|1 set the default status
	 *	// only for select
	 *	'options' => an array(array of objects, attribute name for value, attribute name for option, optional value for empty option)
	 *	'disabled' => if set as name of boolean attribute of array of objects disable options where attribute is true
	 *	'multiple' => integer, set multiple select height
	 *
	 * @static
	 * @param	string	$name form name
	 * @param	string	$action form action
	 * @param	mixed	$fields array of form fields or prebuilded form string
	 * @param	array	$buttons array of form buttons (reset button, submit button, alignment)
	 * @param	string	$method form method
	 * @param	string	$extra extra value for the form (eg. enctype="multipart/form-data")
	 * @param	string	$submit_action javascript to run on submit
	 * @param	string	$reset_actionjavascript to run on reset
	 * @param	boolean	$inline label in the same line of the input field
	 * @return string
	 */
	public static function doform($name, $action, $fields, $buttons = array(_RESET, _SUBMIT, 'text-center'), $method = 'post', $extra = '', $submit_action = '', $reset_action = '', $inline = false)
	{
		if (!empty($submit_action))
        {
			$extra .= ' onsubmit="return false"';
        }
        
		// sanitize action
		$action = htmlentities(strip_tags($action), ENT_QUOTES, 'UTF-8', false);

		$btn = false;
		if (is_array($fields))
		{
			// add x4 token field security token
			$fields[] = array(
				'label' => null,
				'type' => 'hidden',
				'value' => md5($_SESSION['token'].$name),
				'name' => 'x4token'
			);

			// check for buttons
			$btn = true;

			// build form tag
			$str = '<form id="'.$name.'" name="'.$name.'" action="'.$action.'" method="'.$method.'" '.$extra.'>';

            $str .= self::doform_section($fields, '', $inline);
		}
		else
		{
			// if fields is not an array inject in a prebuilded form
			// build form tag
			$str = '<form id="'.$name.'"  action="'.$action.'" method="'.$method.'" '.$extra.'>
				'.$fields.'<input type="hidden" value="'.md5($_SESSION['token'].$name).'" name="x4token" id="x4token" />';
		}

		// buttons box
		if ($btn)
		{
			$str .= self::buttons($buttons, $name, $submit_action, $reset_action);
		}

		$str .= '</form>';
		return $str;
	}

	/**
	 * Build a form section
	 *
	 * @static
	 * @param	mixed	$fields array of form fields or prebuilded form string
	 * @param	string	$form name
	 * @return string
	 */
	public static function doform_section($fields, $name = '', $inline = false)
	{
		$str = '';

		if (is_array($fields))
		{
			if (!empty($name))
			{
				// add x4 token field security token
				$fields[] = array(
					'label' => null,
					'type' => 'hidden',
					'value' => md5($_SESSION['token'].$name),
					'name' => 'x4token'
				);
			}

			foreach ($fields as $i)
			{
				$tmp = $req = '';
				// check for hidden
				if (isset($i['hide']))
                {
					$str .= '<div id="'.$i['hide'].'" class="hide">';
                }

				// label
				if (!is_null($i['label']) && $i['type'] != 'button')
				{
					$req = '';
					if (isset($i['rule']))
					{
						$rules = explode('|', $i['rule']);
						if (in_array('required', $rules))
						{
							$req = '*';
						}
					}
					$err = (isset($i['error'])) ? ' class="error"' : '';

					if ($inline || (isset($i['extra']) && strstr($i['extra'], 'inline') != ''))
					{
						$str .= '
						<label for="'.$i['name'].'" '.$err.'>'.stripslashes($i['label']).$req;
					}
					else
					{
						$str .= '
						<label for="'.$i['name'].'" '.$err.'>'.stripslashes($i['label']).$req.'</label>';
					}
				}

				switch($i['type'])
				{
				case 'clear':
					$str .= self::clear();
					break;
				case 'loading':
					$str .= '<div id="loading" style="visibility:hidden;"><img src="'.ROOT.'files/files/ajax-loader.gif" alt="Loading..." /></div>';
					break;
				case 'slider':
					$str .= self::slider($i['name']);
					break;
				case 'html':
					$str .= $i['value'];
					break;
				case 'button':
					$str .= '<div class="xcenter">'.self::button($i).'</div>';
					break;
				default:
					// for: fieldset, hidden, text, file, password, checkbox, radio, texarea, select
					$t = $i['type'];
					$str .= self::$t($i, $req);
					break;
				}

				// close label
				if (($inline || (isset($i['extra']) && strstr($i['extra'], 'inline') != '')) && !is_null($i['label']))
				{
					$str .= '</label>';
				}

				// close hidden
				if (isset($i['hide']))
				{
					$str .= '</div>';
				}
			}
		}

		return $str;
	}

	/**
	 * Return clear div
	 *
	 * @static
	 * @return string
	 */
	public static function clear()
	{
		return '<div class="clear"></div>';
	}

	/**
	 * Return slider
	 *
	 * @static
	 * @param string	$name id of the slider
	 * @return string
	 */
	public static function slider($name)
	{
		return '<div id="'.$name.'" class="slider"><div class="knob"></div></div>';
	}

	/**
	 * Return fieldset
	 *
	 * @static
	 * @param array		$e Fieldset data
	 * @return string
	 */
	public static function fieldset($e, $req = '')
	{
		if ($e['value'] == 'open')
		{
			$iextra = (isset($e['extra']))
				? $e['extra']
				: '';

			return '<fieldset id="'.$e['name'].'" '.$iextra.'>';
		}
		else
        {
			return '</fieldset>';
        }
	}

	/**
	 * Return hidden input field
	 *
	 * @static
	 * @param array		$e Field data
	 * @return string
	 */
	public static function hidden($e, $req = '')
	{
		$iextra = (isset($e['extra']))
			? $e['extra']
			: '';

		// replace with session value
		if ($e['value'] == 'SESSION')
		{
		    if (isset($_SESSION[$e['name']]))
		    {
		        $e['value'] = $_SESSION[$e['name']];
		    }
		    elseif (isset($_SESSION['id_'.$e['name']]))
		    {
		        $mod = new Log_model();
		        $tokens = explode('|', $e['extra']);
		        $e['value'] = $mod->get_var($_SESSION['id_'.$e['name']], $tokens[0], $tokens[1]);
		        $iextra = '';
		    }
		}

		return '<input type="hidden" name="'.$e['name'].'" id="'.$e['name'].'" value="'.$e['value'].'" '.$iextra.' />';
	}

	/**
	 * Return text input field
	 *
	 * @static
	 * @param array		$e Field data
	 * @return string
	 */
	public static function text($e, $req = '')
	{
		$error = (is_null($e['label']) && isset($e['error']))
			? 'error '
			: '';

		$iextra = (isset($e['extra']))
			? str_replace(' class="', ' class="'.$error, $e['extra'])
			: '';

		if (!empty($error) && empty($iextra))
		{
			$iextra = 'class="error"';
		}

		$readonly = (isset($e['readonly']))
			? ' readonly="readonly"'
			: '';

		$placeholder = (isset($e['placeholder']))
			? ' placeholder="'.$e['placeholder'].'"'
			: '';

		if ($e['name'] == 'captcha')
        {
			$e['value'] = '';
        }

		// to handle special input types like date or time or datetime-local
		$type = (!isset($e['case']))
			? 'text'
			: $e['case'];

		return '<input type="'.$type.'" name="'.$e['name'].'" id="'.$e['name'].'" value="'.$e['value'].'" '.$iextra.$readonly.$placeholder.' />'.self::suggestion($e);
	}

	/**
	 * Return range input field
	 *
	 * @static
	 * @param array		$e Field data
	 * @return string
	 */
	public static function range($e, $req = '')
	{
		$error = (is_null($e['label']) && isset($e['error']))
			? 'error '
			: '';

		$iextra = (isset($e['extra']))
			? str_replace('class="', 'class="'.$error, $e['extra'])
			: '';

		if (!empty($error) && empty($iextra))
		{
			$iextra = 'class="error"';
		}

		$readonly = (isset($e['readonly']))
			? ' readonly="readonly"'
			: '';

		return '<input type="range" name="'.$e['name'].'" id="'.$e['name'].'" value="'.$e['value'].'" '.$iextra.$readonly.' />'.self::suggestion($e);
	}

	/**
	 * Return file input field
	 *
	 * @static
	 * @param array		$e Field data
	 * @return string
	 */
	public static function file($e, $req = '')
	{
		$iextra = (isset($e['extra']))
			? $e['extra']
			: '';

		if (isset($e['multiple']))
		{
			$fname = $e['name'].'[]';
			$multiple = 'multiple="multiple"';
		}
		else
		{
			$fname = $e['name'];
			$multiple = '';
		}
		$field = '<input '.$iextra.' type="file" name="'.$fname.'" id="'.$e['name'].'" value="'.$e['value'].'" '.$multiple.' />';

		// for delete option
		$tmp = '';
		if (isset($e['old']) && !empty($e['old']))
		{
			$tmp = '<p>';
			// for removal
			if (isset($e['delete']) && $req != ' *')
			{
				$tmp .= '<label class="inline" for="delete_'.$e['name'].'"><input type="checkbox" class="check" name="delete_'.$e['name'].'" id="delete_'.$e['name'].'" value="1" /> '.$e['delete'].'</label><br>';
			}
			$tmp .= '<span class="xsmall">';
			// can display the file only if knowns his path
			if (isset($e['folder']))
			{
				switch($e['folder'])
				{
					case 'img';
						$tmp .= '<br /><img class="mthumb dblock" src="'.FPATH.$e['folder'].'/'.$e['old'].'" alt="thumb" />';
						break;
					default:
						// check if exists a efolder directory
						if (is_dir($e['folder']) &&
							file_exists($e['folder'].'/'.$e['old'])
						)
						{
                            if (getimagesize($e['folder'].'/'.$e['old']))
                            {
                                // is an image
							    $tmp .= '<br /><img class="mthumb dblock" src="'.str_replace(PATH, ROOT, $e['folder']).'/'.$e['old'].'" alt="thumb" />';
                            }
                            else
                            {
                                // is a file
                                $tmp .= ' <a href="'.str_replace(PATH, ROOT, $e['folder']).'/'.$e['old'].'" title="">'.$e['old'].'</a>';
                            }
						}
                        elseif (is_dir(FFPATH.$e['folder']) && file_exists(FFPATH.$e['folder'].'/'.$e['old']))
                        {
                            // is a special folder
                            $tmp .= ' <a href="'.ROOT.'cms/files/'.$e['folder'].'/'.$e['old'].'" title="">'.$e['old'].'</a>';
                        }
						break;
				}
			}
			elseif (isset($e['aold']))
			{
				$tmp .= $e['aold'];
			}
			else
			{
				$tmp .= $e['old'];
			}
			$tmp .= '</span></p>';

			$tmp .= '<input type="hidden" name="old_'.$e['name'].'" id="old_'.$e['name'].'" value="'.$e['old'].'" />';
		}
		return $field.self::suggestion($e).$tmp;
	}

	/**
	 * Return password input field
	 *
	 * @static
	 * @param array		$e Field data
	 * @return string
	 */
	public static function password($e, $req = '')
	{
		$iextra = (isset($e['extra']))
			? $e['extra']
			: '';

		return '<input type="password" name="'.$e['name'].'" id="'.$e['name'].'" value="'.$e['value'].'" '.$iextra.' />'.self::suggestion($e);
	}

	/**
	 * Return checkbox input field
	 *
	 * @static
	 * @param array		$e Field data
	 * @return string
	 */
	public static function checkbox($e, $req = '')
	{
		$inline = false;

		$checked = (isset($e['checked']) && intval($e['checked']) > 0)
			? 'checked="checked"'
			: '';

		if (isset($e['extra']))
		{
			$class = (strstr($e['extra'], 'class="'))
				? str_replace('class="', 'class="check ', $e['extra'])
				: 'class="check" '.$e['extra'];

			// is inline?
			$inline = (strstr($e['extra'], 'xinline') != '');
		}
		else
		{
			$class = 'class="check"';
		}

		if ($inline && is_null($e['label']) && isset($e['suggestion']))
		{
			$label_class = isset($e['error'])
				? 'class="xinline error"'
				: 'class="xinline"';

			$tmp = '<label '.$label_class.' for="'.$e['name'].'"><input type="checkbox" '.$class.' name="'.$e['name'].'" id="'.$e['name'].'" value="'.$e['value'].'" '.$checked.' /> &nbsp;&nbsp;<span>'.stripslashes($e['suggestion']).'</span></label>';
		}
		else
		{
			$tmp = '<input type="checkbox" '.$class.' name="'.$e['name'].'" id="'.$e['name'].'" value="'.$e['value'].'" '.$checked.' />'.self::suggestion($e);
		}
		return $tmp;
	}

	/**
	 * Return multiple checkbox input fields
	 *
	 * @static
	 * @param array		$e Field data
	 * @return string
	 */
	public static function mcheckbox($e, $req = '')
	{
		$inline = false;

		if (isset($e['extra']))
		{
			$class = (strstr($e['extra'], 'class="'))
				? str_replace('class="', 'class="check ', $e['extra'])
				: 'class="check" '.$e['extra'];

			// is inline?
			$inline = (strstr($e['extra'], 'xinline') != '');
		}
		else
		{
			$class = 'class="check"';
		}

		$tmp = '';
		if (!empty($e['options'][0]))
		{
			$error = isset($e['error'])
				? 'class="error"'
				: '';

			$v = $e['options'][1];
			$o = $e['options'][2];

			$c = 0;
			foreach ($e['options'][0] as $i)
			{
				$checked = (isset($e['checked']) && in_array($i->$v, $e['checked']))
					? 'checked="checked"'
					: '';

				if ($inline && is_null($e['label']))
				{
					$tmp .= '<div class="checkbox"><input type="checkbox" '.$class.' name="'.$e['name'].'[]" id="'.$e['name'].'_'.$c.'" value="'.$i->$v.'" '.$checked.' /><label '.$error.' for="'.$e['name'].'_'.$c.'">'.stripslashes($i->$v).'</label></div>';
				}
				else
				{
					$tmp .= '<div class="checkbox"><label '.$error.' for="'.$e['name'].'_'.$c.'"><input type="checkbox" '.$class.' name="'.$e['name'].'[]" id="'.$e['name'].'_'.$c.'" value="'.$i->$v.'" '.$checked.' />'.stripslashes($i->$v).'</label></div>';
				}
				$c++;
			}
		}
		return $tmp.self::suggestion($e);
	}

	/**
	 * Return radio input field
	 *
	 * @static
	 * @param array		$e Field data
	 * @return string
	 */
	public static function radio($e, $req = '')
	{
		$inline = false;

		$error = isset($e['error'])
            ? 'class="error"'
            : '';

		if (isset($e['extra']))
		{
			$class = (strstr($e['extra'], 'class="'))
				? str_replace('class="', 'class="radio ', $e['extra'])
				: 'class="radio" '.$e['extra'];

			// is inline
			if (strstr($e['extra'], 'inline') != '')
			{
				$inline = true;
				$br = '';
			}
		}
		else
		{
			$class = 'class="radio"';
		}

		$tmp = '';
		if (!empty($e['options'][0]))
		{
			$v = $e['options'][1];
			$o = $e['options'][2];

			$c = 0;
			foreach ($e['options'][0] as $i)
			{
				$checked = (isset($e['checked']) && $e['checked'] == $i->$v)
					? 'checked="checked"'
					: '';

				if ($inline)
				{
					$tmp .= '<div class="radiobox"><input type="radio" '.$class.' name="'.$e['name'].'" id="'.$e['name'].'_'.$c.'" value="'.$i->$v.'" '.$checked.' /> <label for="'.$e['name'].'_'.$c.'" '.$error.'>'.stripslashes($i->$o).'</label></div>';
				}
				else
				{
					$tmp .= '<div class="radiobox"><label for="'.$e['name'].'_'.$c.'" '.$error.'><input type="radio" '.$class.' name="'.$e['name'].'" id="'.$e['name'].'_'.$c.'" value="'.$i->$v.'" '.$checked.' /> '.stripslashes($i->$o).'</label> </div>';
				}
				$c++;
			}
		}

		if ($inline)
		{
            // NOTE: this works only with TailwindCSS
			$tmp = '<div class="flex flex-col md:flex-row gap-4">'.$tmp.'</div>';
		}

		return $tmp.self::suggestion($e);
	}

	/**
	 * Return a single radio input field
	 *
	 * @static
	 * @param array		$e Field data
	 * @return string
	 */
	public static function singleradio($e, $req = '')
	{
		if (isset($e['extra']))
		{
			$class = (strstr($e['extra'], 'class="'))
				? str_replace('class="', 'class="radio ', $e['extra'])
				: 'class="radio" '.$e['extra'];

			// is inline
			if (strstr($e['extra'], 'inline') != '')
			{
				$inline = true;
			}
		}
		else
		{
			$class = 'class="radio"';
		}

		$checked = (isset($e['checked']) && $e['checked'] == $e['name'])
			? 'checked="checked"'
			: '';

		$tmp = '<input type="radio" '.$class.' name="'.$e['value'].'" id="'.$e['name'].'" value="'.$e['name'].'" '.$checked.' /> ';

		return $tmp.self::suggestion($e);
	}

	/**
	 * Return textarea field
	 *
	 * @static
	 * @param array		$e Field data
	 * @return string
	 */
	public static function textarea($e, $req = '')
	{
		$textra = (isset($e['extra']))
			? $e['extra']
			: '';

        $value = (empty($e['value']) || is_null($e['value']))
            ? ''
            : stripslashes($e['value']);

		return '<textarea cols="60" rows="8" name="'.$e['name'].'" id="'.$e['name'].'" '.$textra.'>'.$value.'</textarea>'.self::suggestion($e);
	}

	/**
	 * Return select field
	 *
	 * @static
	 * @param array		$e Field data
	 * @return string
	 */
	public static function select($e, $req = '')
	{
		$sextra = (isset($e['extra']) && $e['extra'] != 'selectbox')
			? $e['extra']
			: '';

		$tmp = '';
		if (isset($e['multiple']))
		{
			$sextra = (strstr($sextra, 'class="') != '')
				? str_replace('class="', 'class="multiple_select ', $sextra)
				: 'class="multiple_select" '.$sextra;

			$tmp .= '<select '.$sextra.' name="'.$e['name'].'[]" id="'.$e['name'].'" multiple="multiple" size="'.intval($e['multiple']).'">';
		}
		else
		{
			$tmp .= '<select '.$sextra.' name="'.$e['name'].'" id="'.$e['name'].'">';
		}

        // options
        if (empty($e['options']))
        {
            $e['options'] = array([], '', '');
        }

        if ($e['options'][0] == 'template')
        {
            $empty = (isset($e['options'][5]) && !is_null($e['options'][5]))
                ? $e['options'][5]
                : null;

            $tmp .= self::get_options_template(
                $e['options'][1],
                $e['options'][2],
                $e['options'][3],
                $e['options'][4],
                $empty
            );
        }
        else
        {
            // options array = [array of objects, field for value, field for option, empty option]

            // empty option
            $empty = (isset($e['options'][3]) && !is_null($e['options'][3]))
                ? $e['options'][3]
                : null;

            $disabled = (isset($e['disabled']))
                ? $e['disabled']
                : '';

            $disabled2 = (isset($e['disabled2']))
                ? $e['disabled2']
                : '';

            $disabled3 = (isset($e['disabled3']))
                ? true
                : false;

            $tmp .= self::get_options(
                $e['options'][0],
                $e['options'][1],
                $e['options'][2],
                $e['value'],
                $empty,
                isset($i['multiple']),
                $disabled,
                $disabled2,
                $disabled3
            );
        }

/*
        // empty option
		if (isset($e['options'][3]) && !is_null($e['options'][3]))
		{
			if (is_array($e['options'][3]))
			{
				// selected
				if (isset($i['multiple']) && is_array($i['value']))
				{
					$sel = (in_array($e['options'][3][0], $e['value']))
						? 'selected="selected"'
						: '';
				}
				else
				{
					$sel = ($e['value'] == $e['options'][3][0])
						? 'selected'
						: '';
				}

				$tmp .= '<option value="'.$e['options'][3][0].'" '.$sel.'>'.$e['options'][3][1].'</option>';
			}
			else
			{
				// selected
				$sel = ($e['options'][3] == $e['value'])
						? 'selected'
						: '';

				$tmp .= '<option value="'.$e['options'][3].'" '.$sel.'>'.$e['options'][3].'</option>';
			}
		}

		// option 4: sections
		$section = (isset($e['options'][4]))
			? $e['options'][4]
			: '';

		// other options
		if (!empty($e['options'][0]))
		{
			$s = '';
			foreach ($e['options'][0] as $ii)
			{
				$sign = $dis = ' ';
				if (!empty($section) && !empty($ii->$section))
				{
					if ($ii->$section != $s)
					{
						$s = $ii->$section;
						$tmp .= '<option value="" disabled="disabled">'.$s.'</option>';
					}
					$sign = '&nbsp; &nbsp;';
				}

				// use a field to mark items to disable
				// $e['disabled'] contains the field name
				if (isset($e['disabled']))
				{
				    $disabled_label = $e['disabled'];
					if ($ii->$disabled_label > 0)
					{
						$dis = ' disabled="disabled"';
						$sign = (isset($e['disabled_sign']))
							? $e['disabled_sign']
							: 'x';
					}
					else
					{
						$sign = '&nbsp; &nbsp;';
					}
				}

				if (isset($e['disabled2']))
				{
                    $disabled_label = $e['disabled2'];
					if ($ii->$disabled_label < 0)
					{
						$dis = ' disabled="disabled"';
						$sign = '>';
					}
					else
					{
						$sign = '&nbsp; &nbsp;';
					}
				}

				$v = $e['options'][1];
				$o = $e['options'][2];

				// check for selected value
				if (is_array($e['value']))
				{
					$sel = (in_array($ii->$v, $e['value']))
						? 'selected'
						: '';
				}
				else
				{
					$sel = ($e['value'] == $ii->$v)
						? 'selected'
						: '';
				}

				// option 5: readonly emulation
				if (isset($e['options'][5]) && empty($sel) && $e['options'][5] == 'readonly')
				{
					$dis = ' disabled="disabled"';
				}

				if (!empty($sign))
				{
					// to do if needed
				}
				$tmp .= '<option value="'.$ii->$v.'" '.$sel.$dis.'>'.$sign.' '.stripslashes($ii->$o).'</option>';

			}
		}
*/
		$tmp .= '</select>';

		// container for select
		if (isset($e['extra']) && $e['extra'] == 'selectbox')
		{
			$tmp = '<div class="selectbox-container">'.$tmp.'</div>';
		}

		return $tmp.self::suggestion($e);
	}

	/**
	 * Return suggestion field
	 *
	 * @static
	 * @param array		$e Field data
	 * @return string
	 */
	public static function suggestion($e)
	{
		if (isset($e['suggestion']) && !empty($e['suggestion']))
		{
			// put the suggestion bottom
			$br = ($e['type'] == 'textarea' && !isset($e['nobr']))
				? BR
				: ' ';

			return (isset($e['nobrackets']))
                ? $br.'<span class="suggestion"> '.stripslashes($e['suggestion']).'</span>'
                : $br.'<span class="suggestion"> ('.stripslashes($e['suggestion']).')</span>';
		}
		return '';
	}

	/**
	 * Return button field
	 *
	 * @static
	 * @param array		$e Field data
	 * @return string
	 */
	public static function button($e)
	{
		switch($e['value'])
		{
			case 'submit':
				$buttons[1] = null;
				$btn_name = strrev($name);
				break;
			case 'reset':
				$buttons[0] = null;
				$btn_name = $e['name'];
				break;
			default:
				$btn_name = $e['name'];
				break;
		}

		return '<button name="'.$btn_name.'" type="'.$e['value'].'">'.$e['label'].'</button>';
	}

	/**
	 * Return buttons box
	 *
	 * @static
	 * @param array		$buttons Buttons array (can be an array with 3 elements (reset_button_label, submit_button_label, container_class)
	 *							or an array with 2 elements: the first is an array of buttons (where each button is an associative array with this
	 *							keys: type, name (optional), extra (optional), action (optional), label) and the second the container_class
	 * @param string	$form_id
	 * @param string	$submit_action
	 * @param string	$reset_action
	 * @return string
	 */
	public static function buttons($buttons, $form_id, $submit_action = '', $reset_action = '')
	{
		if (sizeof($buttons) >= 3)
		{
			// normal solution
			$reset = $submit = '';
			if (!is_null($buttons[0]))
			{
				$reset = (empty($reset_action))
					? '<button type="reset">'.$buttons[0].'</button>'
					: '<button type="button" '.$reset_action.'>'.$buttons[0].'</button>';
			}

			if (!is_null($buttons[1]))
			{
				$submit = (empty($submit_action))
					? '<button name="'.strrev($form_id).'" type="submit">'.$buttons[1].'</button>'
					: '<button name="'.strrev($form_id).'" id="'.strrev($form_id).'" type="button" '.$submit_action.'>'.$buttons[1].'</button>';
			}

            // is there an extra button?
            $extra = '';
            if (isset($buttons[3]) && is_array($buttons[3]))
            {
                switch ($buttons[3]['element'])
                {
                    case 'modal':
                        $click = '@click="popup(\''.$buttons[3]['url'].'\')"';
                        break;
                    case 'page':
                        $click = '@click="pager(\''.$buttons[3]['url'].'\')"';
                        break;
                    default:
                        $click = 'onclick="location.href=\''.$buttons[3]['url'].'\')"';
                        break;
                }

                $extra = '<button type="button" class="btn gray" '.$click.'>'.$buttons[3]['title'].'</button>';
            }

			return '<div class="'.$buttons[2].'">'.$reset.$submit.$extra.'</div>';
		}
		else
		{
			// personalized solution
			$tmp = '';
			foreach ($buttons[0] as $i)
			{
				// handle the name
				$name = '';
				if ($i['type'] == 'submit')
				{
					$name = 'name="'.strrev($form_id).'"';
				}
				elseif (isset($i['name']))
				{
					$name = 'name="'.$i['name'].'"';
				}

				$tmp .= '<button '.$name.' type="'.$i['type'].'" '.$i['extra'].' '.$i['action'].'>'.$i['label'].'</button>';
			}

			return '<div class="'.$buttons[1].'">'.$tmp.'</div>';
		}
	}

	/**
	 * Create captcha image
	 *
	 * @static
	 * @param integer	$length The length of captcha
	 * @param string	$font	The font to use
	 * @param array		$bg	    Background color
	 * @return image
	 */
	public static function captcha($length = 5, $font = 'espek___.ttf', $bg = array(255, 255, 255))
	{
		$_SESSION['captcha'] = X4Text_helper::random_string($length);
		// create image
		$size_x = 200;
		$size_y = 70;

		$img = imagecreatetruecolor($size_x,$size_y);
		$backgroung = imagecolorallocate($img, $bg[0], $bg[1], $bg[2]);
		// alpha channel for opacity
		//$color[] = imagecolorallocatealpha($img,110,110,110,50);
		$color[] = imagecolorallocatealpha($img,104,171,48,30);
		$color[] = imagecolorallocatealpha($img,187,0,102,40);
		$color[] = imagecolorallocatealpha($img,54,195,255,40);
		$color[] = imagecolorallocatealpha($img,254,135,2,40);
		$color[] = imagecolorallocatealpha($img,170,2,2,40);
		$color[] = imagecolorallocatealpha($img,255,193,7,40);

		shuffle($color);
		$colors = sizeof($color);

		// background
		imagefilledrectangle($img,0,0,$size_x-1,$size_y-1,$backgroung);

		$code = str_split($_SESSION['captcha']);
		// captcha
		foreach ($code as $k => $v)
		{
			// character
			imagettftext(
					 $img,
					 50,
					 -3+rand(0,15), // rotation
					 intval($k+0.8)*24+8,
					 58+rand(-8,8),
					 $color[$k%$colors],
					 $_SERVER['DOCUMENT_ROOT'].ROOT.'files/files/'.$font,
					 $v);
		}

		// output
		header('Content-Type: image/png');
		imagepng($img, null, 0, -1);
		imagedestroy($img);
		die;
	}

	/**
	 * Build options list
	 *
	 * @static
	 * @param array		$o Array of options as object
	 * @param string	$value	field name for option value
	 * @param string	$option field name for option name
	 * @param string	$selected selected value
	 * @param mixed		$empty option value (can be a string ONLY VALUE, or an array(VALUE, OPTION))
     * @param boolean   $multiple
	 * @param string	$disabled1 field name for disabling check value
	 * @param string	$disabled2 field name for disabling check value
	 * @param boolean	$disabled3 to disable all not selected options
	 * @return string
	 */
	public static function get_options(array $o, string $value, string $option, mixed $selected = '', mixed $empty = null, bool $multiple = false, string $disabled1 = '', string $disabled2 = '', bool $disabled3 = false)
	{
		$str = '';

		// empty option
		if (!is_null($empty))
		{
			if (is_array($empty))
			{
				// selected
				$sel = ($selected == $empty[0])
						? 'selected="selected"'
						: '';

				$str .= '<option value="'.$empty[0].'" '.$sel.'>'.$empty[1].'</option>';
			}
			else
			{
				// selected
				$sel = ($empty == $selected)
						? 'selected="selected"'
						: '';

				$str .= '<option value="'.$empty.'" '.$sel.'></option>';
			}
		}

		// other options
		if (!empty($o))
		{
			foreach ($o as $i)
			{
				$sign = $dis = ' ';
				if (!empty($disabled1))
				{
					if ($i->$disabled1 > 0)
					{
						$dis = ' disabled = "disabled"';
						$sign = 'x';
					}
				}

				if (!empty($disabled2))
				{
					if ($i->$disabled2 < 0)
					{
						$dis = ' disabled = "disabled"';
						$sign = '>';
					}
					else
					{
						$sign = '&nbsp; &nbsp;';
					}
				}

				// check for selected value
				if (is_array($selected))
				{
					$sel = (in_array($i->$value, $selected))
						? 'selected="selected"'
						: '';
				}
				else
				{
					$sel = ($selected == $i->$value)
						? 'selected="selected"'
						: '';
				}

				// to disable all not selected options
				if (empty($sel) && $disabled3)
				{
					$dis = ' disabled = "disabled"';
				}

                if (isset($i->$value) && isset($i->$option))
                {
                    $str .= '
                    <option value="'.$i->$value.'" '.$sel.$dis.'>'.$sign.' '.stripslashes($i->$option).'</option>';
                }
			}
		}
		return $str;
	}

    /**
	 * Build options list with template for Alpine.js
	 *
	 * @static
	 * @param string	$alpine_var
	 * @param string	$value	field name for option value
	 * @param string	$option field name for option name
     * @param string    $model
     * @param mixed     $empty
	 * @return string
	 */
	public static function get_options_template(string $alpine_var, string $value, string $option, string $model, mixed $empty = null)
    {
        $str = '';
        // empty option
        if (!is_null($empty))
        {
            if (is_array($empty))
            {
                /*
                // selected
                $sel = ($selected == $empty[0])
                        ? 'selected="selected"' '.$sel.'
                        : '';
                */
                $str .= '<option value="'.$empty[0].'" x-bind:selected="'.$empty[0].' === '.$model.'">'.$empty[1].'</option>';
            }
            else
            {
                /*
                // selected
                $sel = ($empty == $selected)
                        ? 'selected="selected"'
                        : '';
                */
                $str .= '<option value="'.$empty.'" x-bind:selected="'.$empty.' === '.$model.'"></option>';
            }
        }

		$str .= '<template x-for="item in '.$alpine_var.'" :key="item.'.$value.'">
                <option
                    :value="item.'.$value.'"
                    x-text="item.'.$option.'"
                    x-bind:selected="item.'.$value.' === '.$model.'"></option>
            </template>';
        return $str;
    }
}
