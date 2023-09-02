<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

/**
 * Model for X3CLI
 *
 * @package X3CMS
 */
class X3cli_model extends X4Model_core
{
    /**
     * Head
     */
    private $head = '<?php defined(\'ROOT\') or die(\'No direct script access.\');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
 ';

    /**
     * Notes for controllers
     */
    private $controller_notes = '
/*
    NOTES FOR DEVELOPERS
    ----------------------------------------------------------------------------

    The X3CMS is a complex environment. Almost always a part of the CMS is connected and depends from a lot of other parts.

    Usually we have a controller for each table (with the same name of the controller), a model for each controller (to interact with the table) and in most cases at least one view to display your data.

    If you plan to use this controller in the CMS probably you have to create a page from the admin side, this way you will have a menu item to call the controller
    and it will be integrated in the CMS structure (breadcrumb, site map and so on).

    This file contains "commented" a lot of connections with those parts, so you can easily enable what you need.

    Have fun
*/

    ';

    /**
     * Notes for models
     */
    private $model_notes = '
/*
    NOTES FOR DEVELOPERS
    ----------------------------------------------------------------------------

    The X3CMS is a complex environment. Almost always a part of the CMS is connected and depends from a lot of other parts.

    Usually we have a controller for each table (with the same name of the controller), a model for each controller (to interact with the table) and in most cases at least one view to display your data.

    A model usually is related to a table. If a table with the same name of the model was found the CLI will use it as related table, if not the CLI will create a default table in the default database.
    Below the SQL to create a very basic table for X3 CMS, replace the table name SET_HERE_YOUR_TABLE_NAME to fit your needs.
    X3CMS uses the name of the model in lowercase, the name of the related table has to be set in the constructor of the model.

    CREATE TABLE IF NOT EXISTS `SET_HERE_YOUR_TABLE_NAME` (
      `id` int(11) NOT NULL auto_increment,
      `updated` datetime NOT NULL,
      `lang` char(2) NOT NULL,
      `name` varchar(128) NOT NULL,
      `title` varchar(128) NOT NULL,
      `description` varchar(255) NOT NULL,
      `xpos` int(11) NOT NULL,
      `xlock` tinyint(1) NOT NULL,
      `xon` tinyint(1) NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

    This file contains "commented" parts of useful code, so you can easily enable what you need.

    Have fun
*/
    ';

    /**
     * Notes for views
     */
    private $view_notes = '
/*
    NOTES FOR DEVELOPERS
    ----------------------------------------------------------------------------

    The X3CMS is a complex environment. Almost always a part of the CMS is connected and depends from a lot of other parts.

    Usually we have a controller for each table (with the same name of the controller), a model for each controller (to interact with the table) and in most cases at least one view to display your data.

    The view is the part where we collect and organize our data in the HTML code and, if required, Javascript.

    Have fun
*/
    ';

    /**
     * Default table
     */
    protected $sql = 'CREATE TABLE IF NOT EXISTS `XXXTABLE_NAMEXXX` (
      `id` int(11) NOT NULL auto_increment,
      `updated` datetime NOT NULL,
      `id_area` int(11) NOT NULL,
      `lang` char(2) NOT NULL,
      `name` varchar(128) NOT NULL,
      `title` varchar(128) NOT NULL,
      `description` varchar(255) NOT NULL,
      `xpos` int(11) NOT NULL,
      `xlock` tinyint(1) NOT NULL,
      `xon` tinyint(1) NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;';

    /**
     * Admin dictionary
     */
    protected $admin_dictionary = array(
        '_XXX_SEARCH_MSG' => 'Search by title or description',
        '_XXX_MANAGER' => 'XXX manager',
        '_XXX_ITEMS' => 'XXX items',
        '_XXX_ITEM' => 'XXX item',
        '_XXX_ADD' => 'Add a new XXX',
        '_XXX_NEW' => 'New XXX',
        '_XXX_EDIT' => 'Edit XXX',
        '_XXX_DELETE' => 'Delete XXX',
    );

	/**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('logs');
	}

	/**
	 * Create controller
	 *
	 * @param string    $area
	 * @param string    $name
	 * @return  boolean
	 */
	public function create_controller(string $area, string $name)
	{
	    $uname = ucfirst($name);
	    $upper_name = strtoupper($name);

	    // avoid overwrite
	    if (!file_exists(APATH.'controllers/'.$area.'/'.$name.'_controller'.EXT))
	    {
	        // try to create the file
            try
            {
                // create the empty file
                touch(APATH.'controllers/'.$area.'/'.$name.'_controller'.EXT);

                if ($area == 'admin')
                {
                    // very basic admin controller
                    $txt = '
/**
 * Controller for '.$name.' items
 *
 * @package X3CMS
 */
class '.$uname.'_controller extends X3ui_controller
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
	 * Default method
	 *
	 * @return  void
	 */
	public function _default()
	{
		// You can use this method to display an overview of items
		// if you need to use parameters as $id_area and $lang you should use another method with parameters
		// like the index() method, uncomment the following line
		// $this->index();
		// and comment next section

		// /* ------------------------------------------------------------------
		// start section

		// load the dictionary
		// you should have created a section in the dictionary for this controller
		$this->dict->get_wordarray(array(\'faq\'));

		// content
		$view = new X4View_core(\'container\');

		// dummy content
		$view->content = \'
<h1>'.$uname.' Controller</h1>
<p>Dummy content for the _default() method</p>
<p>Congratulations! You have successful created your controller.<br />
If you want to fully use this method you have to create:</p>
<ul>
    <li>a model with the same name (php x3 create model area name)</li>
    <li>a view with the same name (php x3 create view area name)</li>
    <li>a dictionary with the same name (php x3 create dictionary area name)</li>
</ul>
<p>After that you can comment this part in the _default() method and redirect it to the index() method.</p>

<h2>Model + View + Controller + Dictionary</h2>
<p>A faster way to reach the same result: a complete set of controller+model+view+dictionary <b>for the admin area</b> is to use the mvc command (php x3 create mvc area name).</p>

<h3>Files</h3>
<p>You can view the files here:</p>
<ul>
    <li>controller: /cms/controllers/admin/'.$name.'_controller.php</li>
    <li>model: /cms/models/'.$uname.'_model.php</li>
    <li>view: /cms/views/admin/'.$name.'_view.php</li>
</ul>
<h2>Permissions</h2>
<p>With the creation of the model a table and the required records to handle permissions from the admin side were automatically created.<br />Permissions are assigned only to admin group (you need to login after the creation to see the effects).</p>

<h2>Dictionary</h2>
<p>The dictionary section for the admin area contains the basic text labels for the management of the items.<br />Very simple english labels for all languages connected to the admin area.</p>

<h2>Personalization</h2>
<p>If you already created controller, model and view you can personalize them to fit your needs.</p>
<p>Some suggestions:</p>
<ul>
    <li>By default the "'.$uname.'" items are sortable. You can disable this commenting the xpos variable from the '.$uname.'_obj at the end of the '.$uname.'_model.</li>
    <li>Removing the sortable the items are automatically paginated.</li>
    <li>If you need to add fields in the table you have to change the '.$name.'_controller for the form in the edit() method and the editing() method where data are saved in the database.</li>
</ul>
<div class="buttons acenter"><button type="button" onclick="redirect();" title="\'._'.$upper_name.'_MANAGER.\'">\'._'.$upper_name.'_MANAGER.\'</button></div>

<p><b>Have fun</b></p>
<script>
function redirect() {
    X3.content("topic","\'.BASE_URL.\''.$name.'/index", null);
}
</script>\';

		$view->render(TRUE);

		// end section
		// ------------------------------------------------------------------ */
	}

	/**
	 * Index method
	 * If you plan to use the data handled in this pag in only one area set the $id_area default value as you need ((e.g. 2 == public area)
	 *
	 * @param   integer	$id_area  Area ID
	 * @param   string	$lang     Language code
	 * @param   integer	$pp       Active page index
	 * @param   string	$str      Search string
	 * @return  void
	 */
	public function index(int $id_area = 2, string $lang = \'en\', int $pp = 0, string $str = \'\')
	{
	    // load the dictionary
		// you should have created a section in the dictionary for this controller
		$this->dict->get_wordarray(array(\''.$name.'\'));

		// get page
		// if you created a page in the CMS with the same name
		$page = $this->get_page(\''.$name.'\');
		$navbar = array($this->site->get_bredcrumb($page));

		// content

		// if you created a view for that page with the same name
		$view = new X4View_core(\''.$name.'\');
		$view->page = $page;
		$view->id_area = $id_area;
		$view->lang = $lang;
		$view->pp = $pp;
		$view->str = $str;
		$view->navbar = $navbar;

		// if you created a model for that controller
		$mod = new '.$uname.'_model();
		if ($mod->is_sortable($id_area, $lang))
		{
		    $view->items = $mod->get_items($id_area, $lang, $str);
		}
		else
		{
		    // if not sortable we paginate
		    $view->items = X4Pagination_helper::paginate($mod->get_items($id_area, $lang, $str), $pp);
		}

		// switchers
		// language switcher
		$lang = new Language_model();
		$view->langs = $lang->get_languages();
		// area switcher
		$area = new Area_model();
		$view->areas = $area->get_areas();

		$view->render(TRUE);
	}

	/**
	 * '.$uname.' filter
	 * This method populates the top right of the admin layout
	 * It contains the Plus button to add new items and can contain form filters or other general commands
	 *
	 * @param   integer	$id_area  Area ID
	 * @param   string	$lang     Language code
	 * @param   string	$str      Search string
	 * @return  void
	 */
	public function filter(int $id_area, string $lang, string $str = \'\')
	{
		// load the dictionary
		$this->dict->get_wordarray(array(\''.$name.'\'));

		if (X4Route_core::$post)
		{
		    // set message
            $msg = AdmUtils_helper::set_msg(array(0,1));
            $msg->update[] = array(
                    \'element\' => \'topic\',
                    \'url\' => BASE_URL.\''.$name.'/index/\'.$id_area.\'/\'.$lang.\'/0/\'.urlencode(trim($_POST[\'search\'])),
                    \'title\' => null
                );
            $this->response($msg);
		}
		else
		{
            // to avoid error for missing dictionary
            $search = (defined(\'_'.strtoupper($name).'_SEARCH_MSG\'))
                ? _'.strtoupper($name).'_SEARCH_MSG
                : \'Search by title or description\';


            // to avoid error for missing dictionary
            $new = (defined(\'_'.strtoupper($name).'_ADD\'))
                ? _'.strtoupper($name).'_ADD
                : \'Add a new '.$uname.'\';

            echo \'<form id="searchitems" name="searchitems" action="\'.BASE_URL.\''.$name.'/filter/\'.$id_area.\'/\'.$lang.\'" method="post" onsubmit="setForm(\\\'searchitems\\\');return false;">
                        <input type="text" name="search" id="search" value="\'.urldecode($str).\'" title="\'.$search.\'" />
                        <button type="button" name="searcher" class="button" onclick="setForm(\\\'searchitems\\\');">\'._FIND.\'</button>
                        </form>\';

            echo \'<a class="btf" href="\'.BASE_URL.\''.$name.'/edit/\'.$id_area.\'/\'.$lang.\'/0" title="\'.$new.\'"><i class="fas fa-plus fa-lg"></i></a>
<script>
window.addEvent("domready", function()
{
	buttonize("filters", "btf", "modal");
});
</script>\';
        }
	}

	/**
	 * Change status
	 * This method is used to change the xon and xlock fields in the table '.$name.'
	 *
	 * @param   string	$what field to change
	 * @param   integer $id ID of the item to change
	 * @param   integer $value value to set (0 = off, 1 = on)
	 * @return  void
	 */
	public function set(string $what, int $id, int $value = 0)
	{
		$msg = null;
		// check permissions
		$val = ($what == \'xlock\')
			? 4
			: 3;

		$msg = AdmUtils_helper::chk_priv_level($_SESSION[\'xuid\'], \''.$name.'\', $id, $val);
		if (is_null($msg))
		{
			$qs = X4Route_core::get_query_string();

			// do action
			$mod = new '.$uname.'_model();
			$result = $mod->update($id, array($what => $value));

			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);

			// set update
			if ($result[1])
            {
				$msg->update[] = array(
					\'element\' => $qs[\'div\'],
					\'url\' => urldecode($qs[\'url\']),
					\'title\' => null
				);
            }
		}
		$this->response($msg);
	}

	/**
	 * New / Edit form
	 * This is an AJAX form, you can call this only from the admin side
	 * To use this method you need:
	 * - a Table with name = '.$name.' with fields: id, name, title, description
	 * - a dictionary section "'.$name.'" with at least the KEYS:  _EDIT_'.strtoupper($name).' and _ADD_'.strtoupper($name).'
	 * - a model '.$uname.'_model which contains the '.$uname.'_obj class
	 *
	 * @param   integer  $id item ID (if 0 then is a new item)
	 * @return  void
	 */
	public function edit(int $id_area, string $lang, int $id = 0)
	{
		// load dictionaries
		$this->dict->get_wordarray(array(\'form\', \''.$name.'\'));

		// handle id
		$chk = false;
		if ($id < 0)
		{
			$id = 0;
			$chk = true;
		}

		// get the object
		$mod = new '.$uname.'_model();
		$item = ($id)
			? $mod->get_by_id($id)
			: new '.$uname.'_obj($id_area, $lang);

		// build the form
		$fields = array();
		$fields[] = array(
			\'label\' => null,
			\'type\' => \'hidden\',
			\'value\' => $id,
			\'name\' => \'id\'
		);
		$fields[] = array(
			\'label\' => null,
			\'type\' => \'hidden\',
			\'value\' => $id_area,
			\'name\' => \'id_area\'
		);
		$fields[] = array(
			\'label\' => null,
			\'type\' => \'hidden\',
			\'value\' => $lang,
			\'name\' => \'lang\'
		);
		$fields[] = array(
			\'label\' => _TITLE,
			\'type\' => \'text\',
			\'value\' => $item->title,
			\'name\' => \'title\',
			\'rule\' => \'required\',
			\'extra\' => \'class="large"\'
		);
		$fields[] = array(
			\'label\' => _DESCRIPTION,
			\'type\' => \'textarea\',
			\'value\' => $item->description,
			\'name\' => \'description\'
		);

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, \'editor\');
			if ($e)
			{
				$this->editing($id, $_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}

		// contents
		$view = new X4View_core(\'editor\');

		// to avoid error for missing dictionary
		if ($id)
		{
            $title = (defined(\'_'.strtoupper($name).'_EDIT\'))
                ? _'.strtoupper($name).'_EDIT
                : \'Edit '.$uname.'\';
        }
        else
        {
            $title = (defined(\'_'.strtoupper($name).'_NEW\'))
                ? _'.strtoupper($name).'_NEW
                : \'New '.$uname.'\';
        }

		$view->title = $title;

		// form builder
		$view->form = X4Form_helper::doform(\'editor\', BASE_URL.\''.$name.'/edit/\'.$id_area.\'/\'.$lang.\'/\'.$id, $fields, array(_RESET, _SUBMIT, \'buttons\'), \'post\', \'\',
			\'onclick="setForm(\\\'editor\\\');"\');

		$view->js = \'\';

		$view->render(TRUE);
	}

	/**
	 * Register Edit / New item
	 * To use this method you need:
	 * - a Table with name = '.$name.' with fields: id, name, title, description
	 * - you need in the table privtypes record for manage and create table items in '.$name.' ('.$name.' and _'.$name.'_creation)
	 * - you need to assign those privileges to the group of your user in the table gprivs
	 *
	 * @access	private
	 * @param   array 	$id Item ID
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function editing(int $id, array $_post)
	{
		$msg = null;
		// check permission
		$msg = ($id)
		    ? AdmUtils_helper::chk_priv_level($_SESSION[\'xuid\'], \''.$name.'\', $id, 2)
		    : AdmUtils_helper::chk_priv_level($_SESSION[\'xuid\'], \'_'.$name.'_creation\', 0, 4);

		if (is_null($msg))
		{
			// handle _post
			$post = array(
			    \'id_area\' => $_post[\'id_area\'],
			    \'lang\' => $_post[\'lang\'],
				\'name\' => X4Utils_helper::slugify($_post[\'title\']),
				\'title\' => $_post[\'title\'],
				\'description\' => $_post[\'description\']
			);

			$mod = new '.$uname.'_model();

			// save
			if ($id)
			{
			    // update
				$result = $mod->update($id, $post);
			}
			else
			{
			    // check if is a sortable object
			    if ($mod->is_sortable($post[\'id_area\'], $post[\'lang\']))
			    {
			        $post[\'xpos\'] = $mod->get_max_pos($post[\'id_area\'], $post[\'lang\']) + 1;
			    }

			    // insert
				$result = $mod->insert($post);

				// add pemission
				if ($result[1])
				{
					$perm = new Permission_model();
					$array[] = array(
							\'action\' => \'insert\',
							\'id_what\' => $result[0],
							\'id_user\' => $_SESSION[\'xuid\'],
							\'level\' => 4);
					$result = $perm->pexec(\''.$name.'\', $array, $post[\'id_area\']);
				}
			}

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			if ($result[1])
			{
				$msg->update[] = array(
					\'element\' => \'topic\',
					\'url\' => BASE_URL.\''.$name.'/index/\'.$post[\'id_area\'].\'/\'.$post[\'lang\'],
					\'title\' => null
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Delete item form
	 * This is an AJAX form, you can call this only from the admin side
	 * To use this method you need:
	 * - a Table with name = '.$name.' with fields: id, title
	 * - a dictionary section "'.$name.'" with at least the KEYS:  _DELETE_'.strtoupper($name).'
	 * - a model '.$uname.'_model
	 *
	 * @param   integer $id Item ID
	 * @return  void
	 */
	public function delete(int $id)
	{
		// load dictionaries
		$this->dict->get_wordarray(array(\'form\', \''.$name.'\'));

		// get object
		$mod = new '.$uname.'_model();
		$obj = $mod->get_by_id($id, \''.$name.'\', \'id, title, id_area, lang\');

		// build the form
		$fields = array();
		$fields[] = array(
			\'label\' => null,
			\'type\' => \'hidden\',
			\'value\' => $id,
			\'name\' => \'id\'
		);

		// if submitted
		if (X4Route_core::$post)
		{
			$this->deleting($obj, $_POST);
			die;
		}

		// to avoid error for missing dictionary
		$title = (defined(\'_'.strtoupper($name).'_DELETE\'))
		    ? _'.strtoupper($name).'_DELETE
		    : \'Delete '.$uname.'\';

		// contents
		$view = new X4View_core(\'delete\');
		$view->title = $title;
		$view->item = $obj->title;

		// form builder
		$view->form = X4Form_helper::doform(\'delete\', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, \'buttons\'), \'post\', \'\',
			\'onclick="setForm(\\\'delete\\\');"\');
		$view->render(TRUE);
	}

	/**
	 * Delete item
	 *
	 * @access	private
	 * @param   object  $obj Item
	 * @param   array 	$_post _POST array
	 * @return  void
	 */
	private function deleting(stdClass $obj, array $_post)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION[\'xuid\'], \''.$name.'\', $obj->id, 4);

		if (is_null($msg))
		{
			// action
			$mod = new '.$uname.'_model();
			$result = $mod->delete($obj->id);

			// set message
			$msg = AdmUtils_helper::set_msg($result);

			// clear useless permissions
			if ($result[1]) {
				$perm = new Permission_model();
				$perm->deleting_by_what(\''.$name.'\', $obj->id);

				$msg->update[] = array(
					\'element\' => \'topic\',
					\'url\' => BASE_URL.\''.$name.'/index/\'.$obj->id_area.\'/\'.$obj->lang,
					\'title\' => null
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Move fields
	 * Useful with orderable items
	 *
	 * @param   integer $id_area Area ID
	 * @param   string $lang Language code
	 * @return  void
	 */
	public function ordering(int $id_area, string $lang)
	{
		$msg = null;
		// check permission
		$msg = AdmUtils_helper::chk_priv_level($_SESSION[\'xuid\'], \'_'.$name.'_creation\', 0, 3);

		if (is_null($msg) && X4Route_core::$post)
		{
			// handle post
			$elements = explode(\',\', $_POST[\'sort_order\']);

			// do action
			$mod = new '.$uname.'_model();
			$items = $mod->get_items($id_area, $lang);

			$result = array(0, 1);
			if ($items)
			{
				foreach ($items as $i)
				{
					$p = array_search($i->id, $elements) + 1;
					if ($p && $i->xpos != $p)
					{
						$res = $mod->update($i->id, array(\'xpos\' => $p));
						if ($result[1] == 1)
						{
							$result = $res;
						}
					}
				}
			}

			// set message
			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);
		}
		$this->response($msg);
	}

	/**
	 * Rebuild the widget
	 *
	 * @param	string	$title Widget title
	 * @param	integer $id_area Area ID
	 * @param	string	$area Area name
	 * @return	array	string
	 */
	public function rewidget(string $title, int $id_area, string $area)
	{
		$mod = new '.$uname.'_model();
		$r = $mod->get_widget(urldecode($title), $id_area, urldecode($area));
		echo $r[0];
		if (!empty(X4Route_core::$query_string))
		{
			$qs = X4Route_core::get_query_string();
			if (isset($qs[\'refresh\']))
			{
				echo \'<script>
window.addEvent("domready", function()
{
	buttonize("\'.$qs[\'refresh\'].\'", "bta", "topic");
	buttonize("\'.$qs[\'refresh\'].\'", "btr", "\'.$qs[\'refresh\'].\'", "", "\'.$qs[\'refresh\'].\'");
});
</script>\';
            }
		}
	}
}';
                    }
                    else
                    {
                        // check if is a public or a private area
                        $private = $this->get_var('SELECT private FROM areas WHERE name = '.$this->db->escape($name));

                        if ($private)
                        {
                            // is a private area

                            $txt = '
/**
 * Controller for '.$name.' items
 *
 * @package X3CMS
 */
class '.$uname.'_controller extends X4Cms_controller
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

		// this if you have a login page in this private area
		X4Utils_helper::logged(X4Route_core::get_id_area(), X4Route_core::$area.\'/login\');

		// this if you have a login page in the public area
		// X4Utils_helper::logged(X4Route_core::get_id_area(), \'public/login\');
	}

	// put here other methods you need

}';
                        }
                        else
                        {
                            // is a public area

                            $txt = '
/**
 * Controller for '.$name.' items
 *
 * @package X3CMS
 */
class '.$uname.'_controller extends X4Cms_controller
{
    /**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::offline($this->site->site->xon, BASE_URL.\'msg/message/offline\');
	}

	// put here other methods you need

}';
                    }
                }

                // put the contents in the file
                return (bool) file_put_contents(APATH.'controllers/'.$area.'/'.$name.'_controller'.EXT, $this->head.$this->controller_notes.$txt);

            }
            catch (Exception $e)
            {
                echo NL.$e.NL;
            }
        }
        else
        {
            echo NL.'WARNING: the file '.APATH.'controllers/'.$area.'/'.$name.'_controller'.EXT.' already exists'.NL;
        }
	}

	/**
	 * Create model
	 *
	 * @param string    $area
	 * @param string    $name
	 * @return  boolean
	 */
	public function create_model(string $area, string $name)
	{
	    $uname = ucfirst($name);

	    // avoid overwrite
	    if (!file_exists(APATH.'models/'.$uname.'_model'.EXT))
	    {
	        // try to create the file
            try
            {
                // create the empty file
                touch(APATH.'models/'.$uname.'_model'.EXT);

                // check if the table exists
                $table = $this->get_table_name($name);

                if ($table != '')
                {
                    $txt = '
/**
 * '.$uname.' model
 *
 * @package X3CMS
 */
class '.$uname.'_model extends X4Model_core
{
    /*
	// uncomment if you need to personalize search inside this plugin
	// this module require a personalized url for internal search engine
	public $personalized_url = true;

	// here you can define the param to use for get_page_to
	public $search_param;
	*/

	/**
	 * Get url for search
	 * if you need a special URL with search
	 *
	 * @param object	Project obj
	 * @return string
	 */
	public function get_url(stdClass $obj, string $topage)
	{
		return $topage.\'/\'.$obj->id.\'/\'.$obj->url;
	}

    /**
	 * Constructor
	 * set the default table
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct(\''.$table.'\');
	}

	/**
	 * Get items
	 * Join with privs table
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @param   string	$str Search string
	 * @return  array	array of objects
	 */
	public function get_items(int $id_area, string $lang, string $str = \'\')
	{
		$where = \'\';
		if (!empty($str))
		{
			$w = array();
			$tok = explode(\' \', urldecode($str));
			foreach ($tok as $i)
			{
				$a = trim($i);
				if (!empty($a))
                {
					$w[] = \'title LIKE \'.$this->db->escape(\'%\'.$a.\'%\').\' OR
						description LIKE \'.$this->db->escape(\'%\'.$a.\'%\');
                }
			}

			if (!empty($w))
				$where .= \' AND (\'.implode(\') AND (\', $w).\')\';
		}

		// sorting
		$order = ($this->is_sortable($id_area, $lang))
		    ? \'x.xpos ASC\'
		    : \'x.title ASC\';

		return $this->db->query(\'SELECT x.*, p.level
			FROM '.$table.' x
			JOIN uprivs u ON u.id_area = x.id_area AND u.id_user = \'.intval($_SESSION[\'xuid\']).\' AND u.privtype = \\\''.$table.'\\\'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = x.id
			WHERE x.id_area = \'.$id_area.\' AND x.lang = \'.$this->db->escape($lang).$where.\'
			GROUP BY x.id
			ORDER BY \'.$order);
	}
	';
                    if ($area == 'admin')
                    {
                        // add extra methods
                        $txt .= '
    /**
	 * Check if table items are sortable
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @return  boolean
	 */
	public function is_sortable(int $id_area, string $lang)
	{
		$obj = new '.$uname.'_obj($id_area, $lang);
		return isset($obj->xpos);
	}

	/**
	 * Get max xpos value by id_area and lang
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$lang Language code
	 * @return  array	array of objects
	 */
	public function get_max_pos(int $id_area, string $lang)
	{
		return $this->db->query_var(\'SELECT xpos
			FROM '.$table.'
			WHERE
				id_area = \'.$id_area.\' AND
				lang = \'.$this->db->escape($lang).\'
			ORDER BY xpos DESC\');
	}

	/**
	 * Build the widget
	 * This will be useful if you have a controller in the admin area and wnat to create a widget
	 * You need to add a record in the modules table to make this available in the admin side
	 * INSERT INTO modules (updated, id_area, name, title, configurable, admin, searchable, mappable, widget, version, xon) VALUES (NOW(), ID_AREA, \''.$name.'\', \''.$uname.' widget\', 0, 0, 0, 0, 1, \'0\', 1);
	 * Please make attention to the ID_AREA value, set it like the ID of the area where you will use the items in the table, or if you plan to use in more than one area, add a record for each area.
	 *
	 * @param	string	$title Widget title
	 * @param	integer $id_area Area ID
	 * @param	string	$area Area name
	 * @return	array	string
	 */
	public function get_widget(string $title, int $id_area, string $area)
	{
		// TO DO

		// Sample: get total number of active items
		$ntot = (int) $this->db->query_var(\'SELECT COUNT(x.id) AS n
			FROM '.$table.' x
			JOIN uprivs u ON u.id_area = x.id_area AND u.id_user = \'.intval($_SESSION[\'xuid\']).\' AND u.privtype = \\\''.$table.'\\\'
			LEFT JOIN privs p ON p.id_who = u.id_user AND p.what = u.privtype AND p.id_what = x.id
			WHERE x.id_area = \'.$id_area.\' AND x.xon = 1\');

		// dictionary
		$dict = new X4Dict_model(X4Route_core::$folder, X4Route_core::$lang);
		// you need to call a dictionary to get translations for _RELOAD and _DICTIONARY_KEY
		$dict->get_wordarray(array(\''.$name.'\'));

		// title
		$w = \'<div class="wtitle clearfix">
                <span class="half-pad-left">\'.$title.\'</span>\'._TRAIT_.\'<span class="xsmall">\'.$area.\'</span>
                <div class="wtools">
                    <a class="btr" href="\'.BASE_URL.\''.$name.'/rewidget/\'.urlencode($title).\'/\'.$id_area.\'/\'.urlencode($area).\'" title="\'._RELOAD.\'"><i class="fas fa-refresh fa-lg"></i></a>
                    <a class="bta" href="\'.BASE_URL.\''.$name.'/mod/\'.$id_area.\'" title="\'.$title.\'"><i class="fas fa-arrow-right fa-lg"></i></a>
                </div>
            </div>
			<div class="wbox">\';

		// TO DO

		// display total number of active items
		$w .= \'<p>Active items: \'.$ntot.\'</p>\';

		$w .= \'</div>\';

		return array($w, 1);
	}';
                    }

                    $txt .= '
}

/**
 * Empty object
 * Necessary for the creation form of new item
 *
 * @package X3CMS
 */
class '.ucfirst($name).'_obj
{
	// object vars
	public $id_area;
	public $lang;
	public $name;
	public $title;
	public $description;
	public $xpos = 0;

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
}';

                    // check privtypes
                    $check = $this->check_privtypes($name);
                    if ($check > 0)
                    {
                        echo NL.'Privileges are added to the admin group. You need to log-in to refresh your permissions'.NL;
                    }
                    elseif ($check < 0)
                    {
                        echo NL.'WARNING: privileges are NOT added to the admin group'.NL;
                    }

                    // put the contents in the file
                    return (bool) file_put_contents(APATH.'models/'.$uname.'_model'.EXT, $this->head.$this->model_notes.$txt);
                }
                else
                {
                    return false;
                }
            }
            catch (Exception $e)
            {
                echo NL.$e.NL;
            }
        }
        else
        {
            echo NL.'WARNING: the file '.APATH.'models/'.$uname.'_model'.EXT.' already exists'.NL;
        }
	}

	/**
	 * Create view
	 *
	 * @param string    $area
	 * @param string    $name
	 * @return  boolean
	 */
	public function create_view(string $area, string $name)
	{
	    $uname = strtoupper($name);

	    // avoid overwrite
	    if (!file_exists(APATH.'views/'.$area.'/'.$name.'_view'.EXT))
	    {
	        // try to create the file
            try
            {
                // create the empty file
                touch(APATH.'views/'.$area.'/'.$name.'_view'.EXT);

                if ($area == 'admin')
                {
                    // admin view
                    // here we suppose you loaded in the controller the dictionary section for the view
                    $txt = '
// if you don\'t need to switch between languages you can comment this section
// lang switcher
if (MULTILANGUAGE)
{
	echo \'<div class="aright sbox"><ul class="inline-list">\';
	foreach ($langs as $i)
	{
		$on = ($i->code == $lang) ? \'class="on"\' : \'\';
		echo \'<li><a \'.$on.\' href="\'.BASE_URL.\''.$name.'/index/\'.$id_area.\'/\'.$i->code.\'" title="\'._SWITCH_LANGUAGE.\'">\'.ucfirst($i->language).\'</a></li>\';
	}
	echo \'</ul></div>\';
}

// if you don\'t need to switch between areas you can comment this section
// area switcher
if (MULTIAREA)
{
	echo \'<div class="aright sbox"><ul class="inline-list">\';
	foreach ($areas as $i)
	{
		$on = ($i->id == $id_area) ? \'class="on"\' : \'\';
		echo \'<li><a \'.$on.\' href="\'.BASE_URL.\''.$name.'/index/\'.$i->id.\'/\'.$lang.\'" title="\'._SWITCH_AREA.\'">\'.ucfirst($i->name).\'</a></li>\';
	}
	echo \'</ul></div>\';
}

// to avoid errors for missing dictionary
$title = (defined(\'_'.$uname.'_MANAGER\'))
    ? _'.$uname.'_MANAGER
    : \''.ucfirst($name).' Manager\';

$elements = (defined(\'_'.$uname.'_ITEMS\'))
    ? _'.$uname.'_ITEMS
    : \''.ucfirst($name).' items\';

$js = \'\';
?>
<h1><?php echo $title ?></h1>
<?php
// here you should have an array of objects named $items
// if you get paginated items the var $items will be an array like this ($array_of_objects, $array_with_data_for_pagination)
// if not paginated items could be sortable (if they have an attribute named xpos)

// check for pagination
$pagination = (!empty($items) && !is_object($items[0]));

// check for sorting
$sortable = (!$pagination && isset($items[0]->xpos));

if ($sortable)
{
    $list = $items;
    if (!empty($list))
    {
        echo \'<form id="sort_updater" name="sort_updater" action="\'.BASE_URL.\''.$name.'/ordering/\'.$id_area.\'/\'.$lang.\'" method="post">\';
    }
}
else
{
    if ($pagination)
    {
        $list = $items[0];
    }
    else
    {
        $list = $items;
    }
}

if (!empty($list))
{
    echo \'<table class="zebra nomargin">
		<tr>
			<th>\'.$elements.\'</th>
			<th style="width:6em;">\'._ACTIONS.\'</th>
			<th style="width:6em;"></th>
		</tr>\';

	if ($sortable)
	{
	    echo \'</table><ul id="sortable" class="nomargin zebra">\';
	}

	foreach ($list as $i)
	{
		if ($i->xon)
        {
            $status = _ON;
            $on_status = \'orange\';
        }
        else
        {
            $status = _OFF;
            $on_status = \'gray\';
        }

        if ($i->xlock)
        {
            $lock = _LOCKED;
            $lock_status = \'lock\';
        }
        else
        {
            $lock = _UNLOCKED;
            $lock_status = \'unlock-alt\';
        }

        $actions = $delete = \'\';
        if (($i->level > 1 && $i->xlock == 0) || $i->level == 4)
        {
		    $actions = \'<a class="bta" href="\'.BASE_URL.\''.$name.'/edit/\'.$i->id_area.\'/\'.$i->lang.\'/\'.$i->id.\'" title="\'._EDIT.\'"><i class="fas fa-pencil-alt fa-lg"></i></a>\';

		    if ($i->level > 2)
            {
                $actions .= \' <a class="btl" href="\'.BASE_URL.\''.$name.'/set/xon/\'.$i->id.\'/\'.intval(!$i->xon).\'" title="\'._STATUS.\' \'.$status.\'"><i class="far fa-lightbulb fa-lg \'.$on_status.\'"></i></a>\';

                if ($i->level == 4)
                {
                    $delete = \'<a class="btl" href="\'.BASE_URL.\''.$name.'/set/xlock/\'.$i->id.\'/\'.intval(!$i->xlock).\'" title="\'._STATUS.\' \'.$lock.\'"><i class="fas fa-\'.$lock_status.\' fa-lg"></i></a>
                        <a class="bta" href="\'.BASE_URL.\''.$name.'/delete/\'.$i->id.\'" title="\'._DELETE.\'"><i class="fas fa-trash fa-lg red"></i></a>\';
                }
            }
        }

        if ($sortable)
        {
            echo \'<li id="\'.$i->id.\'">
                <table><tr>
                <td><b>\'.$i->title.\'</b></td>
                <td style="width:6em;">\'.$actions.\'</td>
                <td class="aright" style="width:6em;">\'.$delete.\'</td>
                </tr></table></li>\';
            $order[] = $i->id;
        }
        else
        {
            echo \'<tr>
                <td><b>\'.$i->title.\'</b></td>
                <td>\'.$actions.\'</td>
                <td class="aright">\'.$delete.\'</td>
                </tr>\';
        }
    }

    if ($sortable)
    {
        $o = implode(\',\', $order);

        echo \'</ul>
        <input type="hidden" name="sort_order" id="sort_order" value="\'.$o.\'" />
        </form>\';

        $js = \'
zebraUl("zebra");
sortize("sort_updater", "sortable", "sort_order");\';
    }
    else
    {
        echo \'</table>\';

        if ($pagination)
        {
            echo \'<div id="'.$name.'_pager" class="pager">\'.X4Pagination_helper::pager(BASE_URL.\''.$name.'/mod/\'.$id_area.\'/\'.$lang.\'/\', $items[1], 5, false, \'/\'.$str, \'btp\').\'</div>\';

            $js .= \'buttonize("'.$name.'_pager", "btp", "topic");\';
        }
        $js .= \'zebraTable("zebra");\';
    }
}
else
{
	echo \'<p>\'._NO_ITEMS.\'</p>\';
}
?>
<script src="<?php echo THEME_URL ?>js/basic.js"></script>
<script>
window.addEvent("domready", function() {
	X3.content("filters","'.$name.'/filter/<?php echo $id_area.\'/\'.$lang.\'/\'.$str ?>", "<?php echo addslashes(X4Theme_helper::navbar($navbar, \' . \', false)) ?>");
	buttonize("topic", "bta", "modal");
	actionize("topic", "btl", "topic", escape("'.BASE_URL.$name.'/index/<?php echo $id_area.\'/\'.$lang.\'/\'.$pp.\'/\'.$str ?>"));
	<?php echo $js ?>
	linking("ul.inline-list a");
});
</script>';
                }
                else
                {
                    // not admin view
                    $txt = '
<h1>TITLE</h1>
<p>TEXT</p>';
                }

                // put the contents in the file
                return (bool) file_put_contents(APATH.'views/'.$area.'/'.$name.'_view'.EXT, $this->head.$this->view_notes.$txt);
            }
            catch (Exception $e)
            {
                echo NL.$e.NL;
            }
        }
        else
        {
            echo NL.'WARNING: the file '.APATH.'views/'.$area.'/'.$name.'_view'.EXT.' already exists'.NL;
        }
	}

	/**
	 * Get table name
	 *
	 * @param string    $name
	 * @return  string
	 */
	private function get_table_name(string $name)
	{
	    if ($this->table_exists($name))
		{
		    // the table already exists
		    return $name;
		}
		else
		{
		    // create a default table
		    $res = $this->db->single_exec(str_replace('XXXTABLE_NAMEXXX', $name, $this->sql), 'NO_ID');

		    if ($this->table_exists($name))
		    {
		        return $name;
		    }
		    else
		    {
		        return '';
		    }
		}
	}

	/**
	 * Check if a table exists
	 *
	 * @param string    $name
	 * @return  booelan
	 */
	private function table_exists(string $name)
	{
	    return  $this->db->query_var('SELECT COUNT(*) AS n
				FROM information_schema.tables
				WHERE
					table_schema = '.$this->db->escape(X4Core_core::$db['default']['db_name']).' AND
					table_name = '.$this->db->escape($name));
	}

	/**
	 * Check if the privtypes over a table exist
	 * If not exist create and assign them to the admin group
	 *
	 * @param string    $name
	 * @return  booelan
	 */
	private function check_privtypes(string $name)
	{
	    // get admin languages
	    $langs = $this->db->query('SELECT code FROM alang WHERE id_area = 1 ORDER BY language ASC');

	    $sql = array();

	    // creation
	    $creation = (int) $this->db->query_var('SELECT COUNT(id) AS n
				FROM privtypes
				WHERE name = '.$this->db->escape('_'.$name.'_creation'));

		if (!$creation)
		{
		    $sql[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, '_".$name."_creation', '_".strtoupper($name)."_CREATION', 1)";
		    $sql[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, '_".$name."_creation', 4, 1)";
		    foreach ($langs as $i)
		    {
		        $sql[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), '".$i->code."', 'admin', 'groups', '_".strtoupper($name)."_CREATION', '".ucfirst($name)." creation', 0, 1)";
		    }
		}

		$edit = (int) $this->db->query_var('SELECT COUNT(id) AS n
				FROM privtypes
				WHERE name = '.$this->db->escape($name));

		if (!$edit)
		{
		    $sql[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, '".$name."', '".strtoupper($name)."', 1)";
		    $sql[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, '".$name."', 4, 1)";
		    foreach ($langs as $i)
		    {
		        $sql[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), '".$i->code."', 'admin', 'groups', '".strtoupper($name)."', '".ucfirst($name)." manager', 0, 1)";
		    }
		}

		if (!empty($sql))
		{
		    $res = $this->db->multi_exec($sql);
		    return ($res[1])
		        ? 1        // privileges added
		        : -1;      // error message
		}
		else
		{
		    return 0;
		}
	}

	/**
	 * Create dictionary
	 * Only for the admin area
	 *
	 * @param string    $area
	 * @param string    $name
	 * @return  boolean
	 */
	public function create_dictionary(string $area, string $name)
	{
	    $uname = strtoupper($name);

	    // get languages for the area
	    $langs = $this->db->query('SELECT l.code, l.id_area, l.language
	        FROM alang l
	        JOIN areas a ON a.id = l.id_area
	        WHERE a.name = '.$this->db->escape($area).'
	        ORDER BY l.language ASC');

	    foreach ($langs as $i)
	    {
	        // check if the section already exists
	        $chk = (int) $this->db->query_var('SELECT COUNT(id) AS n
                FROM dictionary
                WHERE
                    area = '.$this->db->escape($area).' AND
                    lang = '.$this->db->escape($i->code).' AND
                    what = '.$this->db->escape($name));

            if (!$chk)
            {
                $sql = array();

                // add a section to the dictionary
                foreach ($this->admin_dictionary as $k => $v)
                {
                    $sql[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), '".$i->code."', 'admin', '$name', '".str_replace('XXX', $uname, $k)."', '".str_replace('XXX', $name, $v)."', 0, 1)";;
                }
                $res = $this->db->multi_exec($sql);
                return $res[1];
            }
            else
            {
                echo NL.'WARNING: a dictionary section named "'.$name.'" already exists for the '.$area.' area and the '.$i->language.' language'.NL;
                return null;
            }
        }
	}

}
