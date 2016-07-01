/**
 * X3 CMS - A smart Content Management System
 *
 * @author		Paolo Certo
 * @copyright	(c) 2010-2012 CBlu.net di Paolo Certo
 * @license		http://www.gnu.org/licenses/agpl.htm
 * @package		X3CMS
 */

var X3 = (X3 || {});
var SM;

X3.append = function(hash){
	Object.append(X3, hash);
}.bind(X3);

Browser.webkit = (Browser.safari || Browser.chrome);

X3.append({
	version: '2.0.0',
	initialized: false,
	spinner: false,
	instances: new Hash(),
	registered: new Hash(),
	idCount: 0,
	ieSupport: 'excanvas',	// Makes it easier to switch between Excanvas and Moocanvas for testing
	
	path: root,
	theme: theme,
	
	form: '',
	input: null,
	inputFiles: null,
	
	initialize: function(){
		if (Browser.ie6 && document.body.filters){
			alert('Browser not supported!')
		}
		if(Browser.ie9){
			this.ieSupport = '';
		}
		X3.initialized = true;
	},
	
	cleanUrl: function(url){
		// Cleans URLs
		if (url !== null){
			if (url.substr(0,7) == 'http://' || url.substr(0,8) == 'https://'){
				return url;
			} else {
				url = url.replace(new RegExp('^('+root+')'), '');
				return root + url;
			}
		} else {
			return null;
		}
	},
	
	content: function(container, url, title){
		var req = new Request.HTML({
			method: 'get',
			url: X3.cleanUrl(url),
			update: $(container),
			onRequest: function() {
				X3.spinnerOn();
			},
			onComplete: function() {
				if (title !== null){
					$('page-title').set('html', title);
					buttonize('page-title', null, 'topic');
				}
				X3.spinnerOff();
			}
		}).send();
	},
	
	spinnerOn: function(){
		if (!X3.spinner){
			$('spinner').addClass('fa-spin');
			X3.spinner = true;
		}
	},
	
	spinnerOff: function(){
		if (X3.spinner){
			$('spinner').removeClass('fa-spin');
			X3.spinner = false;
		}
	},
	
	/* FORM */
	
	/**
	 * Updates multiple elements
	 *
	 * @param	array	Array of elements to update. Array('element_id' => 'url_to_call')
	 *
	 */
	updateElements: function (elements)
	{
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
	 */
	getFormObject: function(url, data, div, reload)
	{
		if (!data) {
			data = '';
		}
		
		var qs = '';
		if (div != null && reload != null)
			qs = '?div=' + div + '&url=' + X3.cleanUrl(reload);
		
		var req = new Request.JSON({
			url: X3.cleanUrl(url) + qs, 
			method: 'post',
			loadMethod: 'xhr',
			data: data,
			onRequest: function()
			{
				X3.spinnerOn();
			},
			onFailure: function(xhr) 
			{
				X3.spinnerOff();
				
				// Error notification
				X3.notification('error', xhr.responseJSON);
			},
			onSuccess: function(responseJSON, responseText){
				X3.spinnerOff();
				
				// Update the elements transmitted through JSON
				if (responseJSON && responseJSON.update){
					// Updates all the elements in the update array
					X3.updateElements(responseJSON.update);
				}
				
				// JS Callback
				if (responseJSON && responseJSON.callback){
					X3.execCallbacks(responseJSON.callback);
				}
				
				// User notification
				if (responseJSON && responseJSON.message_type){
					if (responseJSON.message_type == 'error'){
						X3.notification('error', responseJSON);
					}
					else{
						X3.success(responseJSON.message_type, responseJSON.message);
					}
				}
			}
		}).send();
		return req;
	},
	
	/**
	 * Returns the form object
	 *
	 * @param	string		URL to send the form data. With or without the base URL prefix. Will be cleaned.
	 * @param	mixed		Form data
	 */
	getFormUploadObject: function(url, data, div, reload)
	{
		if (!data) {
			data = '';
		}
		
		var qs = '';
		if (div != null && reload != null)
			qs = '?div=' + div + '&url=' + X3.cleanUrl(reload);
		
		return {
			url: X3.cleanUrl(url) + qs, 
			
			onRequest: function(){
				X3.spinnerOn();
				if($('progressZone')){
					$('progressZone').setStyles({display: 'block', width: 0});
				}
			},
			onFailure: function(xhr){
				X3.spinnerOff();
				if($('progressZone')){
					$('div.progress').setStyle('display', 'none');
				}
				
				// Error notification
				responseJSON = JSON.decode(xhr.responseText, true);
				X3.notification('error', responseJSON);
			},
			onProgress: function(event){
				var loaded = event.loaded, total = event.total;
				if($('progressZone')){
					$('progressZone').setStyle('width', parseInt(loaded / total * 100, 10).limit(0, 100) + '%');
				}
			},
			onSuccess: function(responseText, responseXML){
				X3.spinnerOff();
				if($('progressZone')){
					$('progressZone').setStyle('width', '100%');
				}
				//X3.reset_files();
				
				responseJSON = JSON.decode(responseText, true);
				// Update the elements transmitted through JSON
				if (responseJSON && qs.length > 0) {
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
	getObject: function(url, div, reload)
	{
		return {
			url: X3.cleanUrl(url) + '?div=' + div + '&url=' + reload,
			method: 'get',
			loadMethod: 'xhr',
			data: '',
			onRequest: function() {
				X3.spinnerOn();
			},
			onFailure: function(xhr) {
				X3.spinnerOff();
				
				// Error notification
				X3.notification('error', xhr.responseJSON);
			},
			onSuccess: function(responseJSON, responseText) {
				X3.spinnerOff();
				
				// Update the elements transmitted through JSON
				if (responseJSON && responseJSON.update) {
					// Updates all the elements in the update array
					X3.updateElements(responseJSON.update);
				}
				
				// JS Callback
				if (responseJSON && responseJSON.callback) {
					X3.execCallbacks(responseJSON.callback);
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
	
	/* UPLOAD */
	
	/* MULTIPLE FILES */
	
	droppize: function(id_form, id_file_input, drop_msg){
		if ('FormData' in window) {
			X3.form = document.id(id_form);
			X3.input = document.id(id_file_input);
			
			var drop = new Element('div.droppable', {
				id: 'dropZone',
				text: drop_msg
			}).inject(X3.input, 'after');
			
			var list = new Element('ul.uploadList', {
				id: 'uploadZone'
			}).inject(drop, 'after');
			
			var progress = new Element('div.progress', {
				id: 'progressZone'
			}).setStyle('display', 'none').inject(list, 'after');
			
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
	
	uploadize: function(id_form, id_file_input, div, reload){
		if ('FormData' in window && X3.inputFiles !=  null) {
			var req_options = X3.getFormUploadObject(X3.form.get('action'), id_form, div, reload);
			var uploadReq = new Request.File(req_options);
			var inputname = X3.input.get('name');
			
			X3.inputFiles.getFiles().each(function(file){
				uploadReq.append(inputname, file);
			});
			
			// post
			var input = $(id_form).getElements('select,input,textarea');
			input.each(function(item){
				i = item.get('name');
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
			alert('Browser not supported!');
		}
	},
	
	/* SINGLE FILE */
	
	single_upload: function(id_form, id_file_input){
		if ('FormData' in window) {
			X3.form = document.id(id_form);
			
			if (X3.input == null) {
				X3.input = new Array();
			}
			X3.input[id_file_input] = document.id(id_file_input);
			
			if (X3.inputFiles == null) {
				X3.inputFiles = new Array();
			}
			X3.inputFiles[X3.input[id_file_input].get('name')] = new Form.SingleFileInput(id_file_input);
		} else {
			alert('Browser not supported!');
		}
	},
	
	uploadize2: function(id_form, array_id_files_input, div, reload){
		if ('FormData' in window && X3.inputFiles !=  null) {
			X3.form = document.id(id_form);
			var req_options = X3.getFormUploadObject(X3.form.get('action'), id_form, div, reload);
			var uploadReq = new Request.File(req_options);
			
			array_id_files_input.each(function(id_input){
				if (X3.input[id_input] != null){
					inputname = X3.input[id_input].get('name');
					
					X3.inputFiles[inputname].getFiles().each(function(file){
						uploadReq.append(inputname, file);
					});
				}
			});
			
			// post
			var input = $(id_form).getElements('select,input,textarea');
			input.each(function(item){
				i = item.get('name');
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
	
	reset_files: function(){
		X3.inputFiles = null;
	},
	
	/* MODAL WINDOWS */
	
	modal: function(type, title, url){
		
		X3.spinnerOn();
		SM = new SimpleModal();
		
		SM.show({
			"model":"modal-ajax",
			"title": title,
			"param":{
				"url": X3.cleanUrl(url),
				"onRequestComplete": function(){ 
				    var scroll_fx = new Fx.Scroll(window);
				    scroll_fx.toTop();
					X3.spinnerOff();
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
	 */
	notification: function(type, msg, modal)
	{
		if ($('msg-container') != null) {
			$('msg-container').set('html', '<p class="' + type + '">'+msg.message+'</p>');
			$('msg-container').addClass('msg');
			$('msg-container').setStyle('margin', '40px -3px 40px -3px');
		} else {
			X3.unscroll();
			SM = new SimpleModal();
			SM.show({
				model: type,
				contents: '<div class="' + type + ' zerom">' + msg.message_close + '<p>'+msg.message+'</p></div>'
			});
			
			$('close-modal').addEvent('click', function(e){
				e.stop();
				SM.hide();
				X3.unscroll();
			});
		}
	},
	
	success: function(type, msg)
	{
		if (msg != null) {
			X3.unscroll();
			SM = new SimpleModal();
			SM.show({
				model: type,
				contents: '<p class="' + type + '">'+msg+'</p>'
			});
		}
	},
	
	unscroll: function()
	{
		if ($('scrolled') != null) {
			$('scrolled').fireEvent('mouseleave');
		}
	},
	
	/**
	 * Execute the callbacks
	 *
	 * @param	Mixed.	Function name or array of functions.
	 */
	execCallbacks: function(args)
	{
		var callbacks = new Array();

		// More than one callback
		if (typeOf(args) == 'array') {
			callbacks = args;
		} else {
			callbacks.push(args);
		}
		
		callbacks.each(function(item, idx){
			var cb = (item.fn).split(".");
			var func = null;
			var obj = null;
			
			if (cb.length > 1) {
				obj = window[cb[0]];
				func = obj[cb[1]];
			} else {
				func = window[cb];
			}
			func.delay(100, obj, item.args);
		});
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
    var xto_top = new Element('span#xscroll_up.scroll.scroll-to-top.fa.fa-2x.fa-arrow-circle-up').addEvent('click', function() { scroll_fx.toTop(); }).inject(document.body); 
    var xto_bottom = new Element('span#xscroll_down.scroll.scroll-to-bottom.fa.fa-2x.fa-arrow-circle-down').addEvent('click', function() { scroll_fx.toBottom(); }).inject(document.body); 
    
    var checkScroll = function() { 
	    var body_scroll = document.body.getScroll(); 
	    xto_top.setStyle('visibility', body_scroll.y > 100 ? 'visible' : 'hidden'); 
	    xto_bottom.setStyle('visibility', (body_scroll.y + window.getCoordinates().height) < window.getScrollSize().y ? 'visible' : 'hidden'); 
	};
    
    checkScroll(); 
    window.addEvent('scroll', function(e) { checkScroll(); });
});

// Initialize when the DOM is ready
window.addEvent('domready',function(){
	XUI.desktop(start_page, start_title);
    linking();
});

//var WINDOW_CHANGE_EVENT = ('onorientationchange' in window) ? 'orientationchange' : 'resize';

var windowHeight = function (){
		return window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
	},
	windowWidth = function (){
		return window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	},
	autoSize = function(container, element, what, minus, max, min) {
		var c = $(container).getSize(), s = 0;
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
		var pos = 0;
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
			var w = '', qs = '', rqs = '';
			if (type == null) {
				w = '#' + container + ' a';
			} else { 
				w = '#' + container + ' a.' + type;
			}
			
			$$(w).addEvent('click', function(e){
				e.stop();
				qs = (referer != null && referer.length > 0)
					? '?ref='+referer
					: '';
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
					X3.modal('', this.get('title'), this.get('href')+qs+rqs);
				} else {					
					X3.content(div, this.get('href')+qs+rqs, null);
				}
				return false;
			});
		}
	},
	actionize = function(container, type, div, reload, callback) {
		if (container != null) {
			$$('#' + container + ' a.' + type).addEvent('click', function(e) {
				e.stop();
				var options = X3.getObject(this.get('href'), div, reload);
				var r = new Request.JSON(options);
				r.send();
				
				if (callback != null){
					f = window[callback];
					f(this);
				}
			});
		}
	},
	tabberize = function(type, div) {
		if ($('tabber') != null) {
			$$('#tabber a.' + type).each(function(el) {
				el.addEvent('click', function(e){
					e.stop();
					X3.content(div, this.get('href'), null);
					$$('.tabs ul li.on').removeClass('on');
					el.getParent('li').addClass('on');
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
	xsetValue = function(id_element, url, funct) {
		var req = new Request({
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
		var req = new Request.JSON({
			url: url,
			async: false,
			onComplete: function(jsonObj) {
				functs.each(function(el, i){
//					if (el != null) {
						eval(el+'(jsonObj["'+inputs[i]+'"])');
//					} else {
//						$(inputs[i]).set("value", jsonObj[inputs[i]]);
//					}
				});
			}
		}).send();
	},
	setForm = function(id_form, tiny_mce, div, reload) {
		if (tiny_mce != null) {
			tmce = tiny_mce.split('|');
			Array.each(tmce, function(f, i) {
				removeEditor(f);
			});
		}
		// Send the form
		var options = X3.getFormObject($(id_form).get('action'), $(id_form), div, reload);
		var r = new Request.JSON(options);
		r.send();
		if (tiny_mce != null) {
			Array.each(tmce, function(f, i) {
				addEditor(f);
			});
		}
	},
	setUploadForm = function(id_form, input_names, div, reload, tiny_mce) {
		var n = 1;
		if (tiny_mce != null) {
			tmce = tiny_mce.split('|');
			Array.each(tmce, function(f, i) {
				removeEditor(f);
			});
		}
		// Send the form
		var inputs = input_names.split('|');
		X3.uploadize2(id_form, inputs, div, reload);
				
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
			var _class = i % 2 ? 'even' : 'odd'; el.addClass(_class);
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
			var _class = i % 2 ? 'even' : 'odd'; el.addClass(_class);
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
	pickerize = function(time, opts) {
		if (time == null) {
			if (opts == null) {
				opts = {
					pickerClass: 'datepicker_dashboard',
					timePicker: true,
					timeWheelStep: 10,
					format: '%Y-%m-%d %H:%M'
				}
			}
			new Picker.Date($$('input.date_toggled'), opts);
		} else {
			if (opts == null) {
				opts = {
					pickerClass: 'datepicker_dashboard',
					timePicker: false,
					format: '%Y-%m-%d'
				}
			}
			new Picker.Date($$('input.date_toggled'), opts);
		}
	},
	pickerange = function(e, opts) {
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
		var n = sdata.length;
		$$(classname).each(function(el) {
			//change event
			el.addEvent('change',function() {
				// build url
				var str = '';
				for (i=2;i<n;i++) {
					str = str+'/'+$(sdata[i]).get('value');
				}
				//ajax request object
				var req = new Request.HTML({
					method: 'get',
					url: sdata[1]+str,
					update: $(sdata[0]),
					onRequest: function() {
						X3.spinnerOn();
					},
					onComplete: function() {
						X3.spinnerOff();
					}
				}).send();
			});
		});
	},
	blanking = function() {
		$$('a').each(function(a) {
			var href = a.get('href'), ext = '';
			
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
			document.write("<ul>")
			for(var p in theObj){
				if(theObj[p].constructor == Array|| theObj[p].constructor == Object){
					document.write("<li>["+p+"] => "+typeof(theObj)+"</li>");
					document.write("<ul>")
					print_r(theObj[p]);
					document.write("</ul>")
				} else {
					document.write("<li>["+p+"] => "+theObj[p]+"</li>");
				}
			}
			document.write("</ul>")
		}
	},
	sortize = function(id_form, container, sort_info) {
		var sortInput = document.id(sort_info),
			list = document.id(container),
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
						var options = X3.getFormObject($(id_form).get('action'), $(id_form));
						var r = new Request.JSON(options);
						r.send();
					}
					chk = false;
				}
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
			onRequest: function() {
				$(id_input).setStyle('background', '#ddd');
			},
			onComplete: function() {
				$(id_input).setStyle('background','#fff');
			}
		});
	},
	bulkize = function(selector, field_class, button) {
		var bulker = $(selector),
			checkboxes = $$('.' + field_class);
		$(button).setStyle('display','none');
		bulker.addEvent('click', function() {
			checkboxes.each(function(el) { 
				el.checked = bulker.checked;
			});
			checkize(checkboxes, button);
		});
		
		checkboxes.each(function(e) {
			e.addEvent('change', function(e) {
				checkize(checkboxes, button);
			});
		});
		
		function checkize(items, button) {
			var chk = false;
			items.each(function(e) {
				if (e.checked) chk = true;
			});
			if (chk) $(button).setStyle('display','inline');
			else $(button).setStyle('display','none');
		}
	},
	saccordion = function(container, toggle_class, elementClass) {
		new Fx.Accordion($(container), toggle_class, elementClass, {
			onActive: function(toggler) { toggler.addClass("active-accordion"); },
			onBackground: function(toggler) { toggler.removeClass("active-accordion");}
		});
	},
	raccordion = function(container, toggleClass, elementClass, options){
		var selector = '#' + container + ' > .';
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
						var height = 0;
						a.getParent().getChildren().each(function(e){
							height = height + e.offsetHeight;
						});
						if(height != a.getParent().offsetHeight && a.getParent().offsetHeight != 0)
							a.getParent().setStyle('height','');
					}
				}
			});
			selector += elementClass + ' > .';
			if($defined($$(selector)[0]))
				makeaccordion(selector, toggleClass, elementClass, options);
	},
	inliner = function() {
		if ($('.inliner') != null) {
			$$('input.inline:first-child').setStyle('margin-left', '.5em');
		}
	},
	loadize = function(id, container, action) {
		$(id).addEvent('change', function(e) {
			v = this.get('value');
			$(container).load(action + v);
		});
	};
	
	
// functions for Tiny MCE
function toggleEditor(id) {
	var ids = id.split('|');
	ids.each(function(el, i){
		tinyMCE.execCommand('mceToggleEditor',false, el);
	});
}

function removeEditor(id) {
	var ids = id.split('|');
	ids.each(function(el, i) {
		tinyMCE.execCommand('mceRemoveEditor', false, el);
	});
}

function addEditor(id) {
	var ids = id.split('|');
	ids.each(function(el, i) {
		tinyMCE.execCommand('mceAddEditor', false, el);
	});
}

// format numbers
function number_format(number, decimals, dec_point, thousands_sep) {
    // http://kevin.vanzonneveld.net
    number = (number+'').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number, 
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
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

function flashObject(url, id, width, height, version, bg, flashvars, params, att)
{
	var pr = '';
	var attpr = '';
	var fv = '';
	var nofv = 0;
	for(i in params)
	{
		pr += '<param name="'+i+'" value="'+params[i]+'" />';
		attpr += i+'="'+params[i]+'" ';
		if(i.match(/flashvars/ig))
		{
			nofv = 1;
		}
	}
	if(nofv==0)
	{
		fv = '<param name="flashvars" value="';
		for(i in flashvars)
		{
			fv += i+'='+escape(flashvars[i])+'&';
		}
		fv += '" />';
	}
	htmlcode = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"  codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0" width="'+width+'" height="'+height+'">'
+'	<param name="movie" value="'+url+'" />'+pr+fv
+'	<embed src="'+url+'" width="'+width+'" height="'+height+'" '+attpr+'type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer"></embed>'
+'</object>';
	document.getElementById(id).innerHTML=htmlcode;
}

function rotateElement(id, degrees) {
	var elem = document.getElementById(id);
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
