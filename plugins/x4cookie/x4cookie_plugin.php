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
	 */
	public function __construct(X4Site_model $site)
	{
		parent::__construct($site);
		$this->dict = new X4Dict_model(X4Route_core::$area, X4Route_core::$lang);
	}

	/**
	 * Default method
	 */
	public function get_module(stdClass $page, array $args, string $param = '') : mixed
	{
		// if param can be exploded
		$p = explode('|', $param);

		switch ($p[0])
		{
        case 'alert':
            // if already set we offer link to change previous choices
			$alert = !isset($_COOKIE[COOKIE.'_policy']);

            return $this->cookie_setup($page, $alert);
			break;

		default:
			return '';
			break;
		}
	}

    /**
	 * Cookie config
     * here we offer the option to change previous choices
	 */
	private function cookie_setup(stdClass $page, bool $alert) : string
	{
        // get conf
		$conf = $this->site->get_module_param('x4cookie', $page->id_area);

        $init = '';
        if ($alert)
        {
            // we don't open the modal on the info page
            if (!empty($conf['url']) && $conf['url'] == $page->url)
            {
                return '';
            }

            // default values
            $thirdy = 'true';
            $profile = 'false';

            $init = '
            init() {
                let event = new CustomEvent("popup", {detail: root+"plugin/x4cookie/default"});
                window.dispatchEvent(event);
            },';
        }
        else
        {
            if (!isset($conf['edit_settings']) || !$conf['edit_settings'])
            {
                return '';
            }

            $cookie = json_decode(base64_decode($_COOKIE[COOKIE.'_policy']), true);

            $thirdy = $cookie['thirdy']
                ? 'true'
                : 'false';

            if (!isset($_SESSION['thirdy']) || $_SESSION['thirdy'] !== $thirdy)
            {
                $_SESSION['thirdy'] = $thirdy;
                header("Refresh:0");
            }

            $profile = isset($cookie['profile']) && $cookie['profile']
                ? 'true'
                : 'false';
        }

        $this->dict->get_wordarray(array('x4cookie'));

        // add var for profilation if you need it
		$xdata = '{
            tech: true,
            thirdy: '.$thirdy.',
            profile: '.$profile.',
            '.$init.'
            settings() {
                let event = new CustomEvent("popup", {detail: root+"plugin/x4cookie/settings"});
                window.dispatchEvent(event);
            }
        }';

		return '
<dix
    id="x4cookie_cfg"
    class="fixed text-white z-10 left-0 pl-8 bottom-0 pb-8"

    x-data=\''.$xdata.'\'
>
    <a @click="settings()" class="link" title="'._X4COOKIE_CONFIG.'"><i class="fa-solid fa-2xl fa-cookie-bite"></i></a>
</div>
<script>
function cookier() {
    return {
        tech: true,
        thirdy: '.$thirdy.',
        profile: '.$profile.',
        settings() {
            let event = new CustomEvent("popup", {detail: root+"plugin/x4cookie/settings"});
            window.dispatchEvent(event);
        },
        setup() {
            let str = JSON.stringify({tech: true, thirdy: this.thirdy});
            document.cookie="'.COOKIE.'_policy="+btoa(str)+";expires='.gmdate(DATE_COOKIE, strtotime('next year')).';path=/;SameSite=Strict;Secure";
            location.reload();
        }
    }
}
</script>';
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
	public function plugin(string $control, mixed $a, mixed $b, mixed $c, mixed $d) : void
	{
		switch ($control)
		{
		case 'default':
            // the content of the dialog for the first time
			$this->default();
			break;

		case 'settings':
			$this->settings();
			break;

		default:
			echo '';
			break;
		}
	}

    /**
	 * default
     * is the content of the dialog for the first time
	 */
	private function default() : void
	{
        $this->dict->get_wordarray(array('x4cookie'));

        // get conf
		$conf = $this->site->get_module_param('x4cookie', $this->site->area->id);

        $more = '';
        if (!empty($conf['url']))
        {
            $more = '<button type="button" class="btn gray" onclick="window.location.href=\''.BASE_URL.$conf['url'].'\'">'._X4COOKIE_MORE_INFO.'</button>';
        }

        // edit
        $edit = '';
        if (isset($conf['edit_settings']) && $conf['edit_settings'])
        {
            $edit = _X4COOKIE_EDIT;
        }

        $view = new X4View_core('default/modal');
        $view->title = _X4COOKIE_SETUP;
        $view->close = '';

        $view->content = new X4View_core('x4cookie_default', 'x4cookie');
        $view->content->edit = $edit;
        $view->content->more = $more;

        echo $view->render(false);

    }

    /**
	 * Settings
     * is the content of the dialog to change previous choices
	 */
	private function settings() : void
	{
        $this->dict->get_wordarray(array('x4cookie'));

        // get conf
		$conf = $this->site->get_module_param('x4cookie', $this->site->area->id);

        $more = '';
        if (!empty($conf['url']))
        {
            $more = '<button type="button" class="btn gray" onclick="window.location.href=\''.$this->site->data->domain.BASE_URL.$conf['url'].'\'">'._X4COOKIE_MORE_INFO.'</button>';
        }

        $view = new X4View_core('default/modal');
        $view->title = _X4COOKIE_SETUP;
        if (isset($_COOKIE[COOKIE.'_policy']))
        {
            $view->title = _X4COOKIE_CONFIG;
            $data = json_decode(base64_decode($_COOKIE[COOKIE.'_policy']), true);

            $thirdy = (is_null($data['thirdy']) || empty($data['thirdy']))
                ? ''
                : 'checked';

            $profile = (!isset($data['profile']) || is_null($data['profile']) || empty($data['profile']))
                ? ''
                : 'checked';
        }
        else
        {
            // can't close the modal
            $view->close = '';
        }



        $view->content = new X4View_core('x4cookie_settings', 'x4cookie');
        $view->content->conf = $conf;
        $view->content->thirdy = $thirdy;
        $view->content->profile = $profile;
        $view->content->more = $more;

        echo $view->render(false);
	}

	/**
	 * SAMPLE method
	 */
	private function test($a, $b)
	{
		// TO DO
		/*
		Here you can execute an action or you can get data to display
		*/
	}

}
