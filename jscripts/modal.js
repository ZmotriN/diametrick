class Modal {
    
    obj = null;
    cont = null;

    constructor(obj) {
        this.obj = typeof obj == 'string' ? query(obj) : obj;
        this.cont = create('div', 'modal');
        this.cont.append(this.obj);
        document.body.append(this.cont);
    }

    show() {
        setTimeout(() => {
            this.cont.classList.add('show');
        }, 1);
        
    }

    hide() {
        this.cont.classList.remove('show');
    }

}


class ModalOpt extends Modal {

    opened = false;
    
    constructor(obj) {
        super(obj);
        bind(this.cont, 'mousedown', (evt) => {
            if(evt.target.classList.contains('modal')) {
                this.hide();
            }
        });
        bind(document, 'keydown', (evt) => {
            if(this.opened && (evt.key === 'Escape' && !(evt.ctrlKey || evt.altKey || evt.shiftKey))) {
                this.hide();
            }
        });
    }

    hide() {
        this.opened = false;
        super.hide();
    }

    show() {
        this.opened = true;
        super.show();
    }

}


class ModalForm extends ModalOpt {

}


class ModalAnim extends Modal {
    
    ttl = null;

    constructor(obj, ttl=5000) {
        super(obj);
        this.ttl = ttl;
    }

    show(clb=null) {
        super.show();
        setTimeout(() => {
            this.hide();
            if(clb) clb();
        }, this.ttl);
    }

}


 class ModalAlert extends ModalOpt {
    
    modal = null;

    constructor(type='info') {
        super(create('div', 'modal-alert modal-'+type));
        bind(this.obj, 'mousedown', (evt) => {
            this.hide();
        });
    }

    show(str) {
        this.obj.innerHTML = str;
        super.show();
    }

 }


 class ModalPass extends ModalOpt {

    pass = null;
    clb = null;

    constructor() {
        const obj = create('div', 'modal-box modal-pass');
        obj.create('div').innerHTML = "Veuillez entrer votre mot de passe:";

        const form = obj.create('form');
        const pass = form.create('div').create('input');
        const submit = form.create('div').create('input');

        pass.type = 'password';
        pass.name = 'password';
        pass.required = true;

        submit.type = 'submit';
        submit.value = 'Confirmer';

        super(obj);
        this.pass = pass;

        bind(form, 'submit', (evt) => {
            evt.preventDefault();
            this.submit();
        });
    }

    show(clb=null) {
        this.clb = clb;
        this.pass.value = '';
        super.show();
        setTimeout(() => {
            this.pass.focus();
        }, 200);
    }

    submit() {
        this.hide();
        if(this.clb) this.clb(this.pass.value);
    }

 }


class ModalPlane extends ModalAnim {
    
    constructor() {
        super(create('div', 'modal-anim modal-plane'), 1600);
    }

}


class ModalLoading extends Modal {

    constructor() {
        super(create('div', 'modal-object modal-loading'));
    }

}



const ModalBox = {

    _loading: null,
    _modalplane: null,
    _modalinfo: null,
    _modalerror: null,
    _modalwarning: null,
    _modalthumbsup: null,
    _modalbravo: null,
    _modalpass: null,

    get loading() {
        if(!this._loading) this._loading = new ModalLoading();
        return this._loading;
    },

    get modalplane() {
        if(!this._modalplane) this._modalplane = new ModalPlane();
        return this._modalplane;
    },

    get modalinfo() {
        if(!this._modalinfo) this._modalinfo = new ModalAlert('info');
        return this._modalinfo;
    },

    get modalerror() {
        if(!this._modalerror) this._modalerror = new ModalAlert('error');
        return this._modalerror;
    },

    get modalwarning() {
        if(!this._modalwarning) this._modalwarning = new ModalAlert('warning');
        return this._modalwarning;
    },

    get modalthumbsup() {
        if(!this._modalthumbsup) this._modalthumbsup = new ModalAlert('thumbsup');
        return this._modalthumbsup;
    },

    get modalbravo() {
        if(!this._modalbravo) this._modalbravo = new ModalAlert('bravo');
        return this._modalbravo;
    },

    get modalpass() {
        if(!this._modalpass) this._modalpass = new ModalPass();
        return this._modalpass;
    },


    plane(clb=null) {
        this.modalplane.show(clb);
    },

    info(msg) {
        this.modalinfo.show(msg);
    },

    error(msg) {
        this.modalerror.show(msg);
    },

    warning(msg) {
        this.modalwarning.show(msg);
    },

    bravo(msg) {
        this.modalbravo.show(msg);
    },

    thumbsup(msg) {
        this.modalthumbsup.show(msg);
    },

    pass(clb=null) {
        this.modalpass.show(clb);
    }

};