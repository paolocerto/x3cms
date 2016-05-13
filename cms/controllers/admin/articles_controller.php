<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
 
/**
 * Controller for Articles
 * 
 * @package X3CMS
 */
class Articles_controller extends X3ui_controller
{
	/**
	 * tabs cases
	 */
	protected $cases = array(
			'latest_articles' => array('articles/index', 'btm'),
			'by_page' => array('articles/by_page', 'bta'), 
			'context_order' => array('articles/index', 'btm'),
			'category_order' => array('articles/index', 'btm'),
			'author_order' => array('articles/index', 'btm'), 
			'key_order' => array('articles/index', 'btm')
		);
	
	/**
	 * Constructor
	 * check if user is logged
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::logged();
	}
	
	/**
	 * Show articles (table view)
	 *
	 * @return  void
	 */
	public function _default()
	{
		// redirect to index
		$this->index(2, X4Route_core::$lang);
	}
	
	/**
	 * Show articles (table view)
	 * Can show articles by context, by category, by author, by key, by page
	 * Default view is reverse chronological order
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string	$case view switcher
	 * @param   mixed   $id_what view parameter
	 * @param   integer $pp index for pagination
	 * @param   string	$str Search string
	 * @return  void
	 */
	public function index($id_area = 2, $lang = '' , $case = 'latest_articles', $id_what = 0, $pp = 0, $str = '')
	{
		// load dictionary
		$this->dict->get_wordarray(array('articles'));
		
		$_SESSION['referer'] = 'index/'.$id_area.'/'.$lang.'/'.$case.'/'.$id_what.'/'.$pp.'/'.$str;
		
		// get page
		$page = $this->get_page('articles');
		$navbar = array($this->site->get_bredcrumb($page));
		
		// content
		$mod = new Article_model();
		
		$view = new X4View_core('container');
			
		$view->content = new X4View_core('articles/article_list');
		$view->content->navbar = $navbar;
		switch($case) 
		{
			case 'context_order':
				$cmod = new Context_model();
				$con = $cmod->get_contexts($id_area, $lang);
				if ($id_what == 0 && $con) 
				{
				    $id_what = $con[0]->code;
				}
				$view->content->items = X4Pagination_helper::paginate($mod->get_by_context($id_area, $lang, $id_what, $str), $pp);
				$view->content->contexts = $con;
				break;
			case 'category_order':
				$cmod = new Category_model();
				$ctg = $cmod->get_categories($id_area, $lang);
				if ($id_what === 0 && $ctg) 
				{
					$id_what = $ctg[0]->name;
				}
				$view->content->items = X4Pagination_helper::paginate($mod->get_by_category($id_area, $lang, $id_what, $str), $pp);
				$view->content->categories = $ctg;
				break;
			case 'author_order':
				$aut = $mod->get_authors($id_area, $lang);
				if ($id_what == 0 && $aut) 
				{
					$id_what = $aut[0]->id_editor;
				}
				$view->content->items = X4Pagination_helper::paginate($mod->get_by_author($id_area, $lang, $id_what, $str), $pp);
				$view->content->authors = $aut;
				break;
			case 'key_order':
				$keys = $mod->get_keys($id_area, $lang);
				if ($id_what == 0 && $keys) 
				{
					$id_what = $keys[0]->xkeys;
				}
				$view->content->items = X4Pagination_helper::paginate($mod->get_by_key($id_area, $lang, $id_what, $str), $pp);
				$view->content->keys = $keys;
				break;
			case 'by_page':
				$pmod = new Page_model();
				$spage = $pmod->get_by_id($id_what);
				$view->content->items = X4Pagination_helper::paginate($mod->get_by_page($id_area, $lang, $id_what, $str), $pp);
				$view->content->page = $spage;
				break;
			default:
				$view->content->items = X4Pagination_helper::paginate($mod->get_articles($id_area, $lang, 'id', $str), $pp);
				break;
		}
		
		// for the construction of the tabs
		$view->content->xcase = $case;
		$view->content->cases = $this->cases;
		$view->content->id_what = $id_what;
		$view->content->pp = $pp;
		$view->content->str = $str;
		
		// area switcher
		$view->content->id_area = $id_area;
		$area = new Area_model();
		$view->content->areas = $area->get_areas();
		
		// language switcher
		$view->content->lang = $lang;
		$lang = new Language_model();
		$view->content->langs = $lang->get_languages();
		
		$view->render(TRUE);
	}
	
	/**
	 * Article filter
	 *
	 * @param   integer $id_area Area ID
	 * @param   string $lang Language code
	 * @param   string $xcase
	 * @param   integer $id_what view parameter
	 * @param   string $str
	 * @return  void
	 */
	public function filter($id_area, $lang, $xcase = '', $id_what = '', $str = '')
	{
		// load the dictionary
		$this->dict->get_wordarray(array('articles'));
		
		if (X4Route_core::$post)
		{
		    // set message
            $msg = AdmUtils_helper::set_msg(array(0,1));
            $msg->update[] = array(
                    'element' => 'topic', 
                    'url' => BASE_URL.'articles/index/'.$id_area.'/'.$lang.'/'.$xcase.'/'.$id_what.'/0/'.urlencode(trim($_POST['search'])),
                    'title' => null
                );
            $this->response($msg);
		}
		else
		{
		    $js = '';
            switch ($xcase)
            {
                case 'bulk':
                    $js = 'bulkize("bulk_selector", "bulkable", "bulk");';
                    echo ' <button type="button" name="bulk" id="bulk" class="button" onclick="setForm(\'bulk_action\');">'._DELETE_BULK.'</button>';
                    break;
                case '':
                    // add nothing
                    break;
                default:
                    echo '<form id="searchitems" name="searchitems" action="'.BASE_URL.'articles/filter/'.$id_area.'/'.$lang.'/'.$xcase.'/'.$id_what.'" method="POST" onsubmit="return false;">
                        <input type="text" name="search" id="search" value="'.urldecode($str).'" title="'._ARTICLES_SEARCH_MSG.'" />
                        <button type="button" name="searcher" class="button" onclick="setForm(\'searchitems\');">'._FIND.'</button>
                        </form>';
                    break;
            }
            
	    echo '<a class="btf" href="'.BASE_URL.'articles/edit/'.$id_area.'/'.$lang.'" title="'._NEW_ARTICLE.'"><i class="fa fa-plus fa-lg"></i></a>
			
<script>
window.addEvent("domready", function()
{
	buttonize("filters", "btf", "topic");
	'.$js.'
});
</script>';
		}
	}
	
	/**
	 * Article bulk action
	 *
	 * @param   integer $id_area Area ID
	 * @param   string $lang Language code
	 * @param   string $bid Article unique code
	 * @return  void
	 */
	public function bulk($id_area, $lang, $bid)
	{
		$msg = null;
		if (X4Route_core::$post) 
		{
			if (isset($_POST['bulk']))
			{
				if (is_array($_POST['bulk']) && !empty($_POST['bulk']))
				{
					$mod = new Article_model();
					$perm = new Permission_model();
					foreach($_POST['bulk'] as $i)
					{
						$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'articles', $i, 4);
						if (is_null($msg))
						{
							$result = $mod->delete($i);
							
							if ($result[1])
							{
								$perm->deleting_by_what('articles', $i);
							}
						}
					}
					
					// set message
					$this->dict->get_words();
					$msg = AdmUtils_helper::set_msg($result);
					
					// set update
					if ($result[1])
					{
						$msg->update[] = array(
							'element' => 'topic',
							'url' => BASE_URL.'articles/history/'.$id_area.'/'.$lang.'/'.$bid.'/0',
							'title' => null
						);
					}
				}
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Page selector form (use Ajax)
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang language code
	 * @return  void
	 */
	public function by_page($id_area, $lang)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'articles'));
		
		// build form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $_SERVER["HTTP_REFERER"],
			'name' => 'from'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id_area,
			'name' => 'id_area'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $lang,
			'name' => 'lang'
		);
		
		$mod = new Article_model();
		$fields[] = array(
			'label' => ucfirst(_PAGE),
			'type' => 'select', 
			'value' => '',
			'name' => 'id_page',
			'options' => array($mod->get_pages($id_area, $lang), 'id', 'name'),
			'extra' => 'class="large"'
		);
		
		// if submitted
		if (X4Route_core::$post) 
		{
			$this->search_list(BASE_URL.'articles/index/'.$_POST['id_area'].'/'.$_POST['lang'].'/by_page/'.$_POST['id_page']);
			die;
		}
		
		// contents
		$view = new X4View_core('editor');
		$view->title = _BY_PAGE;
		
		// form builder
		$view->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SEARCH, 'buttons'), 'post', '', 
			'onclick="setForm(\'editor\');"');
		$view->render(TRUE);
	}
	
	/**
	 * URL redirection
	 *
	 * @access	private
	 * @param   string  $url URL
	 * @return  void
	 */
	private function search_list($url)
	{
		$msg = AdmUtils_helper::set_msg(true);
		$msg->update[] = array(
			'element' => 'topic',
			'url' => urldecode($url),
			'title' => null
		);
		$this->response($msg);
	}
	
	/**
	 * Change status
	 *
	 * @param   string  $what field to change
	 * @param   integer $id ID of the item to change
	 * @param   integer $value value to set (0 = off, 1 = on)
	 * @return  void
	 */
	public function set($what, $id, $value = 0)
	{
		// check permissions
		$val = ($what == 'xlock') 
			? 4 
			: 3;
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'articles', $id, $val);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// do action
			$mod = new Article_model();
			$result = $mod->update($id, array($what => $value));
			
			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);
			
			// set update
			if ($result[1])
				$msg->update[] = array(
					'element' => $qs['div'],
					'url' => urldecode($qs['url']),
					'title' => null
				);
		}
		$this->response($msg);
	}
	
	/**
	 * Change status by bid
	 * bid is the uniqueID of articles
	 *
	 * @param   string	$what field to change
	 * @param   integer $id ID of the item to change
	 * @param   integer $value value to set (0 = off, 1 = on)
	 * @return  void
	 */
	public function set_by_bid($what, $id, $value = 0)
	{
		// check permissions
		$val = ($what == 'xlock') 
			? 4 
			: 3;
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'articles', $id, $val);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// do action
			$mod = new Article_model();
			$result = $mod->update_by_bid($id, array($what => $value));
			
			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);
			
			// set update
			if ($result[1])
				$msg->update[] = array(
					'element' => $qs['div'],
					'url' => urldecode($qs['url']),
					'title' => null
				);
		}
		$this->response($msg);
	}
	
	/**
	 * Get the list of available modules
	 * Called via Ajax
	 *
	 * @param   integer $id_area Area ID
	 * @return  string 	Options list
	 */
	public function refresh_module($id_area)
	{
		// get array of modules
		$plugin = new X4Plugin_model();
		$items = $plugin->get_modules($id_area);
		
		// build options list
		$str = '<option value=""></option>';
		foreach($items as $i)
		{
			$str .= '<option value="'.$i->name.'">'.$i->description.'</option>';
		}
		echo $str;
	}
	
	/**
	 * Get the list of available contexts
	 * Called via Ajax
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @return  string 	Options list
	 */
	public function refresh_context($id_area, $lang)
	{
		// get array of contexts
		$mod = new Context_model();
		$items = $mod->get_contexts($id_area, $lang, 1);
		
		// build options list
		$str = '';
		foreach($items as $i)
		{
			$str .= '<option value="'.$i->code.'">'.$i->name.'</option>';
		}
		echo $str;
	}
	
	/**
	 * Get the list of available pages
	 * Called via Ajax
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   integer	$code_context Context code
	 * @return  string 	Options list
	 */
	public function refresh_pages($id_area, $lang, $code_context)
	{
		// get context key
		$mod = new Context_model();
		$context = $mod->get_by_code($id_area, $lang, $code_context);
		
		$str = '';
		// only if code key is pages return options list
		if ($context->xkey == 'pages') 
		{
			// get pages
			$items = $mod->get_pages($id_area, $lang);
			foreach($items as $i)
			{
				$str .= '<option value="'.$i->id.'">'.$i->name.'</option>';
			}
		}
		echo $str;
	}
	
	/**
	 * New / Edit article form
	 * The form is simplified if site use simple editing
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   integer	$code_context Context code
	 * @param   string	$bid, the unique ID of articles
	 * @param   integer	$id_page Page ID (for simple editing)
	 * @param   boolean	$duplicate Duplicate article resetting bid
	 * @return  void
	 */
	public function edit($id_area = 2, $lang = '', $code_context = 0, $bid = 0, $id_page = 0, $duplicate = 0)
	{
		// set language 
		$lang = (empty($lang)) 
			? X4Route_core::$lang 
			: $lang;
		
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'articles'));
		
		// referer
		$qs = X4Route_core::get_query_string();
		$referer = (isset($qs['ref']))
			? $qs['ref']
			: '';
		
		$mod = new Article_model();
		
		// simple editing
		if ($id_page && $bid != 'x3') 
		{
			$bid = $mod->get_bid_by_id_page($id_page);
		}
		
		// get object
		$m = ($bid && $bid != 'x3') 
			? $mod->get_by_bid($bid) 
			: new Article_obj($id_area, $lang, $code_context);
		
		// dedicated page when called from composer
		if ($bid == 'x3')
		{
			$m->id_page = $id_page;
		}
		
		// if duplicate reset bid
		if ($duplicate)
		{
			$m->name = _COPY_OF.' '.$m->name;
			$m->bid = $mod->get_new_bid();
		}
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div class="band double-inner-pad clearfix"><div id="left-box" class="four-fifth md-three-fourth sm-two-third xs-one-whole">'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $referer,
			'name' => 'from'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $m->bid,
			'name' => 'bid'
		);
		
		$lmod = new Language_model();
		if ($id_page == 0 || $bid == 'x3') 
		{
			// advanced editing
			
			// area
			$amod = new Area_model();
			
			$fields[] = array(
				'label' => null,
				'type' => 'html', 
				'value' => '<div class="band clearfix"><div class="one-fourth sm-one-half pad-right xs-one-whole xs-pad-none">'
			);
			$fields[] = array(
				'label' => _AREA,
				'type' => 'select',
				'value' => $m->id_area,
				'options' => array($amod->get_areas(), 'id', 'name'),
				'name' => 'id_area',
				'extra' => 'class="large spinner spin2"'
			);
			$fields[] = array(
				'label' => null,
				'type' => 'hidden', 
				'value' => 'module|'.BASE_URL.'articles/refresh_module|id_area',
				'name' => 'spinner1_data'
			);
			
			$fields[] = array(
				'label' => null,
				'type' => 'html', 
				'value' => '</div><div class="one-fourth sm-one-half pad-right xs-one-whole sm-pad-none">'
			);
			
			// language
			$fields[] = array(
				'label' => _LANGUAGE,
				'type' => 'select',
				'value' => $m->lang,
				'options' => array($lmod->get_languages(), 'code', 'language'),
				'name' => 'lang',
				'extra' => 'class="large spinner spin2"'
			);
			// value = id_to_update|url|ids_to_get
			$fields[] = array(
				'label' => null,
				'type' => 'hidden', 
				'value' => 'code_context|'.BASE_URL.'articles/refresh_context|id_area|lang',
				'name' => 'spinner2_data'
			);
			
			$fields[] = array(
				'label' => null,
				'type' => 'html', 
				'value' => '</div><div class="one-fourth sm-one-half pad-right xs-one-whole xs-pad-none">'
			);
			
			// contexts
			$cmod = new Context_model();
			$fields[] = array(
				'label' => _CONTEXT,
				'type' => 'select',
				'value' => $m->code_context,
				'options' => array($cmod->get_contexts($m->id_area, $m->lang), 'code', 'name'),
				'name' => 'code_context',
				'extra' => 'class="large spin2"'
			);
			
			$fields[] = array(
				'label' => null,
				'type' => 'hidden', 
				'value' => $m->code_context,
				'name' => 'old_context'
			);
			
			// value = id_to_update|url|ids_to_get
			$fields[] = array(
				'label' => null,
				'type' => 'hidden', 
				'value' => 'id_page|'.BASE_URL.'articles/refresh_pages|id_area|lang|code_context',
				'name' => 'spinner3_data'
			);
			
			$fields[] = array(
				'label' => null,
				'type' => 'html', 
				'value' => '</div><div class="one-fourth sm-one-half xs-one-whole">'
			);
			
			$fields[] = array(
				'label' => _PAGE,
				'type' => 'select',
				'value' => $m->id_page,
				'options' => ($m->id_page) ? array($cmod->get_pages($m->id_area, $m->lang, 1), 'id', 'name') : array(),
				'name' => 'id_page',
				'extra' => 'class="large"'
			);
			
			$fields[] = array(
				'label' => null,
				'type' => 'html', 
				'value' => '</div></div>'
			);
		}
		else 
		{
			// simple editing
			
			// hidden fields
			$fields[] = array(
				'label' => null,
				'type' => 'hidden',
				'value' => $m->id_area,
				'name' => 'id_area'
			);
			$fields[] = array(
				'label' => null,
				'type' => 'hidden',
				'value' => $m->lang,
				'name' => 'lang'
			);
			$fields[] = array(
				'label' => null,
				'type' => 'hidden',
				'value' => $m->code_context,
				'name' => 'code_context'
			);
			$fields[] = array(
				'label' => null,
				'type' => 'hidden', 
				'value' => $m->code_context,
				'name' => 'old_context'
			);
			$fields[] = array(
				'label' => null,
				'type' => 'hidden',
				'value' => $m->id_page,
				'name' => 'id_page'
			);
		}
		
		// classification section
		$fields[] = array(
			'label' => _NAME,
			'type' => 'text', 
			'value' => stripslashes($m->name),
			'name' => 'name',
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		
		// content
		$fields[] = array(
			'label' => _CONTENT,
			'type' => 'textarea', 
			'value' => $m->content,
			'name' => 'content'
		);
		
		if (EDITOR_SCRIPTS)
		{
            // content
            $fields[] = array(
                'label' => _SCRIPT,
                'type' => 'textarea', 
                'value' => htmlentities($m->js),
                'name' => 'js',
                'extra' => 'class="NoEditor"',
                'suggestion' => _SCRIPT_MSG
            );
        }
        else
        {
            $fields[] = array(
				'label' => null,
				'type' => 'hidden',
				'value' => $m->js,
				'name' => 'js'
			);
        }
		
		// plugin section
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<h3>'._PLUGIN.'</h3>
						<div class="band clearfix"><div class="one-half pad-right">'
		);
		
		$plugin = new X4Plugin_model();
		// for APC
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $m->module,
			'name' => 'old_module'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $m->param,
			'name' => 'old_param'
		);
		
		$fields[] = array(
			'label' => _MODULE,
			'type' => 'select',
			'value' => $m->module,
			'options' => array($plugin->get_modules($id_area, 0), 'name', 'description', ''),
			'name' => 'module',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div><div class="one-half pad-left">'
		);
		
		$fields[] = array(
			'label' => _PARAM,
			'type' => 'text',
			'value' => $m->param,
			'name' => 'param',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div></div></div>
				<div id="right-box" class="one-fifth md-one-fourth sm-one-third xs-one-whole xs-hidden">'
		);
		
		// time window section
		$fields[] = array(
			'label' => null,
			'type' => 'html',
			'value' => '<h2>'._TIME_WINDOW.'</h2><div class="band clearfix inner-pad"><div class="one-half sm-one-whole">'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $m->date_in,
			'name' => 'old_date_in'
		);
		$fields[] = array(
			'label' => _START_DATE,
			'type' => 'text', 
			'value' => date('Y-m-d', $m->date_in),
			'name' => 'date_in',
			'rule' => 'required|date',
			'extra' => 'class="date date_toggled large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html',
			'value' => '</div><div class="one-half sm-one-whole">'
		);
		
		$fields[] = array(
			'label' => _END_DATE,
			'type' => 'text', 
			'value' => ($m->date_out == 0) ? '' : date('Y-m-d', $m->date_out),
			'name' => 'date_out',
			'rule' => 'date',
			'extra' => 'class="date date_toggled large"',
			'suggestion' => _NO_END_MSG
		);
		
		// classification section
		$fields[] = array(
			'label' => null,
			'type' => 'html',
			'value' => '</div></div><h2>'._ORGANIZATION.'</h2>'
		);
		
		// categories
		$camod = new Category_model();
		$fields[] = array(
			'label' => _CATEGORY,
			'type' => 'select',
			'value' => $m->category,
			'options' => array($camod->get_categories($m->id_area, $m->lang), 'name', 'description', 0),
			'name' => 'category',
			'extra' => 'class="large"'
		);
		// xkeys
		$fields[] = array(
			'label' => _KEYS,
			'type' => 'text', 
			'value' => $m->xkeys,
			'name' => 'xkeys',
			'extra' => 'class="large"'
		);
		
		// tags
		$fields[] = array(
			'label' => _TAGS,
			'type' => 'text', 
			'value' => $m->tags,
			'name' => 'tags',
			'extra' => 'class="large"'
		);
		
		// author
		$fields[] = array(
			'label' => _AUTHOR,
			'type' => 'text', 
			'value' => empty($m->author) ? $_SESSION['mail'] : $m->author,
			'name' => 'author',
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		
		if (EDITOR_OPTIONS)
		{
            // options section
            $fields[] = array(
                'label' => null,
                'type' => 'html', 
                'value' => '<h2>'._OPTIONS.'</h2>
                            <div class="band clearfix">
                                <div class="one-half sm-one-whole">'
            );
            
            $fields[] = array(
                'label' => _SHOW_AUTHOR,
                'type' => 'checkbox', 
                'value' => $m->show_author,
                'name' => 'show_author',
                'checked' => $m->show_author
            );
            
            $fields[] = array(
                'label' => null,
                'type' => 'html', 
                'value' => '</div><div class="one-half sm-one-whole">'
            );
            
            $fields[] = array(
                'label' => _SHOW_DATE,
                'type' => 'checkbox', 
                'value' => $m->show_date,
                'name' => 'show_date',
                'checked' => $m->show_date
            );
            
            $fields[] = array(
                'label' => null,
                'type' => 'html', 
                'value' => '</div><div class="one-half sm-one-whole">'
            );
            
            $fields[] = array(
                'label' => _SHOW_TAGS,
                'type' => 'checkbox', 
                'value' => $m->show_tags,
                'name' => 'show_tags',
                'checked' => $m->show_tags
            );
            
            $fields[] = array(
                'label' => null,
                'type' => 'html', 
                'value' => '</div><div class="one-half sm-one-whole">'
            );
            
            $fields[] = array(
                'label' => _SHOW_ACTIONS,
                'type' => 'checkbox', 
                'value' => $m->show_actions,
                'name' => 'show_actions',
                'checked' => $m->show_actions
            );
            
            $fields[] = array(
                'label' => null,
                'type' => 'html',
                'value' => '</div></div>'
            );
        }
        
        $fields[] = array(
            'label' => null,
            'type' => 'html',
            'value' => '</div></div>'
        );
/*
// TODO: maybe in the future
		$fields[] = array(
			'label' => _SCHEMA,
			'type' => 'textarea', 
			'value' => $m->xschema,
			'name' => 'xschema',
			'extra' => 'class="mceNoEditor"'
		);
*/
		
		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e) 
			{
				$this->editing($m->id, $_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
		
		// content
		$view = new X4View_core('tabber');
		$view->tabs = array();
		$view->down = new X4View_core('editor');
		$view->down->close = false;
		
		// Set the navbar
		$page = $this->get_page('articles/edit');
		
		$ref = (isset($_SESSION['referer']))
			? $_SESSION['referer']
			: 'index/'.$id_area.'/'.$lang;
		
		$navbar = array($this->site->get_bredcrumb($page), array('articles' => $ref));
		
		$pmod = new Page_model();
		if ($id_page && $bid != 'x3') 
		{
			// simple editing
			$page = $pmod->get_by_id($id_page);
			$view->title = _CONTENT_EDITOR.' <a class="bta" href="'.BASE_URL.'pages/index/'.$page->id_area.'/'.$page->lang.'/'.$page->xfrom.'/1" title="'._GO_BACK.'">'.stripslashes($page->name).'</a>'._TRAIT_.$lang;
			$view->down->js = '';
		}
		else 
		{
			// generic back
			$back = '<a class="bta" href="'.BASE_URL.'pages/index/'.$id_area.'/'.$lang.'/home/1" title="'._GO_BACK.'">'._PAGES.'</a>';
			if ($bid)
			{
				if ($m->id_page)
				{
					// back to the right page
					$page = $pmod->get_by_id($m->id_page);
					$back = (ADVANCED_EDITING)
						? '<a class="bta" href="'.BASE_URL.'sections/compose/'.$page->id.'" title="'._GO_BACK.'">'.stripslashes($page->name).'</a>'
						: '<a class="bta" href="'.BASE_URL.'pages/index/'.$page->id_area.'/'.$page->lang.'/'.$page->xfrom.'/1" title="'._GO_BACK.'">'.stripslashes($page->name).'</a>';
				}
				$view->title = $back._TRAIT_._EDIT_ARTICLE._TRAIT_.$lang;
			}
			else
				$view->title = $back._TRAIT_._ADD_ARTICLE._TRAIT_.$lang;
				
			$view->down->js = '
<script>
window.addEvent("domready", function()
{
	if ($chk($("spinner1_data"))) {
		var sdata = $("spinner1_data").get("value").split("|");
		spinnerize(sdata, ".spinner");
	}
	
	if ($chk($("spinner2_data"))) {
		var sdata = $("spinner2_data").get("value").split("|");
		spinnerize(sdata, ".spinner");
	}
	
	if ($chk($("spinner3_data"))) {
		var sdata = $("spinner3_data").get("value").split("|");
		spinnerize(sdata, ".spin2");
	}
});
</script>
			';
		}
		
		$view->down->js .= '
<script>
window.addEvent("domready", function()
{
	X3.content("filters","articles/filter/'.$id_area.'/'.$lang.'", "'.addslashes(X4Utils_helper::navbar($navbar, ' . ')).'");
	buttonize("tabber", "bta", "topic");
	pickerize(1);
});
</script>';
		
		// form builder
		$view->down->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'editor\', \'content\');"');
		$view->down->tinymce = new X4View_core('tinymce');
		$view->down->tinymce->id_area = $id_area;
		
		// rtl
		if ($lmod->rtl($lang)) 
			$view->down->tinymce->rtl = 1;
		
		$view->render(TRUE);
	}
	
	/**
	 * Register New / Edit article data
	 *
	 * @access	private
	 * @param   integer $id Article ID
	 * @param   array	$_post _POST array
	 * @return  void
	 */
	private function editing($id, $_post)
	{
		$msg = null;
		// check permission
		if ($id) 
		{
			$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'articles', $id, 2);
		}
		
		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'bid' => $_post['bid'],
				'id_area' => $_post['id_area'],
				'lang' => $_post['lang'],
				'code_context' => $_post['code_context'],
				'category' => $_post['category'],
				'id_page' => isset($_post['id_page']) ? $_post['id_page'] : 0,
				'date_out' => (empty($_post['date_out'])) ? 0 : intval(strtotime($_post['date_out'])),
				
				'xkeys' => strtolower($_post['xkeys']),
				'name' => $_post['name'],
				'content' => $_post['content'],
				'js' => html_entity_decode($_post['js']),
				'excerpt' => (strstr($_post['content'], '<!--pagebreak-->') !== false) ? 1 : 0,
				'tags' => str_replace(', ', ',', $_post['tags']),
				
				'author' => $_post['author'],
				'module' => $_post['module'],
				'param' => $_post['param'],
				'id_editor' => $_SESSION['xuid'],
				
				'show_author' => intval(isset($_post['show_author'])),
				'show_date' => intval(isset($_post['show_date'])),
				'show_tags' => intval(isset($_post['show_tags'])),
				'show_actions' => intval(isset($_post['show_actions'])),
			//	'xschema' => $_post['xschema'],
				'xon' => AUTOREFRESH
			);
			
			// adjust date_in value in case of set or update
			if ($id == 0 || $_post['date_in'] != date('Y-m-d', $_post['old_date_in'])) 
			{
				$post['date_in'] = strtotime($_post['date_in']) + (date('G')*60 + date('i'))*60 + date('s');
			}
			else
			{
				$post['date_in'] = $_post['old_date_in'];
			}
			
			// insert article
			$mod = new Article_model();
			
			// check for context
			// if the code_context is changed we assign a new bid to the article 
			if ($_post['old_context'] > -1 && $_post['old_context'] != $_post['code_context'])
			{
				$post['bid'] = $mod->get_new_bid();
			}
			
			$result = $mod->insert($post);
			if (APC)
			{
				apc_delete(SITE.'abid'.$post['id_area'].$_post['lang'].$_post['bid']);
				if (!empty($post['old_module']))
				{
					apc_delete(SITE.'pageto'.$post['id_area'].$_post['lang'].$_post['old_module'].$_post['old_param']);
				}
				if (!empty($post['module']))
				{
					apc_delete(SITE.'pageto'.$post['id_area'].$_post['lang'].$post['module'].$post['param']);
				}
			}
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// add permission
			if ($result[1]) 
			{
				$perm = new Permission_model();
				$array[] = array(
						'action' => 'insert', 
						'id_what' => $result[0], 
						'id_user' => $_SESSION['xuid'], 
						'level' => 4);
				$res = $perm->pexec('articles', $array, $_post['id_area']);
				
				if (!empty($_post['from']))
					$msg->update[] = array(
						'element' => 'topic',
						'url' => urldecode($_post['from']),
						'title' => null
					);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Show article's history
	 * Show all versions of an article
	 * Versions are displayed in reverse chronological order
	 *
	 * @param   integer $id_area Area ID
	 * @param   string 	$lang Language code
	 * @param   string	$bid, unique ID for articles
	 * @return  void
	 */
	public function history($id_area, $lang, $bid)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('history', 'articles'));
		
		// get the page
		$page = $this->get_page('articles/history');
		
		$view = new X4View_core('container');
			
		// left
		$view->content = new X4View_core('articles/history');
		$view->content->page = $page;
		$view->content->bid = $bid;
		$view->content->navbar = array($this->site->get_bredcrumb($page), array('articles' => $_SESSION['referer']));
		
		// left
		$mod = new Article_model();
		$view->content->art = $mod->get_by_bid($bid);
		$view->content->history = $mod->get_history($bid);
		$view->render(TRUE);
	}
	
	/**
	 * Date settings form for time window of article's version (use Ajax)
	 *
	 * @param   integer $id Article ID
	 * @return  void
	 */
	public function setdate($id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'history', 'articles'));
		
		// get object
		$mod = new Article_model();
		$obj = $mod->get_by_id($id, 'articles', 'date_in, date_out');
		
		// build the form
		$fields = array();
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id,
			'name' =>'id'
		);
		$fields[] = array(
			'label' => _START_DATE,
			'type' => 'text', 
			'value' => date('Y-m-d', $obj->date_in),
			'name' =>'date_in',
			'rule' => 'required|date',
			'extra' => 'class="date date_toggled large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _END_DATE,
			'type' => 'text', 
			'value' => ($obj->date_out > 0) ? date('Y-m-d', $obj->date_out) : '',
			'name' =>'date_out',
			'suggestion' => _LEAVE_EMPTY_FOR_UNDEFINED,
			'rule' => 'date|after-date_in',
			'extra' => 'class="date date_toggled large sweep"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div></div>'
		);
		
		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e) 
			{
				$this->setting_date($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
		
		// content
		$view = new X4View_core('editor');
		$view->title = _SET_DATE;
		
		// form builder
		$view->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'editor\');"');
		
		$view->js = '
<script>
window.addEvent("domready", function()
{
	if ($chk($$("input.date"))) {
		pickerize(1);
	}
});
</script>';
		
		$view->render(TRUE);
	}
	
	/**
	 * Register article's time window
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function setting_date($_post)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'articles', $_post['id'], 2);
		
		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'date_in' => strtotime($_post['date_in']),
				'date_out' => (empty($_post['date_out'])) ? 0 : strtotime($_post['date_out'])
			);
			
			// do action
			$mod = new Article_model();
			$obj = $mod->get_by_id($_post['id'], 'articles', 'id_area, lang, bid');
			$result = $mod->update($_post['id'], $post);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// set what update
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => 'topic', 
					'url' => BASE_URL.'articles/history/'.$obj->id_area.'/'.$obj->lang.'/'.$obj->bid,
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Delete Article form (use Ajax)
	 * Delete all versions of an article
	 *
	 * @param   $bid string, unique ID for articles
	 * @return  void
	 */
	public function delete($bid)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'articles'));
		
		// get object
		$mod = new Article_model();
		$obj = $mod->get_by_bid($bid);
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $bid,
			'name' => 'bid'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $obj->id_area,
			'name' => 'id_area'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $obj->lang,
			'name' => 'lang'
		);
		
		// if submitted
		if (X4Route_core::$post)
		{
			$this->deleting($_POST);
			die;
		}
		
		// contents
		$view = new X4View_core('delete');
		$view->title = _DELETE_ARTICLE;
		$view->item = $obj->name;
		
		// form builder
		$view->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '', 
			'onclick="setForm(\'delete\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Delete article
	 * Delete all versions of an article
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function deleting($_post)
	{
		$msg = null;
		// check permissions on articles
		$mod = new Article_model();
		$artt = $mod->get_all_by_bid($_post['bid']);
		foreach($artt as $i)
		{
			if (is_null($msg))
				$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'articles', $i->id, 4);
		}
		
		// check permissions on pages
		$smod = new Section_model();
		$pp = $smod->get_pages_by_bid($_post['bid']);
		foreach($pp as $i)
		{
			if (is_null($msg))
				$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'pages', $i->id, 3);
		}
		
		if (is_null($msg))
		{
			// do action
			$result = $mod->delete_by_bid($_post['bid']);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// set what update
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => 'topic', 
					'url' => BASE_URL.'articles/index/'.$_post['id_area'].'/'.$_post['lang'],
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Delete a version of an Article form (use Ajax)
	 *
	 * @param   integer $id Article id
	 * @return  void
	 */
	public function delete_version($id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'articles'));
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id,
			'name' =>'id'
		);
		
		// if submitted
		if (X4Route_core::$post)
		{
			$this->deleting_version($id);
			die;
		}
		
		// get object
		$mod = new Article_model();
		$obj = $mod->get_by_id($id, 'articles', 'updated');
		
		// contents
		$view = new X4View_core('delete');
		$view->title = _DELETE_ARTICLE;
		$view->item = $obj->updated;
		
		// form builder
		$view->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(_NO, _YES, 'xcenter'), 'post', '', 
			'onclick="setForm(\'delete\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Delete article's version
	 *
	 * @access	private
	 * @param   integer $id article ID
	 * @param   string 	$bid BID code
	 * @return  void
	 */
	private function deleting_version($id)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'articles', $id, 4);
		
		if (is_null($msg))
		{
			// do action
			$mod = new Article_model();
			$obj = $mod->get_by_id($id, 'articles', 'id_area, lang, bid');
			$result = $mod->delete($id);
			
			// clear useless permissions
			if ($result[1]) 
			{
				$perm = new Permission_model();
				$perm->deleting_by_what('articles', $id);
			}
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// set what update
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => 'topic', 
					'url' => BASE_URL.'articles/history/'.$obj->id_area.'/'.$obj->lang.'/'.$obj->bid,
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
}
