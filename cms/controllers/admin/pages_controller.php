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
 * Controller for Page items
 * 
 * @package X3CMS
 */
class Pages_controller extends X3ui_controller
{
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
	 * Show pages
	 *
	 * @return  void
	 */
	public function _default()
	{
		$this->index(2, '', 'home');
	}
	
	/**
	 * Show pages
	 * As default display public area pages
	 * Display all child pages of a given page
	 * If the page is the home of the area, then you also view the home
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang language code
	 * @param   string  $xfrom page URL of origin
	 * @return  void
	 */
	public function index($id_area, $lang = '', $xfrom = 'home')
	{
	    $area = new Area_model();
	    list($id_area, $areas) = $area->get_my_areas($id_area);
	    
		// initialize parameters
		$lang = (empty($lang)) 
			? X4Route_core::$lang 
			: $lang;
			
		$xfrom = str_replace('§', '/', urldecode($xfrom));
		
		// load dictionary
		$this->dict->get_wordarray(array('pages'));
		
		$view = new X4View_core('container');
			
		// content
		$view->content = new X4View_core('pages/pages');
		//$view->page = $this->get_page('pages');
		
		// content
		$mod = new Page_model($id_area, $lang);
		$view->content->id_area = $id_area;
		$view->content->lang = $lang;
		$view->content->xfrom = $xfrom;
		$view->content->area = $mod->get_var($id_area, 'areas', 'name');
		
		$obj = $mod->get_page($xfrom);
		$view->content->page = ($obj) 
			? $obj 
			: new Page_obj($id_area, $lang);
		
		$page = $this->get_page('pages');
		$navbar = array($this->site->get_bredcrumb($page), array('areas' => 'index'));
		$view->content->navbar = $navbar;
		
		// referer
		$view->content->referer = urlencode('pages/index/'.$id_area.'/'.$lang.'/'.$xfrom);
			
		// pages to show
		$view->content->pages = $mod->get_pages($xfrom, $view->content->page->deep);
		// available menus
		$mod = new Menu_model();
		$view->content->menus = $mod->get_menus($id_area, '', 'id');
		// language switcher
		$lang = new Language_model();
		$view->content->langs = $lang->get_languages();
		// area switcher
		
		$view->content->areas = $areas;
		
		$view->render(TRUE);
	}
	
	/**
	 * Pages filter
	 *
	 * @return  void
	 */
	public function filter($id_area, $lang, $xfrom = '')
	{
		if ($id_area)
		{
			// load the dictionary
			$this->dict->get_wordarray(array('pages'));
			
			echo '<a class="btf" href="'.BASE_URL.'areas/map/'.$id_area.'/'.$lang.'" title="'._SITE_MAP.'"><i class="fas fa-map-marker fa-lg"></i></a>
				<a class="btf" href="'.BASE_URL.'pages/add/'.$id_area.'/'.$lang.'/'.$xfrom.'" title="'._NEW_PAGE.'"><i class="fas fa-plus fa-lg"></i></a>
	<script>
	window.addEvent("domready", function()
	{
		buttonize("filters", "btf", "modal");
	});
	</script>';
		}
		else
			echo '';
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
		$msg = null;
		// check permission
		$val = ($what == 'xlock') 
			? 4 
			: 3;
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'pages', $id, $val);
		
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			// do action
			$mod = new Page_model('public', X4Route_core::$lang);
			$result = $mod->update_page($id, array($what => $value), $this->site->site->domain);
			
			// get page xfrom
			$page = $mod->get_by_id($id, 'pages', 'xfrom');
			$encoded_xfrom = str_replace('/', '$', $page->xfrom);
						
			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);
			
			// set update
			$msg->update[] = array(
				'element' => $qs['div'],
				'url' => urldecode(str_replace($page->xfrom, $encoded_xfrom, $qs['url'])),
				'title' => null
			);
		}
		$this->response($msg);
	}
	
	/**
	 * New page form (use Ajax)
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   string	$xfrom Page URL of parent page
	 * @param   boolean	$check Switcher between string or echo
	 * @return  mixed
	 */
	public function add($id_area, $lang, $xfrom, $check = 1)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'pages'));
		
		// get object
		$pages = new Page_model($id_area, $lang);
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $lang,
			'name' =>'lang'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => $id_area,
			'name' =>'id_area'
		);
		$fields[] = array(
			'label' => _NAME,
			'type' => 'text', 
			'value' => '',
			'name' => 'name',
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		$fields[] = array(
			'label' => _DESCRIPTION,
			'type' => 'textarea', 
			'value' => '',
			'name' => 'description'
		);
		$fields[] = array(
			'label' => _FROM_PAGE,
			'type' => 'select',
			'value' => str_replace('§', '/', urldecode($xfrom)),
			'options' => array($pages->get_pages(), 'url', 'title'),
			'name' => 'xfrom',
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		$fields[] = array(
			'label' => _TEMPLATE,
			'type' => 'select',
			'value' => '',
			'options' => array($pages->get_templates(), 'name', 'description'),
			'name' =>'tpl',
			'extra' => 'class="large"'
		);
		
		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e) 
			{
				$this->adding($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
		
		// contents
		$view = new X4View_core('editor');
		$view->title = _ADD_PAGE;
		
		// form builder
		$view->form = X4Form_helper::doform('editor', BASE_URL.'pages/add/'.$id_area.'/'.$lang.'/'.$xfrom.'/'.intval($check), $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'editor\');"');
		
		if ($check)
		{
			$view->render(true);
		}
		else
		{
			return $view->render();
		}
	}
	
	/**
	 * Register new page
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function adding($_post)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], '_page_creation', 0, 4);
		
		if (is_null($msg))
		{
			// remove slash from url
			if ($_post['id_area'] > 1) 
				$_post['name'] = str_replace('/', '-', $_post['name']);
			
			// handle _post
			$post = array(
				'lang' => $_post['lang'],
				'id_area' => $_post['id_area'],
				'url' => X4Utils_helper::unspace($_post['name'], true),
				'name' => $_post['name'],
				'title' => $_post['name'],
				'description' => $_post['description'],
				'xfrom' => $_post['xfrom'],
				'tpl' => $_post['tpl']
			);
			
			// load model
			$mod = new Page_model($_post['id_area'], $_post['lang']);
			
			// check if a page with the same URL already exists
			$check = (boolean) $mod->exists($post['url']);
			if ($check) 
			{
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_PAGE_ALREADY_EXISTS', 'msg'));
			}
			else 
			{
				// set css for the template of the new page
				$tmod = new Template_model();
				$css = $tmod->get_css($_post['id_area'], $_post['tpl']);
				$post['css'] = $css;
				
				// set xrif for admin pages
				$post['xid'] = ($_post['id_area'] == 1) 
					 ? 'pages'
					 : '';
				
				// insert the new page
				$result = $mod->insert_page($post, $this->site->site->domain);
				
				// add permission
				if ($result[1]) 
				{
					$perm = new Permission_model();
					$array[] = array(
							'action' => 'insert', 
							'id_what' => $result[0], 
							'id_user' => $_SESSION['xuid'], 
							'level' => 4);
					$result = $perm->pexec('pages', $array, $post['id_area']);
				}
				
				// set message
				$msg = AdmUtils_helper::set_msg($result);
				
				// set what update
				if ($result[1])
				{
					$msg->update[] = array(
						'element' => 'topic', 
						'url' => BASE_URL.'pages/index/'.$post['id_area'].'/'.$post['lang'].'/'.str_replace('/', '-', $post['xfrom']),
						'title' => null
					);
				}
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Edit SEO data of a page (use Ajax)
	 *
	 * @param   integer  $id Page ID
	 * @return  void
	 */
	public function seo($id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'pages'));
		
		// get object
		$mod = new Page_model('', '', $id);
		$page = $mod->get_page_by_id($id);
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id,
			'name' => 'id'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _FROM_PAGE,
			'type' => 'select',
			'value' => $page->xfrom,
			'options' => array($mod->get_pages('', 0, $page->url), 'url', 'title'),
			'name' =>'xfrom',
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _NOT_IN_MAP,
			'type' => 'checkbox',
			'value' => $page->hidden,
			'name' => 'hidden',
			'checked' => $page->hidden
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div></div>'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div id="accordion" class="gap-top">'
		);
		
		$fields[] = array(
            'label' => null,
            'type' => 'html', 
            'value' => '<h4 class="context">'._TEMPLATE.'</h4><div class="section">'
        );
		
		$fields[] = array(
			'label' => _TEMPLATE,
			'type' => 'select',
			'value' => $page->tpl,
			'options' => array($mod->get_templates(), 'name', 'description'),
			'name' =>'tpl',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
            'label' => null,
            'type' => 'html', 
            'value' => '</div><h4 class="context">'._SEO_TOOLS.'</h4><div class="section">'
        );
		
		$fields[] = array(
			'label' => _URL,
			'type' => 'text', 
			'value' => $page->url,
			'name' => 'url',
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div class="band inner-pad clearfix"><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _NAME,
			'type' => 'text', 
			'value' => $page->name,
			'name' => 'name',
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div><div class="one-half xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _TITLE,
			'type' => 'text', 
			'value' => $page->title,
			'name' => 'title',
			'rule' => 'required',
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div></div>'
		);
		
		$fields[] = array(
			'label' => _DESCRIPTION,
			'type' => 'textarea', 
			'value' => $page->description,
			'name' => 'description'
		);
		
		$fields[] = array(
			'label' => _KEYS,
			'type' => 'textarea', 
			'value' => $page->xkeys,
			'name' => 'xkeys'
		);

		$fields[] = array(
			'label' => _ROBOT,
			'type' => 'text', 
			'value' => $page->robot,
			'name' => 'robot',
			'suggestion' => _ROBOT_MSG,
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '<div class="band inner-pad clearfix"><div class="one-fifth xs-one-whole">'
		);
		
		$codes = array(301, 302);
		$fields[] = array(
			'label' => _REDIRECT_CODE,
			'type' => 'select', 
			'value' => $page->redirect_code,
			'name' => 'redirect_code',
			'options' => array(X4Array_helper::simplearray2obj($codes, 'value', 'option'), 'value', 'option', 0),
			'extra' => 'class="large"'
		);
		
		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div><div class="four-fifth xs-one-whole">'
		);
		
		$fields[] = array(
			'label' => _REDIRECT,
			'type' => 'text', 
			'value' => $page->redirect,
			'name' => 'redirect',
			'rule' => 'requiredif§redirect_code§!0|url',
			'suggestion' => _REDIRECT_MSG,
			'extra' => 'class="large"'
		);

		$fields[] = array(
			'label' => null,
			'type' => 'html', 
			'value' => '</div></div>'
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
				$this->reg_seo($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
		
		// contents
		$view = new X4View_core('editor');
		$view->title = _SEO_TOOLS;
		
		// form builder
		$view->form = '<div id="scrolled">'.X4Form_helper::doform('editor', BASE_URL.'pages/seo/'.$id, $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'editor\');"').'</div>';
		
		$view->js = '
<script>
window.addEvent("domready", function()
{
    var myScroll = new Scrollable($("scrolled"));
	saccordion("accordion", "#accordion h4", "#accordion .section");
});
</script>';

		$view->render(TRUE);
	}
	
	/**
	 * Register SEO data
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function reg_seo($_post)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'pages', $_post['id'], 2);
		
		if (is_null($msg))
		{
			// get object
			$mod = new Page_model('', '', $_post['id']);
			$page = $mod->get_by_id($_post['id'], 'pages', 'id_area, lang, url, xfrom');
			
			// this pages cannot be changed
			$no_change = array('home', 'msg', 'search');
			
			// remove slash from url
			if ($page->id_area > 1) 
				$_post['url'] = str_replace('/', '-', $_post['url']);
			
			// handle _post
			$post = array(
				'url' => (!in_array($page->url, $no_change)) ? X4Utils_helper::unspace($_post['url']) : $page->url,
				'name' => $_post['name'],
				'title' => $_post['title'],
				'description' => $_post['description'],
				'xfrom' => (!in_array($page->url, $no_change)) ? $_post['xfrom'] : $page->xfrom,
				'hidden' => intval(isset($_post['hidden'])),
				'xkeys' => $_post['xkeys'],
				'robot' => $_post['robot'],
				'redirect_code' => $_post['redirect_code'],
				'redirect' => $_post['redirect'],
				'tpl' => $_post['tpl']
				);
			
			// check if a page with the same URL already exists
			$check = (boolean) $mod->exists($post['url'], $_post['id']);
			if ($check) 
				$msg = AdmUtils_helper::set_msg(false, '', $this->dict->get_word('_PAGE_ALREADY_EXISTS', 'msg'));
			else 
			{
				// set css for the page
				$tmod = new Template_model();
				$css = $tmod->get_css($page->id_area, $_post['tpl']);
				$post['css'] = $css;
				
				// update page data
				$result = $mod->update_page($_post['id'], $post, $this->site->site->domain);
				
				if (APC)
				{
					apc_clear_cache();
					apc_clear_cache('user');
					apc_clear_cache('opcode');
				}
				
				// set message
				$msg = AdmUtils_helper::set_msg($result);
				
				// set what update
				if ($result[1])
				{
					$msg->update[] = array(
						'element' => 'topic', 
						'url' => BASE_URL.'pages/index/'.$page->id_area.'/'.$page->lang.'/'.str_replace('/', '-', $page->xfrom).'/0/',
						'title' => null
					);
				}
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Delete Page form (use Ajax)
	 *
	 * @param   integer $id Page ID
	 * @return  void
	 */
	public function delete($id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('form', 'pages'));
		
		// get object
		$mod = new Page_model('', '', $id);
		$page = $mod->get_by_id($id, 'pages', 'name');
		
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
			$this->deleting($id);
			die;
		}
		
		// contents
		$view = new X4View_core('delete');
		$view->title = _DELETE_PAGE;
		$view->item = $page->name;
		
		// form builder
		$view->form = X4Form_helper::doform('delete', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, 'buttons'), 'post', '', 
			'onclick="setForm(\'delete\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Delete page
	 *
	 * @access	private
	 * @param   integer 	$id Page ID
	 * @return  void
	 */
	private function deleting($id)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'pages', $id, 4);
		
		if (is_null($msg))
		{
			// action
			$mod = new Page_model('', '', $id);
			$page = $mod->get_by_id($id, 'pages', 'id_area, lang, xfrom');
			$result = $mod->delete_page($id, $this->site->site->domain);
			
			// clear useless permissions
			if ($result[1]) 
			{
				$perm = new Permission_model();
				$perm->deleting_by_what('pages', $id);
			}
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// set what update
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => 'topic', 
					'url' => BASE_URL.'pages/index/'.$page->id_area.'/'.$page->lang.'/'.str_replace('/', '-', $page->xfrom),
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Initialize area: create default pages
	 *
	 * @param   integer	$id_area Area ID
	 * @param   string	$lang Language code
	 * @return  void
	 */
	public function init($id_area, $lang)
	{
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chklevel($_SESSION['xuid'], '_page_creation', 0, 4);
		
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// get object: the area
			$area = new Area_model();
			$a = $area->get_by_id($id_area);
			
			$mod = new Page_model($id_area, $lang);
			
			// build the post array
			$post = array();
			
			if ($id_area == 1)
			{
				// admin area
				
				// uses admin area with language = SESSION['lang'] as base and duplicates all pages
				$pmod = new Page_model($id_area, $_SESSION['lang']);
				$pages = $pmod->get_pages();
				
				foreach($pages as $i)
				{
					$post[] = array($i->url, 
						array(
							'lang' => $lang,
							'id_area' => $id_area,
							'xid' => $i->xid,
							'url' => $i->url,
							'name' => $i->name,
							'title' => $i->title,	
							'description' => $i->description,
							'xfrom' => $i->xfrom,
							'tpl' => $i->tpl, 
							'css' => $i->css,
							'id_menu' => $i->id_menu, 
							'xpos' => $i->xpos, 
							'deep' => $i->deep, 
							'ordinal' => $i->ordinal, 
							'xon' => $i->xon
						)
					);
				}
			}
			else
			{
				// other areas
				
				// home
				$post[] = array('home', array('lang' => $lang,'id_area' => $id_area,'xid' => 'pages','url' => 'home','name' => 'Home page',
					'title' => 'Home page',	'description' => 'Home page','xfrom' => 'home','tpl' => 'base', 'css' => 'base',
					'id_menu' => 0, 'xpos' => 0, 'deep' => 0, 'ordinal' => 'A', 'xon' => 1));
				// x3admin
				$post[] = array('x3admin', array('lang' => $lang,'id_area' => $id_area,'xid' => 'pages','url' => 'x3admin','name' => 'Editor',
					'title' => 'Editor', 'description' => 'Editor','xfrom' => 'home','tpl' => 'base', 'css' => 'base',
					'id_menu' => 0, 'xpos' => 1, 'deep' => 1, 'ordinal' => 'A0000001', 'hidden' => 1, 'xlock' => 1, 'xon' => 1));
				// msg
				$post[] = array('comunication', array('lang' => $lang,'id_area' => $id_area,'xid' => 'pages','url' => 'msg','name' => 'Communication',
					'title' => 'Communication','description' => 'Communication','xfrom' => 'home','tpl' => 'base', 'css' => 'base',
					'id_menu' => 0, 'xpos' => 2, 'deep' => 1, 'ordinal' => 'A0000002', 'hidden' => 1, 'xlock' => 1,'xon' => 1));
				// search
				$post[] = array('search', array('lang' => $lang,'id_area' => $id_area,'xid' => 'pages','url' => 'search','name' => 'Search result',
					'title' => 'Search result','description' => 'Search result','xfrom' => 'home','tpl' => 'base', 'css' => 'base',
					'id_menu' => 0, 'xpos' => 3, 'deep' => 1, 'ordinal' => 'A0000003', 'hidden' => 1, 'xlock' => 1,'xon' => 1));
				
				// if is a private area
				if ($a->private) 
				{
					// exit
					$post[] = array('logout', array('lang' => $lang,'id_area' => $id_area,'xid' => 'pages','url' => 'logout','name' => 'Logout',
						'title' => 'Logout','description' => 'Logout','xfrom' => 'home','tpl' => 'base', 'css' => 'base',
						'id_menu' => 0, 'xpos' => 4, 'deep' => 1, 'ordinal' => 'A0000004', 'hidden' => 0, 'xlock' => 1,'xon' => 1));
				}
			}
			
			// action
			$result = $mod->initialize_area($id_area, $lang, $post);
			
			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);
			
			if ($result[1]) 
			{
				// create default contexts
				$mod->initialize_context($id_area, $lang);
				
				// set update
				$msg->update[] = array(
					'element' => $qs['div'],
					'url' => urldecode($qs['url']),
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Duplicate an area for another language (secret method)
	 * If you need to add another language to an area you can call this script
	 * /admin/pages/duplicate_area_lang/ID_AREA/OLD_LANG/NEW_LANG
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $old_lang Old language to copy
	 * @param   string  $new_lang New language to set
	 * @return  string
	 */
	public function duplicate_area_lang($id_area, $old_lang, $new_lang)
	{
		// Comment the next row to enable the method
		die('Operation disabled!');
		
		$mod = new Page_model();
		
		// duplicate
		$res = $mod->duplicate_area_lang($id_area, $old_lang, $new_lang);
			
        if ($res[1])
        {
            echo '<h1>CONGRATULATIONS!</h1>';
            echo '<p>The changes on the database are applied.</p>';
            
            // print instructions for manual changes
            echo '<p>Follow this instructions to perform manual changes.</p>
            <ul>
                <li>Install the following modules: '.implode(', ', $res[0]).' and configure them if needed</li>
            </ul>
            <p>Done!</p>
            
            <p>NOTE: this operation acts on the pages and articles of the CMS, if you use plugins you have to check if you need to duplicate contents.</p>';
        }
        else
        {
            echo '<h1>WARNING!</h1>';
            echo '<p>Something went wrong, changes are not applied.</p>';
        }
		die;
	}
}
