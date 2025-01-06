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
 * X3ui controller extends X4Cms_controller for pages of the site with X3 UI
 *
 * @package		X3CMS
 */
class X3ui_controller extends X4Cms_controller
{
	/**
	 * Constructor
	 * Check the site status
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
     * Send an answer to the browser
     */
    public function response(Msg $msg) : void
    {
        header('Content-type: application/json');
    	echo json_encode($msg);
    }

	/**
	 * Return error message
	 */
	public function notice(array $fields, string $title = '_form_not_valid', bool $session = false) : void
	{
		$error_msg = $this->dict->get_word($title, 'form');
		$fields = X4Utils_helper::normalize_form($fields);

		foreach ($fields as $i)
		{
			if (isset($i['error']))
			{
				foreach ($i['error'] as $e)
				{
					// set the available label
					$label = $this->label($i);

					// error message
                    $error_msg .= $this->related($fields, $e, $label);
				}
			}
		}

		if ($session)
		{
		    $_SESSION['msg'] = $error_msg;
		}
		else
		{
		    // set message
		    $msg = AdminUtils_helper::set_msg(false, $error_msg, $error_msg);
            header('Content-type: application/json');
            echo json_encode($msg);
		}
	}

    /**
	 * Return label
	 */
	public function label(array $field) : string
	{
        if (
            (is_null($field['label']) && isset($field['alabel'])) ||
            isset($field['alabel'])
        )
        {
            $label = $field['alabel'];
        }
        else
        {
            $label = !is_null($field['label'])
                ? $field['label']
                : '>>>'.$field['name'].'<<<';   // warning missing label
        }
        return $label;
    }

    /**
	 * Return label
	 */
	public function related(array $fields, array $error, string $label) : string
	{
        // debug
        if (isset($error['debug']))
        {
            return '<br />'.$error['debug'];
        }

        // for related fields
        if (isset($error['related']))
        {
            $src = array('XXXRELATEDXXX');
            $rpl = [];

            $related = $error['related'];
            if (isset($fields[$related]))
            {
                // if is a related field
                $rpl[] = (
                    (is_null($fields[$related]['label']) && isset($fields[$related]['alabel'])) ||
                    isset($fields[$related]['alabel'])
                )
                    ? $fields[$related]['alabel']
                    : $fields[$related]['label'];
            }
            else
            {
                // if is a related value
                $rpl[] = $related;
            }

            if (isset($error['relatedvalue']))
            {
                $src[] = 'XXXVALUEXXX';
                $rpl[] = $error['relatedvalue'];
            }

            $error_msg = '<br /><u>'.$label.'</u> '.str_replace($src, $rpl, $this->dict->get_word($error['msg'], 'form'));
        }
        elseif (isset($error['relatedvalue']))
        {
            // case with only relatedvalue
            $src[] = 'XXXVALUEXXX';
            $rpl[] = $error['relatedvalue'];

            $error_msg = '<br /><u>'.$label.'</u> '.str_replace($src, $rpl, $this->dict->get_word($error['msg'], 'form'));
        }
        else
        {
            $error_msg = '<br /><u>'.$label.'</u> '.$this->dict->get_word($error['msg'], 'form');
        }
        return $error_msg;
    }

    /**
     * Info memo
     */
    public function memo(string $url, int $id_user) : array
    {
        $mod = new Memo_model();
        return $mod->count_memos($url, $id_user);
    }

}
