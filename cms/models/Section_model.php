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
 * Model for unlocked sections Items
 *
 * @package X3CMS
 */
class Section_model extends X4Model_core
{
    /**
     * Default settings
     */
	public $settings = array(
		'columns' => 3,
        'col_sizes' => '2+1',
		'bgcolor' => '#ffffff',
		'fgcolor' => '#444444',
		'img_h' => '',		// horizontal image
		'img_v' => '',		// vertical image
		'width' => 'container mx-auto',
		'height' => 'free',	// free: the section will have the height of the contents, fullscreen: the height will be strecthed to be fullscreen
		'style' => '',
        'class' => '',		// useful to link a section to a set of CSS rules
		'col_settings' => ['bg0' => '', 'fg0' => '', 'style0' => '', 'class0' => '', 'bg1' => '', 'fg1' => '', 'style1' => '', 'class1' => '']
	);

	/**
	 * Constructor
	 * set the default table
	 */
	public function __construct()
	{
		parent::__construct('sections');
	}

	/**
	 * Get items
	 */
	public function get_items(int $id_page) : array
	{
		$sql = 'SELECT x.*, IF(p.id IS NULL, u.level, p.level) AS level
            FROM sections x
			JOIN uprivs u ON u.id_area = x.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('pages').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = x.id_page
			WHERE u.id_user = '.intval($_SESSION['xuid']).' AND x.id_page = '.$id_page.'
			GROUP BY x.id
            ORDER BY x.progressive ASC';

		return $this->db->query($sql);
	}

	/**
	 * Get max progressive value by id_area and id_page
	 */
	public function get_max_pos(int $id_area, int $id_page) : int
	{
		return (int) $this->db->query_var('SELECT progressive
			FROM sections
			WHERE
				id_area = '.$id_area.' AND
				id_page = '.$id_page.'
			ORDER BY progressive DESC');
	}

	/**
	 * Get sections by page
	 */
	public function get_sections(int $id_page) : array
	{
		return $this->db->query('SELECT s.id, s.name, s.progressive, s.settings, s.articles
			FROM sections s
			WHERE s.xon = 1 AND s.id_page = '.$id_page.'
			ORDER BY s.progressive ASC');
	}

    /**
	 * Get theme styles by id_page
	 */
	public function get_theme_styles(int $id_page) : array
	{
		$styles = $this->db->query_var('SELECT th.styles
					FROM themes th
                    JOIN templates t ON t.id_theme = th.id
					JOIN pages p ON p.tpl = t.name
					JOIN areas a ON a.id = p.id_area AND a.id_theme = t.id_theme
					WHERE p.id = '.$id_page);

        $res = ['sections' => [], 'articles' => []];
        if (!empty($styles))
        {
            $tmp = json_decode($styles);
            foreach($tmp as $i)
            {
                if ($i->what == 'section')
                {
                    $res['sections'][$i->style] = $i->description;
                }
                else
                {
                    $res['articles'][$i->style] = $i->description;
                }
            }
        }
        return $res;
	}

	/**
	 * Get template data by id_page
	 */
	public function get_template_data(int $id_page, string $field) : mixed
	{
		$fields = array('*', 'id', 'settings', 'sections');

		if (in_array($field, $fields))
		{
			if ($field == '*')
			{
				return $this->db->query_row('SELECT t.'.$field.'
					FROM templates t
					JOIN pages p ON p.tpl = t.name
					JOIN areas a ON a.id = p.id_area AND a.id_theme = t.id_theme
					WHERE p.id = '.$id_page);
			}
			else
			{
				return $this->db->query_var('SELECT t.'.$field.'
					FROM templates t
					JOIN pages p ON p.tpl = t.name
					JOIN areas a ON a.id = p.id_area AND a.id_theme = t.id_theme
					WHERE p.id = '.$id_page);
			}
		}
	}

	/**
	 * count default sections in a page with settings
	 */
	public function count_default_sections(int $id_page) : int
	{
		return (int) $this->db->query_var('SELECT COUNT(s.id) AS n FROM sections s
			WHERE s.id_page = '.$id_page.' AND s.settings LIKE '.$this->db->escape('%"locked":"y"%'));
	}

	/**
	 * Initialize sections for a page
	 * called on section index if page sections are less than template minimum sections
	 */
	public function initialize(int $id_area, int $id_page) : void
	{
		// get template
		$tpl = $this->get_template_data($id_page, '*');

		if ($tpl)
		{
            // get sections
		    $sections = X4Array_helper::indicize($this->get_sections($id_page), 'progressive');

			// get template settings
			$settings = json_decode($tpl->settings, true);

            for ($i = 1; $i <= sizeof($settings); $i++)
			{
                if (isset($settings['s'.$i]))
                {
                    // fix for missing col_sizes
                    if (!isset($settings['s'.$i]['col_sizes']))
                    {
                        $cs = array_fill(0, $settings['s'.$i]['columns'], '1');
                        $settings['s'.$i]['col_sizes'] = implode('+', $cs);
                    }

                    // recreate missing sections
                    if (!isset($sections[$i]))
                    {
                        // to know default section
                        $settings['s'.$i]['locked'] = "y";

                        // create section
                        $post = array(
                            'id_area' => $id_area,
                            'name' => 's'.$i,
                            'id_page' => $id_page,
                            'progressive' => $i,
                            'settings' => json_encode($settings['s'.$i]),
                            'xon' => 1
                        );
                        $result = $this->insert($post);

                        if ($result[1])
                        {
                            AdminUtils_helper::set_priv($_SESSION['xuid'], $result[0], 'sections', $id_area);
                        }
                    }
                }
			}
		}
	}

	/**
	 * Reset section's setting for tpl base of the new theme
	 * Called when you change the theme of an area
	 */
	public function reset(int $id_area) : array
	{
		return $this->db->single_exec('DELETE FROM sections WHERE id_area = '.$id_area);
	}

	/**
	 * Get section contents
	 */
	private function get_by_page(int $id_page, int $progressive) : stdClass
	{
		return $this->db->query_row('SELECT * FROM sections	WHERE id_page = '.$id_page.' AND progressive = '.$progressive);
	}

	/**
	 * Insert and Update section contents
	 */
	public function compose(array $sections) : array
	{
		$a = array();
		foreach ($sections as $i)
		{
			// get existing sections
			$s = $this->get_by_page($i['id_page'], $i['progressive']);

			// update or insert
			if ($s)
			{
				if (isset($i['articles']))
				{
					$res = $this->update($s->id, $i);
				}
				else
				{
					$res = $this->delete($s->id);
				}
			}
			else
			{
				$res = $this->insert($i);
				$a = $res[0];
			}
		}
		return array($a, 1);
	}

	/**
	 * Get Pages IDs by bid in sections
	 */
	public function get_pages_by_bid(string $bid) : array
	{
		return $this->db->query('SELECT id_page FROM sections WHERE articles LIKE '.$this->db->escape('%'.$bid.'%'));
	}

	/**
	 * Update context and page ID in articles by bid
	 */
	public function recode(int $id_area, string $hold_context, string $bid, int $id_page) : array
	{
		// default contexts
		$codes = array('drafts' => 0, 'pages' => 1, 'multi' => 2);

		// update articles
		$sql = 'UPDATE articles SET updated = NOW(), code_context = '.$codes[$hold_context].', id_page = '.$id_page.'
            WHERE code_context != 2 AND bid = '.$this->db->escape($bid).' AND id_area = '.$id_area;
		$this->db->single_exec($sql);
	}

	/**
	 * Get articles by bid
	 */
	public function get_articles(
        int $id_area,
        string $lang,
        string $jbids       // json_encoded arrayof bids, where a bid is the article unique ID
    ) : array
	{
		$bids = json_decode($jbids, true);

		$artt = [];
		if ($bids)
		{
			foreach ($bids as $i)
			{
				$a = $this->db->query_row('SELECT *
					FROM articles
					WHERE xon = 1 AND id_area = '.$id_area.' AND lang = '.$this->db->escape($lang).' AND bid = '.$this->db->escape($i).'
					ORDER BY id DESC');

				if ($a)
                {
					$artt[] = $a;
                }
			}
		}
		return $artt;
	}

	/**
	 * Get contexts
	 */
	public function get_contexts(int $id_area, string $lang) : array
	{
		return $this->db->query('SELECT x.*
			FROM contexts x
			JOIN uprivs u ON u.id_area = x.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('contexts').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = x.id AND p.level > 0
			WHERE
				x.xon = 1 AND
				x.code < 3 AND
				x.id_area = '.$id_area.' AND
				x.lang = '.$this->db->escape($lang).'
			GROUP BY x.id
			ORDER BY x.code ASC');
	}

	/**
	 * Get articles to publish
	 */
	public function get_articles_to_publish(stdClass $page, string $by) : array
	{
		// sorting
		$order = ($by == 'name')
			? 'a.name ASC'
			: 'a.id DESC';

		return $this->db->query('SELECT a.*, c.xkey, IF(p.id IS NULL, u.level, p.level) AS level
			FROM articles a
			LEFT JOIN contexts c ON c.id_area = a.id_area AND c.lang = a.lang AND c.code = a.code_context
			JOIN uprivs u ON u.id_area = a.id_area AND u.id_user = '.intval($_SESSION['xuid']).' AND u.privtype = '.$this->db->escape('articles').'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = a.id
			WHERE a.xon = 1 AND a.id_area = '.$page->id_area.' AND a.lang = '.$this->db->escape($page->lang).' AND c.code < 3 AND
				(
				a.id_page = '.$page->id.' OR c.code != 1
				)
			GROUP BY a.bid
			ORDER BY a.code_context ASC, '.$order);
	}

	/**
	 * Get article by bid
	 */
	public function get_by_bid(string $bid) : stdClass
	{
		return $this->db->query_row('SELECT *
			FROM articles
			WHERE xon = 1 AND bid = '.$this->db->escape($bid).'
			ORDER BY updated DESC');
	}
}

/**
 * Empty Section object
 * Necessary for the creation form of new section
 *
 * @package X3CMS
 */
class Section_obj
{
	public $id_area = 0;
	public $id_page = 0;
	public $name;
	public $progressive = 1;
	public $settings = '';
    public $xlock = 0;

	/**
	 * Constructor
	 * Initialize the new section
	 */
	public function __construct(int $id_area, int $id_page, string $settings)
	{
		$this->id_area = $id_area;
		$this->id_page = $id_page;
		$this->settings = $settings;
	}
}
