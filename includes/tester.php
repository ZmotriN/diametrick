<?php

try {
    // SYS::sendMessage([
    //     // 'user_id' => SYS::getLoggedUser()->id,
    //     'user_id' => 10,
    //     'subject' => 'Sujet test',
    //     'body' => 'test ALLO!',
    // ]);
    // Message::load(['id' => 1, 'unread' => 0])->save();
    // Message::load(1)->delete();
    // Message::load(1)->read();
} catch(Exception $e) {
    _print_r($e);
}

?>

<!-- <script>
ready(() => {
    // ModalBox.alert(' patate asdf pasdfpokajsd gad;lkgja sdga sd asdf aksdf asd fasd fasdf', () => { console.log("CLOSE ALERT"); });
    // ModalBox.warning(' patate asdf pasdfpokajsd gad;lkgja sdga sd asdf aksdf asd fasd fasdf', () => { console.log("CLOSE WARNING"); });
    // ModalBox.error(' patate asdf pasdfpokajsd gad;lkgja sdga sd asdf aksdf asd fasd fasdf', () => { console.log("CLOSE ERROR"); });
    // ModalBox.bravo(' patate asdf pasdfpokajsd gad;lkgja sdga sd asdf aksdf asd fasd fasdf', () => { console.log("CLOSE BRAVO"); }, 1000);
    // ModalBox.thumbsup(' patate asdf pasdfpokajsd gad;lkgja sdga sd asdf aksdf asd fasd fasdf', () => { console.log("CLOSE BRAVO"); }, 1000);
    // ModalBox.confirm('Torvisse de batince de saint-cimonaque de câline de bine de saint-sacrament de crucifix de cibouleau de câline de tabarnouche?', () => { console.log('CONFIRM: YES'); }, () => { console.log('CONFIRM: NO'); });
    // setTimeout(() => { ModalBox.notification('Thème sauvegardé!', 5000); }, 500);
    
    
    ModalPlane.show(() => {
        console.log('PLANE');
    });
    // setTimeout(() => {
    //     ModalPlane.hide();
    //     setTimeout(() => {
    //         ModalPlane.show();
    //     }, 1000);
    // }, 2000)
});
</script> -->


<div class="quiz_disaprove_anim">
    <div class="quiz_disaprove_anim__dislike">
        <svg viewBox="0 0 100 100">
            <path fill="#906623" d="M81 32.3a21.3 21.3 0 0 0-6.3-3.6c-10.5-4-23.2 6.3-23.2 6.3S40.7 25.3 29 28.7C17.3 32 13.4 44.3 13.4 54s4.3 18.4 11.2 24.7c4.1 3.8 9.2 6.8 14.8 8.5a41 41 0 0 0 12 1.8c10.6 0 20-4 27-10.3s11-15 11-24.7c0-7.6-1.9-16.3-8.4-21.7z"/>
            <path fill="#603813" d="M57.4 16.3c4-5.4 9.8-7 16.4-8.3 1.9 2 2.4 4.2.7 6.8-5.8 1.2-11.2 1.9-15 6.3a31.2 31.2 0 0 0-5.2 8 33.6 33.6 0 0 0-1.6 4.4c-.4 1.6-.3 3.5-2 4-.5-3.3.6-7.4 1.5-10.5a34 34 0 0 1 5.2-10.7z"/>
            <path fill="#d4a801" d="M49.5 18.3a24.4 24.4 0 0 0-22.8-8 9 9 0 0 0 1 5c3 6.3 10.1 10.6 17.2 10.3 1.4 0 3-.3 4.3.2s2.6 1.8 2.3 3.2c1.5-3.6.5-7.8-2-10.8z"/>
            <path fill="#6c2918" stroke="#6c2918" stroke-linecap="round" stroke-miterlimit="10" stroke-width="5.7" d="M25.3 40.8c-4 3.2-5.3 11.6-3.9 14.9"/>
            <circle cx="24.8" cy="65" r="3.3" fill="#6c2918" transform="rotate(-35 24.8 65)"/>
            <path d="M81 32.3a50.5 50.5 0 0 1 1 10.2c0 24.8-17.8 44.8-39.7 44.8l-2.9-.1a41 41 0 0 0 12 1.8c10.6 0 20-4 27-10.3s11-15 11-24.7c0-7.6-1.9-16.3-8.4-21.7z" opacity=".2"/>
        </svg>
        <svg viewBox="0 0 100 100" class="quiz_disaprove_anim__dislike__fly">
            <g stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3" class="ldl-scale">
                <ellipse cx="50.1" cy="66.5" fill="#323334" rx="14.6" ry="21.2"/>
                <ellipse cx="50.1" cy="27.5" fill="#323334" rx="1.9" ry="10"/>
                <ellipse cx="42.6" cy="26.6" fill="#96343d" rx="8.2" ry="7.1" transform="rotate(-87 43 27)"/>
                <ellipse cx="57.6" cy="26.6" fill="#96343d" rx="7.1" ry="8.2"/>
                <path fill="none" d="m58 37 16-9-2-10 5-3M59 43l17-4 4 8h4m-23 5 19 4 4 18 4 3M42 37l-16-9 2-10-5-3m18 28-17-4-4 8h-4m23 5-19 4-4 18-4 3"/>
                <path fill="#f1f1f3" d="M73 58c3 7 9 24 4 28-4 4-16-4-22-16-6-13-3-32 2-34 6-1 14 15 16 22zm-46 0c-3 7-9 24-4 28 4 4 17-4 22-16 6-13 3-32-2-34-6-1-13 15-16 22z"/>
                <ellipse cx="50.1" cy="43.1" fill="#323334" rx="8.6" ry="12.6"/>
            </g>
        </svg>
    </div>
</div>

<script>
    const ModalApple = {

        profile: null,

        init() {
            this.profile = query('div.quiz_disaprove_anim');
            return this;
        },

        show(clb=null) {
            this.profile.classList.remove('hide');
            this.profile.classList.add('show');
            setTimeout(() => {
                this.profile.classList.add('hide');
                this.profile.classList.remove('show'); 
                if(clb) clb();
            }, 2500);
        },
    }.init();

    // setTimeout(() => {
    //     ModalApple.show(() => {
    //         console.log('patate');
    //     });
    // }, 100);
    


    // const myIterable = {
    //     *[Symbol.iterator](n) {
    //         for(const i = 0; i < n; i++) {
    //             yield i;
    //         }
    //     },
    // };

    const arrowIterable = (n) => {
        return {
            *[Symbol.iterator]() {
                for(let i = 0; i < n; i++) {
                    yield i;
                }
            },
        };
    };

    function* myIterable(n) {
        for(let i = 0; i < n; i++) {
            yield i;
        }
    }

    for (const value of arrowIterable(4)) {
        console.log(value);
    }

    
</script>