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
 * Controller for Sections
 * 
 * @package X3CMS
 */
class Sections_controller extends X3ui_controller
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
	 * Page compositing
	 *
	 * @param   integer $id_page Page ID
	 * @param   string  $by sort key
	 * @return  void
	 */
	public function compose($id_page, $by = 'name')
	{
		// load dictionaries
		$this->dict->get_wordarray(array('sections', 'form', 'articles'));
		
		// get object
		$mod = new Page_model('', '', $id_page);
		$page_to_edit = $mod->get_page_by_id($id_page);
		
		// get page
		$page = $this->get_page('sections/compose');
		$navbar = array($this->site->get_bredcrumb($page), array('pages' => 'index/'.$page_to_edit->id_area.'/'.$page_to_edit->lang));
		
		// content
		$view = new X4View_core('left');
		
		// left
		$view->left = new X4View_core('sections/compose');
		$view->left->navbar = $navbar;
		$view->left->pagetoedit = $page_to_edit;
		$smod = new Section_model();
		$view->left->mod = $smod;
		
		// get contexts
		$view->left->dict = $this->dict;
		$view->left->codes = $smod->get_contexts($page_to_edit->id_area, $page_to_edit->lang);
		
		// get articles in area/language
		$view->left->articles = $smod->get_articles_to_publish($page_to_edit, $by);
		
		// get sections
		$view->left->sections = $smod->get_sections($page_to_edit);
		$view->left->referer = urlencode('sections/compose/'.$id_page);
		
		// template image
		$theme = $mod->get_theme($page_to_edit->id_area);
		$view->left->layout = (file_exists(PATH.'themes/'.$theme->name.'/img/'.$page_to_edit->tpl.'.png'))
			? ROOT.'themes/'.$theme->name.'/img/'.$page_to_edit->tpl.'.png'
			: '';
		
		$view->render(TRUE);
	}
	
	/**
	 * Compose filter
	 *
	 * @param   integer $id_area Area ID
	 * @param   string $lang Language code
	 * @param   integer $id_page Page id
	 * @return  string
	 */
	public function filter($id_area, $lang, $id_page)
	{
		// load the dictionary
		$this->dict->get_wordarray(array('articles'));
		
		echo '<a class="btf" href="'.BASE_URL.'articles/edit/'.$id_area.'/'.$lang.'/1/x3/'.$id_page.'" title="'._NEW_ARTICLE.'"><i class="fa fa-plus fa-lg"></i></a>
<script>
window.addEvent("domready", function()
{
	buttonize("filters", "btf", "topic", "'.urlencode('sections/compose/'.$id_page).'");
});
</script>';

	}
	
	/**
	 * Update article settings (context and Page ID) and return article
	 * Called via Ajax
	 * During composition of page contents, user drag and drop articles from context list to page sections and vice versa
	 * When an article move from contexts to sections the system calls this method
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang Language code
	 * @param   integer $id_page Page ID
	 * @param   string  $bid, article unique ID
	 * @return  string
	 */
	public function get_article($id_area, $lang, $id_page, $bid)
	{
		// load dictionary
		$this->dict->get_wordarray(array('articles'));
		
		// get object
		$mod = new Section_model();
		$art = $mod->get_by_bid($id_area, $lang, $bid);
		
		// set context and id page
		$this->recode_article($id_area, $lang, 'pages', $bid, $id_page);
		
		// plugin info
		$m = (empty($art->module)) 
			? _TRAIT_ 
			: $art->module;
			
		// parameter info
		$p = (empty($art->param)) 
			? _TRAIT_ 
			: $art->param;
			
		// return article
		echo '<div class="sbox"><b>'.stripslashes($art->name).'</b>'._TRAIT_.'<a class="btm" href="'.BASE_URL.'articles/edit/'.$id_area.'/'.$lang.'/'.$art->code_context.'/'.$art->bid.'" title="'._EDIT.'">'._EDIT.'</a></div>
			'.$art->content.'
			<div class="tbox">'._MODULE.': '.$m.'&nbsp;&nbsp;|&nbsp;&nbsp;'._PARAM.': '.$p.'</div>';
	}
	
	/**
	 * Recode an article, set context code and page ID
	 * This method is called from get_article and via Ajax
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang Language code
	 * @param   string  $holder context name
	 * @param   string  $bid, article unique ID
	 * @param   integer $id_page Page ID
	 * @return  void
	 */
	public function recode_article($id_area, $lang, $holder, $bid, $id_page = 0) 
	{
		// set context and id_page
		$mod = new Section_model();
		$mod->recode($id_area, $lang, $holder, $bid, $id_page);
	}
	
	/**
	 * Return article's title
	 * Called via Ajax
	 * During composition of page contents, user drag and drop articles from context list to page sections and vice versa
	 * When an article move from sections to contexts the system calls this method
	 *
	 * @param   integer $id_area Area ID
	 * @param   string  $lang Language code
	 * @param   string  $bid, article unique ID
	 * @return  string
	 */
	public function get_title($id_area, $lang, $bid)
	{
		// get article object
		$mod = new Section_model();
		$art = $mod->get_by_bid($id_area, $lang, $bid);
		
		// return article's title
		echo '<strong>'.stripslashes($art->name).'</strong>';
	}
	
	/**
	 * Register page's composition
	 * Use _POST data
	 *
	 * @param   integer item id (if 0 then is a new item)
	 * @param   array 	_POST array
	 * @return  void
	 */
	public function compositing()
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'pages', $_POST['id_page'], 3);
		
		if (is_null($msg))
		{
			// handle _POST
			$sections = array();
			$post = array(
				'id_area' => $_POST['id_area'],
				'id_page' => $_POST['id_page'],
				'xon' => 1
			);
			
			// handle _POST for each section 
			for($i = 1; $i <= $_POST['snum']; $i++)
			{
				$post['progressive'] = $i;
				
				// delete first comma
				$articles = (substr($_POST['sort'.$i], 0, 1) == ',') 
					? substr($_POST['sort'.$i], 1) 
					: $_POST['sort'.$i];
				
				$post['articles'] = str_replace(',', '|', $articles);
				$sections[] = $post;
			}
			
			// register composition
			$mod = new Section_model();
			$result = $mod->compose($sections);
			APC && apc_delete(SITE.'sections'.$post['id_page']);
			
			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);
			
			// add permissions on new sections
			if ($result[1])  
			{
				$msg->update[] = array(
					'element' => 'topic', 
					'url' => BASE_URL.'sections/compose/'.$post['id_page'],
					'title' => null
				);
				
				if (is_array($result[0]) && !empty($result[0]))
				{
					$perm = new Permission_model();
					$array = array();
					foreach($result[0] as $i)
					{
						$array[] = array(
								'action' => 'insert', 
								'id_what' => $i, 
								'id_user' => $_SESSION['xuid'], 
								'level' => 4);
					}
					$result = $perm->pexec('sections', $array, $_POST['id_area']);
				}
			}
		}
		$this->response($msg);
	}
}
