<?php defined('ROOT') or die('No direct script access.');
/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/gpl-3.0.html
 * @package		X3CMS
 */
?>

<div id="login" x-data="login_box()" x-init="load_captcha()">
	<h2 class="pt-4"><?php echo _LOGIN ?></h2>

<?php
// message
if (isset($msg))
{
	echo '<div id="msg"><p class="failed px-4 py-4 rounded">'.$msg.'</p></div>';
}
echo $form;
?>
    <div class="text-center text-sm">
        <a href="<?php echo BASE_URL ?>login/recovery" title="<?php echo _RESET_PWD_TITLE ?>"><?php echo _RESET_PWD ?></a>
    </div>
</div>

<script>
document.getElementById('username').focus();

function login_box() {
	return {
        counter: 0,
        captcha: "",
        load_captcha() {
            this.counter++;
            var src = root + "/admin/captcha/55/65/81";
            this.captcha = '<img id="captcha_img" class="mx-auto" src="' + src + '/' + this.counter + '" alt="captcha" />'
        },
        logging() {
            const e = document.getElementById("antispam");
            e.remove();
            $refs.xlogin.submit()
        }
	}
}
</script>
