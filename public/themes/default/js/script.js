const modal_error = "<h3 class=\"font-bold\">" + warning + "</h3><p>" + error + "</p>";

window.addEventListener('resize', function(event) {
    event.stopPropagation();
    setTimeout(resizer, 250);
}, true);

function resizer() {
    setTimeout(function() {
        let event = new CustomEvent("resizing", {detail: 100});
        window.dispatchEvent(event);
    }, 500);
}

function targetBlank() {
    let internal = location.host.replace("www.", "");
    internal = new RegExp(internal, "i");

    let a = document.links;
    for (i = 0; i < a.length; i++) {
        let href = a[i].href;
        if(href != 'javascript:void(0)' && !internal.test(href) ) {
            a[i].setAttribute('target', '_blank');
        }
    }
};

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


function alphaCheck(str) {
	return str.replace(/[^a-zA-ZàèìòùÀÈÌÒÙ'\s]/gi, '');
}
function phoneCheck(str) {
	return str.replace(/[^\d]/gi, '');
}

function numberCheck(str) {
	return str.replace(",", ".").replace(/[^0-9\.]/g,'');
}

function alphaNumCheck(str) {
    return str.replace(/[^0-9a-zA-ZàèìòùÀÈÌÒÙ]/gi, '');
}

function symbolsCheck(str) {
    return str.replace(/[^0-9a-zA-ZàèìòùÀÈÌÒÙ\s-!$%^&*()_+|~={}\[\]:;?.]/gi, '');
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

function encodeQueryData(formData) {
    const ret = [];
    formData.forEach((value, key) => (ret.push(encodeURIComponent(key) + '=' + encodeURIComponent(value))));
    return ret.join('&');
}

const modal_ok = ' \
<div id="modal" class="px-2 pt-16 md:pt-28"> \
    <div @click.away="modal=false" \
        class="fixed overflow-y-auto inset-x-2 md:inset-x-6 lg:inset-x-1/3 \
            p-4 md:p-8 mr-2 rounded shadow-2xl max_h80 xmodal bg-lime-600" \
    > \
        <p class="text-white md:text-lg" x-html="modal_msg"></p> \
    </div> \
</div>';

const modal_ko = ' \
<div id="modal" class="px-2 pt-16 md:pt-28"> \
    <div @click.away="modal=false" \
        class="fixed overflow-y-auto inset-x-2 md:inset-x-6 lg:inset-x-1/3 \
            p-4 md:p-8 mr-2 rounded shadow-2xl max_h80 xmodal failed" \
    > \
            <div class="modal_head flex flex-row items-center justify-between"> \
                <div> \
                    <h3 class="font-bold tracking-tight" x-text="modal_title"></h3> \
                </div> \
                <a @click="close()"> \
                    <i class="fa-solid fa-2x fa-circle-xmark text-white" ></i> \
                </a> \
            </div> \
            <div class="text-sm text-left"> \
                <p x-html="modal_msg"></p> \
            </div> \
    </div> \
</div>';

function spinner_box() {
    return {
        working:false,
        run(status) {
            this.working = status;
        },
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
        async popup(data) {
            this.status(true);
            let url, js;
            if (typeof data == "string") {
                url = data;
            } else {
                url = data.url;
                js = data.js;
            }
            this.files = [],
            this.html_modal = '<div class="text-center text-white pt-60"><i class="fa-solid fa-circle-notch fa-5x fa-spin"></i></div>';
            this.error_msg = "";
            this.modal = true;
            await fetch(url, {
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
                setTimeout(() => {location.reload();}, 1500);
            }
        },
        failed(data) {
            this.modal_title = data.title;
            this.modal_msg = data.message;
            this.modal = true;
            this.html_modal = modal_ko;
        },
        close() {
            if (this.loaded_file != null) {
                unloadJsFile(this.loaded_file);
            }
            this.html_modal = "";
            this.modal = false;
            this.error_msg = "";
        },
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
                        case 'completed':
                            this.completed(json.message, 'modal');
                            break;
                        case 'modal':
                            this.completed(json.message, 'modal');
                            if (this.loaded_file != null) {
                                unloadJsFile(this.loaded_file);
                            }
                            this.popup(json.update['url']);
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
                            break;
                    }
                    this.afterSubmission(btn);
                } else {
                    //console.log(json.message);
                    if (json.message != "") {
                        this.error_msg = json.message;
                    }
                    this.afterSubmission(btn);
                }
            })
            .catch((error) => {
                this.error_msg = '<p class="failed md:px-8 p-6">'+error+'</p>';
                this.afterSubmission(btn);
            });
        },
        afterSubmission(btn) {
            this.status(false);
            if (document.getElementById(btn) != null) {
                document.getElementById(btn).removeAttribute("disabled");
            }
        },
        status(status) {
            let event = new CustomEvent("working", {detail: status});
            window.dispatchEvent(event);
        }
    }
    return {...modal, ...xactions};
}

function msg() {
    return {
        show: true,
        archiveMsg(id) {
            fetch(root+"plugin/x3notify/archive/" + id, {
                method: "GET",
                headers: { "Content-Type": "text/html" }
            })
            .then(res => res.json())
            .then(json => {
                if (json.success == 1) {
                    let event = new CustomEvent("refreshing");
                    window.dispatchEvent(event);
                    this.show = false;
                } else {
                    document.getElementById("msg"+id).innerHTML = error;
                }
            })
            .catch(() => {
                document.getElementById("msg"+id).innerHTML = error;
            });
        },
        deleteMsg(id) {
            fetch(root+"plugin/x3notify/delete/" + id, {
                method: "GET",
                headers: { "Content-Type": "text/html" }
            })
            .then(res => res.json())
            .then(json => {
                if (json.success == 1) {
                    this.show = false;
                    let event = new CustomEvent("refreshing");
                    window.dispatchEvent(event);
                } else {
                    document.getElementById("msg"+id).innerHTML = error;
                }
            })
            .catch(() => {
                document.getElementById("msg"+id).innerHTML = error;
            });
        }
    }
}

let tmx;
function navBar() {
    return {
        open: false,
        cart_amount: "",
        msgs: 0,
        xinterval: 3*60*1000,
        ooops: "",
        setUp(amount) {
            this.updateCart(amount);
        },
        updateCart(amount) {
            this.cart_amount = amount;
        },
        getInterval() {
            fetch(root+"plugin/x3notify/interval", {
                method: "GET",
                headers: { "Content-Type": "text/html" }
            })
            .then(res => res.json())
            .then(json => {
                this.xinterval = json.t;
                this.ooops = json.msg;
            });
            this.setPoll();
        },
        setPoll() {
            this.checkMsg();
            tmx = setInterval(this.checkMsg, this.xinterval);
        },
        checkMsg() {
            fetch(root+"plugin/x3notify/refresh", {
                method: "GET",
                headers: { "Content-Type": "text/html" }
            })
            .then(res => res.json())
            .then(json => {
                if (json.n > this.msgs) {
                    this.msgs = json.n;
                }
            });

        },
        viewMsg(what) {
            /*
            let event = new CustomEvent("popup", {detail: root+"plugin/x3notify/latest/"+what});
            window.dispatchEvent(event);
            */
        },
        editProfile() {
            let event = new CustomEvent("popup", {detail: root+"plugin/x3users/profile"});
            window.dispatchEvent(event);
        },
        logOut() {
            window.location.replace(root+"plugin/x3users/logout");
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
                const status = criterion.test ? 'success' : 'alert';
                return `<i class="fa-solid fa-circle-check ${status}"></i> ${this.msgs[criterion.msg]}`;
            });

            this.pwd_msg = tmp.join("<br>");
        }
    }
}

function getViewportSize(w) {
    w = w || window;
    if(w.innerWidth != null)
        return {w:w.innerWidth, h:w.innerHeight};
    let d = w.document;
    if (document.compatMode == "CSS1Compat") {
        return {
            w: d.documentElement.clientWidth,
            h: d.documentElement.clientHeight
        };
    }
    return { w: d.body.clientWidth, h: d.body.clientWidth };
}

function isViewportVisible(e) {
    if (e != null) {
        let box = e.getBoundingClientRect();
        let height = box.height || (box.bottom - box.top);
        let width = box.width || (box.right - box.left);
        let viewport = getViewportSize();
        if (!height || !width) {
            return false;
        }
        if (box.top > viewport.h || box.bottom < 0) {
            return false;
        }
        if (box.right < 0 || box.left > viewport.w) {
            return false;
        }
    }
    return true;
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
        filter() {
            let formData = getFormData("xfilter", []);
            const queryString = new URLSearchParams(formData).toString();
            let action = document.getElementById("xfilter").action;
            this.reload(action+"?"+queryString);
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
                    setTimeout(() => {this.update(json);}, 1500);
                } else {
                    this.error_msg = json.message;
                }
                this.afterSubmission(btn);
            })
            .catch((error) => {
               this.error_msg = '<p class="failed md:px-8 p-6">'+error+'</p>';
                this.afterSubmission(btn);
            });
        },
        afterSubmission(btn) {
            this.status(false);
            if (document.getElementById(btn) != null) {
                document.getElementById(btn).removeAttribute("disabled");
            }
        },
        update(json) {
            switch (json.update['element']) {
                case 'page':
                    this.reload(json.update['url']);
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
                    setTimeout(() => {this.update(json);}, 1500);
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
                            document.querySelector('input[name="'+e.name+'"]').value = chkr; // [value="'+chkr+'"]').checked = true;
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

function curve() {
    return {
        setUp(options) {
            let container = document.getElementById("chart");
            let chart = new ApexCharts(container, options);
            chart.render();
            setTimeout(function(){window.dispatchEvent(new Event('resize'));}, 100);
        }
    }
}

function time2str(h, m, s, short) {
    if (h < 10) {h = '0'+h;}
    if (m < 10) {m = '0'+m;}
    if (s < 10) {s = '0'+s;}
    if (h == '00' && short) {
        return m+':'+s;
    } else {
        return h+':'+m+':'+s;
    }
}

function time2seconds(time) {
    let t = time.split(":"),
    	h = parseInt(t[0], 10),
    	m = parseInt(t[1], 10),
    	s = parseInt(t[2], 10);
    return h*3600+m*60+s;
}

function seconds2time(seconds) {
    if (seconds == "") return "";
    let h = Math.floor(seconds / 3600);
    let m = Math.floor((seconds - (h * 3600)) / 60);
    let s = seconds - (h * 3600) - (m * 60);
    return time2str(h, m, s, true);
}

String.prototype.timex2 = function (seconds, complete) {
    seconds = Number(seconds);
    let h = Math.floor(seconds % (3600*24) / 3600);
    let m = Math.floor(seconds % 3600 / 60);
    if (complete != null) {
        let s = Math.floor(seconds % 60);
        return time2str(h, m, s);
    } else {
        return h+'h:'+m+'min';
    }
}
