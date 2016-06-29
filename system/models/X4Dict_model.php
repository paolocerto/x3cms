<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
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
	 * Get a dictionary section and define keys
	 *
	 * @param string	dictionary section
	 * @return void
	 */
	public function get_words($what = 'global')
	{
		// check APC
		$keys = (APC)
			? apc_fetch(SITE.'dict'.$this->area.$this->lang.$what)
			: array();
		
		if (empty($keys))
		{
			$keys = $this->db->query('SELECT xkey, xval FROM dictionary WHERE area = \''.$this->area.'\' AND lang = \''.$this->lang.'\' AND what = '.$this->db->escape($what).' AND xon = 1');
			
			if (APC)
				apc_store(SITE.'dict'.$this->area.$this->lang.$what, $keys);
		}
		
		foreach($keys as $i)
		{
			if (!defined($i->xkey)) 
				define($i->xkey, stripslashes($i->xval));
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
		foreach($array as $i)
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
	 * @return object
	 */
	public function get_message($title, $key, $what = 'global')
	{
		$msg = $this->get_word($key, $what);
		$m = (strstr($msg, '<br />') != '')
			? $msg
			: nl2br($msg);
		
		if (empty($m)) 
			$m = _MSG_ERROR;
		$obj = new Obj_msg($title, $m);
		return $obj;
	}
	
	/**
	 * Build a specified message object
	 *
	 * @param string	message title
	 * @param string	message
	 * @return object
	 */
	public function build_message($title, $msg)
	{
		$msg = urldecode($msg);
		$m = (strstr($msg, '<br />') != '')
			? $msg
			: nl2br($msg);
		
		$obj = new Obj_msg($title, $m);
		return $obj;
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
	
	/**
	 * Constructor
	 * Set message content
	 *
	 * @param   string message title
	 * @param   string message body
	 * @param   boolean envelope switcher
	 * @param   string Hn to use
	 * @return  void
	 */
	public function __construct($title, $msg, $envelope = true, $hn = 'h2')
	{
		$title = (is_null($title))
			? ''
			: '<'.$hn.'>'.$title.'</'.$hn.'>';
		
		$this->content = ($envelope)
			? $title.'<p>'.$msg.'</p>'
			: $title.$msg;
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
}
