<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X4WEBAPP
 */

/**
 * App Helper
 * This class contains methods for not admin areas
 *
 * @package X4WEBAPP
 */
class X4App_helper
{
    /**
	 * Put the message into a session variable
	 */
	public static function set_msg(mixed $res, string $ok = _MSG_OK, string $ko = _MSG_ERROR, string $type = '') : Msg2
	{
		$msg = new Msg2();
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
            case 'integer':
                if ($res > 0)
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
			default:
				// is a string so is an error
				$msg->message_type = 'error';
				$msg->message = $ko;
				break;
		}
		return $msg;
	}

    /**
     * Get error message for form inside modal
     */
    public static function error(string $msg, string $type = 'failed') : string
    {
        return '<div id="msg" class="rounded '.$type.' mt-4 p-4 text-sm font-semibold"><p>'.$msg.'</p></div>';
    }

    /**
     * Send an answer to the browser
     */
    public static function response(Msg2 $msg) : void
    {
        header('Content-type: application/json');
    	echo json_encode($msg);
    }

    /**
	 * Check User permission level
     */
	public static function chk_priv_level(string $priv, int $min_value) : mixed
	{
		if ($_SESSION['privs']->$priv < $min_value)
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
     * Calculate distance for face recognition
     */
    public static function euclideanDistance(array $arr1, array $arr2) : float
    {
        if (count($arr1) !== count($arr2)) {
            return 1;
            //throw new Exception('euclideanDistance: arr1 and arr2 must have the same length');
        }

        $sumOfSquares = 0;
        for ($i = 0; $i < count($arr1); $i++) {
            $diff = $arr1[$i] - $arr2[$i];
            $sumOfSquares += $diff * $diff;
        }

        return sqrt($sumOfSquares);
    }

    /*
    use NumPHP\Matrix;

function euclideanDistanceNumPHP(array $arr1, array $arr2): float {
    $v1 = new Matrix($arr1);
    $v2 = new Matrix($arr2);
    $diff = $v1 - $v2;
    return sqrt($diff->dot($diff));
}
    */

}


/**
 * Msg object
 *
 * @package		X4WEBAPP
 */
class Msg2
{
	public $message_type = 'error';
	public $message = '';
	public $command = [];
	public $update = [];
	public $redirect;
}
