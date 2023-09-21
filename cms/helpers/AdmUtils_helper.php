<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright		(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

/**
 * Helper for admin operations
 *
 * @package X3CMS
 */
class AdmUtils_helper
{
	/**
	 * Put the message into a session variable
	 *
	 * @static
	 * @param mixed		$res boolean/array query result
	 * @param string	$ok message if all run fine
	 * @param string	$ko error message
	 * @return void
	 */
	public static function set_msg($res, string $ok = _MSG_OK, string $ko = _MSG_ERROR)
	{
		$msg = new Msg();
		//$close = '<div id="close-modal" class="zerom double-gap-top white" title="'._CLOSE.'"><i class="fas fa-times fa-lg"></i></div>';
		switch(gettype($res))
		{
			case 'boolean':
				if ($res)
				{
					$msg->message_type = 'success';
					$msg->message = $ok;
					//$msg->message_close = '';
				}
				else
				{
					$msg->message_type = 'error';
					$msg->message = $ko;
					//$msg->message_close = $close;
				}
				break;
			case 'array':
				switch ($res[1])
				{
				case 0:
					$msg->message_type = 'error';
					$msg->message = $ko;
					//$msg->message_close = $close;
					break;
				default:
					$msg->message_type = 'success';
					$msg->message = $ok;
					//$msg->message_close = '';
					break;
				}
				break;
			default:
				// is a string so is an error
				$msg->message_type = 'error';
				$msg->message = $ko;
				//$msg->message_close = $close;
				break;
		}
		return $msg;
	}

	/**
	 * Get User permission level on a record of a table
	 *
	 * @static
	 * @param   integer	$id_who User ID
	 * @param   string	$what Privilege type
	 * @param   integer	$id_what Item ID
     * @param   integer	$id_area
	 * @return  integer	Permission level
	 */
	public static function get_priv_level(int $id_who, string $what, int $id_what, int $id_area = 0)
	{
		$mod = new Permission_model();
		return $mod->check_priv($id_who, $what, $id_what, $id_area);
	}

	/**
	 * Get User permission level on a table
	 *
	 * @static
     * @param   integer	$id_area
	 * @param   integer	$id_who User ID
	 * @param   string	$what Privilege type
	 * @return  integer	Permission level
	 */
	public static function get_ulevel(int $id_area, int $id_who, string $what)
	{
		$mod = new Permission_model();
		return $mod->get_upriv($id_area, $id_who, $what);
	}

	/**
	 * Check User permission level on a record of a table
	 *
	 * @static
     * @param   integer	$id_area
	 * @param   integer	$id_who User ID
	 * @param   string	$what Privilege type
	 * @param   integer	$id_what Item ID
	 * @param   integer	$value Privilege value
	 * @param   boolean	$force Enable check even if User is an administrator
	 * @return  void
	 */
	public static function chklevel(int $id_area, int $id_who, string $what, int $id_what, int $value, bool $force = false)
	{
		// if not administrator with god permission
		if ($_SESSION['level'] < 4 || $force)
		{
			// get level
			$level = self::get_priv_level($id_who, $what, $id_what, $id_area);

			// if level lower than required redirect
			if ($level < $value)
			{
				header('Location: '.ROOT.X4Route_core::$area.'/msg/message/_msg_error');
				die;
			}
		}
	}

	/**
	 * Check User permission level on a record of a table
	 *
	 * @static
     * @param   integer	$id_area
	 * @param   integer	$id_who User ID
	 * @param   string	$what Privilege type
	 * @param   integer	$id_what Item ID
	 * @param   integer	$value Privilege value
	 * @return  null or object
	 */
	public static function chk_priv_level(int $id_area, int $id_who, string $what, int $id_what, int $value)
	{
		// get level
		$level = self::get_priv_level($id_who, $what, $id_what, $id_area);

		// if level lower than required redirect
		if ($level < $value)
		{
			$dict = new X4Dict_model(X4Route_core::$folder, X4Route_core::$lang);
			$msg = $dict->get_word('_NOT_PERMITTED', 'msg');
			return self::set_msg(false, '', $msg);
		}
		else
		{
			return null;
		}
	}

	/**
	 * Check User permission level on a record of a table
	 *
	 * @static
	 * @param   integer	$id_area Area ID
	 * @param   integer	$id_who User ID
	 * @param   string	$what Privilege type
	 * @param   integer	$value Privilege value
	 * @return  null or object
	 */
	public static function chk_upriv_level(int $id_area, int $id_who, string $what, int $value)
	{
		// get level
		$level = self::get_ulevel($id_area, $id_who, $what);

		// if level lower than required redirect
		if ($level < $value)
		{
			$dict = new X4Dict_model(X4Route_core::$folder, X4Route_core::$lang);
			$msg = $dict->get_word('_NOT_PERMITTED', 'msg');
			return self::set_msg(false, '', $msg);
		}
		else
		{
			return null;
		}
	}

	/**
	 * Check if a file or a directory is writable
	 *
	 * @static
	 * @param   string	$path File or Directory path
	 * @return  null or object
	 */
	public static function chk_writable(string $path)
	{
		// if level lower than required redirect
		if (!is_writable($path))
		{
			$dict = new X4Dict_model(X4Route_core::$folder, X4Route_core::$lang);
			$msg = $dict->get_word('_NOT_WRITEABLE', 'msg');
			return self::set_msg(false, '', $msg.' <b>'.$path.'</b>');
		}
		else
		{
			return null;
		}
	}

    /**
	 * Build a simple breadcrumb
	 *
	 * @static
	 * @param array		$array array of pages
	 * @param string	$sep separator
	 * @param boolean	$home show the home page link in navbar
	 * @return string
	 */
	public static function navbar($array, $sep = ' > ', $home = true)
	{
		$str = '';
		if (!empty($array))
		{
			// chain of pages
			$item = array_pop($array[0]);

			// additional URL params
			$url_params = (isset($array[1]))
				? $array[1]
				: array();

			foreach ($array[0] as $i)
			{
				// handle params
				$param = (isset($url_params[$i->url]))
					? '/'.$url_params[$i->url]
					: '';

                // is the URL the home page?
				$url = ($i->url == 'home')
					? 'home/dashboard'
					: $i->url;

				// add a crumb
				if ($home || $i->url != 'home')
				{
					$str .= '<a @click="pager(\''.BASE_URL.$url.$param.'\')" title="'.stripslashes($i->description).'">'.stripslashes($i->name).'</a><span>'.$sep.'</span>';
				}
			}
			// do we have to show home?
			if ($home || $item->url != 'home')
			{
				$str .= '<span>'.stripslashes($item->name).'</span>';
			}
		}
		return $str.'&nbsp;';
	}

    /**
	 * Build statuses info
	 *
	 * @static
	 * @param   object	$obj to analyze
	 * @return  array
	 */
	public static function statuses(stdClass $obj, array $fields = ['xon', 'xlock'])
	{
        // available options
        $options = [
            'xon' => [_ON, 'on', _OFF, 'off'],
            'xlock' => [_LOCKED, 'fa-lock', _UNLOCKED, 'fa-unlock'],
            'hidden' => [_HIDDEN, 'fa-link-slash', _VISIBLE, 'fa-link'],
        ];

        // where we store info
        $status = [];
        foreach($fields as $i)
        {
            if ($obj->$i)
            {
                $status[$i]['label'] = $options[$i][0];
                $status[$i]['class'] = $options[$i][1];
            }
            else
            {
                $status[$i]['label'] = $options[$i][2];
                $status[$i]['class'] = $options[$i][3];
            }
        }
        return $status;
	}

    /**
	 * Build admin links
	 *
	 * @static
	 * @param   string	$action
	 * @param   string	$url
     * @param   array   $statuses
     * @param   string  $title
	 * @return  string
	 */
	public static function link(string $action, string $url, array $statuses = [], $title = '')
	{
		switch ($action)
        {
            case 'edit':
                return '<a class="link" @click="popup(\''.BASE_URL.$url.'\')" title="'._EDIT.'">
                    <i class="fa-solid fa-lg fa-pen-to-square"></i>
                </a>';
                break;
            case 'settings':
                return '<a class="link" @click="popup(\''.BASE_URL.$url.'\')" title="'._SETTINGS.'">
                    <i class="fa-solid fa-lg fa-sliders"></i>
                </a>';
            case 'xon':
                return '<a class="link" @click="setter(\''.BASE_URL.$url.'\')" title="'._STATUS.' '.$statuses['xon']['label'].'">
                    <i class="far fa-lightbulb fa-lg '.$statuses['xon']['class'].'"></i>
                </a>';
                break;
            case 'xlock':
                return '<a class="link" @click="setter(\''.BASE_URL.$url.'\')" title="'._STATUS.' '.$statuses['xlock']['label'].'">
                    <i class="fa-solid fa-lg '.$statuses['xlock']['class'].'"></i>
                </a>';
                break;
            case 'delete':
                return '<a class="link" @click="popup(\''.BASE_URL.$url.'\')" title="'._DELETE.'">
                    <i class="fa-solid fa-lg fa-trash warn"></i>
                </a>';
                break;
            case 'refresh':
                $title = (empty($title))
                    ? _GENERATE
                    : $title;
                return '<a class="link" @click="setter(\''.BASE_URL.$url.'\')" title="'.$title.'">
                    <i class="fa-solid fa-rotate fa-lg"></i>
                </a>';
                break;
            case 'duplicate':
                $title = (empty($title))
                    ? _DUPLICATE
                    : $title;
                return '<a class="link" @click="popup(\''.BASE_URL.$url.'\')" title="'.$title.'">
                    <i class="fa-solid fa-copy fa-lg"></i>
                </a>';
                break;

        }
	}

    /**
	 * Return recorded selected options
	 *
	 * @param   string 	$str Encoded options
     * @param   array   $fields structure
	 * @param   boolean	$move With or without direction buttons
	 * @param   boolean	$echo Return or echo
	 * @return  string
	 */
	public static function decompose(string $str, array $fields, int $move = 0, int $echo = 0)
	{
        $res = '';
		if (!empty($str))
		{
            // replace substitution
            $str = str_replace(['@', '._', ',', '_.', '+'], ['%3A', '%22', '%2C', '%2F', ' '], $str);
			$str = urldecode($str);
			if ($echo)
			{
                // is an AJAX call so we have to replace some character
			    $str = str_replace(array('_ZZZ_', '_XXX_'), array(NL, '#'), $str);
			}
            // for values
            $data = [];
            // table head
            $res = '<tr>';
            foreach($fields as $f)
            {
                $data[] = $f['name'];
                $res .= '<th>'.$f['name'].'</th>';
            }
            $res .= '<th></th></tr>';

			$c = 0;
			$items = json_decode($str, true);
            $n = sizeof($items);
			foreach ($items as $k => $v)
			{
                $actions = '';
			    if ($v != [])
				{
                    if ($move)
                    {
                        if ($k < $n - 1)
                        {
                            // down
                            $actions = '<a class="link" @click="moveItem('.$c.', 1)"><i class="fa-solid fa-lg fa-chevron-down"></i></a>';
                        }
                        if ($k > 0)
                        {
                            // up
                            $actions .= '<a class="link" @click="moveItem('.$c.', -1)"><i class="fa-solid fa-lg fa-chevron-up"></i></a>';
                        }
                    }
                    $res .= '<tr class="row'.$c.'" rel="'.$c.'">';
                    // show values
                    foreach ($data as $i)
                    {
                        $res .= '<td>'.$v[$i].'</td>';
                    }

                    $res .= '<td class="space-x-2 text-right">
                                '.$actions.'
                                <a class="link" @click="editItem('.$c.')"><i class="fa-solid fa-lg fa-pen-to-square"></i></a>
                                <a class="link" @click="deleteItem('.$c.')"><i class="fa-solid fa-lg fa-trash warn"></i></a>
                            </td>
                        </tr>';
                    $c++;
                }
			}
		}
        return $res;
    }
}

/**
 * Msg object
 *
 * @package		X4WEBAPP
 */
class Msg
{
	//public $id;
	public $message_type = 'error';
	//public $message_close = '';
	public $message = '';
	public $command = array();
	public $update = array();
	public $redirect;
}
