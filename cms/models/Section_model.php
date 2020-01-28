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
 * Model for Sections Items
 *
 * @package X3CMS
 */
class Section_model extends X4Model_core
{
	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('sections');
	}
	
	/**
	 * Get sections by page
	 * Join with themes, areas and pages tables
	 *
	 * @param   object	$page Page object
	 * @return  array	array of section objects
	 */
	public function get_sections($page)
	{
		// get how many sections there are in the page template
		$n = $this->db->query_var('SELECT t.sections 
			FROM templates t
			JOIN themes th ON th.id = t.id_theme
			JOIN areas a ON a.id_theme = th.id
			JOIN pages p ON p.id_area = a.id
			WHERE p.id = '.intval($page->id).' AND t.name = '.$this->db->escape($page->tpl));
		
		$a = array_fill(1, $n, array());
		foreach($a as $k => $v)
		{
			// get section content
			$tmp = $this->get_by_page($page->id, $k);
			
			if ($tmp) 
				$a[$k] = $tmp;
		}
		return $a;
	}
	
	/**
	 * Get section contents
	 *
	 * @param   integer	$id_page Page ID
	 * @param   integer	$progressive Number of section
	 * @return  object
	 */
	private function get_by_page($id_page, $progressive)
	{
		return $this->db->query_row('SELECT * FROM sections	WHERE id_page = '.intval($id_page).' AND progressive = '.intval($progressive));
	}
	
	/**
	 * Insert and Update section contents
	 *
	 * @param   array	$sections Array of post
	 * @return  array
	 */
	public function compose($sections)
	{
		$a = array();
		foreach($sections as $i) 
		{
			// get existing sections
			$s = $this->get_by_page($i['id_page'], $i['progressive']);
			
			// update or insert
			if ($s) 
				$res = $this->update($s->id, $i);
			else 
			{
				$res = $this->insert($i);
				$a = $res[0];
			}
		}
		return array($a, 1);
	}
	
	/**
	 * Get Pages IDs by bid
	 *
	 * @param   string	$bid, article unique ID
	 * @return  object
	 */
	public function get_pages_by_bid($bid)
	{
		return $this->db->query('SELECT id_page FROM sections WHERE articles LIKE '.$this->db->escape('%'.$bid.'%'));
	}
	
	/**
	 * Update context and page ID of articles by bid
	 * Use articles
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string	$lang Language code
	 * @param   string	$holder Context type
	 * @param   string	$bid, article unique ID
	 * @param   integer	$id_page Page ID
	 * @return  void
	 */
	public function recode($id_area, $lang, $holder, $bid, $id_page)
	{
		// default contexts
		$codes = array('drafts' => 0, 'pages' => 1, 'multi' => 2);
		
		// update articles
		$sql = 'UPDATE articles SET updated = NOW(), code_context = '.$codes[$holder].', id_page = '.intval($id_page).' WHERE code_context != 2 AND bid = '.$this->db->escape($bid).' AND id_area = '.intval($id_area).' AND lang = '.$this->db->escape($lang);
		$this->db->single_exec($sql);
	}
	
	/**
	 * Get articles by bid
	 * Use articles
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string	$lang Language code
	 * @param   string	$bid_str bid's chain, where bid is the article unique ID
	 * @return  array	Array of articles
	 */
	public function get_articles($id_area, $lang, $bid_str)
	{
		$bids = explode('|', $bid_str);
		
		$artt = array();
		if ($bids) 
		{
			foreach ($bids as $i)
			{
				$a = $this->db->query_row('SELECT * 
					FROM articles 
					WHERE xon = 1 AND id_area = '.intval($id_area).' AND lang = '.$this->db->escape($lang).' AND bid = '.$this->db->escape($i).' 
					ORDER BY id DESC');
				
				if ($a) 
					$artt[] = $a;
			}
		}
		return $artt;
	}
	
	/**
	 * Get contexts
	 * Use contexts
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string	$lang Language code
	 * @return  array	Array of objects
	 */
	public function get_contexts($id_area, $lang)
	{
		return $this->db->query('SELECT x.* 
			FROM contexts x
			JOIN uprivs u ON u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('contexts').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = x.id AND p.level > 0
			WHERE 
				x.xon = 1 AND 
				x.code < 3 AND 
				x.id_area = '.intval($id_area).' AND 
				x.lang = '.$this->db->escape($lang).' 
			GROUP BY x.id
			ORDER BY x.code ASC');
	}
	
	/**
	 * Get articles to publish
	 * Use articles, contexts and privs
	 *
	 * @param   object	$page Page object
	 * @param   string	$by Sort key
	 * @return  array	Array of objects
	 */
	public function get_articles_to_publish($page, $by)
	{
		// sorting
		$order = ($by == 'name') 
			? 'a.name ASC' 
			: 'a.id DESC';
		
		return $this->db->query('SELECT a.*, c.xkey, IF(p.id IS NULL, u.level, p.level) AS level
			FROM articles a
			LEFT JOIN contexts c ON c.id_area = a.id_area AND c.lang = a.lang AND c.code = a.code_context
			JOIN uprivs u ON u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('articles').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = a.id
			WHERE a.xon = 1 AND a.id_area = '.intval($page->id_area).' AND a.lang = '.$this->db->escape($page->lang).' AND c.code < 3 AND 
				(
				a.id_page = '.intval($page->id).' OR c.code != 1
				)
			GROUP BY a.bid
			ORDER BY a.code_context ASC, '.$order);
	}
	
	/**
	 * Get article by bid
	 * Use articles
	 *
	 * @param   object	$id_area Area ID
	 * @param   string	$lang Language code
	 * @param   string	$bid, article unique ID
	 * @return  object
	 */
	public function get_by_bid($id_area, $lang, $bid)
	{
		return $this->db->query_row('SELECT * 
			FROM articles 
			WHERE xon = 1 AND id_area = '.intval($id_area).' AND lang = '.$this->db->escape($lang).' AND bid = '.$this->db->escape($bid).'
			ORDER BY updated DESC');
	}
}
