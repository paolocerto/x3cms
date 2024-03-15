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
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
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
     * Notes for forms
     */
    private $form_notes = '
/*
    NOTES FOR DEVELOPERS
    ----------------------------------------------------------------------------

    The X3CMS is a complex environment. Almost always a part of the CMS is connected and depends from a lot of other parts.

    Usually we have a controller for each table (with the same name of the controller), a model for each controller (to interact with the table) and in most cases at least one view to display your data.

    For actions like editing we use forms inside a modal. A form could be very complex so we use dedicated files.
    There are some useful scripts for Alpine.js in the file /themes/admin/js/xui.js that we sometimes use inside forms, for one-time scripts is better to put them here

    This file contains "commented" some frequently used snippets, so you can easily enable what you need.

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
      `id_area` int(11) NOT NULL,
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
	 */
	public function create_controller(string $area, string $name) : bool
	{
	    $uname = ucfirst($name);
	    $upper_name = strtoupper($name);

	    // avoid overwrite
	    if (!file_exists(APATH.'controllers/'.$area.'/'.$name.'_controller.php'))
	    {
	        // try to create the file
            try
            {
                // create the empty file
                touch(APATH.'controllers/'.$area.'/'.$name.'_controller.php');

                if ($area == 'admin')
                {
                    // check if the table exists
                    $table = $this->get_table_name($name);

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
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::logged();
	}

	/**
	 * Default method
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
		$view = new X4View_core(\'page\');
        $view->breadcrumb = [];
        $view->actions = \'\';

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
    <li>form: /cms/forms/admin/'.$name.'_edit_form.php</li>
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
    <li>If you need to add fields in the table you have to change the '.$uname.'_model (at the end you will find the object used to create new items), the '.$name.'_edit_form then the '.$name.'_controller for the form in the editing() method where data are saved in the database.</li>
</ul>
<div class="buttons text-center"><button type="button" @click="pager(\''.$name.'/index\')" title="\'._'.$upper_name.'_MANAGER.\'">\'._'.$upper_name.'_MANAGER.\'</button></div>

<p><b>Have fun</b></p>\';

		$view->render(true);

		// end section
		// ------------------------------------------------------------------ */
	}

	/**
	 * Index method
	 * If you plan to use the data handled in this page in only one area set the $id_area default value as you need ((e.g. 2 == public area)
	 */
	public function index(int $id_area = 2, string $lang = \'en\', int $pp = 0) : void
	{
	    // you should have created a section in the dictionary for this controller
		$this->dict->get_wordarray(array(\''.$name.'\'));

        // get query string from filters
        // here we handle only search buy string but you can extend to filter for more fields
        // $qs = X4Route_core::get_query_string();
        // handle filters
        // $qs[\'xstr\'] = $qs[\'xstr\'] ?? \'\';

		// get page
		// if you created a page in the CMS with the same name
		$page = $this->get_page(\''.$name.'\');
		$view = new X4View_core(\'page\');
        $view->breadcrumb = array($this->site->get_bredcrumb($page));
		$view->actions = AdmUtils_helper::link(
            \'memo\',
            \''.$name.'\':\'.$lang,
            [],
            _MEMO
        ).$this->actions($id_area, $lang);

		// content

		// if you created a view for that page with the same name
		$view->content = new X4View_core(\''.$name.'\');
		$view->content->id_area = $id_area;
		$view->content->lang = $lang;
		$view->content->pp = $pp;

        // do you use filters?
        // $view->content->qs = $qs;

		// if you created a model for that controller
		$mod = new '.$uname.'_model();
		if ($mod->is_sortable($id_area, $lang))
		{
		    $view->content->items = $mod->get_items($id_area, $lang, $qs);
		}
		else
		{
		    // if not sortable we paginate
		    $view->content->items = X4Pagination_helper::paginate($mod->get_items($id_area, $lang, $qs), $pp);
		}

		// switchers
        if (MULTILANGUAGE)
        {
            // language switcher
            $lang = new Language_model();
            $view->langs = $lang->get_languages();
        }
		// area switcher
		$area = new Area_model();
		$view->areas = $area->get_areas();

		$view->render(true);
	}

	/**
	 * '.$uname.' actions
	 * This method populates the top right of the admin layout
	 * It usually contains the Plus button to add new items
	 */
	private function actions(int $id_area, string $lang) : string
	{
		return \'<a class="link" @click="popup(\\\'\'.BASE_URL.\''.$name.'/edit/\'.$id_area.\'/\'.$lang.\'\\\')" title="\'._'.strtoupper($name).'_NEW.\'">
                <i class="fa-solid fa-lg fa-circle-plus"></i>
            </a>\';
	}

	/**
	 * Change status
	 * This method is used to change the xon and xlock fields in the table '.$name.'
	 */
	public function set(string $what, int $id_area, int $id, int $value = 0) : void
	{
		$msg = null;
		$msg = AdmUtils_helper::chk_priv_level($id_area, \''.$name.'\', $id, $what);
		if (is_null($msg))
		{
			$mod = new '.$uname.'_model();
			$result = $mod->update($id, array($what => $value));

			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);

			if ($result[1])
            {
				$msg->update = array(
					\'element\' => \'page\',
					\'url\' => $_SERVER[\'HTTP_REFERER\']
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
	 */
	public function edit(int $id_area, string $lang, int $id = 0) : void
	{
		$this->dict->get_wordarray(array(\'form\', \''.$name.'\'));

		// handle id
		$chk = false;
		if ($id < 0)
		{
			$id = 0;
			$chk = true;
		}

		$mod = new '.$uname.'_model();
        $item = ($id)
			? $mod->get_by_id($id)
			: new '.$uname.'_obj($id_area, $lang);

        $form_fields = new X4Form_core(\''.$uname.'_edit\', \''.$uname.'\');
		$form_fields->id = $id;
		$form_fields->item = $item;

        $fields = $form_fields->render();

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

        $view = new X4View_core(\'modal\');
        // if you need a larger modal
        // $view->wide = \'md:w-2/3 lg:w-2/3\';
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

		$view->content = new X4View_core(\'editor\');
        // can user edit?
        $submit = AdmUtils_helper::submit_btn($item->id_area, \'x3_plugins\', $id, $item->xlock);
		$view->content->form = X4Form_helper::doform(\'editor\', BASE_URL.\''.$name.'/edit/\'.$id_area.\'/\'.$lang.\'/\'.$id, $fields, array(_RESET, $submit, \'buttons\'), \'post\', \'\',
            \'@click="submitForm(\\\'editor\\\')"\');

		$view->render(true);
	}

	/**
	 * Register Edit / New item
	 * To use this method you need:
	 * - a Table with name = '.$name.' with fields: id, id_area, lang, name, title, description
	 * - you need in the table privtypes record for manage and create table items in '.$name.' ('.$name.' and _'.$name.'_creation)
	 * - you need to assign those privileges to the group of your user in the table gprivs
	 */
	private function editing(int $id, array $_post) : void
	{
		$msg = null;
		$msg = ($id)
		    ? AdmUtils_helper::chk_priv_level($_post[\'id_area\'], \''.$name.'\', $id, \'edit\')
		    : AdmUtils_helper::chk_priv_level($_post[\'id_area\'], \'_'.$name.'_creation\', 0, \'create\');

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

			if ($id)
			{
			    $result = $mod->update($id, $post);
			}
			else
			{
			    if ($mod->is_sortable($post[\'id_area\'], $post[\'lang\']))
			    {
			        $post[\'xpos\'] = $mod->get_max_pos($post[\'id_area\'], $post[\'lang\']) + 1;
			    }
			    $result = $mod->insert($post);
			}

			$msg = AdmUtils_helper::set_msg($result);

			if ($result[1])
			{
				$msg->update = array(
					\'element\' => \'page\',
					\'url\' => $_SERVER[\'HTTP_REFERER\']
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Delete item form
	 * You can call this only from the admin side
	 * To use this method you need:
	 * - a Table with name = '.$name.' with fields: id, title
	 * - a dictionary section "'.$name.'" with at least the KEYS:  _DELETE_'.strtoupper($name).'
	 * - a model '.$uname.'_model
	 */
	public function delete(int $id) : void
	{
		$this->dict->get_wordarray(array(\'form\', \''.$name.'\'));

		$mod = new '.$uname.'_model();
		$item = $mod->get_by_id($id, \''.$name.'\', \'id, id_area, title\');

		$fields = array();
		$fields[] = array(
			\'label\' => null,
			\'type\' => \'hidden\',
			\'value\' => $id,
			\'name\' => \'id\'
		);

		if (X4Route_core::$post)
		{
			$this->deleting($item);
			die;
		}

        $view = new X4View_core(\'modal\');
		// to avoid error for missing dictionary
		$title = (defined(\'_'.strtoupper($name).'_DELETE\'))
		    ? _'.strtoupper($name).'_DELETE
		    : \'Delete '.$uname.'\';
        $view->title = $title;

		$view->content = new X4View_core(\'delete\');
        $view->content->item = $item->title;
		$view->content->form = X4Form_helper::doform(\'delete\', $_SERVER["REQUEST_URI"], $fields, array(null, _YES, \'buttons\'), \'post\', \'\',
			\'@click="submitForm(\\\'delete\\\')"\');

		$view->render(true);
	}

	/**
	 * Delete item
	 */
	private function deleting(stdClass $item) : void
	{
		$msg = null;
		$msg = AdmUtils_helper::chk_priv_level($item->id_area, \''.$name.'\', $item->id, \'delete\');

		if (is_null($msg))
		{
			$mod = new '.$uname.'_model();
			$result = $mod->delete($item->id);

			$msg = AdmUtils_helper::set_msg($result);

			if ($result[1]) {
				AdmUtils_helper::delete_priv(\''.$name.'\', $item->id);

				$msg->update = array(
					\'element\' => \'page\',
					\'url\' => $_SERVER[\'HTTP_REFERER\']
				);
			}
		}
		$this->response($msg);
	}

	/**
	 * Move fields
	 * Useful with orderable items
	 */
	public function ordering(int $id_area, string $lang) : void
	{
		$msg = null;
		if (is_null($msg) && X4Route_core::$input)
		{
			$_post = X4Route_core::$input;
			$elements = $_post[\'sort_order\'];

			$mod = new '.$uname.'_model();
			$items = $mod->get_items($id_area, $lang);

			$result = array(0, 1);
			if ($items)
			{
				foreach ($items as $i)
				{
					$p = array_search($i->id, $elements);
					if ($p && $i->xpos != $p)
					{
						$res = $mod->update($i->id, array(\'xpos\' => $p));
                        // update result only if there was not errors before
						if ($result[1] == 1)
						{
							$result = $res;
						}
					}
				}
			}

			$this->dict->get_words();
			$msg = AdmUtils_helper::set_msg($result);
		}
		$this->response($msg);
	}

	/**
	 * Rebuild the widget
	 */
	public function rewidget(string $title, int $id_area, string $area) : string
	{
		$mod = new '.$uname.'_model();
		echo $mod->get_widget(urldecode($title), $id_area, urldecode($area), false);
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
	 */
	public function __construct()
	{
		parent::__construct();
		X4Utils_helper::offline($this->site->data->xon, BASE_URL.\'msg/message/offline\');
	}

	// put here other methods you need

}';
                    }
                }

                // put the contents in the file
                return (bool) file_put_contents(APATH.'controllers/'.$area.'/'.$name.'_controller.php', $this->head.$this->controller_notes.$txt);

            }
            catch (Exception $e)
            {
                echo NL.$e.NL;
            }
        }
        else
        {
            echo NL.'WARNING: the file '.APATH.'controllers/'.$area.'/'.$name.'_controller.php already exists'.NL;
        }
	}

    /**
	 * Create form
	 */
	public function create_form(string $area, string $name) : bool
	{
	    // avoid overwrite
	    if (!file_exists(APATH.'forms/'.$area.'/'.$name.'_form.php'))
	    {
	        // try to create the file
            try
            {
                // create the empty file
                touch(APATH.'forms/'.$area.'/'.$name.'_form.php');

                $txt = '

<?php defined(\'ROOT\') or die(\'No direct script access.\');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */

// '.$name.' Edit form

// to handle file\'s label
$file_array = array();
// to handle optional JS build with the form construction
$js_array = array();

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

// this container fix the background of the modal for the form
$fields[] = array(
    \'label\' => null,
    \'type\' => \'html\',
    \'value\' => \'<div class="bg-white text-gray-700 md:px-8 md:pb-8 px-4 pb-4" style="border:1px solid white">\'
);

/*
// IF YOU NEED TO SET COLUMNS

// open grid
$fields[] = array(
    \'label\' => null,
    \'type\' => \'html\',
    \'value\' => \'<div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div>\'
);

// put an input field here

// separator
$fields[] = array(
    \'label\' => null,
    \'type\' => \'html\',
    \'value\' => \'</div><div></div>\'
);

// another input field here

// close grid
$fields[] = array(
    \'label\' => null,
    \'type\' => \'html\',
    \'value\' => \'</div></div>\'
);
*/

/*
// IF YOU NEED TO USE ACCORDION

// open accordion
$fields[] = array(
    \'label\' => null,
    \'type\' => \'html\',
    \'value\' => \'<div x-data="{ open: false }" class="cursor-pointer group">
    <button @click="open = !open" class="bg2 rounded flex items-center justify-between w-full p-4 text-left select-none mb-1">
        <span>Accordion title</span>
        <svg class="w-4 h-4 duration-200 ease-out" :class="{ \\\'rotate-180\\\': open }" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
    </button>
    <div x-show="open" @click.away="open = false" x-transition:enter.duration.300ms x-transition:leave.duration.50ms x-cloak>
        <div class="p-4 pt-0">\'
);

// your input fields here

// close accordion
$fields[] = array(
    \'label\' => null,
    \'type\' => \'html\',
    \'value\' => \'</div></div>\'
);
*/

$fields[] = array(
    \'label\' => _TITLE,
    \'type\' => \'text\',
    \'value\' => $item->title,
    \'name\' => \'title\',
    \'rule\' => \'required\',
    \'extra\' => \'class="w-full"\'
);
$fields[] = array(
    \'label\' => _DESCRIPTION,
    \'type\' => \'textarea\',
    \'value\' => $item->description,
    \'name\' => \'description\'
);

$fields[] = array(
    \'label\' => null,
    \'type\' => \'html\',
    \'value\' => \'</div>\'
);
';

                // put the contents in the file
                return (bool) file_put_contents(APATH.'forms/'.$area.'/'.$name.'_form.php', $this->head.$this->form_notes.$txt);

            }
            catch (Exception $e)
            {
                echo NL.$e.NL;
            }
        }
        else
        {
            echo NL.'WARNING: the file '.APATH.'forms/'.$area.'/'.$name.'_form.php already exists'.NL;
        }

    }

	/**
	 * Create model
	 */
	public function create_model(string $area, string $name) : bool
	{
	    $uname = ucfirst($name);

	    // avoid overwrite
	    if (!file_exists(APATH.'models/'.$uname.'_model.php'))
	    {
	        // try to create the file
            try
            {
                // create the empty file
                touch(APATH.'models/'.$uname.'_model.php');

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
	 */
	public function get_url(stdClass $obj, string $topage) : string
	{
		return $topage.\'/\'.$obj->id.\'/\'.$obj->url;
	}

    /**
	 * Constructor
	 * set the default table
	 */
	public function __construct()
	{
		parent::__construct(\''.$table.'\');
	}

	/**
	 * Get items
	 */
	public function get_items(int $id_area, string $lang, array $qs = []) : array
	{
		$where = \'\';
		if (isset($qs[\'xstr\']) && !empty($qs[\'xstr\']))
        {
            $w = array();
            $tok = explode(\' \', urldecode($qs[\'xstr\']));
            foreach ($tok as $i)
            {
                $a = trim($i);
                if (!empty($a))
                {
                    $w[] = \'x.title LIKE \'.$this->db->escape(\'%\'.$a.\'%\').\' OR
                        x.description LIKE \'.$this->db->escape(\'%\'.$a.\'%\');
                }
            }

            if (!empty($w))
            {
                $where .= \' AND (\'.implode(\') AND (\', $w).\')\';
            }
        }

		// sorting
		$order = ($this->is_sortable($id_area, $lang))
		    ? \'x.xpos ASC\'
		    : \'x.title ASC\';

		return $this->db->query(\'SELECT x.*, IF(p.id IS NULL, u.level, p.level) AS level
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
	 */
	public function is_sortable(int $id_area, string $lang) : bool
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
	public function get_max_pos(int $id_area, string $lang) : int
	{
		return (int) $this->db->query_var(\'SELECT xpos
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
	 */
	public function get_widget(string $title, int $id_area, string $area) : string
	{
		// here get the data you want to show inside the widget

        // dictionary
        $dict = new X4Dict_model(X4Route_core::$folder, X4Route_core::$lang);
        $dict->get_wordarray(array(\''.$name.'\'));

        // title
        $w = \'<div class="bg rounded-t px-4 py-4 flex items-center justify-between">
                    <h4>\'.$title._TRAIT_.\'<span class="text-sm">\'.$area.\'</span></h4>
                    <div class="space-x-4">
                        <a class="link" @click="refresh(\\\''.$name.'_widget\\\', \\\'\'.BASE_URL.\''.$name.'/rewidget/\'.urlencode($title).\'/\'.$id_area.\'/\'.urlencode($area).\'\\\')" title="\'.XPREFIXX_RELOAD.\'">
                            <i class="fa-solid fa-lg fa-rotate"></i></a>
                        <a class="link" @click="pager(\\\'\'.BASE_URL.\''.$name.'/mod/\'.$id_area.\'\\\')" title="\'.$title.\'">
                            <i class="fa-solid fa-lg fa-chevron-right"></i></a>
                    </div>
                </div>
                <div class="bg2 h-full px-4 pt-4 pb-8">
                    <p>Here put your data</p>
                </div>\';

        return $container
            ? \'<div id="'.$name.'_widget">\'.$w.\'</div>\'
            : $w;

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
    public $xlock = 0;

	/**
	 * Constructor
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
                    return (bool) file_put_contents(APATH.'models/'.$uname.'_model.php', $this->head.$this->model_notes.$txt);
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
            echo NL.'WARNING: the file '.APATH.'models/'.$uname.'_model.php already exists'.NL;
        }
	}

	/**
	 * Create view
	 */
	public function create_view(string $area, string $name) : bool
	{
	    $uname = strtoupper($name);

	    // avoid overwrite
	    if (!file_exists(APATH.'views/'.$area.'/'.$name.'_view.php'))
	    {
	        // try to create the file
            try
            {
                // create the empty file
                touch(APATH.'views/'.$area.'/'.$name.'_view.php');

                if ($area == 'admin')
                {
                    // admin view
                    // here we suppose you loaded in the controller the dictionary section for the view
                    $txt = '
// if you don\'t need to switch between languages you can comment this section
echo \'<div class="switcher">\';
// lang switcher
if (MULTILANGUAGE)
{
    echo \'<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">\';
	foreach ($langs as $i)
	{
		$on = ($i->code == $lang)
			? \'class="link"\'
            : \'class="dark"\';
		echo \'<a \'.$on.\' @click="pager(\\\'\'.BASE_URL.$name.\'/index/\'.$id_area.\'/\'.$i->code.\'\\\')" title="\'._SWITCH_LANGUAGE.\'">\'.ucfirst($i->language).\'</a>\';
	}
	echo \'</div>\';
}

// if you don\'t need to switch between areas you can comment this section
// area switcher
if (MULTIAREA)
{
    echo \'<div class="text-sm flex justify-end py-1 space-x-4 border-b border-gray-200">\';
	foreach ($areas as $i)
	{
        if ($i->id > 1)
        {
            $on = ($i->id == $id_area)
                ? \'class="link"\'
                : \'class="dark"\';
            echo \'<a \'.$on.\' @click="pager(\\\'\'.BASE_URL.$name.\'/index/\'.$i->id.\'/\'.$lang.\'\\\')" title="\'._SWITCH_AREA.\'">\'.ucfirst($i->name).\'</a>\';
        }
	}
	echo \'</div>\';
}
echo \'</div>\';

/*
// filter selector
echo \'<form name="xfilter" id="xfilter" action="\'.BASE_URL.\''.$name.'/index/\'.$id_area.\'/\'.$lang.\'" method="GET" onsubmit="return false">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">\';
*/
/*
// filter sample
echo \'<div>
    <label for="xwhat">What filter</label>
    <select id="xwhat" name="xwhat" class="w-full" @change="filter()">
        \'.X4Form_helper::get_options(X4Array_helper::simplearray2obj($whats), \'value\', \'option\', $qs[\'xwhat\'], \'\').\'
    </select>
</div>\';
*/

/*
echo \'<div class="col-span-2">
        <label for="xstr">\'.'.strtoupper($name).'_SEARCH_MSG.\'</label>
        <input
            type="text"
            id="xstr"
            name="xstr"
            class="w-full uppercase"
            value="\'.$qs[\'xstr\'].\'"
            autocomplete="off"
            placeholder="\'._ENTER_TO_FILTER.\'"
            @keyup="if ($event.key === \\\'Enter\\\') { filter(); }" />
    </div>\';
echo \'</div></form>\';
*/

// to avoid errors for missing dictionary
$title = (defined(\'_'.$uname.'_MANAGER\'))
    ? _'.$uname.'_MANAGER
    : \''.ucfirst($name).' Manager\';

$elements = (defined(\'_'.$uname.'_ITEMS\'))
    ? _'.$uname.'_ITEMS
    : \''.ucfirst($name).' items\';

?>
<h1 class="mt-6"><?php echo $title ?></h1>
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
    echo \'<table>
        <thead>
            <tr>
                <th>\'.$elements.\'</th>
                <th class="w-48">\'._ACTIONS.\'</th>
            </tr>
        </thead>\';

	if ($sortable)
	{
	    echo \'</table>
            <div x-data="xsortable()" x-init="setup(\\\'sortable\\\', \\\''.$name.'/ordering/\'.$id_area.\'/\'.$lang.\'\\\')">
                <div id="sortable">\';
	}
    else
    {
        echo \'<tbody>\';
    }

	foreach ($list as $i)
	{
		$statuses = AdmUtils_helper::statuses($i);

        $actions = \'\';
        if (($i->level > 1 && $i->xlock == 0) || $i->level >= 3)
        {
            $actions = AdmUtils_helper::link(\'edit\', \''.$name.'/edit/\'.$i->id_area.\'/\'.$i->lang.\'/\'.$i->id);

            if ($i->level > 2)
            {
                $actions .= AdmUtils_helper::link(\'xon\', \''.$name.'/set/xon/\'.$i->id.\'/\'.intval(!$i->xon), $statuses);

                if ($i->level >= 4)
                {
                    $actions .= AdmUtils_helper::link(\'xlock\', \''.$name.'/set/xlock/\'.$i->id.\'/\'.intval(!$i->xlock), $statuses);
                    $actions .= AdmUtils_helper::link(\'delete\', \''.$name.'/delete/\'.$i->id);
                }
            }
        }

        if ($sortable)
        {
            echo \'<div class="sort-item" id="\'.$i->id.\'">
                <table class="my-0">
                    <tr>
                        <td><strong>\'.$i->title.\'</strong></td>
                        <td class="w-40 space-x-2 text-right">\'.$actions.\'</td>
                    </tr>
                </table>
            </div>\';
        }
        else
        {
            echo \'<tr>
                <td><b>\'.$i->title.\'</b></td>
                <td class="w-40 space-x-2 text-right">\'.$actions.\'</td>
            </tr>\';
        }
    }

    if ($sortable)
    {
        echo \'</div></div>\';
    }
    else
    {
        echo \'</tbody>
            </table>\';

        if ($pagination)
        {
            echo \'<div id="'.$name.'_pager" class="pager">\'.X4Pagination_helper::tw_admin_pager(BASE_URL.\''.$name.'/mod/\'.$id_area.\'/\'.$lang.\'/\', $items[1], 5, false, \'?\'.http_build_query($qs), \'\').\'</div>\';
        }
    }
}
else
{
	echo \'<p>\'._NO_ITEMS.\'</p>\';
}';
                }
                else
                {
                    // not admin view
                    $txt = '
<h1>TITLE</h1>
<p>TEXT</p>';
                }

                // put the contents in the file
                return (bool) file_put_contents(APATH.'views/'.$area.'/'.$name.'_view.php', $this->head.$this->view_notes.$txt);
            }
            catch (Exception $e)
            {
                echo NL.$e.NL;
            }
        }
        else
        {
            echo NL.'WARNING: the file '.APATH.'views/'.$area.'/'.$name.'_view.php already exists'.NL;
        }
	}

	/**
	 * Get table name
	 */
	private function get_table_name(string $table_name) : string
	{
	    if ($this->table_exists($table_name))
		{
		    return $table_name;
		}

        // create a default table
        $this->db->single_exec(str_replace('XXXTABLE_NAMEXXX', $table_name, $this->sql), 'NO_ID');
        if ($this->table_exists($table_name))
        {
            return $table_name;
        }
        else
        {
            return '';
        }
	}

	/**
	 * Check if a table exists
	 */
	private function table_exists(string $table_name) : int
	{
	    return (int) $this->db->query_var('SELECT COUNT(*) AS n
				FROM information_schema.tables
				WHERE
					table_schema = '.$this->db->escape(X4Core_core::$db['default']['db_name']).' AND
					table_name = '.$this->db->escape($table_name));
	}

	/**
	 * Check if the privtypes over a table exist
	 * If not exist create and assign them to the admin group
	 */
	private function check_privtypes(string $table_name) : int
	{
	    // get admin languages
	    $langs = $this->db->query('SELECT code FROM alang WHERE id_area = 1 ORDER BY language ASC');

	    $sql = array();

	    // creation
	    $creation = (int) $this->db->query_var('SELECT COUNT(*) AS n
				FROM privtypes
				WHERE name = '.$this->db->escape('_'.$table_name.'_creation'));

		if (!$creation)
		{
		    $sql[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, '_".$table_name."_creation', '_".strtoupper($table_name)."_CREATION', 1)";
		    $sql[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, '_".$table_name."_creation', 4, 1)";
		    foreach ($langs as $i)
		    {
		        $sql[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), '".$i->code."', 'admin', 'groups', '_".strtoupper($table_name)."_CREATION', '".ucfirst($table_name)." creation', 0, 1)";
		    }
		}

		$edit = (int) $this->db->query_var('SELECT COUNT(*) AS n
				FROM privtypes
				WHERE name = '.$this->db->escape($table_name));

		if (!$edit)
		{
		    $sql[] = "INSERT INTO privtypes (updated, xrif, name, description, xon) VALUES (NOW(), 1, '".$table_name."', '".strtoupper($table_name)."', 1)";
		    $sql[] = "INSERT INTO gprivs (updated, id_group, what, level, xon) VALUES (NOW(), 1, '".$table_name."', 4, 1)";
		    foreach ($langs as $i)
		    {
		        $sql[] = "INSERT INTO dictionary (updated, lang, area, what, xkey, xval, xlock, xon) VALUES (NOW(), '".$i->code."', 'admin', 'groups', '".strtoupper($table_name)."', '".ucfirst($table_name)." manager', 0, 1)";
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
	 */
	public function create_dictionary(string $area, string $name) : int
	{
	    $uname = strtoupper($name);

	    // get languages for the area
	    $langs = $this->db->query('SELECT l.code, l.id_area, l.language
	        FROM alang l
	        JOIN areas a ON a.id = l.id_area
	        WHERE a.name = '.$this->db->escape($area).'
	        ORDER BY l.language ASC');

        $res = [0, 0];
	    foreach ($langs as $i)
	    {
	        // check if the section already exists
	        $chk = (int) $this->db->query_var('SELECT COUNT(*) AS n
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
            }
            else
            {
                echo NL.'WARNING: a dictionary section named "'.$name.'" already exists for the '.$area.' area and the '.$i->language.' language'.NL;
            }
        }
        return $res[1];
	}

}
