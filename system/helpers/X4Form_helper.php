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
     * $extra extra value for the form (eg. enctype="multipart/form-data")
	 */
	public static function doform(
        string $name,
        string $action,
        array $fields,
        array $buttons = [_RESET, _SUBMIT, 'text-center'],
        string $method = 'post',
        string $extra = '',
        string $submit_action = '',
        string $reset_action = '',
        bool $inline = false
    ) : string
	{
		if (!empty($submit_action))
        {
			$extra .= ' onsubmit="return false"';
        }

		// sanitize action
		$action = htmlentities(strip_tags($action), ENT_QUOTES, 'UTF-8', false);

        // add x4 token field security token
        $fields[] = array(
            'label' => null,
            'type' => 'hidden',
            'value' => md5($_SESSION['token'].$name),
            'name' => 'x4token'
        );

        $body = self::doform_section($fields, '', $inline);
		if (!empty($buttons))
		{
			$body .= self::buttons($buttons, $name, $submit_action, $reset_action);
		}
		return '<form id="'.$name.'" name="'.$name.'" action="'.$action.'" method="'.$method.'" '.$extra.'>
                '.$body.'
            </form>';
	}

	/**
	 * Build a form section
	 */
	public static function doform_section(array $fields, string $name = '', bool $inline = false) : string
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

        $body = '';
        foreach ($fields as $i)
        {
            $req = '';
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
                    $body .= '
                    <label for="'.$i['name'].'" '.$err.'>'.stripslashes($i['label']).$req;
                }
                else
                {
                    $body .= '
                    <label for="'.$i['name'].'" '.$err.'>'.stripslashes($i['label']).$req.'</label>';
                }
            }

            switch($i['type'])
            {
            case 'loading':
                $body .= '<div id="loading" style="visibility:hidden;"><img src="'.ROOT.'files/files/ajax-loader.gif" alt="Loading..." /></div>';
                break;
            case 'slider':
                $body .= self::slider($i['name']);
                break;
            case 'html':
                $body .= $i['value'];
                break;
            case 'button':
                $body .= '<div class="xcenter">'.self::button($i).'</div>';
                break;
            default:
                // for: fieldset, hidden, text, file, password, checkbox, radio, texarea, select
                $t = $i['type'];
                $body .= self::$t($i, $req);
                break;
            }

            // close label
            if (($inline || (isset($i['extra']) && strstr($i['extra'], 'inline') != '')) && !is_null($i['label']))
            {
                $body .= '</label>';
            }
        }
		return $body;
	}

	/**
	 * Return slider
	 */
	public static function slider(string $name) : string
	{
		return '<div id="'.$name.'" class="slider"><div class="knob"></div></div>';
	}

	/**
	 * Return fieldset
	 */
	public static function fieldset(array $e, string $req = '') : string
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
	 */
	public static function hidden(array $e, string $req = '') : string
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
	 */
	public static function text(array $e, string $req = '') : string
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
	 */
	public static function range(array $e, string $req = '') : string
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
	 */
	public static function file(array $e, string $req = '') : string
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
	 */
	public static function password(array $e, string $req = '') : string
	{
		$iextra = (isset($e['extra']))
			? $e['extra']
			: '';

		return '<input type="password" name="'.$e['name'].'" id="'.$e['name'].'" value="'.$e['value'].'" '.$iextra.' />'.self::suggestion($e);
	}

	/**
	 * Return checkbox input field
	 */
	public static function checkbox(array $e, string $req = '') : string
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

			$tmp = '<div class="check"><label '.$label_class.' for="'.$e['name'].'"><input type="checkbox" '.$class.' name="'.$e['name'].'" id="'.$e['name'].'" value="'.$e['value'].'" '.$checked.' /> &nbsp;&nbsp;<span>'.stripslashes($e['suggestion']).'</span></label></div>';
		}
		else
		{
			$tmp = '<input type="checkbox" '.$class.' name="'.$e['name'].'" id="'.$e['name'].'" value="'.$e['value'].'" '.$checked.' />'.self::suggestion($e);
		}
		return $tmp;
	}

	/**
	 * Return multiple checkbox input fields
	 */
	public static function mcheckbox(array $e, string $req = '') : string
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
					$tmp .= '<div class="check"><label '.$error.' for="'.$e['name'].'_'.$c.'"><input type="checkbox" '.$class.' name="'.$e['name'].'[]" id="'.$e['name'].'_'.$c.'" value="'.$i->$v.'" '.$checked.' />'.stripslashes($i->$o).'</label></div>';
				}
				$c++;
			}
		}
		return $tmp.self::suggestion($e);
	}

	/**
	 * Return radio input field
	 */
	public static function radio(array $e, string $req = '') : string
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
	 */
	public static function singleradio(array $e, string $req = '') : string
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
	 */
	public static function textarea(array $e, string $req = '') : string
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
	 */
	public static function select(array $e, string $req = '') : string
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

            $disabled = (isset($e['disabled']) && is_array($e['disabled']))
                ? $e['disabled']
                : [''];

            $tmp .= self::get_options(
                $e['options'][0],
                $e['options'][1],
                $e['options'][2],
                $e['value'],
                $empty,
                $disabled
            );
        }

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
	 */
	public static function suggestion(array $e) : string
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
	 * Button field
	 */
	public static function button(array $e) : string
	{
		switch($e['value'])
		{
			case 'submit':
				$buttons[1] = null;
				$btn_name = strrev($e['name']);
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
	 * Buttons array (can be an array with 3 elements (reset_button_label, submit_button_label, container_class)
	 * or an array with 2 elements: the first is an array of buttons (where each button is an associative array with this
	 * keys: type, name (optional), extra (optional), action (optional), label) and the second the container_class
	 */
	public static function buttons(array $buttons, string $form_id, string $submit_action = '', string $reset_action = '') : string
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
                    case 'call':
                        $click = '@click="'.$buttons[3]['function'].'"';
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
	 */
	public static function captcha(int $length = 5, string $font = 'espek___.ttf', array $bg = [255, 255, 255])
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
     * $disabled is an array with [field to check, relation, value]
	 */
	public static function get_options(
        array $o,
        string $value,
        string $option,
        mixed $selected = '',
        mixed $empty = null,
        array $disabled = ['']
    ) : string
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
				if (!empty($disabled[0]) && $disabled[0] != 'NOT_SELECTED')
				{
                    $field = $disabled[0];
                    eval('$chk = '.$i->$field.$disabled[1].$disabled[2].';');
					if ($chk)
					{
						$dis = ' disabled = "disabled"';
						$sign = 'x';
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
				if (empty($sel) && $disabled[0] == 'NOT_SELECTED')
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
	 */
	public static function get_options_template(string $alpine_var, string $value, string $option, string $model, mixed $empty = null) : string
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
