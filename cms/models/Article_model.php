<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

/**
 * Model for Articles Items
 *
 * @package X3CMS
 */
class Article_model extends X4Model_core
{
	/**
	 * @var integer	$time PHP time for time zone
	 */
	protected $time;

	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('articles');
		$this->time = time();
	}

	/**
	 * Get a new bid (unique ID for articles) for duplicates
	 *
	 * @return  string	Article unique ID
	 */
	public function get_new_bid()
	{
		return md5(time().'-'.$_SESSION['xuid']);
	}

	/**
	 * Get last version of article with requested bid (unique ID for articles)
	 *
     * @param   integer $id_area
     * @param   string  $lang
	 * @param   string	$bid, article unique ID
	 * @return  object	article object
	 */
	public function get_by_bid(int $id_area, string $lang, string $bid)
	{
        return $this->db->query_row('SELECT *
			FROM articles
			WHERE
                id_area = '.$id_area.' AND
                lang = '.$this->db->escape($lang).' AND
                bid = '.$this->db->escape($bid).'
			ORDER BY id DESC');
	}

	/**
	 * Get all versions of article with requested bid (unique ID for articles)
	 *
     * @param   integer $id_area
     * @param   string  $lang
	 * @param   string	$bid, article unique ID
	 * @return  array 	array of article object
	 */
	public function get_all_by_bid(int $id_area, string $lang, string $bid)
	{
		return $this->db->query('SELECT *
			FROM articles
			WHERE
                id_area = '.$id_area.' AND
                lang = '.$this->db->escape($lang).' AND
                bid = '.$this->db->escape($bid).'
			ORDER BY id DESC');
	}

	/**
	 * Get bid (unique ID for articles) of article in a page with requested ID
	 * Useful in case of simple editing (one page -> one article)
	 *
	 * @param   integer	$id_page Page ID
	 * @return  string 	bid of article
	 */
	public function get_bid_by_id_page(int $id_page)
	{
		return $this->db->query_var('SELECT bid
			FROM articles
			WHERE code_context = 1 AND id_page = '.$id_page.'
			ORDER BY id DESC');
	}

	/**
	 * Get where
	 *
	 * @param   array	$qs
	 * @return  string
	 */
	private function get_where(array $qs)
	{
		$where = '';
		if (!empty($qs['xstr']))
		{
			$w = array();
			$tok = explode(' ', urldecode($qs['xstr']));
			foreach ($tok as $i)
			{
				$a = trim($i);
				if (!empty($a))
                {
					$w[] = 'name LIKE '.$this->db->escape('%'.$a.'%').' OR
						content LIKE '.$this->db->escape('%'.$a.'%'). ' OR
						tags LIKE '.$this->db->escape('%'.$a.'%');
                }
			}

			if (!empty($w))
            {
				$where .= ' AND ('.implode(') AND (', $w).')';
            }
		}

        if ($qs['xpage'])
        {
            $where .= ' AND id_page = '.intval($qs['xpage']);
        }

        if ($qs['xkey'])
        {
            $where .= ' AND xkeys = '.$this->db->escape($qs['xkey']);
        }
        if ($qs['xcnt'] >= 0)
        {
            $where .= ' AND code_context = '.intval($qs['xcnt']);
        }

        if (!empty($qs['xctg']))
        {
            $where .= ' AND category = '.$this->db->escape($qs['xctg']);
        }

		return $where;
	}

	/**
	 * Get all articles of a requested area ID and language code
	 * There are two order options (name ASC, id DESC)
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   array   $qs
	 * @return  array 	array of article objects
	 */
	public function get_articles(int $id_area, string $lang, array $qs)
	{
        // order condition
		$order = 'aa.id DESC';  // ($by == 'name') ? 'aa.name ASC' :

		$where = $this->get_where($qs);

		return $this->db->query('SELECT aa.*, c.name AS context, pa.name AS page, IF(p.id IS NULL, u.level, p.level) AS level
			FROM articles aa
            JOIN (
                SELECT MAX(id) AS id, bid
                FROM articles
                WHERE
                    id_area = '.$id_area.' AND
                    lang = '.$this->db->escape($lang).'
                    '.$where.'
                GROUP BY bid
				) b ON b.id = aa.id AND b.bid = aa.bid
			JOIN uprivs u ON u.id_area = aa.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('articles').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = aa.id
			LEFT JOIN contexts c ON c.id_area = aa.id_area AND c.lang = aa.lang AND c.code = aa.code_context
			LEFT JOIN pages pa ON pa.id = aa.id_page
			GROUP BY aa.id
			ORDER BY '.$order);
	}

    /**
	 * Get contexts by Area ID and Language code
	 * Join with privs table
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @return  array	Array of Context objects
	 */
	public function get_contexts(int $id_area, string $lang)
	{
		return $this->db->query('SELECT c.*, IF(p.id IS NULL, u.level, p.level) AS level
				FROM contexts c
				JOIN uprivs u ON u.id_area = c.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('categories').'
				LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = c.id
				WHERE c.id_area = '.$id_area.' AND c.lang = '.$this->db->escape($lang).'
				GROUP BY c.id
				ORDER BY c.name ASC');
	}

    /**
	 * Get categories by Area ID and Language code
	 * Join with privs table
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string 	$tag Category tag
	 * @return  array	array of category objects
	 */
	public function get_all_categories(int $id_area, string $lang, string $tag = '')
	{
	    $where = (empty($tag))
	        ? ''
	        : ' AND c.tag = '.$this->db->escape($tag);

		return $this->db->query('SELECT c.*, IF(p.id IS NULL, u.level, p.level) AS level
			FROM categories c
			JOIN uprivs u ON u.id_area = c.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('categories').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = c.id
			WHERE c.id_area = '.$id_area.' AND c.lang = '.$this->db->escape($lang).$where.'
			GROUP BY c.id
			ORDER BY c.name ASC');
	}

	/**
	 * Get articles of a requested area ID and language code by context code
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   integer $code_context Context code
 	 * @param   string 	$str Search string
	 * @return  array 	array of article objects
	 */
	public function get_by_context(int $id_area, string $lang, int $code_context, string $str)
	{
	    $where = $this->get_where($str);

	    return $this->db->query('SELECT a.*, c.name AS context, pa.name AS page, IF(p.id IS NULL, u.level, p.level) AS level
			FROM articles a
            JOIN (
                SELECT MAX(id) AS id, bid
                FROM articles
                WHERE
                    id_area = '.$id_area.' AND
                    lang = '.$this->db->escape($lang).'
                GROUP BY bid
                ) b ON b.id = a.id AND b.bid = a.bid
			LEFT JOIN pages pa ON pa.id = a.id_page
			JOIN contexts c ON c.id_area = a.id_area AND c.lang = a.lang AND c.code = a.code_context
			JOIN uprivs u ON u.id_area = a.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('articles').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = a.id
			WHERE c.code = '.$code_context.$where.'
			GROUP BY a.id
			ORDER BY a.updated DESC');
	}

	/**
	 * Get articles of a requested area ID and language code by category
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string 	$ctg Category name
	 * @param   string 	$str Search string
	 * @return  array 	array of article objects
	 */
	public function get_by_category(int $id_area, string $lang, string $ctg, string $str)
	{
	    $where = $this->get_where($str);

		return $this->db->query('SELECT a.*, c.description AS title, pa.name AS page, IF(p.id IS NULL, u.level, p.level) AS level
			FROM articles a
            JOIN (
                SELECT MAX(id) AS id, bid
                FROM articles
                WHERE
                    id_area = '.$id_area.' AND
                    lang = '.$this->db->escape($lang).'
                GROUP BY bid
                ) b ON b.id = a.id AND b.bid = a.bid
		    LEFT JOIN pages pa ON pa.id = a.id_page
			JOIN categories c ON c.id_area = a.id_area AND c.lang = a.lang AND c.name = a.category
			JOIN uprivs u ON u.id_area = a.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('articles').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = a.id
			WHERE c.name = '.$this->db->escape($ctg).$where.'
			GROUP BY a.id
			ORDER BY a.updated DESC');
	}

	/**
	 * Get articles of a requested area ID and language code by author
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   integer $id_author Author ID
	 * @param   string 	$str Search string
	 * @return  array 	array of article objects
	 */
	public function get_by_author(int $id_area, string $lang, int $id_author, string $str)
	{
		if ($id_author == 0)
			return array();
		else
		{
		    $where = $this->get_where($str);

			return $this->db->query('SELECT a.*, c.name AS context, pa.name AS page, IF(p.id IS NULL, u.level, p.level) AS level
				FROM articles a
                JOIN (
                    SELECT MAX(id) AS id, bid
                    FROM articles
                    WHERE
                        id_area = '.$id_area.' AND
                        lang = '.$this->db->escape($lang).'
                    GROUP BY bid
                    ) b ON b.id = a.id AND b.bid = a.bid
			    LEFT JOIN pages pa ON pa.id = a.id_page
				JOIN contexts c ON c.id_area = a.id_area AND c.lang = a.lang AND c.code = a.code_context
				JOIN uprivs u ON u.id_area = a.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('articles').'
				LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = a.id
				WHERE a.id_editor = '.$id_author.$where.'
				GROUP BY a.id
				ORDER BY a.updated DESC');
		}
	}

	/**
	 * Get author of articles of a requested area ID and language code
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string 	$str Search string
	 * @return  array 	array of article objects
	 */
	public function get_authors(int $id_area, string $lang)
	{
		return $this->db->query('SELECT a.id, a.id_editor, a.author, IF(p.id IS NULL, u.level, p.level) AS level
			FROM articles a
			JOIN uprivs u ON u.id_area = a.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('articles').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = a.id
			WHERE a.id_area = '.$id_area.' AND a.lang = '.$this->db->escape($lang).' AND LENGTH(a.author) > 0
			GROUP BY a.author
			ORDER BY a.author ASC');
	}

	/**
	 * Get articles of a requested area ID and language code by key
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string 	$key Article Key
	 * @param   string 	$str Search string
	 * @return  array 	array of article objects
	 */
	public function get_by_key(int $id_area, string $lang, string $key, string $str)
	{
	    $where = $this->get_where($str);

		return $this->db->query('SELECT a.*, c.name AS context, pa.name AS page, IF(p.id IS NULL, u.level, p.level) AS level
				FROM articles a
                JOIN (
                    SELECT MAX(id) AS id, bid
                    FROM articles
                    WHERE
                        id_area = '.$id_area.' AND
                        lang = '.$this->db->escape($lang).'
                    GROUP BY bid
                    ) b ON b.id = a.id AND b.bid = a.bid
		        LEFT JOIN pages pa ON pa.id = a.id_page
				JOIN contexts c ON c.id_area = a.id_area AND c.lang = a.lang AND c.code = a.code_context
				JOIN uprivs u ON u.id_area = a.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('articles').'
				LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = a.id
				WHERE a.xkeys = '.$this->db->escape($key).$where.'
				GROUP BY a.id
				ORDER BY a.updated DESC');
	}

	/**
	 * Get articles of a requested area ID and language code by page ID
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   integer $id_page Page ID
	 * @param   string 	$str Search string
	 * @return  array 	array of article objects
	 */
	public function get_by_page(int $id_area, string $lang, int $id_page, string $str)
	{
	    $where = $this->get_where($str);

		return $this->db->query('SELECT a.*, c.name AS context, pa.name AS page, IF(p.id IS NULL, u.level, p.level) AS level
				FROM articles a
                JOIN (
                    SELECT MAX(id) AS id, bid
                    FROM articles
                    WHERE
                        id_area = '.$id_area.' AND
                        lang = '.$this->db->escape($lang).'
                    GROUP BY bid
                    ) b ON b.id = a.id AND b.bid = a.bid
		        LEFT JOIN pages pa ON pa.id = a.id_page
				JOIN contexts c ON c.id_area = a.id_area AND c.lang = a.lang AND c.code = a.code_context
				JOIN uprivs u ON u.id_area = a.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('articles').'
				LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = a.id
				WHERE a.id_page = '.$id_page.$where.'
				GROUP BY a.id
				ORDER BY a.updated DESC');
	}

	/**
	 * Get keys of articles of a requested area ID and language code
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @return  array 	array of article objects
	 */
	public function get_keys(int $id_area, string $lang)
	{
		return $this->db->query('SELECT a.*, IF(p.id IS NULL, u.level, p.level) AS level
			FROM articles a
			JOIN uprivs u ON u.id_area = a.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('articles').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = a.id
			WHERE a.id_area = '.$id_area.' AND a.lang = '.$this->db->escape($lang).' AND LENGTH(a.xkeys) > 0
			GROUP BY a.xkeys
			ORDER BY a.xkeys ASC');
	}

	/**
	 * Get article history by bid (unique ID for articles)
	 *
	 * @param   integer	$id_area Area ID
     * @param   string 	$lang
	 * @param   string 	$bid, article unique ID
	 * @return  array 	array of article objects
	 */
	public function get_history(int $id_area, string $lang, string $bid)
	{
		return $this->db->query('SELECT a.*, IF(p.id IS NULL, u.level, p.level) AS level
			FROM articles a
			JOIN uprivs u ON u.id_area = a.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('articles').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = a.id
			WHERE a.id_area = '.$id_area.' AND a.lang = '.$this->db->escape($lang).' AND a.bid = '.$this->db->escape($bid).'
			GROUP BY a.id
			ORDER BY a.id DESC');
	}

	/**
	 * Update all versions of an article
	 *
	 * @param   integer	$id Article ID
	 * @param   array 	$array associative array (field => value)
	 * @return  array
	 */
	public function update_by_bid(int $id, array $array)
	{
		$obj = $this->get_by_id($id);
		$update = '';
		foreach ($array as $k => $v)
        {
			$update .= ', '.addslashes($k).' = '.$this->db->escape($v);
		}

		// get last active item
		$item = $this->db->query_row('SELECT a.id
			FROM articles a
			WHERE a.bid = '.$this->db->escape($obj->bid).'
			ORDER BY a.id DESC');

		// update
		return $this->db->single_exec('UPDATE '.$this->table.' SET updated = NOW() '.$update.' WHERE id = '.$item->id);

	}

	/**
	 * Delete all versions of an article
	 *
     * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string	$bid, article unique ID
	 * @return  array
	 */
	public function delete_by_bid(int $id_area, string $lang, string $bid)
	{
		$sql = array();
		// delete from sections
		$sql[] = 'UPDATE sections SET articles = REPLACE(articles, '.$this->db->escape('|'.$bid.'|').', \'|\' ) WHERE id_area = '.$id_area.' AND lang = '.$this->db->escape($lang);
		$sql[] = 'UPDATE sections SET articles = REPLACE(articles, '.$this->db->escape($bid.'|').', \'\' )  WHERE id_area = '.$id_area.' AND lang = '.$this->db->escape($lang);
		$sql[] = 'UPDATE sections SET articles = REPLACE(articles, '.$this->db->escape('|'.$bid).', \'\' ) WHERE id_area = '.$id_area.' AND lang = '.$this->db->escape($lang);
		// delete from articles
		$sql[] = 'DELETE FROM articles WHERE  WHERE id_area = '.$id_area.' AND lang = '.$this->db->escape($lang).' AND bid = '.$this->db->escape($bid);
		return $this->db->multi_exec($sql);
	}

	/**
	 * Get articles by context
	 * public function
	 * Get enabled articles in time window
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string 	$context context
	 * @param   integer	$limit index for pagination
	 * @return  array 	array of article objects
	 */
	public function get_artt_by_context(int $id_area, string $lang, string $context, int $limit = PP)
	{
		return $this->db->query('SELECT a.*
			FROM articles a
            JOIN (
                SELECT MAX(id) AS id, bid
                FROM articles
                WHERE
                    id_area = '.$id_area.' AND
                    lang = '.$this->db->escape($lang).' AND
                    xon = 1 AND
					date_in <= '.$this->time.' AND
					(date_out = 0 OR date_out >= '.$this->time.')
                GROUP BY bid
                ) b ON b.id = a.id AND b.bid = a.bid
		    JOIN contexts c ON c.id_area = a.id_area AND c.lang = a.lang AND c.code = a.code_context AND c.xkey = '.$this->db->escape($context).'
			ORDER BY a.date_in DESC, a.id DESC
			LIMIT 0, '.$limit);
	}

	/**
	 * Get articles in reverse chronological order
	 * public function
	 * Get enabled articles in time window
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string 	$context context
	 * @return  array 	array of article objects
	 */
	public function get_latest(int $id_area, string $lang, string $context)
	{
		return $this->db->query('SELECT a.*
		        FROM articles a
                JOIN (
                    SELECT MAX(id) AS id, bid
                    FROM articles
                    WHERE
                        id_area = '.$id_area.' AND
                        lang = '.$this->db->escape($lang).' AND
                        xon = 1 AND
                        date_in <= '.$this->time.' AND
                        (date_out = 0 OR date_out >= '.$this->time.')
                    GROUP BY bid
                    ) b ON b.id = a.id AND b.bid = a.bid
		    	JOIN contexts c ON c.id_area = a.id_area AND c.lang = a.lang AND c.code = a.code_context AND c.xkey = '.$this->db->escape($context).'
				ORDER BY a.date_in DESC, a.id DESC');
	}

	/**
	 * Get tags of articles
	 * public function, return an associative array frequency=>tag
	 * Get enabled articles in time window
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string 	$context context
	 * @return  array 	array of tags (strings)
	 */
	public function get_tags(int $id_area, string $lang, string $context)
	{
		$tags = $this->db->query('SELECT a.tags
		    FROM articles a
            JOIN (
                SELECT MAX(id) AS id, bid
                FROM articles
                WHERE
                    id_area = '.$id_area.' AND
                    lang = '.$this->db->escape($lang).' AND
                    xon = 1 AND
                    date_in <= '.$this->time.' AND
                    (date_out = 0 OR date_out >= '.$this->time.')
                GROUP BY bid
                ) b ON b.id = a.id AND b.bid = a.bid
			JOIN contexts c ON c.id_area = a.id_area AND c.lang = a.lang AND c.code = a.code_context AND c.xkey = '.$this->db->escape($context).'
			ORDER BY a.date_in DESC, a.id DESC
			');

		$a = array();
		foreach ($tags as $i)
		{
			$tmp = explode(',', $i->tags);
			foreach ($tmp as $t)
			{
				$a[] = trim($t);
			}
		}
		$b = array_count_values($a);
		ksort($b);
		return $b;
	}

	/**
	 * Get categories of articles
	 * public function, return an associative array frequency=>category
	 * Get enabled articles in time window
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string 	$context context
	 * @return  array 	array of categories (strings)
	 */
	public function get_categories(int $id_area, string $lang, string $context)
	{
		$ctgs = $this->db->query('SELECT a.xkeys
		    FROM articles a
            JOIN (
                SELECT MAX(id) AS id, bid
                FROM articles
                WHERE
                    id_area = '.$id_area.' AND
                    lang = '.$this->db->escape($lang).' AND
                    xon = 1 AND
                    date_in <= '.$this->time.' AND
                    (date_out = 0 OR date_out >= '.$this->time.')
                GROUP BY bid
                ) b ON b.id = a.id AND b.bid = a.bid
			JOIN contexts c ON c.id_area = a.id_area AND c.lang = a.lang AND c.code = a.code_context AND c.xkey = '.$this->db->escape($context).'
			ORDER BY a.xkeys ASC');
		$a = array();
		foreach ($ctgs as $i)
		{
			$a[] = trim(strtolower($i->category));
		}
		$b = array_count_values($a);
		ksort($b);
		return $b;
	}

	/**
	 * Get articles by context and key
	 * public function, return an array which contains latest versions of all articles with the requested context and key
	 * Get enabled articles in time window
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string 	$context context
	 * @param   string 	$key Article key
	 * @return  array 	array of article object
	 */
	public function get_items_by_key(int $id_area, string $lang, string $context, string $key)
	{
		return $this->db->query('SELECT a.*
		    FROM articles a
            JOIN (
                SELECT MAX(id) AS id, bid
                FROM articles
                WHERE
                    id_area = '.$id_area.' AND
                    lang = '.$this->db->escape($lang).' AND
                    xon = 1 AND
                    date_in <= '.$this->time.' AND
                    (date_out = 0 OR date_out >= '.$this->time.')
                GROUP BY bid
                ) b ON b.id = a.id AND b.bid = a.bid
			JOIN contexts c ON c.id_area = a.id_area AND c.lang = a.lang AND c.code = a.code_context AND c.xkey = '.$this->db->escape($context).'
			WHERE a.xkeys = '.$this->db->escape($key).'
			ORDER BY a.date_in DESC, a.id DESC');
	}

	/**
	 * Get articles by tag
	 * public function, return an array which contains latest versions of all articles with the requested context and tag
	 * Get enabled articles in time window
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string 	$context context
	 * @param   string 	$tag article tag
	 * @return  array 	array of tags
	 */
	public function get_by_tag(int $id_area, string $lang, string $context, string $tag)
	{
		return $this->db->query('SELECT a.*
		    FROM articles a
            JOIN (
                SELECT MAX(id) AS id, bid
                FROM articles
                WHERE
                    id_area = '.$id_area.' AND
                    lang = '.$this->db->escape($lang).' AND
                    xon = 1 AND
                    date_in <= '.$this->time.' AND
                    (date_out = 0 OR date_out >= '.$this->time.')
                GROUP BY bid
                ) b ON b.id = a.id AND b.bid = a.bid
			JOIN contexts c ON c.id_area = a.id_area AND c.lang = a.lang AND c.code = a.code_context AND c.xkey = '.$this->db->escape($context).'
			WHERE a.tags LIKE '.$this->db->escape('%'.$tag.'%').'
			ORDER BY a.date_in DESC, a.id DESC');
	}

	/**
	 * Get Pages for search by page
	 *
	 * @param   integer	$id_area
	 * @param   string	$lang
	 * @return  array
	 */
	public function get_pages(int $id_area, string $lang)
	{
		return $this->db->query('SELECT id, CONCAT(REPEAT(\'- \', deep), name) AS name
			FROM pages p
			WHERE
				id_area = '.$id_area.' AND
				lang = '.$this->db->escape($lang).'
			ORDER BY ordinal ASC');
	}

	/**
	 * Check ftext field
	 *
	 * @return  integer
	 */
	public function chk_ftext()
	{
		return $this->db->query_var('SELECT COUNT(id) AS n
			FROM articles
			WHERE id_area > 1 AND ftext = \'\'');
	}

	/**
	 * Get articles for ftext field
	 *
	 * @return  array
	 */
	public function get_article_ftext()
	{
		return $this->db->query('SELECT id, name, content
			FROM articles
			WHERE id_area > 1 AND content != \'\'');
	}

	/**
	 * Add filter
	 *
	 * @return  array
	 */
	public function add_filter()
	{
	    $sql = 'ALTER TABLE articles ADD FULLTEXT `ftext` (ftext)';
	    return $this->db->single_exec($sql);
	}
}

/**
 * Empty Article object
 * Necessary for the creation form of new article
 *
 * @package X3CMS
 */
class Article_obj
{
	// object vars
	public $id = 0;
	public $id_area;
	public $lang;
	public $code_context = -1;
	public $id_page = 0;
	public $category;

	public $bid;
	public $date_in;
	public $date_out;

	public $name = '';
	public $content = '';
	public $js = '';
	public $xkeys = '';
	public $tags = '';
	public $author;
	public $module = '';
	public $param = '';

	public $show_author;
	public $show_date;
	public $show_tags;
	public $show_actions;

    public $xlock = 0;

// TODO: maybe in the future public $xschema;

	/**
	 * Constructor
	 * initialize article
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   integer $code_context Context code
	 * @return  void
	 */
	public function __construct(int $id_area, string $lang, int $code_context)
	{
		// unique ID for all article's versions
		$this->bid = md5(time().'-'.$_SESSION['xuid']);

		$this->id_area = $id_area;
		$this->lang = $lang;
		$this->code_context = $code_context;

		$this->author = $_SESSION['mail'];
		$this->date_in = time();
	}
}
