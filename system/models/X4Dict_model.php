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
 * Model for Dictionary
 * Each dictionary are linked to an area and a language
 *
 * @package X3CMS
 */
class X4Dict_model extends X4Model_core
{
	/**
	 * Dictionary area
	 */
	private $area = '';

	/**
	 * Dictionary language code
	 */
	private $lang = '';

	/**
	 * Initialize dictionary model
	 *
	 * @param string	area name
	 * @param string	language code
	 * @return void
	 */
	public function __construct($area, $lang)
	{
		parent::__construct('dictionary');
		$this->area = $area;
		$this->lang = $lang;
	}

	/**
	 * Set area
	 *
	 * @param string	area name
	 * @return void
	 */
	public function __set_area($area)
	{
		$this->area = $area;
	}

	/**
	 * Set lang
	 *
	 * @param string	lang
	 * @return void
	 */
	public function __set_lang($lang)
	{
		$this->lang = $lang;
	}

	/**
	 * Get a dictionary section and define keys
	 *
	 * @param string	dictionary section
	 * @return void
	 */
	public function get_words($what = 'global')
	{
		// check APC
		$keys = (APC)
			? apcu_fetch(SITE.'dict'.$this->area.$this->lang.$what)
			: array();

		if (empty($keys))
		{
           $keys = $this->db->query('SELECT xkey, xval FROM dictionary WHERE area = \''.$this->area.'\' AND lang = \''.$this->lang.'\' AND what = '.$this->db->escape($what).' AND xon = 1');

			APC && apcu_store(SITE.'dict'.$this->area.$this->lang.$what, $keys);
		}

		foreach ($keys as $i)
		{
			!defined($i->xkey) && define($i->xkey, stripslashes($i->xval));
		}
	}

	/**
	 * Define multiple sections
	 *
	 * @param array		array of sections
	 * @return void
	 */
	public function get_wordarray($array)
	{
		array_unshift($array, 'global');
		foreach ($array as $i)
		{
			$this->get_words($i);
		}
	}

	/**
	 * Get a specified key from dictionary
	 *
	 * @param string	key dictionary
	 * @param string	dictionary section name
	 * @return string
	 */
	public function get_word($key, $what = 'global', $lang = '')
	{
	    $lang = (empty($lang))
	        ? $this->lang
	        : $lang;

	    return $this->db->query_var('SELECT xval FROM dictionary WHERE area = \''.$this->area.'\' AND lang = \''.$lang.'\' AND what = '.$this->db->escape($what).' AND xon = 1 AND xkey = '.$this->db->escape($key));
	}

	/**
	 * Get a specified key from dictionary and build a message object
	 *
	 * @param string	message title
	 * @param string	key message
	 * @param string	dictionary section name
	 * @param array		$options
	 * @return object
	 */
	public function get_message($title, $key, $what = 'global', $options = array())
	{
		$msg = $this->get_word($key, $what);
		$m = (strstr($msg, '<br />') != '')
			? $msg
			: nl2br($msg);

		if (empty($m))
        {
			$m = _MSG_ERROR;
        }

		return new Obj_msg($title, $m, $options);
	}

	/**
	 * Build a specified message object
	 *
	 * @param string	message title
	 * @param string	message
	 * @param array		$options
	 * @return object
	 */
	public function build_message($title, $msg, $options = array())
	{
		if (isset($_SESSION[$msg.'_msg']))
		{
		    // this is for complex HTML encoded messages
		    $msg = $_SESSION[$msg.'_msg'];
		    unset($_SESSION[$msg.'_msg']);
		}
		else
		{
	        $msg = nl2br(urldecode($msg));
		}

		// handle break line
		$msg = str_replace(array('<br/>', '<br>'), '<br />', $msg);
		$m = (strstr($msg, '<br />') != '')
			? $msg
			: nl2br($msg);

		return new Obj_msg($title, $m, $options);
	}
}

/**
 * Message object
 *
 * @package X4WEBAPP
 */
class Obj_msg
{
	public $content;
	public $module = '';
	public $param = '';
	public $bid = '';

	/**
	 * Constructor
	 * Set message content
	 *
	 * @param   string message title
	 * @param   string message body
	 * @param	array  options
	 * @return  void
	 */
	public function __construct($title, $msg, $options = array())
	{
		if (!isset($options['Hn']))
		{
			$options['Hn'] = 'h2';
		}

		if (!isset($options['Hclass']))
		{
			$options['Hclass'] = '';
		}

		if (!isset($options['envelope']))
		{
			$options['envelope'] = 'XXX';
		}

		if (!isset($options['container']))
		{
			$options['container'] = 'XXX';
		}

		$title = (is_null($title))
			? ''
			: '<'.$options['Hn'].' '.$options['Hclass'].'>'.$title.'</'.$options['Hn'].'>';

		$msg = str_replace('XXX', $msg, $options['envelope']);
		$msg = str_replace('XXX', $msg, $options['container']);

        // envelope
        if (substr($msg, 0, 1) != '<')
        {
            $msg = '<p>'.$msg.'</p>';
        }

		$this->content = $title.$msg;
	}

	/**
	 * Replace message content
	 *
	 * @param   string message title
	 * @param   string message body
	 * @return  void
	 */
	public function replace($title = '', $str = '')
	{
		$title = (is_null($title))
			? ''
			: '<h2>'.urldecode($title).'</h2>';

		$this->content = $title.'<p>'.nl2br(urldecode($str)).'</p>';
	}

	/**
	 * Replace message content
	 *
	 * @param   string message body
	 * @return  void
	 */
	public function replace_html($str = '')
	{
		$this->content = $str;
	}

	/**
	 * Replace message content
	 *
	 * @param   string message body
	 * @return  void
	 */
	public function enclose($before, $after)
	{
		$this->content = $before.$this->content.$after;
	}

	/**
	 * Set module and param
	 *
	 * @param   string message body
	 * @return  void
	 */
	public function module($module, $param)
	{
		$this->module = $module;
		$this->param = $param;
	}
}
