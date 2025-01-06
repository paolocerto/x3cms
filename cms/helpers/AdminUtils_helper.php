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
class AdminUtils_helper
{
    /**
     * flmngr links
     */
    public static $flmngr_links = [
        '<script src="//cdn.public.flmngr.com/'.FLMNGR_API_KEY.'/widgets.js"></script>',
        '<script src="//cdn.flmngr.com/widgets.js?apiKey='.FLMNGR_API_KEY.'"></script>'
    ];

    /**
     * Clean annoyng string
     */
    public static function flmngr(string $str) : string
    {
        return str_replace(self::$flmngr_links, '', $str);
    }

	/**
	 * Put the message into a session variable
	 */
	public static function set_msg(mixed $res, string $ok = _MSG_OK, string $ko = _MSG_ERROR, string $type = '') : Msg
	{
		$msg = new Msg();
        if (!empty($type))
        {
            $msg->message_type = $type;
            return $msg;
        }

		switch(gettype($res))
		{
			case 'boolean':
				if ($res)
				{
					$msg->message_type = 'success';
					$msg->message = $ok;
				}
				else
				{
					$msg->message_type = 'error';
					$msg->message = $ko;
				}
				break;
			case 'array':
				switch ($res[1])
				{
				case 0:
					$msg->message_type = 'error';
					$msg->message = $ko;
					break;
				default:
					$msg->message_type = 'success';
					$msg->message = $ok;
					break;
				}
				break;
			default:
				// is a string so is an error
				$msg->message_type = 'error';
				$msg->message = $ko;
				break;
		}
		return $msg;
	}

    /**
     * Build error message for file errors
     */
    public static function build_error_msg(array $error, array $file_array) : Msg
    {
        $str = [];
        $dict = new X4Dict_model(X4Route_core::$folder, X4Route_core::$lang);
        foreach ($error as $k => $v)
        {
            foreach ($v as $i)
            {
                // each error
                $str[] = $file_array[$k]._TRAIT_.$dict->get_word(strtoupper($i), 'msg');
            }
        }
        return AdminUtils_helper::set_msg(false, '', implode('<br />', $str));
    }

	/**
	 * Get User permission level on a table
	 */
	public static function get_ulevel(int $id_area, int $id_who, string $what) : mixed
	{
		$mod = new Permission_model();
		return $mod->get_upriv($id_area, $id_who, $what);
	}

    /**
	 * Get User permission level on an item
	 */
	public static function get_priv_level(int $id_area, string $what, int $id_what, string $action = '') : int
	{
        if ($_SESSION['level'] == 5)
        {
            return 5;
        }

		$mod = new Permission_model();
        $priv = $mod->check_priv($_SESSION['xuid'], $what, $id_what, $id_area);

        // limited actions for not superadmins
        $limited = ['create', 'delete'];

        if (
            $priv > 3 &&
            in_array($what, $mod->superadmin_privtypes) &&
            !empty($action) &&
            in_array($action, $limited)
        )
        {
            return 3;
        }
        else
        {
            return $priv;
        }
	}

	/**
	 * Check User permission level on a record of a table
     */
	public static function chk_priv_level(int $id_area, string $what, int $id_what, string $action) : mixed
	{
		// get priv level on the item
		$level = self::get_priv_level($id_area, $what, $id_what, $action);

		// if level lower than required redirect
		if ($level < self::action2level($action))
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
	 * Set User priv on an item
	 */
	public static function set_priv(int $id_who, int $id_what, string $what, int $id_area) : void
	{
		$mod = new Permission_model();
		$array[] = array(
            'action' => 'insert',
            'id_what' => $id_what,
            'id_user' => $id_who,
            'level' => 4
        );
        $mod->pexec($what, $array, $id_area);
	}

    /**
	 * Delete Users priv on an item
	 */
	public static function delete_priv(string $what, int $id_what) : void
	{
		$mod = new Permission_model();
		$mod->deleting_by_what($what, $id_what);
	}

    /**
	 * Get the required level for each action
	 */
    public static function action2level(string $action) : int
    {
        // default level for unexpected actions
        $level = 3;
        switch ($action)
        {
            case 'read':
                $level = 1;
                break;
            case 'edit':
                $level = 2;
                break;
            case 'manage':
            case 'xon':
                $level = 3;
                break;
            case 'create':
            case 'xlock':
            case 'delete':
                $level = 4;
                break;
        }
        return $level;
    }

    /**
	 * Get value to set submit button over edit item
     * Check if the user can edit it
	 */
	public static function submit_btn(int $id_area, string $what, int $id_what, int $xlock, string $label = _SUBMIT) : mixed
	{
		// get priv level on the item
		$level = self::get_priv_level($id_area, $what, $id_what);

        // expected results
        // xlock == 0 and level < 2 => false
        // xlock == 1 and level < 3 => false
        $chk = $xlock
            ? ($level >= 3)
            : ($level >= 2);

        // form dictionary should be already loaded
        return ($chk)
            ? $label
            : null;
	}

	/**
	 * Check if a file or a directory is writable
	 */
	public static function chk_writable(string $path) : mixed
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
	 * Build statuses info
	 */
	public static function statuses(stdClass $obj, array $fields = ['xon', 'xlock']) : array
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
	 */
	public static function link(string $action, string $url, array $statuses = [], string $title = '') : string
	{
		switch ($action)
        {
            case 'edit':
                return '<a class="link" @click="popup(\''.BASE_URL.$url.'\')" title="'._EDIT.'">
                    <i class="fa-solid fa-lg fa-fw fa-pen-to-square"></i>
                </a>';
                break;
            case 'settings':
                return '<a class="link" @click="popup(\''.BASE_URL.$url.'\')" title="'._SETTINGS.'">
                    <i class="fa-solid fa-lg fa-fw fa-sliders"></i>
                </a>';
            case 'xon':
                return '<a class="link" @click="setter(\''.BASE_URL.$url.'\')" title="'._STATUS.' '.$statuses['xon']['label'].'">
                    <i class="far fa-lightbulb fa-lg fa-fw '.$statuses['xon']['class'].'"></i>
                </a>';
                break;
            case 'xlock':
                return '<a class="link" @click="setter(\''.BASE_URL.$url.'\')" title="'._STATUS.' '.$statuses['xlock']['label'].'">
                    <i class="fa-solid fa-lg fa-fw '.$statuses['xlock']['class'].'"></i>
                </a>';
                break;
            case 'delete':
                return '<a class="link" @click="popup(\''.BASE_URL.$url.'\')" title="'._DELETE.'">
                    <i class="fa-solid fa-lg fa-fw fa-trash warn"></i>
                </a>';
                break;
            case 'refresh':
                $title = (empty($title))
                    ? _GENERATE
                    : $title;
                return '<a class="link" @click="setter(\''.BASE_URL.$url.'\')" title="'.$title.'">
                    <i class="fa-solid fa-rotate fa-lg fa-fw"></i>
                </a>';
                break;
            case 'duplicate':
                $title = (empty($title))
                    ? _DUPLICATE
                    : $title;
                return '<a class="link" @click="popup(\''.BASE_URL.$url.'\')" title="'.$title.'">
                    <i class="fa-solid fa-copy fa-lg fa-fw"></i>
                </a>';
                break;
            case 'memo':
                // $url have to be structured this way: page_url:lang
                return '<a class="link" @click="popup(\''.BASE_URL.'memo/index/'.$url.'\')" title="'.$title.'">
                    <i class="fas fa-thumbtack fa-lg fa-fw '.$statuses['n'].'"></i>
                </a>';
                break;
            default:
                return '';
                break;
        }
	}

    /**
	 * Return recorded selected options
	 */
	public static function decompose(string $str, array $fields, int $move = 0, int $echo = 0) : string
	{
        $res = '';
		if (!empty($str))
		{
			if ($echo)
			{
                // is an AJAX call so we have to replace some character
                $str = str_replace(['=', '*', '@', ',', '_.', '+'], ['%22%3A0%2C%22', '%22%3A0%7D%2C%7B%22', '%22%3A%22', '%22%2C%22', '%2F', ' '], $str);
                $str = urldecode($str);
			    $str = str_replace(array('_ZZZ_', '_XXX_'), array(NL, '#'), $str);
			}
            // for values
            $data = [];
            // table head
            $res = '<tr>';
            foreach($fields as $f)
            {
                $data[] = $f['name'];
                $label = isset($f['label'])
                    ? $f['label']
                    : $f['name'];
                $res .= '<th>'.$label.'</th>';
            }
            $res .= '<th></th></tr>';

			$c = 0;
			$items = json_decode($str, true);
            if (is_array($items))
            {
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
                                $actions = '<a class="link" @click="moveItem('.$c.', 1)"><i class="fa-solid fa-lg fa-fw fa-chevron-down"></i></a>';
                            }
                            if ($k > 0)
                            {
                                // up
                                $actions .= '<a class="link" @click="moveItem('.$c.', -1)"><i class="fa-solid fa-lg fa-fw fa-chevron-up"></i></a>';
                            }
                        }
                        $res .= '<tr class="row'.$c.'" rel="'.$c.'">';
                        // show values
                        foreach ($data as $i)
                        {
                            $res .= (is_array($v[$i]))
                                ? '<td>'.json_encode($v[$i]).'</td>'
                                : '<td>'.$v[$i].'</td>';
                        }

                        $res .= '<td class="text-right">
                                    '.$actions.'
                                    <a class="link" @click="editItem('.$c.')"><i class="fa-solid fa-lg fa-fw fa-pen-to-square"></i></a>
                                    <a class="link" @click="deleteItem('.$c.')"><i class="fa-solid fa-lg fa-fw fa-trash warn"></i></a>
                                </td>
                            </tr>';
                        $c++;
                    }
                }
            }
		}
        return $res;
    }

    /**
	 * Build the language switcher
	 */
	public static function lang_switcher(int $id_area, string $lang, string $url = '') : string
	{
        $res = '';
        $mod = new Language_model();
        $languages = $mod->get_alanguages($id_area);
        if (sizeof($languages) > 1)
        {
            $src = ['XAREAX', 'XLANGX'];
            $res .= '<div class="switcher text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
            foreach ($languages as $i)
            {
                $on = ($i->code == $lang)
                    ? 'class="link"'
                    : 'class="dark"';
                $res .= '<a '.$on.' @click="pager(\''.BASE_URL.str_replace($src, [$id_area, $i->code], $url).'\')" title="'._SWITCH_LANGUAGE.'">'.ucfirst($i->language).'</a>';
            }
            $res .= '</div>';
        }
        return $res;
	}

    /**
	 * Build the area switcher
	 */
	public static function area_switcher(int $id_area, string $lang, array $areas, string $url = '') : string
	{
        $res = '';
        $src = ['XAREAX', 'XLANGX'];
        $res = '<div class="switcher text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">';
        foreach ($areas as $i)
        {
            $on = ($i->id == $id_area)
                ? 'class="link"'
                : 'class="dark"';
            $res .= '<a '.$on.' @click="pager(\''.BASE_URL.str_replace($src, [$i->id, $lang], $url).'\')" title="'._SWITCH_AREA.'">'.ucfirst($i->name).'</a>';
        }
        $res .= '</div>';
        return $res;
	}

    /**
     * Add note to text
     */
    public static function add_note(string $old, string $new) : mixed
    {
        if (!empty($new))
        {
            $tmp = X4Text_helper::clean_text($new);
            if (!empty($tmp))
            {
                $tmp = '#'.$_SESSION['xuid'].'-'.$_SESSION['username'].'-'.date('Y-m-d H:i:s').NL.$tmp;
                $a = array_filter([$old, $tmp]);
                return implode(NL, $a);
            }
        }
        return false;
    }
}

/**
 * Msg object
 *
 * @package		X4WEBAPP
 */
class Msg
{
	public $message_type = 'error';
	public $message = '';
	public $command = [];
	public $update = [];
	public $redirect;
}
