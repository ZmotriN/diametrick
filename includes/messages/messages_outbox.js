const MessageForm = {

    modal: null,
    saveclb: null,
    parent: null,
    editor: null,
    user: null,
    subject: null,

    init: function(modal) {
        this.editor = new PellEditor('#message_body');
        this.parent = query('form#message input[name="parent_id"]');
        this.subject = query('form#message input[name="subject"]');
        this.user = query('form#message select[name="user_id"]');
        bind('form#message input[name="cancel"]', 'click', (evt) => { this.cancel(); });
        bind('form#message', 'submit', (evt) => { evt.preventDefault(); this.save(); });
        this.modal = new ModalForm(modal);
    },

    show: function(message, reply=false, saveclb) {
        this.saveclb = saveclb;
        this.user.value = message.user_id;
        this.subject.value = message.subject;
        this.parent.value = message.parent_id ?? 0;
        this.editor.setValue(message.body);
        if(reply) {
            document.getElementById('message_dest_row').style.display = 'none';
            document.getElementById('message_subject_row').style.display = 'none';
            setTimeout(() => { this.editor.focus(); }, 200);
        } else {
            document.getElementById('message_dest_row').style.display = '';
            document.getElementById('message_subject_row').style.display = '';
            setTimeout(() => { this.user.focus(); }, 200);
        }
        this.modal.show();
    },

    hide: function() {
        this.modal.hide();
    },

    cancel: function() {
        this.hide();
    },

    save: function() {
        if(!striptags(decodeEntities(this.editor.value)).trim()) return ModalBox.warning("Vous devez entrer le message.", () => { this.editor.focus(); });
        if(typeof this.saveclb == 'function') {
            this.saveclb({
                parent_id: this.parent.value,
                user_id: this.user.value,
                subject: this.subject.value,
                body: this.editor.value,
            });
        }
    },
}


const app = {
    params: null,
    list: null,

    init(params) {
        this.params = params;
        this.list = query('.mlist');
        this.params.messages.forEach(item => { this.addMessage(item); });
        bind('div.icon.inbox', 'click', evt => { redirect('messages') });
        bind('div.icon.send', 'click', evt => { this.newMessage(); });
        bind('ul.mlist li summary', 'click', (evt) => { this.expandMessage(evt); });
        bind('div.scroll .scroll__up', 'click', (evt) => { window.scrollTo(0, 0); });
        bind('div.scroll .scroll__down', 'click', (evt) => { window.scrollTo(0, document.body.scrollHeight); });
        MessageForm.init('#modal_message');
    },

    addMessage(message) {
        let str = '';
        let open = '';
        let li = document.createElement('li');
        str += '<h3>À: '+message.user_name+'&nbsp;<em>('+message.sent+')</em></h3>';
        str += '<h4>Sujet: '+message.subject+'</h4>';
        if(localStorage.getItem('message_details_'+message.id) === 'true') open = ' open';
        str += '<details'+open+'><summary data-id="'+message.id+'">Voir le message</summary>'+message.body+'</details>';
        li.innerHTML = str;
        li.id = 'message_'+message.id;
        if(parseInt(message.unread)) li.classList.add('unread');
        this.list.appendChild(li);
    },

    getMessage(id) {
        let message = null;
        this.params.messages.every(item => {
            if(item.id == id) message = item;
            else return true;
        });
        return message;
    },

    expandMessage(evt) {
        if(evt.target.parentElement.open) localStorage.removeItem('message_details_'+evt.target.dataset.id);
        else localStorage.setItem('message_details_'+evt.target.dataset.id, 'true');
    },

    newMessage() {
        MessageForm.show({
            user_id: '',
            subject: '',
            body: '',
        }, false, data => {
            data.action = 'send'
            post(data);
            MessageForm.hide();
            ModalPlane.show(() => {
                location.reload();
            });
        });
    },

}