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
            }
            else {
                this.elm.classList.add('light');
                document.body.classList.add('light');
                localStorage.setItem('lightmode', 'true');
                document.cookie = "lightmode=true; expires=Thu, 18 Dec 2050 12:00:00 UTC; path=/";
                query('link[rel~="icon"]').href = root + 'favicon_light.ico';
            }
            this.light = !this.light;
        });
        click('header .logo', (evt) => { redirect(); });
        click('header div.mail', (evt) => { redirect('messages'); });
    },
}


