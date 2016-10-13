<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
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
	public static function set_msg($res, $ok = _MSG_OK, $ko = _MSG_ERROR)
	{
		$msg = new Msg();
		$close = '<div id="close-modal" class="zerom double-gap-top white" title="'._CLOSE.'"><i class="fa fa-times fa-lg"></i></div>';
		switch(gettype($res))
		{
			case 'boolean':
				if ($res)
				{
					$msg->message_type = 'success';
					$msg->message = $ok;
					$msg->message_close = '';
				}
				else
				{
					$msg->message_type = 'error';
					$msg->message = $ko;
					$msg->message_close = $close;
				}
				break;
			case 'array':
				switch ($res[1])
				{
				case 0:
					$msg->message_type = 'error';
					$msg->message = $ko;
					$msg->message_close = $close;
					break;
				default:
					$msg->message_type = 'success';
					$msg->message = $ok;
					$msg->message_close = '';
					break;
				}
				break;
			default:
				// is a string so is an error
				$msg->message_type = 'error';
				$msg->message = $ko;
				$msg->message_close = $close;
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
	 * @return  integer	Permission level
	 */
	public static function get_priv_level($id_who, $what, $id_what)
	{
		$mod = new Permission_model();
		$level = $mod->check_priv($id_who, $what, $id_what);
		return $level;
	}
	
	/**
	 * Get User permission level on a table
	 *
	 * @static
	 * @param   integer	$id_who User ID
	 * @param   string	$what Privilege type
	 * @return  integer	Permission level
	 */
	public static function get_ulevel($id_area, $id_who, $what)
	{
		$mod = new Permission_model();
		$level = $mod->get_upriv($id_area, $id_who, $what);
		return $level;
	}
	
	/**
	 * Check User permission level on a record of a table
	 *
	 * @static
	 * @param   integer	$id_who User ID
	 * @param   string	$what Privilege type
	 * @param   integer	$id_what Item ID
	 * @param   integer	$value Privilege value
	 * @param   boolean	$force Enable check even if User is an administrator
	 * @return  void
	 */
	public static function chklevel($id_who, $what, $id_what, $value, $force = false)
	{
		// if not administrator with god permission
		if ($_SESSION['level'] < 4 || $force) 
		{
			// get level
			$level = self::get_priv_level($id_who, $what, $id_what);
			
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
	 * @param   integer	$id_who User ID
	 * @param   string	$what Privilege type
	 * @param   integer	$id_what Item ID
	 * @param   integer	$value Privilege value
	 * @return  null or object
	 */
	public static function chk_priv_level($id_who, $what, $id_what, $value)
	{
		// get level
		$level = self::get_priv_level($id_who, $what, $id_what);
		
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
	 * @param   integer	$id_what Item ID
	 * @param   integer	$value Privilege value
	 * @return  null or object
	 */
	public static function chk_upriv_level($id_area, $id_who, $what, $value)
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
	public static function chk_writable($path)
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
	 * Build arrows to sort orderable items
	 *
	 * @static
	 * @param   integer	$c Current position in the list
	 * @param   integer	$n Number of items
	 * @param   string	$link URL to move item 
	 * @return  string
	 */
	public static function updown($c, $n, $link)
	{
		// if there are items to sort
		if ($n > 1)	
		{
			// switch by  position
			switch($c)
			{
				case 0:
					// only down
					return '<a href="'.$link.'/1" title="'._MOVE_DOWN.'"><img src="'.THEME_URL.'img/down.png" alt="'._DOWN.'" /></a>';
					break;
				case ($n-1):
					// only up
					return '<a href="'.$link.'/-1" title="'._MOVE_UP.'"><img src="'.THEME_URL.'img/up.png" alt="'._UP.'" /></a>';
					break;
				default:
					// up and down
					return '<a href="'.$link.'/-1" title="'._MOVE_UP.'"><img src="'.THEME_URL.'img/up.png" alt="'._UP.'" /></a> <a href="'.$link.'/1" title="'._MOVE_DOWN.'"><img src="'.THEME_URL.'img/down.png" alt="'._DOWN.'" /></a>';
					break;
			}
		}
	}
	
	/**
	 * Build option list for select fields
	 *
	 * @static
	 * @param   array	$array Array of options as objects
	 * @param   string	$value Field name for values
	 * @param   string	$name Field name for names
	 * @param   string	$selected Selected value
	 * @return  string
	 */
	public static function get_opt($array, $value, $name, $selected = '')
	{
		$opt = '';
		foreach($array as $i) 
		{
			// check for selected
			$sel = (!empty($selected) && $i->$value == $selected) 
				? SELECTED 
				: '';
				
			// option
			$opt .= '<option value="'.$i->$value.'" '.$sel.'>' . $i->$name . '</option>';
		}
		return $opt;
	}
}

/**
 * Msg object
 *
 * @package		X4WEBAPP
 */
class Msg 
{
	public $id;
	public $message_type = 'error';
	public $message_close = '';
	public $message = '';
	public $command = array();
	public $update = array();
	public $redirect;
}
