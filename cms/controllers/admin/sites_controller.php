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
 * Controller for Site
 * 
 * @package X3CMS
 */
class Sites_controller extends X3ui_controller
{
	protected $cases = array(
			'sites' => array('sites', 'btm'),
			'by_page' => array('languages', 'btm'), 
			'context_order' => array('themes', 'btm'),
			'category_order' => array('users', 'btm')
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
	 * Show site status
	 *
	 * @return  void
	 */
	public function _default()
	{
		$this->show();
	}
	
	
	/**
	 * Show site
	 *
	 * @param   integer  $tab Enable disable tabber view
	 * @return  void
	 */
	public function show($tab = 0)
	{
		// load dictionary
		$this->dict->get_wordarray(array('sites'));
		
		// get page
		$page = $this->get_page('sites');
		$navbar = array($this->site->get_bredcrumb($page));
		
		if ($tab)
		{
			$view = new X4View_core('tabber');
			$view->title = _SITE_MANAGER;
			$menu = $this->site->get_menus($page->id_area);
			
			$view->tabs = $menu['sidebar'];
			$view->tkeys = array('name', 'url', 'url', $page->url);
			$view->down = new X4View_core('container');
			$view->tabber_container = 'tdown';
			
			$view->down->content = new X4View_core('sites/sites');
			$view->down->content->navbar = $navbar;
			$view->down->content->page = $page;
		}
		else
		{
			$view = new X4View_core('container');
			
			$view->content = new X4View_core('sites/sites');
			$view->content->navbar = $navbar;
			$view->content->page = $page;
		}
		$view->render(TRUE);
	}
	
	/**
	 * Sites filter
	 *
	 * @return  void
	 */
	public function filter()
	{
		echo '';
	}
	
	/**
	 * Change site status
	 *
	 * @param   integer  $id Site ID
	 * @param   integer  $value value to set (0 = off, 1 = on)
	 * @return  void
	 */
	public function offline($id, $value = 0)
	{
	    $this->dict->get_words();
	    
		$msg = null;
		// check permissions
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'sites', $id, 4);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();
			
			// do action
			$result = $this->site->update($id, array('xon' => $value));
			if (APC)
			{
				apc_clear_cache();
				apc_clear_cache('user');
				apc_clear_cache('opcode');
			}
			
			// set message
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
	 * Site config form (use Ajax)
	 *
	 * @param   integer  $id Site ID
	 * @return  void
	 */
	public function config($id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array('sites', 'form'));
		
		// get object
		$site = $this->site->get_by_id($id);
		
		// get global parameters
		$params = $this->site->get_param($id);
		
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
			'type' => 'hidden',
			'value' => 'site',
			'name' => 'xrif'
		);
		$fields[] = array(
			'label' => null,
			'type' => 'hidden',
			'value' => 0,
			'name' => 'id_area'
		);
		
		foreach($params as $i)
		{
			switch($i->xtype) 
			{
				case '0|1':
					$tmp = array(
						'label' => ucfirst(str_replace('_', ' ', $i->name))._TRAIT_.$i->description,
						'type' => 'checkbox',
						'value' => $i->xvalue,
						'name' => $i->name,
						'suggestion' => _ON.'/'._OFF,
						'checked' => $i->xvalue
					);
					break;
				case 'IMG':
					// TODO: set image as param
					break;
				case 'INTEGER':
					$tmp = array(
						'label' => ucfirst(str_replace('_', ' ', $i->name))._TRAIT_.$i->description,
						'type' => 'text', 
						'value' => $i->xvalue,
						'name' => $i->name,
						'suggestion' => $i->xtype,
						'extra' => 'class="medium aright"',
						'rule' => 'numeric'
					);
					if ($i->required == '1') $tmp['rule'] = 'required';
					break;
				case 'EMAIL':
					$tmp = array(
						'label' => ucfirst(str_replace('_', ' ', $i->name))._TRAIT_.$i->description,
						'type' => 'text', 
						'value' => $i->xvalue,
						'name' => $i->name,
						'suggestion' => $i->xtype,
						'extra' => 'class="medium"',
						'rule' => 'mail'
					);
					if ($i->required == '1') $tmp['rule'] = 'required';
					break;
				default:
					$tmp = array(
						'label' => ucfirst(str_replace('_', ' ', $i->name))._TRAIT_.$i->description,
						'type' => 'text', 
						'value' => $i->xvalue,
						'name' => $i->name,
						'suggestion' => $i->xtype,
						'extra' => 'class="medium"'
					);
					if ($i->required == '1') $tmp['rule'] = 'required';
					break;
			}
			$fields[] = $tmp;
		}
		
		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'configure');
			if ($e) 
			{
				$this->configure($_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
		
		// contents
		$view = new X4View_core('editor');
		$view->title = _SITE_CONFIG.': '.$site->domain;
		
		// form builder
		$view->form = '<div id="scrolled">'.X4Form_helper::doform('configure', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'configure\');"').'</div>';
		
		$view->js = '
<script>
window.addEvent("domready", function()
{
	var myScroll = new Scrollable($("scrolled"));
});
</script>';
		
		
		$view->render(TRUE);
	}
	
	/**
	 * Register the site configuration
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function configure($_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'sites', $_post['id'], 3);
		
		if (is_null($msg))
		{
			// get parameters before update
			$params = $this->site->get_param($_post['id']);
			
			// build update array
			$sql = array();
			foreach($params as $i) 
			{
				// handle _post
				switch($i->xtype) 
				{
					case '0|1':
						$val = intval(isset($_post[$i->name]));
						break;
					case 'IMG':
						$val = $_post[$i->name];
						break;
					default:
						$val = $_post[$i->name];
						break;
				}
				
				// if the new value is different then update
				if ($val != $i->xvalue) 
					$sql[$i->id] = $val;
			}
			
			// do update
			$plugin = new X4Plugin_model();
			$result = $plugin->update_param($sql);
			APC && apc_delete(SITE.'param'.$_post['id']);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// set what update
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => 'topic', 
					'url' => BASE_URL.'sites/show/1',
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Edit site form (use Ajax)
	 *
	 * @param   integer  $id Site ID
	 * @return  void
	 */
	public function edit($id)
	{
		// load dictionary
		$this->dict->get_wordarray(array('form', 'sites'));
		
		// get object
		$site = $this->site->get_by_id($id);
		
		// build the form
		$fields = array();
		$fields[] = array(
			'label' => null,
			'type' => 'hidden', 
			'value' => $id,
			'name' => 'id'
		);
		$fields[] = array(
			'label' => _X3CMS.' '._VERSION,
			'type' => 'text', 
			'value' => $site->version,
			'name' => 'version',
			'extra' => 'class="large" disabled="disabled"'
		);
		$fields[] = array(
			'label' => _KEYCODE,
			'type' => 'text', 
			'value' => $site->xcode,
			'name' => 'xcode',
			'extra' => 'class="large"'
		);
		$fields[] = array(
			'label' => _DOMAIN,
			'type' => 'text', 
			'value' => $site->domain,
			'name' => 'domain',
			'rule' => 'required|url',
			'extra' => 'class="large"'
		);
		
		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e) 
				$this->editing($_POST);
			else 
				$this->notice($fields);
			die;
		}
		
		// contents
		$view = new X4View_core('editor');
		$view->title = _EDIT_SITE;
		
		// form builder
		$view->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '', 
			'onclick="setForm(\'editor\');"');
		$view->render(TRUE);
	}
	
	/**
	 * Register site data
	 *
	 * @access	private
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing($_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION['xuid'], 'sites', $_post['id'], 4);
		
		if (is_null($msg))
		{
			// handle _post
			$post = array(
				'xcode' => X4Utils_helper::unspace($_post['xcode']),
				'domain' => $_post['domain']
			);
			
			// do update
			$result = $this->site->update($_post['id'], $post);
			
			// set message
			$msg = AdmUtils_helper::set_msg($result);
			
			// set what update
			if ($result[1])
			{
				$msg->update[] = array(
					'element' => 'topic', 
					'url' => BASE_URL.'sites/show/1',
					'title' => null
				);
			}
		}
		$this->response($msg);
	}
	
	/**
	 * Clear cache
	 *
	 * @return  void
	 */
	public function clear_cache()
	{
		$files = glob(APATH.'files/tmp/*');
		foreach($files as $i) 
		{
			unlink($i);
		}
		
		// set message
		$this->dict->get_words();
		$msg = AdmUtils_helper::set_msg(true);
		$msg->update[] = array(
			'element' => 'topic', 
			'url' => BASE_URL.'sites/show/1',
			'title' => null
		);
		$this->response($msg);
	}
	
	/**
	 * Clear APC cache
	 *
	 * @return  void
	 */
	public function clear_apc()
	{
		// do action
		if (APC)
		{
			apc_clear_cache();
			apc_clear_cache('user');
			apc_clear_cache('opcode');
  		}
		
		// set message
		$this->dict->get_words();
		$msg = AdmUtils_helper::set_msg(true);
		$msg->update[] = array(
			'element' => 'topic', 
			'url' => BASE_URL.'sites/show/1',
			'title' => null
		);
		$this->response($msg);
	}
}
