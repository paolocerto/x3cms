function targetBlank() {
    var internal = location.host.replace("www.", "");
        internal = new RegExp(internal, "i");

    var a = document.links;
    for (i = 0; i < a.length; i++) {
        var href = a[i].href;
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

var modal_ok = ' \
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

var modal_ko = ' \
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

function xmodal() {
    return {
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
        selectFile(input, selected) {
            let el = document.getElementById(input);
            if (this.files[input] == null) this.files[input] = [];
            if (selected.length) {
                if (el.multiple) {
                    this.files[input] = [...this.files[input], ...selected];
                    //this.files[input] = this.files[input].concat(selected);
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
        },
    }
}
