/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2015 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */


window.addEvent('domready', function() 
{
	spammize();
	
	if ($chk($('sidebar'))) {
		alert('pippo');
		window.location = window.location;
	}
	
	if ($chk($('captcha_img'))) {
		$('reload_captcha').addEvent('click', function(e){
			reload_captcha();
			return false;
		});
	}
});

var c = 0,
	spammize = function() {
		if ($chk($('antispam'))) {
			$('antispam').dispose();
		}
	},
	sweepize = function() {
		$$('.sweep').each(function(input){
			input.addEvent('click', function(e){
				e.stop();
				input.set('value', '');
			});
		});
	},
	submitform = function(id_form, id_container, link) {
		var APop = new Fx.Slide(id_container);
		var req = new Request.HTML({
			method: 'post',
			url: $(id_form).get('action'),
			data: $(id_form),
			update: $(id_container),
			//onRequest: function() {tinyMCE.triggerSave(true,true);},
			onComplete: function() {
				APop.hide().show();
				if (id_container == 'main') enable_pop(id_container, link)
			}
		}).send();
	},
	reload_captcha = function() {
		c = c + 1;
		var src = $('reload_captcha').get('href');
		$('captcha_img').dispose();
		
		var newcha = new Element('img', {
			'id': 'captcha_img',
			'src': src + '/' + c,
			'alt': 'captcha'
		});
		newcha.inject('cha', 'top');
	};

