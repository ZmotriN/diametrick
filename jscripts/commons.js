const ready = (evt) => {
    document.addEventListener("DOMContentLoaded", (e) => {  evt(e); });
}


const bind = (elm, evt, clb) => {
    if(typeof elm == 'string') {
        document.querySelectorAll(elm).forEach((item) => {
            item.addEventListener(evt, clb);
        });
    } else {
        elm.addEventListener(evt, clb);
    }
}


const query = (selector, all=false) => {
    if(all) return document.querySelectorAll(selector);
    else return document.querySelector(selector);
}


const click = (elm, evt) => {
    if(typeof elm == 'string') elm = query(elm);
    elm.addEventListener('click', evt);
}


const redirect = (path='') => {
    document.location.href = root + path;
}


const redirectReferer = () => {
    let link = root;
    const baseuri = new URL(document.baseURI);
    const referer = new URL(document.referrer, document.baseURI);
    if(referer.hostname == baseuri.hostname && referer.pathname != baseuri.pathname) link = referer.href;
    document.location.href = link;
};


const striptags = (str) => {
    return str.replace(/(<([^>]+)>)/gi, "");
}


const count = (arr) => {
    return arr.filter(function(v){ return true; }).length;
}


const lowslug = (str) => {
	return str
		.trim()
		.normalize("NFD")
		.replace(/[\u0300-\u036f]/g, "")
		.toLowerCase()
		.replace(/[^a-z0-9\s\-]/g, "")
		.replace(/[\s\t]+/g, "")
	;
}


const copyToClipboard = async (textToCopy) => {
    if (navigator.clipboard && window.isSecureContext) {
        await navigator.clipboard.writeText(textToCopy);
    } else {
        const textArea = document.createElement("textarea");
        textArea.value = textToCopy;
        textArea.style.position = "absolute";
        textArea.style.left = "-999999px";
        document.body.prepend(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
        } catch (error) {
            console.error(error);
        } finally {
            textArea.remove();
        }
    };
}


const forceKeyPressUppercase = (e) => {
    let el = e.target;
    let charInput = e.keyCode;
    if((charInput >= 97) && (charInput <= 122)) {
        if(!e.ctrlKey && !e.metaKey && !e.altKey) {
            let newChar = charInput - 32;
            let start = el.selectionStart;
            let end = el.selectionEnd;
            el.value = el.value.substring(0, start) + String.fromCharCode(newChar) + el.value.substring(end);
            el.setSelectionRange(start+1, start+1);
            e.preventDefault();
        }
    }
};


const decodeEntities = (function() {
    let element = document.createElement('div');
    let entity = /&(?:#x[a-f0-9]+|#[0-9]+|[a-z0-9]+);?/ig;
    return function decodeHTMLEntities(str) {
        str = str.replace(entity, function(m) {
            element.innerHTML = m;
            return element.textContent;
        });
        element.textContent = '';
        return str;
    }
})();


const isInViewport = (el, partial=false) => {
    const { top, left, bottom, right } = el.getBoundingClientRect();
    const { innerHeight, innerWidth } = window;
    return partial
        ? ((((top > 0 && top < innerHeight) || (bottom > 0 && bottom < innerHeight))) || (top < 0 && bottom > innerHeight)) &&
          (((left > 0 && left < innerWidth) || (right > 0 && right < innerWidth)) || (left < 0 && right > innerWidth))
        : top >= 0 && left >= 0 && bottom <= innerHeight && right <= innerWidth;
};


const post = (obj, debug=false) => {
    let url = (new URL(document.baseURI)).pathname;
    return fetch(url, {
        method: "POST",
        body: JSON.stringify(obj)
    })
    .then((response) => response.text())
    .then((responseData) => {
        if(debug) console.log(responseData);
        let response = JSON.parse(responseData);
        if(response.success == undefined) return {
            success: false,
            errmsg: "Réponse du serveur invalide.",
        };
        return response;
    })
    .catch(error => {
        return {
            success: false,
            errmsg: "Format de réponse du serveur invalide.",
        };
    });
}


const geturl = (url, debug=false) => {
    return fetch(url)
    .then((response) => response.text())
    .then((responseData) => {
        if(debug) console.log(responseData);
        return JSON.parse(responseData);
    })
    .catch(error => console.warn(error));
}


const postmodal = (backurl, obj, debug=false) => {
    Modal.show();
    post(obj, debug).then(results => {
        if(typeof results == 'undefined') {
            Modal.hide();
        } else {
            if(!results.success) {
                Modal.hide();
                ModalBox.error('Erreur: ' + results.errmsg)
            } else {
                if(debug) Modal.hide();
                else if(backurl) {
                    if(typeof backurl == 'function') {
                        Modal.hide();
                        backurl(results);
                    } else if(backurl === true) {
                        const baseuri = new URL(document.baseURI);
                        const referer = new URL(document.referrer, document.baseURI);
                        if(referer.hostname == baseuri.hostname) {
                            document.location.href = document.referrer;
                        } //else redirect('');
                    } else redirect(backurl);
                }
                else location.reload();
            }
        }
    });
}


const Modal = {
    show: function() {
        document.querySelector('div#modal').classList.add('show');
    },
    hide: function() {
        document.querySelector('div#modal').classList.remove('show');
    }
}


const ModalPlane = {
    show: function(clb=null) {
        document.querySelector('div#modal_plane').classList.add('show');
        setTimeout(() => {
            this.hide();
            if(clb) clb();
        }, 1650);
    },
    hide: function() {
        document.querySelector('div#modal_plane').classList.remove('show');
    }
}


class ModalForm {
    modal = null;
    loading = null;
    message = null;
    container = null;
    opened = false;

    constructor(modal) {
        this.modal = document.querySelector(modal);
        this.loading = this.modal.querySelector('.modalform__loading');
        this.message = this.modal.querySelector('.modalform__message');
        this.container = this.modal.querySelector('.modalform__container');
        this.modal.addEventListener('mousedown', (evt) => {
            if(evt.target.classList.contains('modalform') || evt.target.classList.contains('modalform__message')) {
                this.hide();
            }
        });
        document.addEventListener('keydown', (evt) => {
            if(this.opened) {
                if (evt.key === 'Escape' && !(evt.ctrlKey || evt.altKey || evt.shiftKey)) {
                    this.hide();
                }
            }
        });
    }

    show(msg=null, ttl=0) {
        this.opened = true;
        if(this.loading) {
            this.loading.classList.add('show');
            this.container.classList.add('hide');
        }
        if(this.message && msg) {
            this.message.innerHTML = msg;
            if(ttl) setTimeout(() => { this.hide(); }, ttl);
        }
        this.modal.classList.add('show');
    }

    hide() {
        this.opened = false;
        this.modal.classList.remove('show');
    }

    load(data) {
        this.container.innerHTML = data;
        this.loading.classList.remove('show');
        this.container.classList.remove('hide');
    }

    unload() {
        this.loading.classList.add('show');
        this.container.classList.add('hide');
    }

}


class ModalBoxContainer {
    modal = null;
    alert = null;
    warning = null;
    error = null;
    bravo = null;
    thumbsup = null;

    opened = false;
    clb = null;

    constructor(modal) {
        this.modal = document.querySelector(modal);
        this.alert = this.modal.querySelector('.modalform__box.alert');
        this.warning = this.modal.querySelector('.modalform__box.warning');
        this.error = this.modal.querySelector('.modalform__box.error');
        this.bravo = this.modal.querySelector('.modalform__box.bravo');
        this.thumbsup = this.modal.querySelector('.modalform__box.thumbsup');
        this.modal.addEventListener('mousedown', (evt) => {
            if(
                evt.target.classList.contains('modalform') ||
                evt.target.classList.contains('modalform__box')
            ) {
                this.hide();
            }
        });
        document.addEventListener('keydown', (evt) => {
            if(this.opened) {
                if (evt.key === 'Escape' && !(evt.ctrlKey || evt.altKey || evt.shiftKey)) {
                    evt.preventDefault();
                    evt.stopPropagation();
                    this.hide();
                } else if (evt.key === 'Enter' && !(evt.ctrlKey || evt.altKey || evt.shiftKey)) {
                    evt.preventDefault();
                    this.hide();
                }
            }
        });
    }

    show(msg=null, clb=null) {
        this.opened = true;
        this.clb = clb;
        if(this.alert) this.alert.innerHTML = msg;
        else if(this.warning) this.warning.innerHTML = msg;
        else if(this.error) this.error.innerHTML = msg;
        else if(this.bravo) this.bravo.innerHTML = msg;
        else if(this.thumbsup) this.thumbsup.innerHTML = msg;
        this.modal.classList.add('show');
    }

    hide() {
        this.opened = false;
        this.modal.classList.remove('show');
        if(this.clb) this.clb();
    }

}


class ModalBoxConfirm {
    modal = null;
    container = null;
    opened = false;
    clbyes = null;
    clbno = null;

    constructor(modal) {
        this.modal = document.querySelector(modal);
        this.container = this.modal.querySelector('.modalform__confirm__container');
        this.modal.querySelector('.modalform__confirm__icons .checkmark').addEventListener('click', (evt) => { this.yes(); });
        this.modal.querySelector('.modalform__confirm__icons .xmark').addEventListener('click', (evt) => { this.no(); });
        this.modal.addEventListener('mousedown', (evt) => { if(evt.target.classList.contains('modalform')) this.no(); });
        document.addEventListener('keydown', (evt) => {
            if(this.opened) {
                if (evt.key === 'Escape' && !(evt.ctrlKey || evt.altKey || evt.shiftKey)) this.no();
                else if (evt.key === 'Enter' && !(evt.ctrlKey || evt.altKey || evt.shiftKey)) this.yes();
            }
        });
    }

    show(msg=null, clbyes=null, clbno=null) {
        this.opened = true;
        this.clbyes = clbyes;
        this.clbno = clbno;
        this.container.innerHTML = msg;
        this.modal.classList.add('show');
    }

    hide() {
        this.opened = false;
        this.modal.classList.remove('show');
    }

    yes() {
        this.hide();
        if(this.clbyes) this.clbyes();
    }

    no() {
        this.hide();
        if(this.clbno) this.clbno();
    }

}


const ModalBox = {

    modalalert: null,
    modalwarning: null,
    modalerror: null,
    modalbravo: null,
    modalthumbsup: null,
    modalconfirm: null,
    modalnotif: null,

    alert: function(msg, clb=null) {
        if(!this.modalalert) this.modalalert = new ModalBoxContainer('#modal_alert');
        this.modalalert.show(msg, clb);
    },

    warning: function(msg, clb=null) {
        if(!this.modalwarning) this.modalwarning = new ModalBoxContainer('#modal_warning');
        this.modalwarning.show(msg, clb);
    },

    error: function(msg, clb=null) {
        if(!this.modalerror) this.modalerror = new ModalBoxContainer('#modal_error');
        this.modalerror.show(msg, clb);
    },

    bravo: function(msg, clb=null, ttl=0) {
        if(!this.modalbravo) this.modalbravo = new ModalBoxContainer('#modal_bravo');
        this.modalbravo.show(msg, clb);
        if(ttl) setTimeout(() => { this.modalbravo.hide(); }, ttl);
    },

    thumbsup: function(msg, clb=null, ttl=0) {
        if(!this.modalthumbsup) this.modalthumbsup = new ModalBoxContainer('#modal_thumbsup');
        this.modalthumbsup.show(msg, clb);
        if(ttl) setTimeout(() => { this.modalthumbsup.hide(); }, ttl);
    },

    confirm: function(msg, clbyes=null, clbno=null) {
        if(!this.modalconfirm) this.modalconfirm = new ModalBoxConfirm('#modal_confirm');
        this.modalconfirm.show(msg, clbyes, clbno);
    },

    notification: function(msg, ttl=1500) {
        if(!this.modalnotif) this.modalnotif = query('#modal_notif');
        this.modalnotif.innerHTML = msg;
        this.modalnotif.classList.add('show');
        setTimeout(() => { this.modalnotif.classList.remove('show'); }, ttl);
    },

}


class PellEditor {

    editor = null;
    value = null;
    elm = null;

    constructor(selector, value='') {
        this.value = value;
        this.elm = query(selector);
        this.elm.classList.add('pell');
        this.editor = pell.init({
            element: this.elm,
            onChange: html => { this.value = html; },
            defaultParagraphSeparator: 'p',
            styleWithCSS: false,
            actions: [
                'bold',
                'underline',
                'italic',
                'superscript',
                'subscript',
            ]
        });
        this.editor.addEventListener('paste', function (e) {
            e.stopPropagation();
            e.preventDefault();
            if(navigator.clipboard) {
                navigator.clipboard.readText().then(clip => {
                    var TeampEl = document.createElement('div');
                    TeampEl.innerHTML = clip;
                    var text = TeampEl.textContent;
                    window.pell.exec('insertText', text.replace(/[\n\r]+|[\s]{2,}/g, ' ').trim());
                    TeampEl.remove();
                });
            } else {
                var clipboardData = e.clipboardData || window.clipboardData;
                var TeampEl = document.createElement('div');
                TeampEl.innerHTML = clipboardData.getData('text/html');
                var text = TeampEl.textContent;
                window.pell.exec('insertText', text.replace(/[\n\r]+|[\s]{2,}/g, ' ').trim());
                TeampEl.remove();
            }
            return true;
        });
        this.editor.content.innerHTML = this.value;
    }

    getValue() {
        return this.editor.content.innerHTML;
    }

    setValue(val) {
        this.value = val;
        this.editor.content.innerHTML = this.value;
    }

    focus() {
        setTimeout(() => { this.editor.content.focus(); }, 0)
    }
}


class DragList {

    parent = null;
    list = null;
    items = [];

    constructor(selector, parent) {
        this.parent = parent;
        this.list = query(selector);
        this.list.classList.add('draglist');
    }

    addItem(elm) {
        let idx = this.items.length;
        this.items.push(new DragItem(elm, idx, this));
        this.items[idx].setNumber(count(this.items));
        return idx;
    }

    dragend(evt, dragitem) {
        this.items.every(item => {
            if(item.isDragOver(evt)) {
                item.container.classList.remove('dragover');
                if(item.container.compareDocumentPosition(dragitem.container) == Node.DOCUMENT_POSITION_PRECEDING)
                    item.container.after(dragitem.container);
                else item.container.before(dragitem.container);
            } else return true;
        });
        this.resetNumbers();
    }

    resetNumbers(i=1) {
        this.list.querySelectorAll('li').forEach((item) => {
            this.items[item.dataset.idx].setNumber(i++);
        });
    }

    trashItem(idx) {
        this.parent.trashItem(idx, () => {
            this.items[idx].trash();
            delete this.items[idx];
            this.resetNumbers();
        });
    }

    editItem(idx) {
        this.parent.editItem(idx);
    }

    getIndexes() {
        let indexes = [];
        this.list.querySelectorAll('li').forEach((item) => { indexes.push(item.dataset.idx); });
        return indexes;
    }

}


class DragItem {

    idx = null;
    parent = null;
    container = null;
    content = null;
    over = false;
    number = 0;

    constructor(item, idx, parent) {
        this.idx = idx;
        this.parent = parent;
        this.container = document.createElement('li');
        this.container.dataset.idx = this.idx;

        let icons = document.createElement('div');
        icons.className = 'dragicons';

        this.number = document.createElement('span');
        this.number.className = 'dragnumber';
        this.number.innerText = '#1';
        icons.appendChild(this.number);

        let edit = document.createElement('div');
        edit.className = 'icon edit';
        icons.appendChild(edit);
        click(edit, (evt) => { this.parent.editItem(this.idx); });

        let trash = document.createElement('div');
        trash.className = 'icon trash';
        icons.appendChild(trash);
        click(trash, (evt) => { this.parent.trashItem(this.idx); });

        let dots = document.createElement('div');
        dots.className = 'icon dots';
        dots.draggable = true;
        icons.appendChild(dots);

        this.content = document.createElement('div');
        this.content.className = 'dragcontent';

        this.content.appendChild(item);
        this.container.appendChild(icons);
        this.container.appendChild(this.content);
        this.parent.list.appendChild(this.container);

        bind(dots, 'dragstart', evt => {
            this.container.classList.add('dragging');
        });

        bind(dots, 'dragend', evt => {
            this.container.classList.remove('dragging');
            this.parent.dragend(evt, this);
        });

        bind(this.container, 'dragover', evt => {
            evt.preventDefault();
            return true;
        });

        bind(this.container, 'dragenter', evt => {
            evt.preventDefault();
            this.container.classList.add('dragover');
        });

        bind(this.container, 'dragleave', evt => {
            evt.preventDefault();
            if(!this.isDragOver(evt)) {
                this.container.classList.remove('dragover');
            }
        });
    }

    isDragOver(evt) {
        let bodyRect = document.body.getBoundingClientRect(),
            elemRect = this.container.getBoundingClientRect(),
            top      = elemRect.top - bodyRect.top,
            bottom   = elemRect.bottom - bodyRect.top,
            left     = elemRect.left - bodyRect.left,
            right    = elemRect.right - bodyRect.left;
        return evt.pageX > left && evt.pageX < right && evt.pageY > top && evt.pageY < bottom;
    }

    setNumber(number) {
        this.number.innerText = '#'+number;
    }

    edit(elm) {
        this.content.querySelectorAll('*').forEach((item) => {  item.remove(); });
        this.content.appendChild(elm);
    }

    trash() {
        this.container.remove();
    }

}