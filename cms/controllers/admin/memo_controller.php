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
 * Controller for Admin Memos
 *
 * @package X3CMS
 */
class Memo_controller extends X3ui_controller
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
	 * Memos list
	 */
	public function index(string $url) : void
	{
		$this->dict->get_wordarray(array('memo', 'form'));

		$mod = new Memo_model();
        $items = $mod->get_memos($url, $_SESSION['xuid']);
        if (empty($items))
        {
            // show the form
            $this->edit($url);
        }

        $view = new X4View_core('modal');
        $view->title = _MEMO;
        $view->wide = 'md:w-2/3 lg:w-2/3';

        $view->content = new X4View_core('memos/memos');
        $view->content->items = $items;

        $view->render(true);
	}

    /**
	 * New / Edit memo
	 */
	public function edit(string $url, int $id = 0) : void
	{
		$this->dict->get_wordarray(array('form', 'memo'));

		$mod = new Memo_model();
		$item = ($id)
            ? $mod->get_by_id($id)
            : new Memo_obj($this->site->area->lang, $url);

        $form_fields = new X4Form_core('memo/memo_edit');
        $form_fields->id_area = $this->site->area->id;
		$form_fields->item = $item;

		// get the fields array
		$fields = $form_fields->render();

		// if submitted
		if (X4Route_core::$post)
		{
			$e = X4Validation_helper::form($fields, 'editor');
			if ($e)
			{
				$this->editing($id, $item, $_POST);
			}
			else
			{
				$this->notice($fields);
			}
			die;
		}
        $view = new X4View_core('modal');
        $view->title = _MEMO;
        $view->wide = 'md:w-2/3 lg:w-2/3';

		$view->content = new X4View_core('editor');
		$view->content->form = X4Form_helper::doform('editor', $_SERVER["REQUEST_URI"], $fields, array(_RESET, _SUBMIT, 'buttons'), 'post', '',
            '@click="submitForm(\'editor\')"');

        $view->render(true);
	}

    /**
	 * Register Edit / New Memo
	 */
	private function editing(int $id, mixed $item, array $_post) : void
	{
        // no permission each user can handle only his memos
        if ($item->xuid != 0 && $item->xuid != $_SESSION['xuid'])
        {
            $msg = AdminUtils_helper::set_msg([0, 0]);
            $this->response($msg);
        }

        $mod = new Memo_model();
        if (intval(isset($_post['delete'])))
        {
            $result = $mod->delete($item->id);
        }
        else
        {
            $post = array(
                'xuid' => $_SESSION['xuid'],
                'lang' => $_post['lang'],
                'url' => $_post['url'],
                'title' => $_post['title'],
                'description' => str_replace('<script src="//cdn.public.flmngr.com/pM7MjiPd/widgets.js"></script>', '', $_post['description']),
                'personal' => intval(isset($_post['personal'])),
                'xon' => 1
            );

            $result = ($id)
                ? $mod->update($id, $post)
                : $mod->insert($post);

        }

        // set message
        $msg = AdminUtils_helper::set_msg($result);

        if ($result[1])
        {
            $msg->update = array(
                'element' => 'modal',
                'url' => BASE_URL.'memo/index/'.$_post['url']
            );
        }
		$this->response($msg);
	}
}
