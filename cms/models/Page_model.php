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
 * Model for Page Items
 * The constructor set Area ID and Language code so each object lives within it
 *
 * @package X3CMS
 */
class Page_model extends X4Model_core
{
	/**
	 * @var integer Area ID
	 */
	protected $id_area;

	/**
	 * @var string	Language code
	 */
	protected $lang;

	/**
	 * @var string	URL of parent page
	 */
	protected $xfrom;

	/**
	 * @var object	Menu model
	 */
	protected $menu;

	/**
	 * Constructor
	 *
	 * @param integer	$id_area Area ID
	 * @param string	$lang Language code
	 * @param integer	$id_page Page ID
	 * @param string	$from Parent page URL
	 * @return  void
	 */
	public function __construct(int $id_area = 0, string $lang = '', int $id_page = 0, string $from = '')
	{
		// set default table
		parent::__construct('pages');

		if ($id_page)
		{
			// create object by Page ID
			$data = $this->db->query_row('SELECT p.id_area, p.lang, p.xfrom
				FROM pages p
				JOIN areas a ON a.id = p.id_area
				WHERE p.id = '.$id_page);

			// override parameters
			$id_area = $data->id_area;
			$lang = $data->lang;
			$from = $data->xfrom;
		}

		// initialize object
		$this->id_area = $id_area;
		$this->lang = $lang;
		$this->xfrom = $from;
		$this->menu = new Menu_model();
	}

	/**
	 * Check if a page with the same URL already exists
	 *
	 * @param   string	$url URL to check
	 * @param   integer $id Page ID, if the page already exists
	 * @return  integer	the number of pages with the searched URL
	 */
	public function exists(string $url, int $id = 0)
	{
		// condition
		$where = ($id)
			? ' AND id <> '.$id
			: '';

		return $this->db->query_var('SELECT COUNT(id)
			FROM pages
			WHERE
				id_area = '.$this->id_area.' AND
				lang = '.$this->db->escape($this->lang).' AND
				url = '.$this->db->escape($url).' '.$where);
	}

	/**
	 * Get Page by URL
	 *
	 * @param   string	$url Page URL
	 * @return  object
	 */
	public function get_page(string $url)
	{
        return $this->db->query_row('SELECT p.*, a.name AS area
			FROM pages p
			JOIN areas a ON a.id = p.id_area
			WHERE
				p.id_area = '.$this->id_area.' AND
				p.lang = '.$this->db->escape($this->lang).' AND
				p.url = '.$this->db->escape($url));
	}

	/**
	 * Get Page by ID
	 *
	 * @param   integer	$id Page ID
	 * @return  object
	 */
	public function get_page_by_id(int $id)
	{
		return $this->db->query_row('SELECT p.*, a.name AS area
			FROM pages p
			JOIN areas a ON a.id = p.id_area
			WHERE p.id = '.$id);
	}

	/**
	 * Get Parent URL by Page URL
	 *
	 * @param   string	$url Page URL
	 * @return  object
	 */
	public function get_from(string $url)
	{
		return $this->db->query_row('SELECT p.xfrom, p.url, p.name, p.description, p.deep
			FROM pages p
			JOIN areas a ON a.id = p.id_area
			WHERE
				p.id_area = '.$this->id_area.' AND
				p.lang = '.$this->db->escape($this->lang).' AND
				p.url = '.$this->db->escape($url));
	}

	/**
	 * Get Pages by Parent URL and Deep
	 * The deep indicates the distance from the home page
	 * A sub page of the homepage has deep = 1
	 * A sub page of a subpage of the home has deep = 2 and so on
	 *
	 * @param   string	$xfrom Parent URL
	 * @param   integer	$deep Required Deep
	 * @param   string	$diff Page URL to exclude
	 * @return  object
	 */
	public function get_pages(string $xfrom = '', int $deep = 0, string $diff = '' )
	{
		// conditions

		// correction for special case
		// the home have url and xfrom = home
		if ($xfrom == 'home')
		{
			$from = 'AND (p.url = \'home\' OR p.xfrom = \'home\')';
		}
		else if($deep)
		{
			$from = 'AND p.deep = '.($deep+1).' AND p.xfrom = '.$this->db->escape($xfrom);
		}
		else
		{
			$from = '';
		}

		// if isset diff exclude a page
		if (!empty($diff) && $diff != 'home')
		{
			$from .= ' AND p.url <> '.$this->db->escape($diff);
		}

		return $this->db->query('SELECT p.*, CONCAT(IF(p.id_menu > 0, m.name, \'|\'), REPEAT(\'--\', p.deep), \'>&nbsp;\', p.title) AS deep_title, a.title AS area, IF(pr.id IS NULL, u.level, pr.level) AS level
			FROM pages p
			JOIN areas a ON a.id = p.id_area
            LEFT JOIN menus m ON m.id = p.id_menu
			JOIN uprivs u ON u.id_area = a.id AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('pages').'
			LEFT JOIN privs pr ON pr.id_who = u.id_user AND pr.what = u.privtype AND pr.id_what = a.id
			WHERE
				p.id_area = '.$this->id_area.' AND
				p.lang = '.$this->db->escape($this->lang).'
				'.$from.'
			GROUP BY p.id
			ORDER BY p.ordinal ASC, p.url ASC');
	}

    /**
	 * Get subpages
	 *
	 * @param   string	$xfrom Page URL
     * @param   integer $id_menu
	 * @return  object
	 */
	public function get_subpages(string $xfrom, int $id_menu)
	{
		return $this->db->query('SELECT (xpos+1) AS xpos, name
			FROM pages
			WHERE
				id_area = '.$this->id_area.' AND
				lang = '.$this->db->escape($this->lang).' AND
				xfrom = '.$this->db->escape($xfrom).' AND
                url != \'home\' AND
                id_menu = '.$id_menu.'
            ORDER BY xpos ASC');
	}

	/**
	 * Update page data
	 * Updating the page must maintain the consistency of relations with the other pages
	 * After the update we have to refresh the sitemap.xml file
	 *
	 * @param   stdClass $page
	 * @param   array	$post array
	 * @param   string	$domain Domain name
	 * @return  array
	 */
	public function update_page(stdClass $page, array $post, string $domain)
	{
		// build the update query
		$update = '';
		foreach ($post as $k => $v)
		{
			$update .= ', '.addslashes($k).' = '.$this->db->escape($v);
		}
		$sql = array();
		$sql[] = 'UPDATE pages SET updated = NOW() '.$update.' WHERE id = '.$page->id;

		// if the Page URL is changed we need to update subpages data
		if (isset($post['url']) && $post['url'] != $page->url)
		{
			$sql[] = 'UPDATE pages SET xfrom = '.$this->db->escape($post['url']).'
				WHERE
					id_area = '.$page->id_area.' AND
					lang = '.$this->db->escape($page->lang).' AND
					xfrom = '.$this->db->escape($page->url);
		}

		// if the parent page is changed we need to update xpos and deep
        $deep = $page->deep;
		if (isset($post['xfrom']) && $post['xfrom'] != $page->xfrom)
		{
            // handle id_menu, xpos and deep
			if ($post['xfrom'] == 'home')
			{
				// simple case
                $deep = 1;
				$sql[] = 'UPDATE pages SET updated = NOW(), deep = 1 WHERE id = '.$page->id;
			}
			else
			{
				// get id_menu and deep of parent
				$deep = $this->get_deep($post['xfrom']) + 1;
				$sql[] = 'UPDATE pages SET updated = NOW(), deep = '.$deep.' WHERE id = '.$page->id;
			}

			// we need to update the page order in which it was the page now moved
			if ($page->xpos > 0)
			{
				// shift back xpos in old xfrom
				$sql[] = 'UPDATE pages
					SET updated = NOW(), xpos = (xpos - 1)
					WHERE
						id_area = '.$page->id_area.' AND
						lang = '.$this->db->escape($page->lang).' AND
						xfrom = '.$this->db->escape($page->xfrom).' AND
						id_menu = '.$page->id_menu.' AND
						deep = '.$page->deep.' AND
						xpos > '.$page->xpos;
			}

            // there are subpages?
            $deep_gap = $deep - $page->deep;
            if ($deep_gap != 0)
            {
                $deep_change = $deep_gap > 0
                    ? ', deep = (deep +'.$deep_gap.')'
                    : ', deep = (deep '.$deep_gap.')';

                $sql[] = 'UPDATE pages
					SET updated = NOW() '.$deep_change.'
					WHERE
						id_area = '.$page->id_area.' AND
						lang = '.$this->db->escape($page->lang).' AND
						xfrom = '.$this->db->escape($page->url);
            }
		}
        // we need to update xpos of the pages where me moved
        // shift xpos in new xfrom
        $sql[] = 'UPDATE pages
            SET updated = NOW(), xpos = (xpos + 1)
            WHERE
                id_area = '.$page->id_area.' AND
                lang = '.$this->db->escape($page->lang).' AND
                xfrom = '.$this->db->escape($post['xfrom']).' AND
                id_menu = '.$post['id_menu'].' AND
                deep = '.$deep.' AND
                url != '.$this->db->escape($page->url).' AND
                xpos >= '.$post['xpos'];

		// perform the update
		$result = $this->db->multi_exec($sql);

		// refresh deep, xpos and ordinal
		$this->menu->ordinal($page->id_area, $page->lang, 'home', 'A');

		// update sitemap.xml
		$this->update_sitemap($domain);

		return $result;
	}

	/**
	 * Insert a new page
	 * After the insertion we have to update the sitemap.xml file
	 *
	 * @param   array	$post array
	 * @param   string	$domain Domain name
	 * @return  array
	 */
	public function insert_page(array $post, string $domain)
	{
		// get deep
		$array['deep'] = $this->get_deep($post['xfrom']) + 1;

		// insert new page
		$res = $this->insert($post);

		// create a default article
		if ($res[1])
		{
			$data = array(
				'id_area' => $this->id_area,
				'lang' => $this->lang,
				'bid' => md5(time().'-'.$_SESSION['xuid']),
				'id_page' => $res[0],
				'name' => $post['name'],
				'code_context' => 1,
				'content' => '<h1>'.$post['name'].'</h1>',
				'date_in' => time(),
				'id_editor' => $_SESSION['xuid'],
				'xon' => 1
			);

			// insert empty article
			$res_article = $this->insert($data, 'articles');
		}

		// refresh ordinal
		$this->menu->ordinal($this->id_area, $this->lang, 'home', 'A');

		// update sitemap.xml
		$this->update_sitemap($domain);

		return $res;
	}

	/**
	 * Get deep of a page in the site tree by Parent URL
	 *
	 * @param   string	$xfrom Parent URL
	 * @return  integer
	 */
	private function get_deep(string $xfrom)
	{
		return (int) $this->db->query_var('SELECT deep FROM pages WHERE id_area = '.$this->id_area.' AND lang = '.$this->db->escape($this->lang).' AND url = '.$this->db->escape($xfrom));
	}

	/**
	 * Initialize a new area
	 * Insert default pages (home, msg, x3admin, search and logout if the area is private)
	 * Insert default articles for each page
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string	$lang Language code
	 * @param   array	$array Array of insert queries
	 * @return  array
	 */
	public function initialize_area(int $id_area, string $lang, array $array)
	{
		$sql = array();
		foreach ($array as $i)
		{
			// insert page
			$res = $this->insert($i[1]);

			// build query for related article
			if ($res[1])
			{
				$sql[] = "INSERT INTO `articles` (`updated`, `bid`, `id_area`, `lang`, `code_context`, `name`, `id_page`, `id_editor`, `date_in`, `date_out`, `content`, `module`, `param`, `xlock`, `xon`) VALUES
					(NOW(), '".md5(time().'-'.$_SESSION['xuid'].$res[0])."', $id_area, '$lang', 1, '".$i[0]."', ".$res[0].", ".$_SESSION['xuid'].", UNIX_TIMESTAMP(), 0, '', '', '', 0, 1)";
			}
		}

		// performs insertion of articles
		if (!empty($sql)) $res = $this->db->multi_exec($sql);
		return $res;
	}

	/**
	 * Initialize contexts
	 * When we create a new area we must also create necessary default contexts
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string	$lang Language code
	 * @return  array
	 */
	public function initialize_context(int $id_area, string $lang)
	{
		$sql = array();
		$sql[] = 'INSERT INTO contexts (updated, id_area, lang, xkey, name, code, xlock, xon) VALUES (NOW(), '.$id_area.', '.$this->db->escape($lang).', \'drafts\', \'drafts\', 0, 0, 1)';
		$sql[] = 'INSERT INTO contexts (updated, id_area, lang, xkey, name, code, xlock, xon) VALUES (NOW(), '.$id_area.', '.$this->db->escape($lang).', \'pages\', \'pages\', 1, 0, 1)';
		$sql[] = 'INSERT INTO contexts (updated, id_area, lang, xkey, name, code, xlock, xon) VALUES (NOW(), '.$id_area.', '.$this->db->escape($lang).', \'multi\', \'multipages\', 2, 0, 1)';
		$res = $this->db->multi_exec($sql);
	}

	/**
	 * Delete a page
	 * After the deletion we have to update the sitemap.xml file
	 *
	 * @param   integer	$id Page ID
	 * @param   string	$domain Domain
	 * @return  array
	 */
	public function delete_page(int $id, string $domain)
	{
		// get page data
		$page = $this->get_page_by_id($id);

		$sql = array();
		// delete related articles
		$sql[] = 'DELETE FROM articles WHERE id_page = '.$id;

		// delete sections
		$sql[] = 'DELETE FROM sections WHERE id_page = '.$id;

        // delete the page
		$sql[] = 'DELETE FROM pages WHERE id = '.$id;

		// move up subpages
		$sql[] = 'UPDATE pages SET xfrom = '.$this->db->escape($page->xfrom).', deep = '.$page->deep.', xpos = 0 WHERE id_area = '.$page->id_area.' AND id_area = '.$this->db->escape($page->lang);

		$res = $this->db->multi_exec($sql);

		if ($res[1])
		{
			// refresh ordinals
			$this->menu->ordinal($page->id_area, $page->lang, 'home', 'A');

			// update sitemap.xml
			$this->update_sitemap($domain);
		}

		return $res;
	}

	/**
	 * Get articles by Page ID
	 *
	 * @param   integer	$id Page ID
	 * @param   boolean	$rows One or more than one result
	 * @return  array
	 */
	public function get_content_by_id(int $id, int $rows = 0)
	{
		if ($rows)
		{
			// get many rows (advanced editing)
			return $this->db->query('SELECT a.*, IF(p.id IS NULL, u.level, p.level) AS level
				FROM articles a
				JOIN uprivs u ON u.id_area = a.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('articles').'
				LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = a.id
				WHERE a.id_page = '.$id.'
				GROUP BY a.id
				ORDER BY a.id DESC');
		}
		else
		{
			// get one row (simple editing)
			return $this->db->query_row('SELECT * FROM articles WHERE id_page = '.$id.' ORDER BY id DESC');
		}
	}

	/**
	 * Update sitemap.xml
	 *
	 * @param   string	$domain Domain name
	 * @return  void
	 */
	private function update_sitemap(string $domain)
	{
		// get pages
		$pages = $this->db->query('SELECT p.url, p.lang, a.updated
			FROM pages p
			JOIN articles a ON a.id_page = p.id
			JOIN alang l ON l.code = p.lang AND p.id_area = l.id_area
			WHERE l.xon = 1 AND p.id_area = 2 AND p.xon = 1 AND p.hidden = 0 AND a.xon = 1
			GROUP BY p.id
			ORDER BY p.lang ASC, p.ordinal ASC, a.updated DESC');

		// build xml
		$head = '<?xml version="1.0" encoding="utf-8"?><urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.NL;

		$body = '';
		if ($pages) {

		    $domain = (MULTILANGUAGE)
		        ? $domain.'/'.$pages[0]->lang
		        : $domain;

			foreach ($pages as $i)
			{
				switch($i->url) {
				case 'map':
					$body .= '<url>
    <loc>'.$domain.'/map</loc>
    <lastmod>'.str_replace(' ', 'T', date('Y-m-d H:i:s')).'+01:00</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
</url>'.NL;
					break;
				case 'home':
					$body .= '<url>
    <loc>'.$domain.'</loc>
    <lastmod>'.str_replace(' ', 'T', $i->updated).'+01:00</lastmod>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
</url>'.NL;
					break;
				default:
					$body .= '<url>
    <loc>'.$domain.'/'.$i->url.'</loc><lastmod>'.str_replace(' ', 'T', $i->updated).'+01:00</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
</url>'.NL;
				}
			}
		}
		$body .= '</urlset>';

		// write file
		$check = file_put_contents(APATH.'files/sitemap.xml', $head.$body);
	}

	/**
	 * Get available templates
	 *
	 * @return  array	Array of objects
	 */
	public function get_templates()
	{
		return $this->db->query('SELECT t.*
			FROM templates t
			JOIN themes h ON h.id = t.id_theme
			JOIN areas a ON a.id_theme = h.id
			WHERE t.xon = 1 AND a.id = '.$this->id_area.'
			ORDER BY t.id ASC');
	}

	/**
	 * Get theme by area
	 *
	 * @return  object
	 */
	public function get_theme(int $id_area)
	{
		return $this->db->query_row('SELECT t.name
			FROM themes t
			JOIN areas a ON a.id_theme = t.id
			WHERE t.xon = 1 AND a.id = '.$id_area);
	}

	/**
	 * Check for redirects
	 *
	 * @param   string	$url Page URL
	 * @return  object
	 */
	public function check_redirect(string $url)
	{
	    return $this->db->query_row('SELECT p.redirect_code, p.url
			FROM pages p
			WHERE p.xon = 1 AND p.redirect = '.$this->db->escape($url));
	}

	// FOR DUPLICATING

	/**
	 * Duplicate area for another language
	 *
     * @param integer   $id_area
     * @param string    $old_lang
     * @param string    $new_lang
	 * @return  array(array_of_installed_modules, res)
	 */
	public function duplicate_area_lang(int $id_area, string $old_lang, string $new_lang)
	{
	    // sync contexts
	    $old = X4Array_helper::indicize($this->db->query('SELECT *
			FROM contexts
			WHERE id_area = '.$id_area.' AND lang = '.$this->db->escape($old_lang).'
			ORDER BY code ASC'), 'code');

		$new = X4Array_helper::indicize($this->db->query('SELECT *
			FROM contexts
			WHERE id_area = '.$id_area.' AND lang = '.$this->db->escape($new_lang).'
			ORDER BY code ASC'), 'code');

		// insert contexts
		foreach ($old as $k => $v)
		{
		    if (!in_array($k, $new))
		    {
		        // create the new context
		        $post = (array) $v;
		        unset($post['id'], $post['updated']);
		        $post['lang'] = $new_lang;
		        $res = $this->insert($post, 'contexts');
            }
		}

		// sync pages

		// get pages in the old
		$old = X4Array_helper::indicize($this->db->query('SELECT *
			FROM pages
			WHERE id_area = '.$id_area.' AND lang = '.$this->db->escape($old_lang).'
			ORDER BY ordinal ASC'), 'url');

		// get pages already in new to avoid duplicates
		$new = X4Array_helper::indicize($this->db->query('SELECT *
			FROM pages
			WHERE id_area = '.$id_area.' AND lang = '.$this->db->escape($new_lang).'
			ORDER BY ordinal ASC'), 'url');

		// memo for modules
		$modules = array();
		$res = 0;

		// insert pages
		foreach ($old as $k => $v)
		{
		    $old_id_page = $v->id;
		    $id_page = 0;

		    if (!in_array($k, $new))
		    {
		        // create the new page
		        $post = (array) $v;
		        unset($post['id'], $post['updated']);
		        $post['lang'] = $new_lang;
		        $res = $this->insert($post, 'pages');

                if ($res[1])
                {
                    $id_page = $res[0];
                }
            }
		    else
		    {
		        // check for contents
		        $id_page = $new[$k]->id;
		    }

		    if ($id_page)
		    {
		        // get sections
		        $sections = $this->db->query('SELECT *
                    FROM sections
                    WHERE id_area = '.$id_area.' AND id_page = '.intval($old_id_page).'
                    ORDER BY progressive ASC');

                if ($sections)
                {
                    foreach ($sections as $i)
                    {
                        $articles = explode('|', $i->articles);
                        $bids = array();

                        if (!empty($articles))
                        {
                            foreach ($articles as $ii)
                            {
                                if (!empty($ii))
                                {
                                    // get the article
                                    $art = $this->db->query_row('SELECT *
                                        FROM articles
                                        WHERE
                                            id_area = '.$id_area.' AND
                                            lang = '.$this->db->escape($old_lang).' AND
                                            bid = '.$this->db->escape($ii).' AND
                                            xon = 1
                                        ORDER BY id DESC');

                                    if ($art)
                                    {
                                        $bid = md5($art->id.time().'-'.$_SESSION['xuid']);

                                        // insert the article
                                        $post = (array) $art;

                                        unset($post['id'], $post['updated']);

                                        $post['bid'] = $bid;
                                        $post['lang'] = $new_lang;
                                        $post['id_page'] = $id_page;
                                        $post['id_editor'] = $_SESSION['xuid'];

                                        $res = $this->insert($post, 'articles');

                                        if ($res[1])
                                        {
                                            // memo for bid
                                            $bids[] = $bid;

                                            // modules
                                            if (!empty($i->module) && !in_array($i->module, $modules))
                                            {
                                                $modules[] = $i->module;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        // create section
                        $post = array(
                            'id_area' => $id_area,
                            'id_page' => $id_page,
                            'progressive' => $i->progressive,
                            'articles' => implode('|', $bids),
                            'xon' => 1
                        );

                        $res = $this->insert($post, 'sections');
                    }
                }
            }
		}
		return array($modules, $res);
	}
}

/**
 * Empty Page object
 * Necessary for the creation form of new page
 *
 * @package X3CMS
 */
class Page_obj
{
	// object vars
	public $id_area;
	public $lang;
	public $url;
	public $name;
	public $description;
	public $xfrom = '';
	public $id_menu = 0;
	public $deep = 0;
	public $area = '';

	public $robot = '';
	public $redirect_code = 0;
	public $redirect = '';

	/**
	 * Constructor
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string	$lang Language code
	 * @return  void
	 */
	public function __construct(int $id_area, string $lang)
	{
		$this->id_area = $id_area;
		$this->lang = $lang;
	}
}
