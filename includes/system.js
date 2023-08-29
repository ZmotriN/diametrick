const ModalSystem = {

    system: null,
    
    init() {
        this.system = query('div.system_anim');
        return this;
    },

    show() {
        this.system.classList.add('show');
    },

    hide(clb=null) {
        this.system.classList.add('hide');
        setTimeout(() => {
            this.system.classList.remove('show');
            this.system.classList.remove('hide');
            if(clb) setTimeout(() => { clb(); }, 200);
        }, 500);
    },

    panic() {
        this.system.classList.remove('show');
        this.system.classList.remove('hide');
    },
}.init();


const app = {

    init() {
        bind('#item_database_backup', 'click', evt => { this.backupDatabase(); });
        return this;
    },

    backupDatabase() {
        ModalSystem.show();
        post({ action: 'backupdatabase', }).then(results => {
            if(!results.success) {
                ModalSystem.panic();
                ModalBox.error(results.errmsg);
            } else {
                ModalSystem.hide(() => {
                    location.href = results.url;
                });
            }
        });
    },
}.init();