class Modal {
    
    obj = null;
    cont = null;

    constructor(obj) {
        this.obj = typeof obj == 'string' ? query(obj) : obj;
        this.cont = document.createElement('div');
        this.cont.className = 'modal';
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


class ModalLoading extends Modal {

    constructor() {
        const loading = document.createElement('div');
        loading.className = 'modal-object modal-loading';
        super(loading);
    }

}