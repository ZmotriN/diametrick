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
        this.cont.classList.add('show');
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


class ModalLoading extends Modal {

    constructor() {
        super(create('div', 'modal-object modal-loading'));
    }

}



const ModalBox = {

};