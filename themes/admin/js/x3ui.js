/**
 * X3 CMS Admin with Alpine.js
 */

// constants
const zero_option = '<option value="0">--</option>';

const modal_ok = ' \
<div id="modal" class="px-2 pt-16 md:pt-28"> \
<div @click.away="modal = false" \
    class="fixed overflow-y-auto inset-x-2 md:inset-x-6 lg:inset-x-1/3 \
        md:px-8 md:py-8 px-4 py-4 mr-2 rounded shadow-2xl max_h80 xmodal ok text-gray-50" \
> \
    <div> \
        <p class="my-3 font-bold text-white" x-text="modal_msg"></p> \
    </div> \
</div> \
</div>';

const modal_ko = ' \
<div id="modal" class="px-2 pt-16 md:pt-28"> \
<div @click.away="modal = false" \
    class="fixed overflow-y-auto inset-x-2 md:inset-x-6 lg:inset-x-1/3 \
        md:px-8 md:py-8 px-4 py-4 mr-2 rounded shadow-2xl max_h80 xmodal failed" \
> \
        <div class="flex flex-row items-start justify-between"> \
            <div class="text-white"> \
                <h4 class="font-bold tracking-tight" x-text="modal_title"></h4> \
                <p class="mt-0" x-html="modal_msg"></p> \
            </div> \
            <a href="javascript:void(0)" @click="modal = false"> \
                <i class="fa-solid va0 fa-lg fa-circle-xmark text-white" ></i> \
            </a> \
            </div> \
    </div> \
</div> \
</div>';

// utilities
window.addEventListener('popstate', function(e){
    let url = e.state;
    if (url != null) {
        let event = new CustomEvent("pager", {detail: url});
        window.dispatchEvent(event);
    }
});

function rightPlace() {
    /* BASIC JS to handle back and reload actions */
    if (document.getElementById('main') == undefined)
    {
        var url = window.location.href.split('/admin/');
        window.location.href = url[0]+'/admin/home/start/'+ url[1].replace(/\//g, '§');
    }
}

function loadJsFile(filename) {
    var fileref = document.createElement('script');
    fileref.setAttribute("type","text/javascript");
    fileref.setAttribute("src", filename);
    document.head.appendChild(fileref);
}

function unloadJsFile(filename) {
    var allsuspects = document.getElementsByTagName("script");
    for (var i = allsuspects.length; i>=0; i--){ //search backwards within nodelist for matching elements to remove
    if (allsuspects[i] && allsuspects[i].getAttribute("src") != null && allsuspects[i].getAttribute("src").indexOf(filename) != -1)
        allsuspects[i].parentNode.removeChild(allsuspects[i]); //remove element by calling parentNode.removeChild()
    }
}

function scrollToItem(id) {
    document.getElementById(id).scrollIntoView({
        block: 'start',
        behavior: 'smooth'
    });
}

function stripslashes(str) {
    str = str.replace(/\\'/g, '\'');
    str = str.replace(/\\"/g, '"');
    str = str.replace(/\\0/g, '\0');
    str = str.replace(/\\\\/g, '\\');
    return str;
}

function humanFileSize(size) {
    const i = Math.floor(Math.log(size) / Math.log(1024));
    return (
        (size / Math.pow(1024, i)).toFixed(2) * 1 +
        " " +
        ["B", "kB", "MB", "GB", "TB"][i]
    );
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

// form handling
function getFormData(formName, files) {
    var formData = new FormData();
    const getAllFormElements = element => Array.from(element.elements).filter(tag => ["select", "textarea", "input"].includes(tag.tagName.toLowerCase()));
    const inputs = getAllFormElements(document.getElementById(formName));
    // get values
    inputs.forEach((el) => {
        switch (el.tagName) {
            case 'INPUT':
                switch (el.type) {
                    case 'file':
                        if (files[el.id] != null) {
                            if (files[el.id] instanceof Array) {
                                for (var i = 0; i <= files[el.id].length; i++) {
                                    formData.append(el.name, files[el.id][i]);
                                }
                            } else {
                                formData.append(el.name, files[el.id]);
                            }
                        }
                        break;
                    case 'checkbox':
                    case 'radio':
                        if (el.checked) {
							formData.append(el.name, el.value);
						}
                        break;
                    case 'datetime-local':
                        formData.append(el.name, el.value.replace("T", " "));
                        break;
                    default:
                        formData.append(el.name, el.value);
                        break;
                }
                break;
            case 'TEXTAREA':
                if (el.classList.contains('tinymce')) {
                    formData.append(el.name, tinymce.get(el.id).getContent());
                } else {
                    formData.append(el.name, el.value);
                }
                break;
            case 'SELECT':
                if (el.multiple == true) {
                    var collection = el.selectedOptions;
                    for (let i = 0; i < collection.length; i++) {
                        if (collection[i].selected) {
                            formData.append(el.name, collection[i].value);
                        }
                    };
                } else {
                    formData.append(el.name, el.value);
                }
                break;
        }
    });
    return formData;
}

const validate = function(e, v) {
    let res = true;
    const rules = e.rule.split('|');
    rules.forEach(function(r) {
        let t = r.split("§");
        switch(t[0]) {
            case "required":
                res = !(v.length == 0)
                break;
            case "numeric":
                v = parseFloat(v);
                if (isNaN(v)) {
                    res = false;
                }
                break;
            case "min":
                v = parseFloat(v);
                if (v < t[1]) {
                    res = false;
                }
                break;
            case "time":
                if (!is_valid_time(v)) {
                    res = false;
                }
                break;
        }
        if (!res) {
            document.getElementById(e.name).classList.add("softwarn");
        }
    });
    return res;
}

function spinner_box() {
    return {
        working:false,
        run(status) {
            this.working = status;
        }
    }
}

function tabs_box() {
    return{
        tabSelected: 1,
        loadedSection: "",
        loadURL(url) {
            this.loadedSection = '<p>Loading...</p>';
            fetch(url, {
                method: "GET",
                headers: { "Content-Type": "text/html" }
            })
            .then(res => res.text())
            .then(txt => {
                this.loadedSection = txt;
            })
            .catch(() => {
                this.loadedSection = '<h2>' + warning + '</h2><p>' + error + '</p>';
            });
        }
    }
}

// actions to merge in xmodal and page_box
const xactions = {
    selectFile(input, selected) {
        let el = document.getElementById(input);
        if (this.files[input] == null) this.files[input] = [];
        if (selected.length) {
            if (el.multiple) {
                this.files[input] = [...this.files[input], ...selected];
            } else {
                this.files[input] = selected[0];
            }
        }
    },
    removeFile(input, index) {
        this.files[input].splice(index, 1);
    },
    altInput(index, name) {
        return '<input type="hidden" name="namef_'+index+'" value="'+name+'" /><input class="w-full border-0" type="text" name="altf_'+index+'" value="'+name.split('.')[0]+'" />';
    },
}

function xmodal() {
    const modal = {
        modal: false,
        html_modal: "",
        modal_title:"",
        modal_msg: "",
        error_msg: "",
        loaded_file: null,  // for extra script files
        files: {} ,
        popup(data) {
            if (typeof data == "string") {
                var url = data;
            } else {
                var url = data.url;
                var js = data.js;
            }
            this.files = [],
            this.html_modal = "";
            this.error_msg = "";
            this.modal = true;
            fetch(url, {
                method: "GET",
                headers: { "Content-Type": "text/html" }
            })
            .then(res => res.text())
            .then(txt => {
                if (js != null) {
                    this.loaded_file = js;
                    loadJsFile(js);
                }
                this.html_modal = txt;
            })
            .catch(() => {
                this.modal_title = warning;
                this.modal_msg = error;
                this.html_modal = modal_ko;
            });
        },
        pager(url) {
            let event = new CustomEvent("pager", {detail: url});
            window.dispatchEvent(event);
        },
        reload(url) {
            if (url == null) {
                this.modal = false;
                location.reload();
            } else {
                window.location.href = url;
            }
        },
        completed(msg, keepOpen) {
            this.modal_msg = msg;
            this.modal = true;
            this.html_modal = modal_ok;
            if (keepOpen == null) {
                setTimeout(() => {this.modal = false;}, 1500);
            }
        },
        failed(data) {
            this.modal_title = data.title;
            this.msg = data.msg;
            this.modal = true;
            this.html_modal = modal_ko;
        },
        close() {
            if (this.loaded_file != null) {
                unloadJsFile(this.loaded_file);
            }
            this.modal = false;
            this.status(false);
            this.error_msg = "";
        },
        status(status) {
            let event = new CustomEvent("working", {detail: status});
            window.dispatchEvent(event);
        },
        submitForm(formName) {
            // this is for form inside the modal
            this.status(true);
            let formData = getFormData(formName, this.files);
            let action = document.getElementById(formName).action;
            fetch(action, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(json => {
                if (json.message_type == "success") {
                    switch (json.update['element']) {
                        case 'page':
                            this.completed(json.message);
                            if (this.loaded_file != null) {
                                unloadJsFile(this.loaded_file);
                            }
                            this.pager(json.update['url']);
                            break;
                        case 'modal':
                            this.completed(json.message, 'modal');
                            if (this.loaded_file != null) {
                                unloadJsFile(this.loaded_file);
                            }
                            this.popup(json.update['url']);
                            break;
                        case 'field':
                            this.completed(json.message);
                            let el = document.getElementById(json.update['field']);
                            el.value = json.update['value'];
                            let event = new Event('change');
                            el.dispatchEvent(event);
                            break;
                        case 'redirect':
                            this.completed(json.message, 'modal');
                            setTimeout(function(){window.location = json.update['url']} , 1000);
                            break;
                        default:
                            this.completed(json.message);
                            this.reload();
                            break;
                    }
                } else {
                    this.error_msg = '<p class="failed md:px-10 px-6 py-6">'+json.message+'</p>';
                }
            })
            .catch((error) => {
                this.error_msg = '<p class="failed md:px-10 px-6 py-6">'+error+'</p>';
            });
            this.status(false);
        }
    }
    return {...modal, ...xactions};
}

// events to merge to other functions
const xevents = {
    status(status) {
        let event = new CustomEvent("working", {detail: status});
        window.dispatchEvent(event);
    },
    success(msg) {
        let event = new CustomEvent("completed", {detail: msg});
        window.dispatchEvent(event);
    },
    failure(msg) {
        let event = new CustomEvent("failed", {detail:{title: warning, msg: msg}});
        window.dispatchEvent(event);
    },
    popup(url) {
        let event = new CustomEvent("popup", {detail: url});
        window.dispatchEvent(event);
    }
}

function page_box() {
    const page = {
        content:"",
        error_msg: "",
        files: {} ,
        pager(url) {
            this.status(true);
            fetch(url, {
                method: "GET",
                headers: { "Content-Type": "text/html" }
            })
            .then(res => res.text())
            .then(txt => {
                this.content = txt;
                history.pushState(url, "", url);
            })
            .catch(() => {
                this.content = error;
            });
            this.status(false);
        },
        filter() {
            let formData = getFormData("xfilter", []);
            const queryString = new URLSearchParams(formData).toString();
            let action = document.getElementById("xfilter").action;
            this.pager(action+"?"+queryString);
        },
        submitForm(formName) {
            // this is for form inside the page
            this.status(true);
            let formData = getFormData(formName, this.files);
            let action = document.getElementById(formName).action;
            fetch(action, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(json => {
                if (json.message_type == "success") {
                    this.success(json.message);
                    this.update(json);
                } else {
                    this.failure(json.message);
                }
                this.status(false);
            })
            .catch((error) => {
                this.failure(error);
            });
        },
        update(json) {
            switch (json.update['element']) {
                case 'page':
                    this.pager(json.update['url']);
                    break;
                case 'field':
                    let el = document.getElementById(json.update['field']);
                    el.value = json.update['value'];
                    let event = new Event('change');
                    el.dispatchEvent(event);
                    break;
                case 'redirect':
                    setTimeout(function(){window.location = json.update['url']} , 1000);
                    break;
                default:
                    this.completed(json.message);
                    this.reload();
                    break;
            }
        },
        setter(url) {
            this.status(true);
            fetch(url, {
                method: 'GET'
            })
            .then(res => res.json())
            .then(json => {
                if (json.message_type == "success") {
                    this.success(json.message);
                    this.update(json);
                } else {
                    this.failure(json.message);
                }
                this.status(false);
            })
            .catch((error) => {
                this.failure(error);
            });
        },
        reload(url) {
            if (url == null) {
                location.reload();
            } else {
                window.location.href = url;
            }
        },
        refresh(id, url) {
            this.status(true);
            fetch(url, {
                method: 'GET'
            })
            .then(res => res.text())
            .then(txt => {
                document.getElementById(id).innerHTML = txt;
                this.status(false);
            })
            .catch((error) => {
                this.failure(error);
            });
        }
    }
    return {...page, ...xactions, ...xevents};
}

function small_tiny(id_area, lang) {
    // load tinymce
    tinymce.init({
        selector: ".tinymce",
        skin : "oxide",
        branding: false,
        language : lang,
        promotion: false,
        paste_as_text: true,

        plugins: [
            "advlist", "autolink", "autosave", "lists", "link", "charmap", "anchor",
            "searchreplace", "visualblocks", "visualchars", "code", "fullscreen",
            "insertdatetime", "nonbreaking", "directionality", "importcss"
        ],

        toolbar1: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link",

        toolbar_items_size: "small",
        style_formats: [
		    {title: "Headers", items: [
		        {title: "h1", block: "h1"},
		        {title: "h2", block: "h2"},
		        {title: "h3", block: "h3"},
		        {title: "h4", block: "h4"},
		        {title: "h5", block: "h5"},
		        {title: "h6", block: "h6"}
		    ]},

		    {title: "Blocks", items: [
		        {title: "p", block: "p"},
		        {title: "div", block: "div"},
		        {title: "pre", block: "pre"}
		    ]},
		],
		visualblocks_default_state: true,
		end_container_on_empty_block: true,
        insertdatetime_formats: ["%H:%M:%S", "%Y-%m-%d", "%d/%m/%Y", "%I:%M:%S %p", "%D"],

        remove_script_host : true,
		document_base_url : domain,
        relative_urls : false,
        extended_valid_elements: "i[class]",
        invalid_elements : "script",

        content_css : "/themes/"+theme+"/css/tinymce"+id_area+".css",
        //template_selected_content_classes: "fake",

        // Drop lists for link/image/media/template dialogs
		//templates : root+"files/js/"+id_area+"/template",
		link_list : root+"files/js/"+id_area+"/files",
    });
}

// tiny MCE
function small_editor() {
    return {
        tinit(id_area, lang) {
            if (tinymce) {
                // reset
                tinymce.remove();
            }
            setTimeout(function(){small_tiny(id_area, lang);},200);
        },
    }
}

function tiny(id_area, lang, api_key) {
    // load tinymce
    tinymce.init({
        selector: ".tinymce",
        skin : "oxide",
        branding: false,
        language : lang,
        promotion: false,
        paste_as_text: true,

        plugins: [
            "advlist", "autolink", "autosave", "lists", "link", "file-manager", "charmap", "anchor", "pagebreak",
            "searchreplace", "visualblocks", "visualchars", "code", "fullscreen",
            "insertdatetime", "media", "nonbreaking", "directionality",
            "table", "importcss"
        ],

        urlFileManager: domain + "/flmngr",
        urlFiles: domain + "/cms/files/x3_/filemanager",

        Flmngr: {
            apiKey: api_key, // Default free key "FLMNFLMN"
        },

        toolbar1: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent blockquote | link image media table",

        toolbar_items_size: "small",
        style_formats: [
		    {title: "Headers", items: [
		        {title: "h1", block: "h1"},
		        {title: "h2", block: "h2"},
		        {title: "h3", block: "h3"},
		        {title: "h4", block: "h4"},
		        {title: "h5", block: "h5"},
		        {title: "h6", block: "h6"}
		    ]},

		    {title: "Blocks", items: [
		        {title: "p", block: "p"},
		        {title: "div", block: "div"},
		        {title: "pre", block: "pre"}
		    ]},

		    {title: "Containers", items: [
		        {title: "section", block: "section", wrapper: true, merge_siblings: false},
		        {title: "article", block: "article", wrapper: true, merge_siblings: false},
		        {title: "blockquote", block: "blockquote", wrapper: true},
		        {title: "hgroup", block: "hgroup", wrapper: true},
		        {title: "aside", block: "aside", wrapper: true},
		        {title: "figure", block: "figure", wrapper: true}
		    ]}
		],
		visualblocks_default_state: true,
		end_container_on_empty_block: true,
        paste_data_images: true,
        image_advtab: true,
        image_dimensions: false,
        insertdatetime_formats: ["%H:%M:%S", "%Y-%m-%d", "%d/%m/%Y", "%I:%M:%S %p", "%D"],

        remove_script_host : true,
		document_base_url : domain,
        relative_urls : false,
        extended_valid_elements: "i[class]",
        invalid_elements : "script",
        // Example content CSS (should be your site CSS)
		//importcss_append: true,
        content_css : "/themes/"+theme+"/css/tinymce"+id_area+".css",
        //template_selected_content_classes: "fake",

        // Drop lists for link/image/media/template dialogs
		//templates : root+"files/js/"+id_area+"/template",
		link_list : root+"files/js/"+id_area+"/files",
		image_list : root+"files/js/"+id_area+"/img",
		media_list : root+"files/js/"+id_area+"/media",

		pagebreak_separator : "<!--pagebreak-->",
    });
}

function configurator() {
    return {
        xindex: -1,
        xmove: 0,
        xfields: null,
        xcontainer: "",
        xtable: "",
        xurl: "",
        xname: "",
        xdata: null,
        xextra_check: null, // callback
        xoptions: null,
        setup(fields, data, container, table, url, name, move, checkItem) {
            this.xfields = fields;
            this.xcontainer = container;
            this.xtable = table;
            this.xurl = url;
            this.xname = name;
            this.xmove = move;
            this.xdata = data;
            // callback to execute extra check on the item
            this.xextra_check = checkItem;
        },
        addItem() {
            let item = this.composer_get_row();
            console.log(item);
            if (item !== false) {
                // convert proxy in array
                let tmp = JSON.parse(JSON.stringify(this.xdata));
                // extra check?
                let chk = true;
                if (this.xextra_check != null) {
                    eval('chk = '+this.xextra_check+'(item);');
                }
                if (chk) {
                    if (this.xindex == -1) {
                        // add to the last
                        tmp.push(item);
                    } else {
                        // replace the item
                        tmp[this.xindex] = item;
                    }
                    this.xdata = tmp;
                    // update hidden
                    document.getElementById(this.xcontainer).value = JSON.stringify(tmp);
                    // update table
                    this.composer_update_table();
                    this.composer_reset();
                }
            }
        },
        composer_get_row()  {
            let item = {};
            let res = true;
            this.xfields.forEach(function(e) {
                let val = null;
                switch(e.type) {
                    case "text":
                    case "integer":
                    case "time":
                        val = document.getElementById(e.name).value;
                        break;
                    case "checkbox":
                        let elem = document.getElementById(e.name);
                        val = elem.checked ? 1 : 0;
                        break;
                    case "select":
                        let selectElement = document.getElementById(e.name);
                        val = selectElement.value;
                        break;
                    case "array":
                        let options = document.getElementById(e.name).selectedOptions;
                        val = Array.from(options).map(({ value }) => value);
                        break;
                }
                res = res && validate(e, val);
                item[e.name] = val;
            });
            return (res) ? item : res;
        },
        composer_change() {
            // reset warnings
            this.xfields.forEach(function(e) {
                document.getElementById(e.name).classList.remove("softwarn");
            });
        },
        composer_reset() {
            this.xindex = -1;
            this.xfields.forEach(function(e) {
                switch(e.type) {
                    case "array":
                        document.getElementById(e.name).selectedOptions = [];
                        break;
                    case "select":
                        let f = document.getElementById(e.name);
                        f.selectedIndex = -1;
                        break;
                    case "checkbox":
                        // nothing
                        break;
                    default:
                        document.getElementById(e.name).value = "";
                    break;
                }
            });
        },
        composer_update_table() {
            fetch(root + this.xurl+JSON.stringify(this.xdata)+"/"+this.xname+"/"+this.xmove+"/1", {
                method: "GET",
                headers: { "Content-Type": "text/html" }
            })
            .then(res => res.text())
            .then(txt => {
                document.getElementById(this.xtable).innerHTML = txt;
            })
            .catch(() => {
                document.getElementById(this.xtable).innerHTML = '<tr><td> --- </td></tr>';
            });
        },
        editItem(index) {
            this.xindex = index;
            // convert proxy in array
            let tmp = JSON.parse(JSON.stringify(this.xdata));
            this.xfields.forEach(function(e) {
                switch(e.type) {
                    case "text":
                    case "integer":
                    case "time":
                    case "select":
                        document.getElementById(e.name).value = tmp[index][e.name];
                        break;
                    case "checkbox":
                        document.getElementById(e.name).checked = tmp[index][e.name];
                        break;
                    case "array":
                        document.getElementById(e.name).selectedOptions = tmp[index][e.name];
                        break;
                }
            });
        },
        deleteItem(index) {
            this.xindex = index;
            let tmp = JSON.parse(JSON.stringify(this.xdata));
            tmp.splice(this.xindex, 1);
            this.xdata = tmp;
            // update hidden
            document.getElementById(this.xcontainer).value = JSON.stringify(tmp);
            // update table
            this.composer_update_table();
        },
        moveItem(index, direction) {
            let tmp = JSON.parse(JSON.stringify(this.xdata));
            // change records
            let moved = tmp[index];
            tmp[index] = tmp[index+direction];
            tmp[index+direction] = moved;

            this.xdata = tmp;
            // update hidden
            document.getElementById(this.xcontainer).value = JSON.stringify(tmp);
            // update table
            this.composer_update_table();
        },
    }
}

function bulkable() {
    const bulk = {
        selectAll: false,
        bulk: [],
        xurl: "",
        xaction: document.getElementById("bulk_action").value,
        setup(url) {
            this.xurl = url;
        },
        toggle() {
            this.selectAll = !this.selectAll;
            let checkboxes = document.querySelectorAll('.bulkable');
            let allValues = [];

            [...checkboxes].map((el) => {
                allValues.push(el.value)
                this.bulk = this.selectAll ? allValues : [];
            });
        },
        setAction(action) {
            this.xaction = action;
        },
        execute() {
            fetch(root + this.xurl, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({action: this.xaction, bulk: this.bulk})
            })
            .then(res => res.json())
            .then(json => {
                if (json.message_type == "success") {
                    this.pager(json.update['url']);
                    this.success(json.message);
                } else {
                    this.failure(json.message);
                }
                this.status(false);
            })
            .catch((error) => {
                this.failure(error);
            });
        }
    }
    return {...bulk, ...xevents};
}

function xsortable() {
    const sorter = {
        setup(container, url) {
            let obj = this;
            dragula([document.getElementById(container)])
            .on('drop', function (el) {
                obj.refresh_order(obj, container, url);
            });
        },
        refresh_order(obj, container, url) {
            obj.status(true);
            // get children order
            let children = document.getElementById(container).children;
            let n = children.length;
            let order = [];
            for (var i = 0; i < n; ++i) {
                order.push(children[i].id);
            }
            // update
            fetch(root + url, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({sort_order: order})
            })
            .then(res => res.json())
            .then(json => {
                if (json.message_type == "success") {
                    obj.success(json.message);
                } else {
                    obj.failure(json.message);
                }
                obj.status(false);
            })
            .catch((error) => {
                obj.failure(error);
            });
        },
    }
    return {...sorter, ...xevents};
}
/*
function imageEditor() {
    return {
        image: "",
        xcoord: 0,
        ycoord: 0,
        width: document.getElementById("width").value,
        height: document.getElementById("height").value,
        ratio: false,
        //thumb: "",
        angle: 0,
        initialize(img, xeditor, xfile) {
            this.image = img;

            // set croppie
            var opts = {
                viewport: { width: this.width-50, height: this.height-50 },
                boundary: { width: this.width, height: this.height },
                showZoomer: true,
                enableResize: true,
                enableOrientation: true,
            };
            let container = document.getElementById(xeditor);
            var cropper = new Croppie(container, opts);
            cropper.bind({
                url: xfile,
            });
        },
        rotating(){
            document.getElementById(this.image).style.transform="rotate("+this.angle+"deg)";
        },
    }
}
*/
