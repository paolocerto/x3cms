/**
 * X3 CMS Admin with Alpine.js
 */



// constants
const zero_option = '<option value="0">--</option>';

const modal_ok = ' \
<div id="modal" class="px-2 pt-16 md:pt-28"> \
<div @click.away="modal = false" \
    class="fixed overflow-y-auto inset-x-2 md:inset-x-6 lg:inset-x-1/3 \
        p-4 md:p-8 mr-2 rounded shadow-2xl max_h80 xmodal ok " \
> \
    <div> \
        <p class="my-3 text-white" x-html="modal_msg"></p> \
    </div> \
</div> \
</div>';

const modal_ko = ' \
<div id="modal" class="px-2 pt-16 md:pt-28"> \
<div @click.away="modal = false" \
    class="fixed overflow-y-auto inset-x-2 md:inset-x-6 lg:inset-x-1/3 \
        p-4 md:p-8 mr-2 rounded shadow-2xl max_h80 xmodal failed" \
> \
        <div class="flex flex-row items-start justify-between"> \
            <div class="text-white"> \
                <h4 class="font-bold tracking-tight" x-text="modal_title"></h4> \
                <p class="mt-0" x-html="modal_msg"></p> \
            </div> \
            <a @click="modal = false"> \
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
    for (let i = allsuspects.length; i>=0; i--){ //search backwards within nodelist for matching elements to remove
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

function numberCheck(str) {
    return str.replace(",", ".").replace(/[^0-9\.]/g,'');
}

function toLower(str) {
    return str.toLowerCase();
}

function alphaNumCheck(str) {
    return str.replace(/[^0-9a-zA-ZàèìòùÀÈÌÒÙ]/gi, '');
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

function curve() {
    return {
        setUp(options) {
            let container = document.getElementById("chart");
            let chart = new ApexCharts(container, options);
            chart.render();
            setTimeout(function(){window.dispatchEvent(new Event('resize'))}, 100);
        }
    }
}

function pwd() {
    return {
        pwd: "",
        pwd_msg: "",
        min_length: 0,
        msgs:{},
        setUp(ml, msgs) {
            this.min_length = ml;
            this.msgs = msgs;
        },
        chk_pwd() {
            this.pwd_msg = "";

            const criteria = [
                { test: /[0-9]/.test(this.pwd), msg: 'digit' },
                { test: /[A-Z]/.test(this.pwd), msg: 'capital' },
                { test: /[a-z]/.test(this.pwd), msg: 'lowercase' },
                { test: /[!"#$%&()*+,-./:;<=>?@\[\]^_{|}~]/.test(this.pwd), msg: 'symbol' },
                { test: this.pwd.length >= this.min_length, msg: 'length' }
            ];

            const tmp = criteria.map(criterion => {
                const status = criterion.test ? 'success' : 'error';
                return `<i class="fa-solid fa-circle-check ${status}"></i> ${this.msgs[criterion.msg]}`;
            });

            this.pwd_msg = tmp.join("<br>");
        }
    }
}

// form handling
function getFormData(formName, files) {
    let formData = new FormData();
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
                                for (let i = 0; i <= files[el.id].length; i++) {
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
                    if (tinymce.get(el.id) == null) {
                        formData.append(el.name, "");
                    } else {
                        formData.append(el.name, tinymce.get(el.id).getContent());
                    }
                } else {
                    formData.append(el.name, el.value);
                }
                break;
            case 'SELECT':
                if (el.multiple) {
                    let collection = el.selectedOptions;
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

function openMenu() {
    return {
        open(evt) {
            if (evt.key == "o" && evt.ctrlKey) {
                let event = new CustomEvent("menu", {detail: root + "home/menu"});
                window.dispatchEvent(event);
            }
        }
    }
}

function spinner_box() {
    return {
        working:false,
        run(status) {
            this.working = status;
        },
        menu() {
            let event = new CustomEvent("menu", {detail: root + "home/menu"});
            window.dispatchEvent(event);
        },
        over() {
            let box = document.getElementById("working");
            box.classList.add("bg-slate-400");
            let icon = document.getElementById("working_icon");
            icon.classList.add("fa-bars");
            icon.classList.remove("fa-slash");
        },
        leave() {
            let box = document.getElementById("working");
            box.classList.remove("bg-slate-400");
            let icon = document.getElementById("working_icon");
            icon.classList.add("fa-slash");
            icon.classList.remove("fa-bars");
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
    }
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
        menu(data) {
            if (!this.modal) {
                this.popup(data);
            }
        },
        popup(data) {
            this.status(true);
            let url, js;
            if (typeof data == "string") {
                url = data;
            } else {
                url = data.url;
                js = data.js;
            }
            this.files = [];
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
                this.status(false);
            })
            .catch(() => {
                this.modal_title = warning;
                this.modal_msg = error;
                this.html_modal = modal_ko;
                this.status(false);
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
            this.modal_msg = data.msg;
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
            let btn = [...formName].reverse().join("");
            document.getElementById(btn).setAttribute("disabled", "");
            let action = document.getElementById(formName).getAttribute("action");
            let formData = getFormData(formName, this.files);
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
                            setTimeout(function(){window.location = json.update['url']}, 1000);
                            break;
                        case 'blank':
                            this.completed(json.message, 'modal');
                            setTimeout(function(){window.open(json.update['url'], json.title)}, 2000);
                            break;
                        default:
                            this.completed(json.message);
                            this.reload();
                            break;
                    }
                    this.afterSubmission(btn);
                } else {
                    console.log(json.message);
                    if (json.message != "") {
                        this.error_msg = '<p class="failed md:px-10 p-6">'+json.message+'</p>';
                    }
                    this.afterSubmission(btn);
                }
            })
            .catch((error) => {
                this.error_msg = '<p class="failed md:px-10 p-6">'+error+'</p>';
                this.afterSubmission(btn);
            });
        },
        afterSubmission(btn) {
            this.status(false);
            document.getElementById(btn).removeAttribute("disabled");
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
        go_top: false,
        error_msg: "",
        files: {} ,
        blank(url) {
            window.open(url, "_blank");
        },
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
                this.status(false);
                //Prism.highlightAll();
            })
            .catch(() => {
                this.content = error;
                this.status(false);
            });
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
            let btn = [...formName].reverse().join("");
            document.getElementById(btn).setAttribute("disabled", "");
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
                this.afterSubmission(btn);
            })
            .catch((error) => {
                this.failure(error);
                this.afterSubmission(btn);
            });
        },
        afterSubmission(btn) {
            this.status(false);
            document.getElementById(btn).removeAttribute("disabled");
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
                    this.success(json.message);
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
            "advlist", "autolink", "autosave", "lists", "image", "link", "media", "charmap", "anchor",
            "searchreplace", "visualblocks", "visualchars", "code", "fullscreen",
            "insertdatetime", "nonbreaking", "directionality", "importcss"
        ],
        menubar: false,
        toolbar1: "styles | undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | image link media | codeformat",

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
        convert_urls: false,
        remove_script_host : false,
        extended_valid_elements: "i[class]",
        invalid_elements : "script",
        // Example content CSS (should be your site CSS)
		importcss_append: true,
        content_css : "/themes/"+theme+"/css/tinymce"+id_area+".css",

        // Drop lists for link/image/media/template dialogs
		//templates : root+"files/js/"+id_area+"/template", // this is deprecated
        // mo images in small tinyMCE
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
        height: 250,
        skin : "oxide",
        branding: false,
        language : lang,
        promotion: false,
        paste_as_text: true,

        plugins: [
            "advlist", "autolink", "autosave", "lists", "image", "link",  "charmap", "anchor", "pagebreak",
            "searchreplace", "visualblocks", "visualchars", "codesample", "fullscreen",
            "insertdatetime", "image", "media", "nonbreaking", "directionality",
            "table", "importcss", "file-manager",
        ],
        /*
        external_plugins: {
            "tiny_mce_wiris": domain + "/node_modules/@wiris/mathtype-tinymce6/plugin.min.js",
        },
        */

        urlFileManager: domain + "/flmngr",
        urlFiles: domain + "/files/x3_/filemanager",

        Flmngr: {
            apiKey: api_key, // Default free key "FLMNFLMN"
        },

        toolbar1: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent blockquote | image link media charmap table codesample",
        // | tiny_mce_wiris_formulaEditor tiny_mce_wiris_formulaEditorChemistry",

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
        convert_urls: false,
        remove_script_host : false,

        // required for wiris,
        extended_valid_elements: '*[.*]',
        invalid_elements : "script",
        // Example content CSS (should be your site CSS)
		importcss_append: true,
        content_css : "/themes/"+theme+"/css/tinymce"+id_area+".css",

        // Drop lists for link/image/media/template dialogs
		//templates : root+"files/js/"+id_area+"/template", // this is deprecated
		link_list : root+"files/js/"+id_area+"/files",
		image_list : root+"files/js/"+id_area+"/img",
		media_list : root+"files/js/"+id_area+"/media",

		pagebreak_separator : "<!--pagebreak-->",
        // for wiris
        draggable_modal: true,
        wirisimagefontsize: '16',
        wirisformulaeditorlang: lang

    });
}

// shorten urlencoded string
function compact(str) {
    return str.replace(/%22%3A0%2C%22/g, "=").replace(/%22%3A0%7D%2C%7B%22/g, "*").replace(/%22%2C%22/g, ",").replace(/%2F/g, "_.").replace(/%22%3A%22/g, "@").replace(/%20/g, "+");
}

const validate = function(e, v) {
    if (v == null) {
        return false;
    }
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
            if (e.type != 'array') {
                document.getElementById(e.name).classList.add("softwarn");
            }
        }
    });
    return res;
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
                    case "textarea":
                        let tel = document.getElementById(e.name);
                        if (tel != null) {
                            val = tel.value;
                            val = val.replace(/'/g, "&quot;");
                        } else {
                            val = "";
                        }
                        break;
                    case "integer":
                    case "time":
                        val = document.getElementById(e.name).value;
                        break;
                    case "checkbox":
                        let elem = document.getElementById(e.name);
                        val = elem.checked ? 1 : 0;
                        break;
                    case "radio":
                        val = document.querySelector('input[name="'+e.name+'"]:checked').value;
                        console.log(val);
                        break;
                    case "select":
                        let selectElement = document.getElementById(e.name);
                        val = selectElement.options[selectElement.selectedIndex].value;
                        break;
                    case "array":
                        val = [];
                        let checkboxes = document.querySelectorAll("input[name='"+e.name+"[]'][type=checkbox]:checked");
                        for (var i = 0; i < checkboxes.length; i++) {
                            val.push(checkboxes[i].value)
                        }
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
                if (e.type != 'array') {
                    document.getElementById(e.name).classList.remove("softwarn");
                }
            });
        },
        composer_reset() {
            this.xindex = -1;
            this.xfields.forEach(function(e) {
                switch(e.type) {
                    case "array":
                        let checkboxes = document.getElementsByName(e.name+"[]");
                        [...checkboxes].map((el) => {
                            el.checked = false;
                        });
                        break;
                    case "select":
                        let f = document.getElementById(e.name);
                        f.selectedIndex = -1;
                        break;
                    case "checkbox":
                        // nothing
                        break;
                    case "radio":
                        document.querySelector('input[name="'+e.name+'"]').checked = false;
                        break;
                    default:
                        if (e.default == null) {
                            let df = document.getElementById(e.name);
                            if (df != null) {
                                if (e.value == null) {
                                    df.value = "";
                                } else {
                                    df.value = e.value;
                                }
                            }
                        }
                    break;
                }
            });
        },
        composer_update_table() {
            let data = encodeURIComponent(JSON.stringify(this.xdata))
            fetch(root + this.xurl+compact(data)+"/"+this.xname+"/"+this.xmove+"/1", {
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
            this.xindex = -1;
        },
        delayEdit(index, delay) {
            setTimeout(() => {this.editItem(index);}, delay);
        },
        editItem(index) {
            this.xindex = index;
            // convert proxy in array
            let tmp = JSON.parse(JSON.stringify(this.xdata));
            this.xfields.forEach(function(e) {
                switch(e.type) {
                    case "text":
                    case "textarea":
                    case "integer":
                    case "time":
                        let chks = tmp[index] != null
                            ? tmp[index][e.name]
                            : "";
                        let el = document.getElementById(e.name);
                        if (el != null) {
                            el.value = chks;
                        }
                        break;
                    case "select":
                        let chkss = tmp[index] != null
                            ? tmp[index][e.name]
                            : "";
                        document.getElementById(e.name).value = chkss;
                        break
                    case "radio":
                        let chkr = tmp[index] != null
                            ? tmp[index][e.name]
                            : "";
                        if (chkr != "") {
                            document.querySelector('input[name="'+e.name+'"][value="'+chkr+'"]').checked = true;
                        }
                        break;
                    case "checkbox":
                        let chkc = tmp[index] != null
                            ? tmp[index][e.name]
                            : false;
                        document.getElementById(e.name).checked = chkc;
                        break;
                    case "array":
                        let checkboxes = document.getElementsByName(e.name+"[]");
                        console.log(tmp);
                        [...checkboxes].map((el) => {
                            if (tmp[index][e.name].includes(el.value)) {
                                el.checked = true;
                            }
                        });
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
        xbtns: {},
        xparams: [],
        setup(url, btns, parameters) {
            this.xurl = url;
            this.xbtns = btns;
            let tmp = [];
            if (parameters != null) {
                parameters.forEach(function(val) {
                    tmp.push(val);
                });
            }
            this.xparams = tmp;
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
        getData() {
            let formData = getFormData("bulk_form", {});
            formData.append("bulk", JSON.stringify(this.bulk));
            return formData;
        },
        execute() {
            fetch(root + this.xurl, {
                method: "POST",
                body: this.getData()
            })
            .then(res => res.json())
            .then(json => {
                if (json.message_type == "popup") {
                    this.popup(json.update['url']);
                } else if (json.message_type == "success") {
                    this.pager(json.update['url']);
                    if (json.message != '') {
                        this.success(json.message);
                    }
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
            for (let i = 0; i < n; ++i) {
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
