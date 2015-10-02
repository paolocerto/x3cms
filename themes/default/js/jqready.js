/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2012 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */
 
// external links
$(function() {
    $('a[href^=http]').not('.no_target').click( function() {
        window.open(this.href);
        return false;
    });
});

// preload images
$.fn.preload = function() {
    this.each(function(){
        $('<img/>')[0].src = this;
    });
}

var c = 0;

$(document).ready(function() {
	
	// leftmenu init
	setTimeout(function(){
		leftmenu();
	}, 100);
	
		
	$('#antispam').remove();
    $('a[href^=http]').not('.no_target').addClass('blank');
	$('<img/>').preload();
	
	recaptcha();
	
	if ($('#menu_top').length) {
		enable_submenu('menu_top');
	}
	
});

// reload captcha
var recaptcha = function() {
		$('#reload_captcha').click(function() {
			c = c + 1;
			var src = $('#reload_captcha').attr('href');
			d = new Date();
			$('#captcha_img').attr('src', src + '/' + c + '?' + d.getTime());
			return false;
		});
	},
	enable_submenu = function(menu_id) {
		$('#'+ menu_id + ' li').each(function() {
			if($(this).has('ul').length) {
				$(this).addClass('menu');
				
				//$(this).trigger('mouseleave');
				$(this).removeClass('on');
			}
		});
	};

function leftmenu() {
	
	
	$('.left-left-menu').css({
		'height': $('#menu_left').outerHeight()
	});
	
	$('#topic').css({
		'margin-top': -$('#menu_left').outerHeight()
	});

}
