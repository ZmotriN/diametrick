const lightswitch = {
    elm: null,
    light: false,
    soundoff: null,
    soundon: null,
    
    init: function() {
        this.elm = query('div.switch');
        if(localStorage.getItem('lightmode') === 'true'){
            this.elm.classList.add('light');
            this.light = true;
        }
        click(this.elm, (evt) => {
            if(this.light) {
                this.elm.classList.remove('light');
                document.body.classList.remove('light');
                localStorage.setItem('lightmode', 'false');
                document.cookie = "lightmode=false; expires=Thu, 18 Dec 2050 12:00:00 UTC; path=/";
                query('link[rel~="icon"]').href = root + 'favicon.ico';
                if(evt.shiftKey) {
                    this.loadSounds();
                    this.soundoff.currentTime = 0;
                    this.soundoff.play();
                    this.soundon.pause();
                }
            }
            else {
                this.elm.classList.add('light');
                document.body.classList.add('light');
                localStorage.setItem('lightmode', 'true');
                document.cookie = "lightmode=true; expires=Thu, 18 Dec 2050 12:00:00 UTC; path=/";
                query('link[rel~="icon"]').href = root + 'favicon_light.ico';
                if(evt.shiftKey) {
                    this.loadSounds();
                    this.soundon.currentTime = 0;
                    this.soundon.play();
                    this.soundoff.pause();
                }
            }
            this.light = !this.light;
        });
        click('header .logo', (evt) => { redirect(); });
        click('header div.mail', (evt) => { redirect('messages'); });
    },
    loadSounds: function() {
        if(!this.soundon) this.soundon = new Audio(root + 'styles/sounds/lightswitch-on.webm');
        if(!this.soundoff) this.soundoff = new Audio(root + 'styles/sounds/lightswitch-off.webm');
    }
}


