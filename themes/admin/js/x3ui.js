/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) CBlu.net di Paolo Certo
 * @license		https://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

// EVENTS
window.addEventListener('popstate', function(e){
    let url = e.state;
    if (url != null) {
        X3.content('topic', url, 'back');
    }
});

// X3 object
var X3 = (X3 || {});
var SM;

X3.append = function(hash){
	Object.append(X3, hash);
}.bind(X3);

Browser.webkit = (Browser.safari || Browser.chrome);

X3.append({
	version: '3.0.0',
	initialized: false,
	spinner: false,
	form: '',
	input: null,
	inputFiles: null,

    /**
     * Initialize
     * here you can check and notice unsupported browsers
     */
	initialize: function(){
		X3.initialized = true;
	},

    /**
     * Fix and clean uncompleted URLs
     * @param string url
     * @returns string
     */
	cleanUrl: function(url){
		if (url !== null && url.substr(0,7) != 'http://' && url.substr(0,8) != 'https://'){
			url = domain + root + url.replace(new RegExp('^('+root+')'), '');
		}
		return url;
	},

	/**
	 * Handle url with query_string
	 *
	 * @param string url
	 * @param string div
	 * @param string reload
	 */
	finalUrl: function(url, div, reload)
	{
		let qs = '';
		if (div != null && reload != null) {
			qs = 'div=' + div + '&url=' + encodeURIComponent(reload);
		}
		if(qs.length) {
			url += (url.includes('?'))
                ? '&' + qs
                : '?' + qs;
		}
		return X3.cleanUrl(url);
	},

    /**
     * Fill a container in the UI
     */
	content: function(container, url, title){
        new Request.HTML({
			method: 'get',
			url: X3.cleanUrl(url),
			update: $(container),
			onRequest: function() {
				X3.setSpinner(true);
			},
			onComplete: function() {
				if (title !== null){
					$('page-title').set('html', title);
					buttonize('page-title', null, 'topic');
				}
				if (container == 'topic' && title != 'back') {
				    history.pushState(url, title, url);
				}
				X3.setSpinner(false);
			}
		}).send();
	},

    /**
     * Set the spinner
     *
     * @param bool spinner status
     */
	setSpinner: function(status){
		if (X3.spinner != status){
            if (status){
                $('spinner').addClass('fa-spin');
            } else {
                $('spinner').removeClass('fa-spin');
            }
			X3.spinner = status;
		}
	},

	/* FORM */

	/**
	 * Updates multiple elements
	 *
	 * @param	array	Array of elements to update. Array('element_id' => 'url_to_call')
	 */
	updateElements: function (elements){
		elements.each(function(el) {
			if (el.element == 'modal') {
				X3.modal('', el.title, el.url);
			} else {
				X3.content(el.element, el.url, el.title);
			}
		});
	},

	/**
	 * Returns the form object
	 *
	 * @param	string		URL to send the form data. With or without the base URL prefix. Will be cleaned.
	 * @param	mixed		Form data
	 * @param	string		ID of the DOM container
	 * @param	string		URL to reload the container
	 */
	getFormObject: function(url, data, div, reload){
		data = data ?? '';
		return new Request.JSON({
			url: X3.finalUrl(url, div, reload),
			method: data.get('method'),
			loadMethod: 'xhr',
			data: data,
			onRequest: function() {
				X3.setSpinner(true);
				if ($('formloader') != null) {
					X3.unscroll();
					$('formloader').removeClass('hidden');
					$('editor').hide();
				}
			},
			onFailure: function(xhr) {
				X3.setSpinner(false);
				if ($('formloader') != null) {
					$('formloader').addClass('hidden');
					$('editor').show();
				}
				// Error notification
				X3.notification('error', xhr.responseJSON, div);
			},
			onSuccess: function(responseJSON, responseText){
				X3.setSpinner(false);
				if ($('formloader') != null) {
					$('formloader').addClass('hidden');
					$('editor').show();
				}

				// Update the elements transmitted through JSON
				if (responseJSON && responseJSON.update){
					// Updates all the elements in the update array
					X3.updateElements(responseJSON.update);
				}

				// JS Callback
				if (responseJSON && responseJSON.callback){
					X3.execCallbacks(responseJSON.callback);
				}

				// JS Command
				if (responseJSON && responseJSON.command) {
					X3.execCommands(responseJSON.command);
				}

				// User notification
				if (responseJSON && responseJSON.message_type){
					if (responseJSON.message_type == 'error'){
						X3.notification('error', responseJSON, div);
					} else {
						X3.success(responseJSON.message_type, responseJSON.message);
					}
				}
			}
		});
	},

	/**
	 * Returns the form object
	 *
	 * @param	string		URL to send the form data. With or without the base URL prefix. Will be cleaned.
	 * @param	mixed		Form data
     * @param   string      Div container
     * @param   string      URL for the reload
	 */
	getFormUploadObject: function(url, data, div, reload){
		data = data ?? '';
        // handle query string
		var qs = '';
        var tmp = url.split('?');
        if (tmp[1] != null) { qs = '?'+tmp[1]; }
		if (div != null && reload != null) {
            if (qs === '') {
                qs = '?div=' + div + '&url=' + X3.cleanUrl(reload);
            } else {
                qs += '&div=' + div + '&url=' + X3.cleanUrl(reload);
            }
        }

		return {
			url: X3.cleanUrl(url) + qs,
			onRequest: function(){
				X3.setSpinner(true);
				if($('progressZone')){
					$('progressZone').setStyles({display: 'block', width: 0});
				}
			},
			onFailure: function(xhr){
				X3.setSpinner(false);
				if($('progressZone')){
					$('div.progress').setStyle('display', 'none');
				}
				// Error notification
				X3.notification('error', JSON.decode(xhr.responseText, true));
			},
			onProgress: function(event){
				var loaded = event.loaded, total = event.total;
				if($('progressZone')){
					$('progressZone').setStyle('width', parseInt(loaded / total * 100, 10).limit(0, 100) + '%');
				}
			},
			onSuccess: function(responseText, responseXML){
				X3.setSpinner(false);
				if($('progressZone')){
					$('progressZone').setStyle('width', '100%');
				}
				//X3.reset_files();
				let responseJSON = JSON.decode(responseText, true);
				// Update the elements transmitted through JSON
                // removed from if  && qs.length > 0
				if (responseJSON) {
					if (responseJSON.message_type == 'success') {
						X3.reset_files();
					}
					// Updates all the elements in the update array
					X3.updateElements(responseJSON.update);
				}

				// JS Callback
				if (responseJSON && responseJSON.callback) {
					X3.execCallbacks(responseJSON.callback);
				}

				// JS Command
				if (responseJSON && responseJSON.command) {
					X3.execCommands(responseJSON.command);
				}

				// User notification
				if (responseJSON && responseJSON.message_type) {
					if (responseJSON.message_type == 'error') {
						X3.notification('error', responseJSON);
					} else {
						X3.success(responseJSON.message_type, responseJSON.message);
					}
				}
			}
		};
	},

	/**
	 * Returns the object
	 *
	 * @param	string		URL to call. With or without the base URL prefix. Will be cleaned.
	 * @param	string		div to reload
	 * @param	string		url to reload
	 */
	getObject: function(url, div, reload){
		return {
			url: X3.finalUrl(url, div, reload),
			method: 'get',
			loadMethod: 'xhr',
			data: '',
			onRequest: function() {
				X3.setSpinner(true);
			},
			onFailure: function(xhr) {
				X3.setSpinner(false);

				// Error notification
				X3.notification('error', xhr.responseJSON);
			},
			onSuccess: function(responseJSON, responseText) {
				X3.setSpinner(false);

				// Update the elements transmitted through JSON
				if (responseJSON && responseJSON.update) {
					// Updates all the elements in the update array
					X3.updateElements(responseJSON.update);
				}

				// JS Callback
				if (responseJSON && responseJSON.callback) {
					X3.execCallbacks(responseJSON.callback);
				}

				// JS Command
				if (responseJSON && responseJSON.command) {
					X3.execCommands(responseJSON.command);
				}

				// User notification
				if (responseJSON && responseJSON.message_type && div != 'modal') {
					if (responseJSON.message_type == 'error') {
						X3.notification('error', responseJSON);
					} else {
						X3.success(responseJSON.message_type, responseJSON.message);
					}
				}
			}
		};
	},

	/* UPLOAD MULTIPLE */

	droppize: function(id_form, id_file_input, drop_msg){
		if ('FormData' in window) {
			X3.form = $(id_form);
			X3.input = $(id_file_input);
			var drop = new Element('div.droppable', {
				id: 'dropZone',
				text: drop_msg
			}).inject(X3.input, 'after');

			new Element('ul.uploadList', {
				id: 'uploadZone'
			}).inject(drop, 'after');
            /*
			var progress = new Element('div.progress', {
				id: 'progressZone'
			}).setStyle('display', 'none').inject(list, 'after');
            */
			X3.inputFiles = new Form.MultipleFileInput(id_file_input, 'uploadZone', 'dropZone', {
				onDragenter: drop.addClass.pass('hover', drop),
				onDragleave: drop.removeClass.pass('hover', drop),
				onDrop: function(){
					drop.removeClass.pass('hover', drop);
				}
			});
		} else {
			alert('Browser not supported!');
		}
	},

    uploadize_multiple: function(id_form, array_id_files_input, div, reload){
		if ('FormData' in window && X3.inputFiles !=  null) {
			X3.form = $(id_form);
			let req_options = X3.getFormUploadObject(X3.form.get('action'), id_form, div, reload);
			let uploadReq = new Request.File(req_options);
			array_id_files_input.each(function(id_input){
				if (X3.input[id_input] != null){
					let inputname = X3.input[id_input].get('name');
					X3.inputFiles[inputname].getFiles().each(function(file){
						uploadReq.append(inputname, file);
					});
				}
			});

			// post
			let input = $(id_form).getElements('select,input,textarea');
			input.each(function(item){
				let i = item.get('name');
				if (X3.input[i] == null){
					if (item.get('tag') == 'input' && (item.get('type') == 'checkbox' || item.get('type') == 'radio')){
						if (item.get('checked')) {
							uploadReq.append(i, item.value);
						}
					} else {
						if (item.get('tag') == 'select' && item.get('multiple') == true) {
							// return an array in a string
							uploadReq.append(i, item.getSelected().get("value").join('|'));
						} else {
							uploadReq.append(i, item.value);
						}
					}
				}
			});
			uploadReq.send();
		} else {
			alert('Browser not supported!');
		}
	},

	/* UPLOAD SINGLE FILE */

	single_upload: function(id_form, id_file_input){
		if ('FormData' in window) {
			X3.form = $(id_form);
            X3.input = X3.input ?? [];
			X3.input[id_file_input] = $(id_file_input);
            X3.inputFiles = X3.inputFiles ?? [];
			X3.inputFiles[X3.input[id_file_input].get('name')] = new Form.SingleFileInput(id_file_input);
		} else {
			alert('File: Browser not supported!');
		}
	},

    uploadize: function(id_form, id_file_input, div, reload){
		if ('FormData' in window && X3.inputFiles !=  null) {
            X3.form = X3.form ?? $(id_form);
			let req_options = X3.getFormUploadObject(X3.form.get('action'), id_form, div, reload);
			let uploadReq = new Request.File(req_options);
			let inputname = X3.input[id_file_input].get('name');

			X3.inputFiles.getFiles().each(function(file){
				uploadReq.append(inputname, file);
			});

			// post
			let input = $(id_form).getElements('select,input,textarea');
			input.each(function(item){
				let i = item.get('name');
				if (i != id_file_input){
					if (item.get('tag') == 'input' && (item.get('type') == 'checkbox' || item.get('type') == 'radio')){
						if (item.get('checked'))
							uploadReq.append(i, item.value);
					} else {
						if (item.get('tag') == 'select' && item.get('multiple') == true) {
							// return an array in a string
							uploadReq.append(i, item.getSelected().get("value").join('|'));
						} else {
							uploadReq.append(i, item.value);
						}
					}
				}
			});
			uploadReq.send();
		} else {
			alert('Upload: Browser not supported!');
		}
	},

	reset_files: function(){
		X3.inputFiles = null;
	},

	/* MODAL WINDOWS */

	modal: function(type, title, url){
		X3.setSpinner(true);
		SM = new SimpleModal();
		SM.show({
			"model":"modal-ajax",
			"title": title,
			"param":{
				"url": X3.cleanUrl(url),
				"onRequestComplete": function(){
				    var scroll_fx = new Fx.Scroll(window);
				    scroll_fx.toTop();
					X3.setSpinner(false);
					$('close-modal').addEvent('click', function(e){
						e.stop();
						SM.hide();
						X3.unscroll();
					});
				}
			}
		});
	},

	/**
	 * X3 notification
	 *
	 * @param	string 	type of notification. Can be: error, notice, success
	 * @param	string	Notification message
	 * @param 	string	ID of the container of the error message
	 */
	notification: function(type, msg, div){
	    $("xscroll_up").fireEvent("click");
	    if (div != undefined) {
	        let container = document.getElementById(div).getElementsByClassName("msg-container")[0];
	        container.set('html', '<p class="' + type + '">'+msg.message+'</p>');
			container.addClass('msg');
			container.setStyle('margin', '40px -3px 40px -3px');
	    } else if ($$('.msg-container').length) {
	        let  mc = $$('.msg-container')[0];
		    mc.set('html', '<p class="' + type + '">'+msg.message+'</p>');
			mc.addClass('msg');
			mc.setStyle('margin', '40px -3px 40px -3px');
		} else {
			X3.unscroll();
			SM = new SimpleModal();
			var close = (msg.hasOwnProperty('message_close'))
				? msg.message_close
				: '';
			SM.show({
				model: type,
				contents: '<div class="' + type + ' zerom">' + close + '<p>'+msg.message+'</p></div>'
			});

			$('close-modal').addEvent('click', function(e){
				e.stop();
				SM.hide();
				X3.unscroll();
			});
		}
	},

	success: function(type, msg){
		if (msg != null) {
			X3.unscroll();
			SM = new SimpleModal();
			SM.show({
				model: type,
				contents: '<p class="' + type + '">'+msg+'</p>'
			});
		}
	},

	unscroll: function(){
		if ($('scrolled') != null) {
			$('scrolled').fireEvent('mouseleave');
		}
		$$('.scrollbar').destroy();
		$$('.datepicker_dashboard').destroy();
		$$('.autocompleter-choices').destroy();
		$$('.colorSphere').destroy();
	},

	/**
	 * Execute the callbacks
	 *
	 * @param	Mixed.	Function name or array of functions.
	 */
	execCallbacks: function(args){
		let callbacks = [];
		// More than one callback
		if (typeOf(args) == 'array') {
			callbacks = args;
		} else {
			callbacks.push(args);
		}
		callbacks.each(function(item, idx){
			let cb = (item.fn).split(".");
			let func = null;
			let obj = null;

			if (cb.length > 1) {
				obj = window[cb[0]];
				func = obj[cb[1]];
			} else {
				func = window[cb];
			}
			func.delay(100, obj, item.args);
		});
	},

	/**
	 * Execute commands
	 *
	 * @param	Mixed.	Action or array of actions
	 */
	execCommands: function(args){
		let commands = [];
		// More than one command
		if (typeOf(args) == 'array') {
			commands = args;
		} else {
			commands.push(args);
		}
		let tmpFunc = new Function(commands.join(';'));
		tmpFunc();
	}

});

var XUI = (XUI || {});

// open desktop
XUI.desktop = function(start_page, start_title){
	X3.content('menu','home/menu', null);
	X3.content('topic',start_page, start_title);
	X3.content('filters','home/filter', null);
};

// link to scroll Up and scroll Down
window.addEvent('load', function() {
    var scroll_fx = new Fx.Scroll(window);
    var xto_top = new Element('span#xscroll_up.scroll.scroll-to-top').inject(document.body).addEvent('click', function() { scroll_fx.toTop(); });
    var xto_bottom = new Element('span#xscroll_down.scroll.scroll-to-bottom').inject(document.body).addEvent('click', function() { scroll_fx.toBottom(); });

    var checkScroll = function() {
	    var body_scroll = document.body.getScroll();
        var xtop = (body_scroll.y > 100) ? 'block' : 'none';
        var xbottom = (body_scroll.y + window.getCoordinates().height) < window.getScrollSize().y ? 'block' : 'none';
        xto_top.setStyle('display', xtop);
	    xto_bottom.setStyle('display', xbottom);
	};

    checkScroll();
    window.addEvent('scroll', function() { checkScroll(); });
});

// Initialize when the DOM is ready
window.addEvent('domready',function(){
	XUI.desktop(start_page, start_title);
    linking();
});

var windowHeight = function (){
		return window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
	},
	windowWidth = function (){
		return window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	},
	autoSize = function(container, element, what, minus, max, min) {
		let c = $(container).getSize(), s = 0;
		if (what == 'width') {
			s = c.x;
		} else {
			s = c.y;
		}
		if (max != null && (s - minus) > max) {
			s = max + minus;
		}
		if (min != null && (s - minus) < min) {
			s = min + minus;
		}
		$(element).setStyle(what, s - minus);
	},
	strstr = function(haystack, needle, bool){
		let pos = 0;
		haystack += '';
		pos = haystack.indexOf(needle);
		if(pos == -1){
			return false;
		} else {
			if(bool){
				return haystack.substr(0, pos);
			} else {
				return haystack.slice(pos);
			}
		}
	},
	linking = function(selector, container){
		container = container || 'topic';
		if (selector != null){
			$$(selector).each(function(el) {
				//check each href for case-insensitive file extensions
				var str = el.get('href');
				if (str != null) {
					el.addEvent('click', function(e){
						e.stop();
						X3.content(container,this.get('href'), null);
					});
				}
			});
		} else {
			var override = 1,
				target = '_blank',
				no_class = 'no_target',
				extensions = ['doc','pdf','xls','jpg','gif','png', 'pps', 'ppt', 'zip', 'swf', 'gif'];
			$$('a').each(function(el) {
				//check each href for case-insensitive file extensions
				var str = el.get('href');
				if (str != null) {
					var ext = str.substring(str.lastIndexOf('.') + 1,str.length);
					if((strstr(str, 'http') || el.hasClass(target) || extensions.contains(ext.toLowerCase())) && ((override) && !el.hasClass(no_class + '')))
					{
						el.setProperty('target',target ? target : '_blank');
					} else if (!el.hasClass('no_link')) {
						el.addEvent('click', function(e){
							e.stop();
							X3.content(container,this.get('href'), this.get('title'));
						});
					}
				}
			});
		}
	},
	buttonize = function(container, type, div, referer, refresh) {
		if (container != null) {
			let w = '', qs = '', rqs = '';
			if (type == null) {
				w = '#' + container + ' a';
			} else {
				w = '#' + container + ' a.' + type;
			}

			$$(w).addEvent('click', function(e){
				e.stop();
                // handle query string
                var tmp = this.get('href').split('?');
                if (tmp[1] != null) { qs = '?'+tmp[1]; }
				if (referer != null && referer.length > 0) {
                    if (qs === '') {
                        qs = '?ref='+referer;
                    } else {
                        qs = qs + '&ref='+referer;
                    }
                }
				if (refresh != null){
					rqs = (qs === '')
						? '?refresh='+refresh
						: '&refresh='+refresh;
				}

				if (container == 'simple-modal') {
					SM.hide();
					X3.unscroll();
				}

				if (div == 'modal') {
					X3.modal('', this.get('title'), tmp[0]+qs+rqs);
				} else {
					X3.content(div, tmp[0]+qs+rqs, null);
				}
				return false;
			});
		}
	},
	buttonize_relay = function(container, type, div, referer, refresh) {
		if (container != null) {
			var qs = '', rqs = '';

			$(container).addEvent('click:relay(a.'+type+')', function(e){
				e.stop();
                // handle query string
				var tmp = this.get('href').split('?');
                if (tmp[1] != null) { qs = '?'+tmp[1]; }
				if (referer != null && referer.length > 0) {
                    if (qs === '') {
                        qs = '?ref='+referer;
                    } else {
                        qs = qs + '&ref='+referer;
                    }
                }
				if (refresh != null){
					rqs = (qs === '')
						? '?refresh='+refresh
						: '&refresh='+refresh;
				}

				if (container == 'simple-modal') {
					SM.hide();
					X3.unscroll();
				}

				if (div == 'modal') {
					X3.modal('', this.get('title'), tmp[0]+qs+rqs);
				} else {
					X3.content(div, tmp[0]+qs+rqs, null);
				}
				return false;
			});
		}
	},
	buttonize_relay_rel = function(container, type, rel) {
		if (container != null) {
			$(container).addEvent('click:relay(a.'+type+')', function(e){
				e.stop();
				X3.setSpinner(true);
				X3.content(this.get('rel'), this.get('href'), null);
				X3.setSpinner(false);
				return false;
			});
		}
	},
	actionize = function(container, type, div, reload, callback) {
		if (container != null) {
			$$('#' + container + ' a.' + type).addEvent('click', function(e) {
				e.stop();
				let options = X3.getObject(this.get('href'), div, reload);
				new Request.JSON(options).send();

				if (callback != null){
					f = window[callback];
					f(this);
				}
			});
		}
	},
	actionize_relay = function(container, type, div, reload, callback) {
		if (container != null) {
			$(container).addEvent('click:relay(a.'+type+')', function(e) {
				e.stop();
				let options = X3.getObject(this.get('href'), div, reload);
				new Request.JSON(options).send();

				if (callback != null){
					f = window[callback];
					f(this);
				}
			});
		}
	},
	tabberize = function(tabber, type, div) {
		if ($(tabber) != null) {
			$$('#'+tabber + ' a.' + type).each(function(el) {
				el.addEvent('click', function(e){
					e.stop();
					X3.content(div, this.get('href'), null);
					$$('#'+ tabber + ' .tabs ul li.on').removeClass('on');
					el.getParent('li').addClass('on');
                    return false;
				});
			});
		}
	},
    tabberize2 = function(tabber, type, div) {
		if ($('tabber') != null) {
			$$('#tabber a.' + type).each(function(el) {
				el.addEvent('click', function(e){
					e.stop();
					$$('#'+ div + ' .tabbox').hide();
					let box = el.get('href').replace('#', '');
					$(box).show();
					$$('#'+ tabber +' .tabs ul li.on').removeClass('on');
					el.getParent('li').addClass('on');
					return false;
				});
			});
		}
	},
	remotize = function(container, type, div, remote, suffix) {
		if (container != null) {
			$$('#' + container + ' a.' + type).addEvent('click', function(e){
				e.stop();
				X3.content(div, remote+this.get('href')+suffix, null);
			});
		}
	},
    xsetOnChange = function(changer, id_element, url) {
		let el = document.getElementById(changer);
		el.addEvent('change', function(e) {
            var v = '';
		    let tag = this.get('tag');
		    if (tag == 'select') {
		        v = this.getSelected()[0].get("value");
		    } else {
		        v = this.get('value');
		    }
            if (v != '') {
                new Request({
                    method: 'get',
                    url: url + v,
                    onSuccess: function(response){
                        $(id_element).set("value", response);
                    }
                }).send();
            } else {
                $(id_element).set("value", "");
            }
		});
	},
	xsetValue = function(id_element, url, funct) {
		new Request({
			method: 'get',
			url: url,
			onSuccess: function(response){
				$(id_element).set("value", response).fireEvent("change");
				if (funct != null) {
					funct;
				}
			}
		}).send();
	},
	getValues = function(url, inputs, functs) {
		new Request.JSON({
			url: url,
			async: false,
			onComplete: function(jsonObj) {
				functs.each(function(el, i){
					eval(el+'(jsonObj["'+inputs[i]+'"])');
				});
			}
		}).send();
	},
    waitForm = function(id_button, status){
        if (status) {
            $(id_button).set("disabled", true).addClass("wait");
        } else {
            $(id_button).set("disabled", false).removeClass("wait");
        }
    },
	setForm = function(id_form, tiny_mce, div, reload, id_button) {
		if (id_button != null)
        {
            waitForm(id_button, true);
        }

        let tmce = [];
        if (tiny_mce != null) {
			tmce = tiny_mce.split('|');
			Array.each(tmce, function(f, i) {
				removeEditor(f);
			});
		}
		// Send the form
		let req = X3.getFormObject($(id_form).get('action'), $(id_form), div, reload);
		req.send();
		if (tiny_mce != null) {
			Array.each(tmce, function(f, i) {
				addEditor(f);
			});
		}
        /*
        if (id_button != null)
        {
            waitForm(id_button, false);
        }
        */
	},
    submitForm = function(id_form) {
        $(id_form).submit();
    },
    freeze_submit = function(form, button) {
        $(button).set("disabled", true);
        setTimeout(function(){if ($(button) != null) {$(button).set("disabled", false);}}, 3000);
        setForm(form);
    },
	setUploadForm = function(id_form, input_names, div, reload, tiny_mce) {
        let tmce = [];
		if (tiny_mce != null) {
			tmce = tiny_mce.split('|');
			Array.each(tmce, function(f, i) {
				removeEditor(f);
			});
		}
		// Send the form
		let inputs = input_names.split('|');
		X3.uploadize_multiple(id_form, inputs, div, reload);

		if (tiny_mce != null) {
			Array.each(tmce, function(f, i) {
				addEditor(f);
			});
		}
	},
	zebraTable = function(table_class) {
		//add table shading
		$$('table.' + table_class + ' tr').each(function(el,i) {
			//do regular shading
			let _class = i % 2 ? 'even' : 'odd';
            el.addClass(_class);
			//do mouseover
			el.addEvent('mouseenter',function() {
				if(!el.hasClass('highlight')) { el.addClass('mo').removeClass(_class); }
			});
			//do mouseout
			el.addEvent('mouseleave',function() {
				if(!el.hasClass('highlight')) { el.removeClass('mo').addClass(_class); }
			});
			//do click
			el.addEvent('click',function() {
				if(el.hasClass('highlight')) {
					//click off
					el.removeClass('highlight').addClass(_class);
				} else {
					//click on
					el.removeClass(_class).removeClass('mo').addClass('highlight');
				}
			});
		});
	},
	zebraUl = function(ul_class) {
		$$('ul.' + ul_class + ' li').each(function(el,i) {
			//do regular shading
			let _class = i % 2 ? 'even' : 'odd';
            el.addClass(_class);
			//do mouseover
			el.addEvent('mouseenter',function() {
				if(!el.hasClass('highlight')) { el.addClass('mo').removeClass(_class); }
			});
			//do mouseout
			el.addEvent('mouseleave',function() {
				if(!el.hasClass('highlight')) { el.removeClass('mo').addClass(_class); }
			});
			//do click
			el.addEvent('click',function() {
				if(el.hasClass('highlight')) {
					//click off
					el.removeClass('highlight').addClass(_class);
				} else {
					//click on
					el.removeClass(_class).removeClass('mo').addClass('highlight');
				}
			});
		});
	},
	sweepize = function() {
		$$('.sweep').each(function(input) {
			input.addEvent('click', function(e) {
				e.stop();
				input.set('value', '');
			});
		});
	},
	pickerize = function(what, opts) {
		// set a different locale
		if (lang != 'en-EN') Locale.use(lang);
		switch (what){
            case 'date':
                // date only
				if (opts == null) {
					opts = {
						pickerClass: 'datepicker_dashboard',
						timePicker: false,
						format: '%Y-%m-%d'
					}
				}
				new Picker.Date($$('input.date_toggled'), opts);
				$$('input.date_toggled').addEvent('dblclick',function(e) {
					this.set("value", "");
				});
                break;
            case 'datetime':
                // date time
                if (opts == null) {
                    opts = {
                        pickerClass: 'datepicker_dashboard',
                        timePicker: true,
                        timeWheelStep: 10,
                        format: '%Y-%m-%d %H:%M'
                    }
                }
                new Picker.Date($$('input.datetime_toggled'), opts);
                $$('input.datetime_toggled').addEvent('dblclick',function(e) {
                    this.set("value", "");
                });
                break;
            case 'time':
				// time only
				if (opts == null) {
					opts = {
						pickerClass: 'datepicker_dashboard',
						pickOnly: 'time',
						timeWheelStep: 5,
						format: '%H:%M'
					}
				}
				new Picker.Date($$('input.time_toggled'), opts);
				$$('input.time_toggled').addEvent('dblclick',function(e) {
					this.set("value", "");
				});
                break;
		}
	},
	pickerange = function(e, opts) {
		// set a different locale
		if (lang != 'en-EN') Locale.use(lang);
		if (opts == null) {
			// note: the script uses a specific date_format
			opts = {
				pickerClass: 'datepicker_dashboard',
				timePicker: false,
				column2: 2,
				footer: false,
				//format: '%Y-%m-%d'
			}
		}
		new Picker.Date.Range($(e), opts);
	},
	get_window_size = function() {
		var s = {'w': 0, 'h': 0};
		if(typeof( window.innerWidth ) == 'number' ) {
			//Non-IE
			s['w'] = window.innerWidth;
			s['h'] = window.innerHeight;
		} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
			//IE 6+ in 'standards compliant mode'
			s['w'] = document.documentElement.clientWidth;
			s['h'] = document.documentElement.clientHeight;
		} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
			//IE 4 compatible
			s['w'] = document.body.clientWidth;
			s['h'] = document.body.clientHeight;
		}
		return s;
	},
	spinnerize = function(sdata, classname) {
		let n = sdata.length;
		$$(classname).each(function(el) {
			//change event
			el.addEvent('change',function() {
				// build url
				let str = '';
				for (let i = 2; i < n; i++) {
					str += '/'+$(sdata[i]).get('value');
				}
				//ajax request object
				new Request.HTML({
					method: 'get',
					url: sdata[1]+str,
					update: $(sdata[0]),
					onRequest: function() {
						X3.setSpinner(true);
					},
					onComplete: function() {
						X3.setSpinner(false);
					}
				}).send();
			});
		});
	},
	blanking = function() {
		$$('a').each(function(a) {
			let href = a.get('href'), ext = '';
			if (href.lastIndexOf('.') > 0) {
				ext = href.substring(href.lastIndexOf('.') + 1, href.length);
			}
			if (ext.length > 1 || strstr(href, 'http') || a.hasClass('blank') || a.hasClass('blank_hide')) {
				a.setProperty('target', '_blank');
				a.addEvent('click',function() {
					window.open(href);
					return false;
				});
			}
		});
	},
	print_r = function(theObj){
		if(theObj.constructor == Array || theObj.constructor == Object){
			document.write("<ul>");
			for(let p in theObj){
				if(theObj[p].constructor == Array|| theObj[p].constructor == Object){
					document.write("<li>["+p+"] => "+typeof(theObj)+"</li>");
					document.write("<ul>");
					print_r(theObj[p]);
					document.write("</ul>");
				} else {
					document.write("<li>["+p+"] => "+theObj[p]+"</li>");
				}
			}
			document.write("</ul>");
		}
	},
	sortize = function(id_form, container, sort_info) {
		let sortInput = $(sort_info),
			list = $(container),
			chk = false,
			s = new Sortables(list, {
				constrain: true,
				clone: true,
				revert: true,
				onSort:function(el,clone) {
					chk = true;
				},
				onComplete: function(el,clone) {
					if (chk){
						sortInput.value = s.serialize();
						var req = X3.getFormObject($(id_form).get('action'), $(id_form));
						req.send();
					}
					chk = false;
				}
			});
	},
	draggize = function(id_form, container, sort_info) {
		let sortInput = $(sort_info),
			droppables = $$('li.dropper'),
			containers = $(container);

		$$('li.dragger').each(function(drag){
			new Drag.Move(drag, {
				droppables: droppables
			});

			drag.addEvent('emptydrop', function(){
				sortInput.value = s.serialize();
				var req = X3.getFormObject($(id_form).get('action'), $(id_form));
				req.send();
				this.setStyle('background-color', '#faec8f');
			});
		});
	},
	autocompletize = function(id_input, url) {
		new Autocompleter.Request.JSON(id_input, url, {
			'postVar': 'input',
			minLength: 3,
			maxChoices: 50,
			autoSubmit: false,
			cache: true,
			delay: 300,
			onSelection: function() {
			    setTimeout(function() {$(id_input).fireEvent('blur');}, 400);
			},
			onRequest: function() {
				$(id_input).setStyle('background', '#ddd');
			},
			onComplete: function() {
				$(id_input).setStyle('background','#fff');
			}
		});
	},
	bulkize = function(selector, field_class, button, style) {
		let bulker = $(selector),
			checkboxes = $$('.' + field_class);

        // button can be a string or an array of strings
        if (button instanceof Array) {
            button.each(function(e) {
                $(e).setStyle('display','none');
            });
        } else {
            $(button).setStyle('display','none');
        }

        if (style == null) style = 'inline';

        if (bulker != null) {
            bulker.addEvent('click', function() {
                checkboxes.each(function(el) {
                    el.checked = bulker.checked;
                });
                checkize(checkboxes, button);
            });

            checkboxes.each(function(e) {
                e.addEvent('change', function(e) {
                    checkize(checkboxes, button, style);
                });
            });

            function checkize(items, button, style) {
                let chk = false, display = 'none';
                items.each(function(e) {
                    if (e.checked) chk = true;
                });
                if (chk) display = style;

				if (button instanceof Array) {
					button.each(function(e) {
						$(e).setStyle('display', display);
					});
				} else {
					$(button).setStyle('display', display);
				}
            }
        }
	},
	saccordion = function(container, toggle_class, elementClass) {
		new Fx.Accordion($(container), toggle_class, elementClass, {
			onActive: function(toggler) { toggler.addClass("active-accordion"); },
			onBackground: function(toggler) { toggler.removeClass("active-accordion");}
		});
	},
	raccordion = function(container, toggleClass, elementClass, options){
		let selector = '#' + container + ' > .';
		makeaccordion(selector, toggleClass, elementClass, options);
	},
	makeaccordion = function(selector, toggleClass, elementClass, options){
		new Accordion(
				$$(selector+toggleClass),
				$$(selector+elementClass),
				options
			).addEvents({
				// The onActive and onComplete events added to the stack here to
				// attempt to address some of the css issues.
				'onActive': function(toggle){
					if(toggle.getParent().getStyle('height') != 0)
						toggle.getParent().setStyle('height', '');
				},
				'onComplete': function(a){
					if ($defined(a)) {
						let height = 0;
						a.getParent().getChildren().each(function(e){
							height = height + e.offsetHeight;
						});
						if(height != a.getParent().offsetHeight && a.getParent().offsetHeight != 0)
							a.getParent().setStyle('height','');
					}
				}
			});
			selector += elementClass + ' > .';
			if($defined($$(selector)[0])) {
				makeaccordion(selector, toggleClass, elementClass, options);
            }
	},
	inliner = function() {
		if ($('.inliner') != null) {
			$$('input.inline:first-child').setStyle('margin-left', '.5em');
		}
	},
	loadize = function(id, container, action) {
	    var el = document.getElementById(id);
		el.addEvent('change', function(e) {
		    tag = this.get('tag');
		    if (tag == 'select') {
		        var v = this.getSelected()[0].get("value");
		    } else {
		        var v = this.get('value');
		    }
		    $(container).load(action + v);
		});
	},
	changer = function(id, id_form, container) {
		let el = document.getElementById(id);
        if (el === null) {
            // try input
            el = $$('input[name="'+id+'"]');
        }
		el.addEvent('change', function(e) {
			X3.setSpinner(true);
            let qs = $(id_form).toQueryString();
            let action = $(id_form).get('action');
            $(container).set('html', '<div class="acenter triple-pad-top"><i class="fas fa-circle-notch fa-5x fa-spin"></i></div>');
			$(container).load(action + '?' + qs_fixer(qs));
			X3.setSpinner(false);
		});
	},
	str_searcher = function(id, id_form, container) {
		let el = document.getElementById(id);
		el.addEvent('keyup:keys(enter)', function(e) {
			X3.setSpinner(true);
			let qs = $(id_form).toQueryString();
			$(container).load($(id_form).get('action') + '?' + qs_fixer(qs));
			X3.setSpinner(false);
		});
	},
    qs_fixer = function(qs) {
        let tmp = qs.split('&');
        let a = {};
        tmp.forEach(function(v) {
            let i = v.split('=');
            if (i[0] in a) {
                if (Array.isArray(a[i[0]])) {
                    a[i[0]].push(i[1]);
                } else {
                    a[i[0]] = [a[i[0]], i[1]];
                }
            } else {
                a[i[0]] = i[1];
            }
        });
        qs = new URLSearchParams(a).toString();
        return qs;
    },
	stepper = function(id, container, stepper, url, where) {
		$(id).addEvent('click', function(e) {
			let c = $(stepper).get('value');
			new Request.HTML({
				method: 'get',
				url: root+url+'/'+c,
				//append: container,
				onRequest: function() {
					X3.setSpinner(true);
				},
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					if (where == 'undefined') {
						where = 'bottom';
					}
					$(container).appendHTML(responseHTML, where);
					X3.setSpinner(false);
				}
			}).send();
		});
	},
	floatize = function(s) {
	    s = s.replace(',', '.');
	    return Number(s.replace(/[^0-9.-]+/g,""));
	},
	equalize = function(items, force){
		let ww = windowWidth();
		let wh = windowHeight();
		let elements = $$(items);
		let h = 0;
	    elements.each(function(e) {
		    let s = e.getSize();
			if (s.y > h) {
				h = s.y;
			}
			//s.setStyle("height", 'auto');
		});
		$$(items).setStyle('height', 'auto');
		if (force !== undefined) {
			$$(items).setStyle('height', h);
		} else if (ww > 768 && h < wh) {
			$$(items).setStyle('min-height', h);
		} else {
			$$(items).setStyle('min-height', 'auto');
		}
	},
    composer_get_row = function(fields)  {
        var row = [];
        var res = true;
        fields.each(function(e) {
            switch(e.type) {
                case "text":
                    var v = $(e.name).get("value");
                    res = composer_validate(e, v);
                    row.push(v);
                    break;
                case "integer":
                    var v = $(e.name).get("value");
                    res = composer_validate(e, v);
                    row.push(v);
                    break;
                case "checkbox":
                    const elem = document.querySelector('#'+e.name);
                    var v = elem.checked ? 1 : 0;   // $(e.name).get("value")
                    res = composer_validate(e, v);
                    row.push(v);
                    break;
                case "time":
                    var v = $(e.name).get("value");
                    res = composer_validate(e, v);
                    row.push(v);
                    break;
                case "select":
                    var v = $(e.name).getSelected()[0].get("value");
                    res = composer_validate(e, v);
                    row.push(v);
                    break;
                case "array":
                    var v = $(e.name).getSelected().get("value");
                    res = composer_validate(e, v);
                    row.push(v.join("§"));
                    break;
            }
        });
        return (res) ? row : res;
    },
    composer_validate = function(el, v) {
        var res = true;
        var rules = el.rule.split('|');
        rules.each(function(r) {
            var t = r.split("§");
            switch(t[0]) {
                case "required":
                    if (v.length == 0) {
                        $(el.name).addClass("softwarn");
                        res = false;
                    }
                    break;
                case "numeric":
                    v = parseFloat(v);
                    if (isNaN(v)) {
                        $(el.name).addClass("softwarn");
                        res = false;
                    }
                    break;
                case "min":
                    v = parseFloat(v);
                    if (v < t[1]) {
                        $(el.name).addClass("softwarn");
                        res = false;
                    }
                    break;
                case "time":
                    if (!is_valid_time(v)) {
                        $(el.name).addClass("softwarn");
                        res = false;
                    }
                    break;
            }
        });
        return res;
    },
    composer_change = function(fields) {
        fields.each(function(e) {
            $(e.name).addEvent("change", function(event){
                this.removeClass("softwarn");
            });
        });
    },
    composer_reset = function(index_holder, fields) {
        $(index_holder).set("value", 0);
        fields.each(function(e) {
            switch(e.type) {
                case "array":
                    $(e.name).set("value", []);
                    break;
                case "checkbox":
                    // nothing $(e.name).set("value", []);
                    break;
                default:
                    $(e.name).set("value", "");
                break;
            }
        });
    },
    composer_add_row = function(table, container, row, move) {
        var lt = $(table).getElements("tr").length + 1;
        // update hidden
        var old = $(container).get("value");
        if (old.length > 0) {
            old = old+"\n";
        }
        $(container).set("value", old+row.join("|"));
        // has move?
        var up_down = '';
        if (move != null) {
            up_down = "<a class=\"tdown\" href=\"#\"><i class=\"fas fa-chevron-down fa-lg\"></i></a> <a class=\"tup\" href=\"#\"><i class=\"fas fa-chevron-up fa-lg\"></i></a>";
        }
        // update table
        var new_row = new Element("tr", {
            rel: lt,
            html: "<td>" + row.join("</td><td>") + "</td><td class=\"aright\">" + up_down + " <a class=\"tedit\" href=\"#\"><i class=\"fas fa-pencil-alt fa-lg\"></i></a> <a class=\"tdelete\" href=\"#\"><i class=\"fas fa-trash fa-lg red\"></i></a></td>"
        });
        $(table).grab(new_row);
    },
    composer_edit_row = function(index_holder, table, container, fields) {
        $(table).addEvent("click:relay(a.tedit)", function(event, target){
            event.preventDefault();
            var el = this.getParent("tr");
            var index = el.get("rel");
            var old = $(container).get("value");
            var r = old.split("\n");

            c = 1;
            r.each(function (e) {
                if (e != ""){
                    row = e.split("|");
                    if (c == index) {
                        $(index_holder).set("value", index);
                        fields.each(function(e, i) {
                            switch(e.type) {
                                case "array":
                                    var element = document.getElementById(e.name);

                                    // Set Values
                                    var values = row[i].split("§");
                                    for (var o = 0; o < element.options.length; o++) {
                                        element.options[o].selected = values.indexOf(element.options[o].value) >= 0;
                                    }
                                    break;
                                case "checkbox":
                                    $(e.name).set("value", 1);
                                    break;
                                default:
                                    $(e.name).set("value", row[i]);
                                    if (e.fire != undefined && e.fire) {
                                        $(e.name).fireEvent("change");
                                    }
                                    break;
                            }
                        });
                    }
                    c++;
                }
            });
        });
    },
    composer_update_row = function(index, table, container, row, move) {
        // remove a row from the selected options
        var old = $(container).get("value");
        var r = old.split("\n");
        var res = [];
        c = 1;
        r.each(function (e) {
            if (e != ""){
                if (c == index){
                    e = row.join("|");
                }
                res.push(e);
                c++;
            }
        });
        $(container).set("value", res.join("\n"));
        // can move?
        var up_down = '';
        if (move != null) {
            up_down = "<a class=\"tdown\" href=\"#\"><i class=\"fas fa-chevron-down fa-lg\"></i></a><a class=\"tup\" href=\"#\"><i class=\"fas fa-chevron-up fa-lg\"></i></a>";
        }
        document.getElementById(table).rows.item(index-1).innerHTML = "<td>" + row.join("</td><td>") +
            "</td><td class=\"aright\">" + up_down +
            "<a class=\"tedit\" href=\"#\"><i class=\"fas fa-pencil-alt fa-lg\"></i></a><a class=\"tdelete\" href=\"#\"><i class=\"fas fa-trash fa-lg red\"></i></a></td>";
    },
    composer_remove_row = function(index, container) {
        // remove a row from the selected options
        var old = $(container).get("value");
        var r = old.split("\n");
        var res = [];
        Array.each(r, function(e, i) {
            if (e != "") {
                row = e.split("|");
                if (index != i+1){
                    res.push(e);
                }
            }
        });
        $(container).set("value", res.join("\n"));
    },
    composer_delete_action = function(table, container) {
        $(table).addEvent("click:relay(a.tdelete)", function(event, target){
            event.preventDefault();
            var el = this.getParent("tr");
            composer_remove_row(parseInt(el.get("rel")), container);
            el.dispose();
        });
    },
    composer_move_action = function(table, container, url)
    {
        $(table).addEvent("click:relay(a.tup)", function(event, target){
            event.preventDefault();
            var el = this.getParent("tr");
            composer_change_position(parseInt(el.get("rel")), container, table, -1, url);
        });

        $(table).addEvent("click:relay(a.tdown)", function(event, target){
            event.preventDefault();
            var el = this.getParent("tr");
            composer_change_position(parseInt(el.get("rel")), container, table, 1, url);
        });
    },
    // direction can be -1 or 1
    composer_change_position = function(index, container, table, direction, url) {
        // change position for selected item
        if (index == 1 && direction < 0) {
            // do nothing
        } else {
            var old = $(container).get("value").replace(/\n\n/g, "\n");
            var r = old.split("\n");
            if (index == r.length && direction == 1) {
                // do nothing
            } else {
                // change rows
                var tmp = r[index - 1];

                r[index - 1] = r[index - 1 + direction];
                r[index - 1 + direction] = tmp;
                $(container).set("value", r.join("\n"));
                composer_reload_fields(table, container, url);
            }
        }
    },
    composer_reload_fields = function(table, container, url) {
        // get the data
        var data = $(container).get("value").replace(/\n/g, "_ZZZ_").replace(/#/g, "_XXX_");
        X3.content(table, root + url + encodeURIComponent(data) + "/1/1", null);
    },

	mimetextize = function() {
		let mimetex = {},
            objs = $$('span.AM');
        //const regex1 = /root\((.+)\)/ig;
        const regex0 = /^\{\((.+?)\):}$/g;
        const regex1 = /root\((.+)\)\((.+)\)/ig;
        const regex2 = /\^\((.+?)\)/g;
        const regex3 = /\((.+?)\)\/\((.+?)\)/g;
        const regex4 = /\log_(.+?)/g;
        const regex5 = /vec\((.+?)\)/g;

        mimetex.imgSrc = domain + "/cgi-bin/mimetex.cgi?";

        objs.each(function(el) {
			let val = el.get('text').replace(/`/g, '');

            // clean
            val = val.replaceAll('&lt;', '<').replaceAll('&gt;', '>');
            val = val.replaceAll('<=', '\\leq\\hspace{0}').replaceAll('>=', '\\geq\\hspace{0}');
            val = val.replaceAll('&', '').replaceAll(';', '');
            val = val.replaceAll('&', '').replaceAll(';', '').replaceAll('*', '\\cdot\\hspace{0}');
            val = val.replaceAll('O/', '\\emptyset\\hspace{0}');
            val = val.replaceAll('lt', '\\lt\\hspace{0}').replaceAll('gt', '\\gt\\hspace{0}');
            val = val.replaceAll('le', '\\le\\hspace{0}').replaceAll('ge', '\\ge\\hspace{0}');
            // uppercase greek letters
            val = val.replaceAll('Gamma', '\\Gamma\\hspace{0}');
            val = val.replaceAll('Delta', '\\Delta\\hspace{0}');
            val = val.replaceAll('Theta', '\\Theta\\hspace{0}');
            val = val.replaceAll('Lamda', '\\Lamda\\hspace{0}');
            val = val.replaceAll('Sigma', '\\Sigma\\hspace{0}');
            val = val.replaceAll('Pi', '\\Pi\\hspace{0}');
            val = val.replaceAll('Upsilon', '\\Upsilon\\hspace{0}');
            val = val.replaceAll('Xi', '\\Xi\\hspace{0}');
            val = val.replaceAll('Phi', '\\Phi\\hspace{0}');
            val = val.replaceAll('Psi', '\\Psi\\hspace{0}');
            val = val.replaceAll('Omega', '\\Omega\\hspace{0}');

            // lowercase greek letters
            val = val.replaceAll('alpha', '\\alpha\\hspace{0}');
            val = val.replaceAll('beta', '\\beta\\hspace{0}');
            val = val.replaceAll('gamma', '\\gamma\\hspace{0}');
            val = val.replaceAll('delta', '\\delta\\hspace{0}');
            val = val.replaceAll('epsilon', '\\epsilon\\hspace{0}');
            val = val.replaceAll('zeta', '\\zeta\\hspace{0}');
            val = val.replaceAll('eta', '\\eta\\hspace{0}');
            val = val.replaceAll('theta', '\\theta\\hspace{0}');
            val = val.replaceAll('iota', '\\iota\\hspace{0}');
            val = val.replaceAll('kappa', '\\kappa\\hspace{0}');
            val = val.replaceAll('lamda', '\\lamda\\hspace{0}');
            val = val.replaceAll('mu', '\\mu\\hspace{0}');
            val = val.replaceAll('nu', '\\nu\\hspace{0}');

            val = val.replaceAll('xi', '\\xi\\hspace{0}');
            val = val.replaceAll('pi', '\\pi\\hspace{0}');
            val = val.replaceAll('rho', '\\rho\\hspace{0}');
            val = val.replaceAll('sigma', '\\sigma\\hspace{0}');
            val = val.replaceAll('tau', '\\tau\\hspace{0}');
            val = val.replaceAll('upsilon', '\\upsilon\\hspace{0}');
            val = val.replaceAll('phi', '\\phi\\hspace{0}');
            val = val.replaceAll('chi', '\\chi\\hspace{0}');
            val = val.replaceAll('psi', '\\psi\\hspace{0}');
            val = val.replaceAll('omega', '\\omega\\hspace{0}');

            val = val.replaceAll(' or ', '\\vee\\hspace{0}').replaceAll('vv', '\\vee\\hspace{0}');
            val = val.replaceAll('xx', '\\times\\hspace{0}');
            val = val.replaceAll('nn', '\\cap\\hspace{0}');
            val = val.replaceAll('uu', '\\cup\\hspace{0}');
            val = val.replaceAll('),(', '\\\\');

            val = val.replaceAll('forall', '\\foral\\hspace{1}');
            val = val.replaceAll('in', '\\in\\hspace{0}');
            val = val.replaceAll('+-', '\\pm\\hspace{0}');
            val = val.replaceAll('rightarrow', '\\rightarrow\\hspace{0}');

            val = val.replaceAll('sqrt', '\\sqrt');
            val = val.replaceAll(regex0, '\\left\{$1\\right');
            val = val.replaceAll(regex1, '\\sqrt[$1]{$2}');
            val = val.replaceAll(regex2, '^{$1}');
            val = val.replaceAll(regex3, '\\frac{$1}{$2}');
            val = val.replaceAll(regex4, '\log_{$1}');
            val = val.replaceAll(regex5, '\vec{$1}');
            val = val.replaceAll('root', '\\root');

            //val = val.replaceAll(regex1, '\\sqrt[$1]');

			let src = mimetex.imgSrc + encodeURIComponent(val);
			let img = new Element('img');
			img.setProperty('src', src);
//console.log(src);
			el.set('text', '');
			img.inject(el);
		});
	};

// functions for Tiny MCE
function toggleEditor(id) {
	let ids = id.split('|');
	ids.each(function(el){
		tinyMCE.execCommand('mceToggleEditor',false, el);
	});
}

function removeEditor(id) {
	let ids = id.split('|');
	ids.each(function(el) {
		tinyMCE.execCommand('mceRemoveEditor', false, el);
	});
}

function addEditor(id) {
	let ids = id.split('|');
	ids.each(function(el) {
		tinyMCE.execCommand('mceAddEditor', false, el);
	});
}

// format numbers
function number_format(number, decimals, dec_point, thousands_sep) {
    // http://kevin.vanzonneveld.net
    number = (number+'').replace(',', '').replace(' ', '');
    let n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            let k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function is_valid_time(str) {
    regexp = /^(2[0-3]|[01]?[0-9]):([0-5]?[0-9])$/;

    if (regexp.test(str)) {
        return true;
    } else {
        return false;
    }
}
function isValidDate(year, month, day) {
    //let months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    let d = Date.parse(year+"-"+month+"-"+day);   // months[month - 1] + ' ' + day + ', ' + year
    console.log(d);
    return (d instanceof Date && d.getDate() == day && d.getFullYear() == year && d.getMonth() == (month - 1));
}

function isAfterToday(date) {
    return new Date(date).valueOf() > new Date().valueOf();
}

function isAfterDate(start, end) {
    return new Date(end).valueOf() > new Date(start).valueOf();
}

function rotateElement(id, degrees) {
	let elem = document.getElementById(id);
	if(navigator.userAgent.match("Chrome")){
		elem.style.WebkitTransform = "rotate("+degrees+"deg)";
	} else if(navigator.userAgent.match("Firefox")){
		elem.style.MozTransform = "rotate("+degrees+"deg)";
	} else if(navigator.userAgent.match("MSIE")){
		elem.style.msTransform = "rotate("+degrees+"deg)";
	} else if(navigator.userAgent.match("Opera")){
		elem.style.OTransform = "rotate("+degrees+"deg)";
	} else {
		elem.style.transform = "rotate("+degrees+"deg)";
	}
}
