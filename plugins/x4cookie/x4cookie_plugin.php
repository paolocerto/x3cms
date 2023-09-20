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
 * x4cookie plugin
 *
 * @package		X3CMS
 */
class X4cookie_plugin extends X4Plugin_core implements X3plugin
{
	/**
	 * Constructor
	 *
	 * @param	object	$site, site object
	 * @return  void
	 */
	public function __construct($site)
	{
		parent::__construct($site);
		$this->dict = new X4Dict_model(X4Route_core::$area, X4Route_core::$lang);
	}

	/**
	 * Default method
	 *
	 * @param object	$page object
	 * @param array		$args array of args
	 * @return string
	 */
	public function get_module($page, $args, $param = '')
	{
		// if param can be exploded
		$p = explode('|', $param);

		switch($p[0])
		{
        case 'alert':
            // if already set we offer link to change previous choices
			return isset($_COOKIE[COOKIE.'_policy'])
                ? $this->cookie_config($page)
                : $this->cookie_alert($page);
			break;

		default:
			return '';
			break;
		}
	}

    /**
	 * Cookie config
     * here we offer the option to change previous choices
	 *
	 * @access private
	 * @param	stdClass    $page
	 * @return  string
	 */
	private function cookie_config(stdClass $page)
	{
	    // load dictionary
		$this->dict->get_wordarray(array('x4cookie'));

        $cookie = json_decode(base64_decode($_COOKIE[COOKIE.'_policy']), true);

        $thirdy = $cookie['thirdy']
            ? 'true'
            : 'false';

        // add var for profilation if you need it
		$xdata = '{
            modal: false,
            modal_title: "",
            modal_html: "",
            modal_msg: "",
            tech: true,
            thirdy: '.$thirdy.',

            settings() {
                this.html_modal = "";
                this.error_msg = "";
                this.modal = true;
                fetch(root + "plugin/x4cookie/"+area_id+"/settings", {
                    method: "GET",
                    headers: { "Content-Type": "text/html" }
                })
                .then(res => res.text())
                .then(txt => {
                    this.modal_html = txt;
                })
                .catch(() => {
                    this.modal_title = warning;
                    this.modal_msg = error;
                    this.modal_html = modal_ko;
                });
            },
            setup() {
                let str = JSON.stringify({tech: true, thirdy: this.thirdy});
                console.log(str);
                console.log(btoa(str));
                document.cookie="'.COOKIE.'_policy="+btoa(str)+";expires='.gmdate(DATE_COOKIE, strtotime('next year')).';path='.$this->site->site->domain.BASE_URL.';SameSite=Strict";
            },
            close() {
                this.setup();
                this.modal = false;
                location.reload();
            }
        }';

        // here we added a dedicated modal so if you want you can personalize it
		$out = '
<dix
    id="x4cookie_cfg"
    class="fixed text-white z-10 left-0 pl-8 bottom-0 pb-8"

    x-data=\''.$xdata.'\'
>
    <a @click="settings()" class="link" title="'._X4COOKIE_CONFIG.'"><i class="fa-solid fa-2xl fa-cookie-bite"></i></a>

    <div
        class="fixed top-0 left-0 h-full w-full bg-gray-900 bg-opacity-60 z-50"
        x-show="modal"
        x-on:close.window="modal = false"
        x-data=""
        x-cloak
    >
        <div x-html="modal_html" class="pt-8 md:pt-16"></div>
    </div>
</div>';

		return $out;
	}

	/**
	 * Cookie alert
     * here we ask to the user for the first time
	 *
	 * @access private
	 * @param	stdClass    $page
	 * @return  string
	 */
	private function cookie_alert(stdClass $page)
	{
	    // load dictionary
		$this->dict->get_wordarray(array('x4cookie'));

        // get conf
		$conf = $this->site->get_module_param('x4cookie', $page->id_area);
        // we don't open the modal on the info page
        if (!empty($conf['url']) && $conf['url'] == $page->url)
        {
            return '';
        }

        // add var for profilation if you need it
        $accepted = base64_encode(json_encode(['tech' => true, 'thirdy' => true]));

        $xdata = '{
            modal: true,
            modal_title: "",
            modal_html: "",
            modal_msg: "",
            tech: true,
            thirdy: false,
            init() {
                this.html_modal = "";
                this.error_msg = "";
                fetch(root + "plugin/x4cookie/"+area_id+"/default", {
                    method: "GET",
                    headers: { "Content-Type": "text/html" }
                })
                .then(res => res.text())
                .then(txt => {
                    this.modal_html = txt;
                })
                .catch(() => {
                    this.modal_title = warning;
                    this.modal_msg = error;
                    this.modal_html = modal_ko;
                });
            },
            settings() {
                this.html_modal = "";
                this.error_msg = "";
                fetch(root + "plugin/x4cookie/"+area_id+"/settings", {
                    method: "GET",
                    headers: { "Content-Type": "text/html" }
                })
                .then(res => res.text())
                .then(txt => {
                    this.modal_html = txt;
                })
                .catch(() => {
                    this.modal_title = warning;
                    this.modal_msg = error;
                    this.modal_html = modal_ko;
                });
            },
            setup() {
                let str = JSON.stringify({tech: true, thirdy: this.thirdy});
                console.log(str);
                console.log(btoa(str));
                document.cookie="'.COOKIE.'_policy="+btoa(str)+";expires='.gmdate(DATE_COOKIE, strtotime('next year')).';path='.$this->site->site->domain.BASE_URL.';SameSite=Lax";
            },
            close() {
                this.setup();
                this.modal = false;
                location.reload();
            }
        }';

		return '
<div
    class="fixed top-0 left-0 h-full w-full bg-gray-900 bg-opacity-60 z-50"
    x-show="modal"
    x-on:close.window="modal = false"
    x-data=\''.$xdata.'\'
    x-cloak
>
    <div id="x4cookie" class="pt-8 md:pt-16">
        <div
            class="fixed max-h-full overflow-y-auto inset-x-2 md:inset-x-6 lg:w-2/3 xl:w-1/3 mx-auto rounded shadow-2xl xmodal"
        >
            <div x-html="modal_html"></div>
        </div>
    </div>
</div>
';

	}

	/**
	 * call plugin actions
	 *
	 * @param   integer $id_area Area ID
	 * @param   string	$control action name
	 * @param   mixed	$a
	 * @param   mixed	$b
	 * @param   mixed	$c
	 * @param   mixed	$d
	 * @return  void
	 */
	public function plugin($id_area, $control, $a, $b, $c, $d)
	{
		switch ($control)
		{

		// call private method
		case 'default':
            // the content of the dialog for the first time
			$this->default($id_area);
			break;

		case 'settings':
			$this->settings($id_area);
			break;

		default:
			return '';
			break;
		}
	}

    /**
	 * default
     * is the content of the dialogo for the first time
	 *
	 * @param   integer $id_area Area ID
	 * @return  mixed
	 */
	private function default($id_area)
	{
        // load dictionary
		$this->dict->get_wordarray(array('x4cookie'));

        // get conf
		$conf = $this->site->get_module_param('x4cookie', $id_area);

        $more = '';

        if (!empty($conf['url']))
        {
            $more = '<button type="button" class="btn gray" onclick="window.location.href=\''.$this->site->site->domain.BASE_URL.$conf['url'].'\'">'._X4COOKIE_MORE_INFO.'</button>';
        }

        echo '<div class="bg-white text-gray-700 md:px-8 px-4 py-10 rounded-lg md:inset-x-6 lg:w-2/3 xl:w-1/3 mx-auto">
            <div class="flex flex-row items-center justify-between">
                <h3 class="mpt0 mb-0 pb-0 font-bold tracking-tight">'._X4COOKIE_SETUP.'</h3>
                <a class="link" @click="close()">
                    <i class="fa-solid fa-2x fa-circle-xmark" ></i>
                </a>
            </div>
            <div class="pt-6">'.nl2br(_X4COOKIE_MESSAGE).'</div>
            <div class="mt-8 flex flex-col md:flex-row justify-end gap-4">
                '.$more.'
                <button type="button" class="btn gray" @click="settings()">'._X4COOKIE_SETUP.'</button>
                <button type="button" class="btn link" @click="close()">'._X4COOKIE_OK.'</button>
            </div>
        </div>';
    }

    /**
	 * Settings
     * is the content of the dialog to change previous choices
	 *
	 * @param   integer $id_area Area ID
	 * @return  mixed
	 */
	private function settings($id_area)
	{
        // load dictionary
		$this->dict->get_wordarray(array('x4cookie'));

        // get conf
		$conf = $this->site->get_module_param('x4cookie', $id_area);

        $more = '';
        if (!empty($conf['url']))
        {
            $more = '<button type="button" class="btn gray" onclick="window.location.href=\''.$this->site->site->domain.BASE_URL.$conf['url'].'\'">'._X4COOKIE_MORE_INFO.'</button>';
        }

        $title =  _X4COOKIE_SETUP;
        $thirdy = '';
        if (isset($_COOKIE[COOKIE.'_policy']))
        {
            $title = _X4COOKIE_CONFIG;
            $data = json_decode(base64_decode($_COOKIE[COOKIE.'_policy']), true);

            $thirdy = (is_null($data['thirdy']) || empty($data['thirdy']))
                ? ''
                : 'checked';
        }

        echo '<div class="bg-white text-gray-700 md:px-8 px-4 py-10 rounded-lg md:inset-x-6 lg:w-2/3 xl:w-1/3 mx-auto">
            <div class="flex flex-row items-center justify-between">
                <h3 class="mpt0 mb-0 pb-0 font-bold tracking-tight">'.$title.'</h3>
                <a class="link" @click="close()">
                    <i class="fa-solid fa-2x fa-circle-xmark" ></i>
                </a>
            </div>
            <div class="pt-6">
                '.nl2br(_X4COOKIE_SETUP_MSG).'
                <div class="flex flex-col gap-4">
                    <div>
                        <label for="tech">
                            <input type="checkbox" name="tech" x-model="tech" checked onclick="return false;" >
                            '._X4COOKIE_TECHNICAL.'
                        </label>
                        <p class="text-sm">'.nl2br(_X4COOKIE_TECHNICAL_MSG).'</p>
                    </div>
                    <div>
                        <label for="thirdy">
                            <input type="checkbox" name="thirdy" x-model="thirdy" '.$thirdy.' >
                            '._X4COOKIE_THIRDY.'
                        </label>
                        <p class="text-sm">'.nl2br(_X4COOKIE_THIRDY_MSG).'</p>
                    </div>
                </div>
            </div>
            <div class="mt-8 flex flex-col md:flex-row justify-end gap-4">
                '.$more.'
                <button type="button" class="btn link" @click="close()">'._X4COOKIE_OK.'</button>
            </div>
        </div>';
	}

	/**
	 * SAMPLE method
	 *
	 * @param   integer $id_area Area ID
	 * @param   mixed	$a
	 * @param   mixed	$b
	 * @return  mixed
	 */
	private function test($id_area, $a, $b)
	{
		// TO DO
		/*
		Here you can execute an action or you can get data to display
		*/
	}

}
