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
	public function __construct(X4Site_model $site)
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
        // get conf
		$conf = $this->site->get_module_param('x4cookie', $page->id_area);

        if (!isset($conf['edit_settings']) || !$conf['edit_settings'])
        {
            return '';
        }

	    // load dictionary
		$this->dict->get_wordarray(array('x4cookie'));

        $cookie = json_decode(base64_decode($_COOKIE[COOKIE.'_policy']), true);

        $thirdy = $cookie['thirdy']
            ? 'true'
            : 'false';

        $profile = isset($cookie['profile']) && $cookie['profile']
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
            profile: '.$profile.',

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
                document.cookie="'.COOKIE.'_policy="+btoa(str)+";expires='.gmdate(DATE_COOKIE, strtotime('next year')).';path='.$this->site->data->domain.BASE_URL.';SameSite=Strict";
            },
            close() {
                this.setup();
                this.modal = false;
                location.reload();
            }
        }';

        // here we added a dedicated modal so if you want you can personalize it
		return '
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
        <div
            class="fixed max-h-full overflow-y-auto inset-x-2 md:inset-x-6 lg:w-2/3 xl:w-1/3 mx-auto"
        >
            <div x-html="modal_html" class="pt-8 md:pt-16"></div>
        </div>
    </div>
</div>';
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
        // disable cache
        X4Utils_helper::nocache();

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
            profile: false,
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
                let str = JSON.stringify({tech: true, thirdy: this.thirdy, profile: this.profile});
                document.cookie="'.COOKIE.'_policy="+btoa(str)+";expires='.gmdate(DATE_COOKIE, strtotime('next year')).';path='.$this->site->data->domain.BASE_URL.';SameSite=Lax";
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
            class="fixed max-h-full overflow-y-auto inset-x-2 md:inset-x-6 lg:w-2/3 xl:w-1/3 mx-auto"
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
	 *@param   string	$control action name
	 * @param   mixed	$a
	 * @param   mixed	$b
	 * @param   mixed	$c
	 * @param   mixed	$d
	 * @return  void
	 */
	public function plugin(string $control, $a, $b, $c, $d)
	{
		switch ($control)
		{

		// call private method
		case 'default':
            // the content of the dialog for the first time
			$this->default();
			break;

		case 'settings':
			$this->settings();
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
	 * @return  mixed
	 */
	private function default()
	{
        // load dictionary
		$this->dict->get_wordarray(array('x4cookie'));

        // get conf
		$conf = $this->site->get_module_param('x4cookie', $this->site->area->id);

        $more = '';

        if (!empty($conf['url']))
        {
            $more = '<button type="button" class="btn gray" onclick="window.location.href=\''.$this->site->data->domain.BASE_URL.$conf['url'].'\'">'._X4COOKIE_MORE_INFO.'</button>';
        }

        // edit
        $edit = '';
        if (isset($conf['edit_settings']) && $conf['edit_settings'])
        {
            $edit = _X4COOKIE_EDIT;
        }

        echo '<div class="bg-white rounded-xl shadow-2xl xmodal text-gray-700 md:px-8 px-4 py-10">
            <div class="flex flex-row items-center justify-between">
                <h3 class="mpt0 mb-0 pb-0 font-bold tracking-tight">'._X4COOKIE_SETUP.'</h3>
                <a class="link" @click="close()">
                    <i class="fa-solid fa-2x fa-circle-xmark" ></i>
                </a>
            </div>
            <div class="pt-6">'.nl2br(_X4COOKIE_MESSAGE.$edit).'</div>
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
	 * @return  mixed
	 */
	private function settings()
	{
        // load dictionary
		$this->dict->get_wordarray(array('x4cookie'));

        // get conf
		$conf = $this->site->get_module_param('x4cookie', $this->site->area->id);

        $more = '';
        if (!empty($conf['url']))
        {
            $more = '<button type="button" class="btn gray" onclick="window.location.href=\''.$this->site->data->domain.BASE_URL.$conf['url'].'\'">'._X4COOKIE_MORE_INFO.'</button>';
        }

        $title =  _X4COOKIE_SETUP;
        $thirdy = $profile = '';
        if (isset($_COOKIE[COOKIE.'_policy']))
        {
            $title = _X4COOKIE_CONFIG;
            $data = json_decode(base64_decode($_COOKIE[COOKIE.'_policy']), true);

            $thirdy = (is_null($data['thirdy']) || empty($data['thirdy']))
                ? ''
                : 'checked';

            $profile = (!isset($data['profile']) || is_null($data['profile']) || empty($data['profile']))
                ? ''
                : 'checked';
        }

        echo '<div class="bg-white rounded-xl shadow-2xl xmodal text-gray-700 md:px-8 px-4 py-10">
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
                    </div>';

        if ($conf['third_party_cookies'])
        {
            echo '  <div>
                        <label for="thirdy">
                            <input type="checkbox" name="thirdy" x-model="thirdy" '.$thirdy.' >
                            '._X4COOKIE_THIRDY.'
                        </label>
                        <p class="text-sm">'.nl2br(_X4COOKIE_THIRDY_MSG).'</p>
                    </div>';
        }

        if ($conf['profiling_cookies'])
        {
            echo '  <div>
                        <label for="thirdy">
                            <input type="checkbox" name="profile" x-model="profile" '.$profile.' >
                            '._X4COOKIE_PROFILE.'
                        </label>
                        <p class="text-sm">'.nl2br(_X4COOKIE_PROFILE_MSG).'</p>
                    </div>';
        }

        echo '</div>
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
	 * @param   mixed	$a
	 * @param   mixed	$b
	 * @return  mixed
	 */
	private function test($a, $b)
	{
		// TO DO
		/*
		Here you can execute an action or you can get data to display
		*/
	}

}
