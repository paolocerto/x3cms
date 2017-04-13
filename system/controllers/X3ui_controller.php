<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
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
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
     * Send an answer to the browser depending on the incoming request
     * If the request cames from XHR, sends a JSON object as response
     * else, check if redirect is defined and redirect
     *
     * @param	array	Message array
     * @param	array	Additional data to put to the answer. Optional.
     *
     */
    public function response($msg, $addon_data = null)
    {
    	/* XHR request : JSON answer
    	 * Sends a JSON javascript object
    	 */
    	if ($this->is_xhr() === true)
    	{
			// Puts additional data to answer
			if ( ! empty($addon_data))
			{
				$msg = array_merge($msg, $addon_data);
			}
			
			echo json_encode($msg);
			exit();
    	}
    }
    
    /**
	 * Returns true if this is an XMLHttpRequest (ie. Javascript).
	 * 
	 * This requires a special header to be sent from the JS
	 * (usually the Javascript frameworks' Ajax/XHR methods add it automatically):
	 * 
	 * <code>
	 * X-Requested-With: XMLHttpRequest
	 * </code>
	 * 
	 * @return bool
	 */
	private function is_xhr()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
	
	/**
	 * Return error message
	 *
	 * @param   mixed 	$fields Form array or boolean
	 * @param   string 	$title Dialog title
	 * @param   boolean     $session If true save message in a session var
	 * @return  void
	 */
	public function notice($fields, $title = '_form_not_valid', $session = false)
	{
		$dict = new X4Dict_model(X4Route_core::$folder, X4Route_core::$lang);
		$error = $dict->get_word($title, 'form');
		$fields = X4Utils_helper::normalize_form($fields);
		
		foreach($fields as $i)
		{
			if (isset($i['error']))
			{
				foreach($i['error'] as $e)
				{
					// set the available label
					$label = ((is_null($i['label']) && isset($i['alabel'])) || isset($i['alabel']))
						? $i['alabel']
						: $i['label'];
					
					// for related fields
					if (isset($e['related']))
					{
						$src = array('XXXRELATEDXXX');
						$rpl = array();
						
						$related = $e['related'];
						if (isset($fields[$related]))
						{
							// if is a related field
							$rpl[] = ((is_null($fields[$related]['label']) && isset($fields[$related]['alabel'])) || isset($fields[$related]['alabel']))
								? $fields[$related]['alabel']
								: $fields[$related]['label'];
						}
						else
						{
							// if is a related value
							$rpl[] = $related;
						}
						
						if (isset($e['relatedvalue']))
						{
							$src[] = 'XXXVALUEXXX';
							$rpl[] = $e['relatedvalue'];
						}
						
						$error .= '<br /><u>'.$label.'</u> '.str_replace($src, $rpl, $dict->get_word($e['msg'], 'form'));
					}
					else
					{
						$error .= '<br /><u>'.$label.'</u> '.$dict->get_word($e['msg'], 'form');
					}
				}
			}
		}
		
		if ($session)
		{
		    $_SESSION['msg'] = $error;
		}
		else
		{
		    // set message
		    $msg = AdmUtils_helper::set_msg(false, $error, $error);
		    
		    $this->response($msg);
		}
	}
}
