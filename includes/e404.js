class Marmotte {

    app = null;
    obj = null;
    ttl = null;
    index = null;
    state = 'in';

    constructor(index, obj, app) {
        this.index = index;
        this.obj = obj;
        this.app = app;

        this.obj.addEventListener('mouseover', (evt) => {
            if(this.state == 'out') {
                clearTimeout(this.ttl);
                this.getIn();
                this.app.popMarmotte(this.index);
            }
        });
    }

    getOut() {
        this.state = 'out';
        this.obj.classList.add('trou-container--out');
        this.ttl = setTimeout(() => {
            this.getIn();
            this.app.popMarmotte(this.index);
        }, Math.floor(Math.random() * 4000) + 1000);
    }

    getIn() {
        this.state = 'in';
        this.obj.classList.remove('trou-container--out');
    }
}


const app = {

    marmottes: [],

    init: function() {
        query('div.trou-container', true).forEach((item) => {
            this.marmottes.push(new Marmotte(this.marmottes.length, item, this));
        });
        this.popMarmotte();
    },

    popMarmotte: function(not=-1) {
        do { var marmotteId = Math.floor(Math.random() * this.marmottes.length); }
        while (marmotteId == not);
        this.marmottes[marmotteId].getOut();
    },
}