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
	 */
	public function __construct(string $area, string $lang)
	{
		parent::__construct('dictionary');
		$this->area = $area;
		$this->lang = $lang;
	}

	/**
	 * Set area
	 */
	public function __set_area(string $area)
	{
		$this->area = $area;
	}

	/**
	 * Set lang
	 */
	public function __set_lang(string $lang)
	{
		$this->lang = $lang;
	}

	/**
	 * Get a dictionary section and define keys
	 */
	public function get_words(string $what = 'global')
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
	 */
	public function get_wordarray(array $sections)
	{
		array_unshift($sections, 'global');
		foreach ($sections as $i)
		{
			$this->get_words($i);
		}
	}

	/**
	 * Get a specified key from dictionary
	 */
	public function get_word(string $key, string $what = 'global', string $lang = '') : string
	{
	    $lang = (empty($lang))
	        ? $this->lang
	        : $lang;

	    return $this->db->query_var('SELECT xval FROM dictionary WHERE area = \''.$this->area.'\' AND lang = \''.$lang.'\' AND what = '.$this->db->escape($what).' AND xon = 1 AND xkey = '.$this->db->escape($key));
	}

	/**
	 * Get a specified key from dictionary and build a message object
	 */
	public function get_message(string $title, string $key, string $what = 'global', array $options = array()) : Obj_msg
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
	 */
	public function build_message(string $title, string $msg, array $options = array()) : Obj_msg
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
 * Create an pseudo-article object to insert in a section
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
	 * Set message contents
	 */
	public function __construct(string $title, string $msg, array $options = array())
	{
        // default options
        $options['Hn'] = $options['Hn'] ?? 'h2';
        $options['Hclass'] = $options['Hclass'] ?? '';
		$options['envelope'] = $options['envelope'] ?? 'XXX';
		$options['container'] = $options['container'] ?? 'XXX';

        if (!empty($title))
        {
            $title = '<'.$options['Hn'].' '.$options['Hclass'].'>'.$title.'</'.$options['Hn'].'>';
        }

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
	 * Replace message contents
	 */
	public function replace(string $title = '', string $str = '')
	{
		if (!empty($title))
        {
            $title = '<'.$options['Hn'].' '.$options['Hclass'].'>'.urldecode($title).'</'.$options['Hn'].'>';
        }

		$this->content = $title.'<p>'.nl2br(urldecode($str)).'</p>';
	}

	/**
	 * Replace message content
	 */
	public function replace_html(string $str = '')
	{
		$this->content = $str;
	}

	/**
	 * Envelope message content
	 */
	public function enclose(string $before, string $after)
	{
		$this->content = $before.$this->content.$after;
	}

	/**
	 * Set module and param
	 */
	public function module(string $module, string $param)
	{
		$this->module = $module;
		$this->param = $param;
	}
}
