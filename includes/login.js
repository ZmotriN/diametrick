
const app = {

    email: null,
    pass: null,
    err_row: null,
    err_cell: null,

    init: function() {
        this.email = query('form#login input[name="email"]');
        this.pass = query('form#login input[name="password"]');
        this.err_row = query('form#login tr.login_msg');
        this.err_cell = query('form#login tr.login_msg td');
        bind('form#login', 'submit', (evt) => { evt.preventDefault(); this.submit(); });
        this.email.focus();
    },

    submit: function() {
        // Modal.show();
        post({
            email: this.email.value,
            password: this.pass.value,
        }, true).then(results => {
            // Modal.hide();
            if(results == undefined) {
                this.error("Erreur inconnue");
            } else {
                if(results.success == undefined) {
                    this.error("Erreur inconnue");
                } else if(!results.success) {
                    this.error(results.errmsg)
                } else {
                    document.location.href = root;
                }
            }
        });
    },

    error: function(msg) {
        this.err_cell.innerHTML = msg;
        this.err_row.classList.add('show');
    },

}