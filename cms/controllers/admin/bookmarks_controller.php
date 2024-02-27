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
 * Controller for Bookmarks
 *
 * @package X3CMS
 */
class Bookmarks_controller extends X3ui_controller
{
	/**
	 * Constructor
	 * check if user is logged
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct('bookmarks');
		X4Utils_helper::logged();
	}

    /**
	 * Admin add bookmark
	 *
	 * @return  void
	 */
	public function add(string $lang)
	{
        $this->dict->get_wordarray(array('menus'));

        $response = [
            'message_type' => 'error',
            'error' => '<div class="flex" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">'._MSG_ERROR.'</div>'
        ];

        if (X4Route_core::$input)
		{
            $mod = new Bookmark_model();

            $_post = X4Route_core::$input;
            // remove last question mark
            if (substr($_post['url'], -1) == '?')
            {
                $_post['url'] = substr($_post['url'], 0, -1);
            }
            $chk = $mod->exists($_SESSION['xuid'], $_post['url']);
            if ($chk > 0)
            {
                // return msg
                $response['error'] = '<div class="flex" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">'._BOOKMARKS_ALREADY_EXISTS.'</div>';
            }
            else
            {
                $post = array(
                    'id_area' => 1,
                    'lang' => $lang,
                    'id_user' => $_SESSION['xuid'],
                    'name' => $_post['name'],
                    'url' => $_post['url'],
                    'xon' => 1
                );

                $xdata2 = '{
                    xshow: true,
                    deleteBookmark(id) {
                        fetch(root + "bookmarks/delete/"+id, {
                            method: "GET",
                            headers: { "Content-Type": "text/html" }
                        })
                        .then(res => res.text())
                        .then(txt => {
                            if (txt == 1) {
                                this.xshow = false;
                            }
                        });
                    }
                }';

                $res = $mod->insert($post);
                if ($res[1])
                {
                    $response['message_type'] = 'success';
                    $response['bookmark'] = '<div x-data=\''.$xdata2.'\' x-show="xshow" class="flex flex-row items-center justify-between space-4">
                        <div class="flex-1"><a href="'.$post['url'].'">'.ucfirst($post['name']).'</a></div>
                        <div class="flex-initial"><a @click="deleteBookmark('.$res[0].')"><i class="fas fa-trash-alt warn"></i></a></div>
                    </div>';
                }
                else
                {
                    $response['error'] = '<div class="flex" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">'._BOOKMARKS_ALREADY_EXISTS.'</div>';
                }
            }
		}
        echo json_encode($response);
	}
    
	/**
	 * Delete category form (use Ajax)
	 *
	 * @param   integer $id Bookmark ID
	 * @return  void
	 */
	public function delete(int $id)
	{
		$mod = new Bookmark_model();

        $item = $mod->get_by_id($id, 'bookmarks', 'id, id_user');

        if ($item && isset($item->id) && $item->id_user == $_SESSION['xuid'])
        {
            $result = $mod->delete($item->id);
            echo 1;
        }
        else
        {
            echo 0;
        }
	}
}
