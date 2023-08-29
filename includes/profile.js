const ModalProfile = {

    profile: null,

    init() {
        this.profile = query('div.profile_anim');
        return this;
    },

    show(clb=null) {
        this.profile.classList.remove('hide');
        this.profile.classList.add('show');
        setTimeout(() => {
            this.profile.classList.add('hide');
            this.profile.classList.remove('show');
            if(clb) clb();
        }, 3000);
    },
}.init();


const ModalPass = {
    modal: null,
    pass: null,
    clb: null,

    init() {
        this.modal = new ModalForm('#modal_pass');
        this.pass = query('#modal_pass input[type="password"]');
        bind('#modal_pass form', 'submit', (evt) => { evt.preventDefault(); this.submit(); });
        return this;
    },

    submit() {
        if(this.clb) this.clb({ password: this.pass.value });
        this.modal.hide();
    },

    show(clb=null) {
        this.clb = clb;
        this.pass.value = '';
        this.modal.show();
        setTimeout(() => { this.pass.focus(); }, 200);
    },

}.init();


const app = {

    name: null,
    email: null,
    password: null,
    confirm: null,

    init() {
        this.name = query('form#profile input[name="name"]');
        this.email = query('form#profile input[name="email"]');
        this.password = query('form#profile input[name="password"]');
        this.confirm = query('form#profile input[name="confirm"]');
        bind('form#profile', 'submit', (evt) => { evt.preventDefault(); this.submit(); });
    },

    submit() {
        if(this.password.value != this.confirm.value){
            ModalBox.error('Votre mot de passe et sa confirmation ne concordent pas.',() => {
                this.confirm.value = '';
                setTimeout(() => { this.password.focus(); }, 200);
            });
        } else {
            let form = {
                name: this.name.value.trim(),
                email: this.email.value.trim(),
            };
            if(this.password.value.length > 0) form.password = this.password.value;
            ModalPass.show(results => {
                form.confirm = results.password;
                postmodal(() => {
                    ModalProfile.show(() => {
                        redirectReferer();
                    });
                }, form);
            });
        }
    },
}.init();


