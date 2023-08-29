
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
            query('form#message table.form thead th').innerHTML = 'Répondre à '+message.from_name;
            document.getElementById('message_dest_row').style.display = 'none';
            document.getElementById('message_subject_row').style.display = 'none';
            setTimeout(() => { this.editor.focus(); }, 200);
        } else {
            query('form#message table.form thead th').innerHTML = 'Envoyer un message';
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
        bind('div.icon.outbox', 'click', evt => { redirect('messages/envoyes') });
        bind('div.icon.send', 'click', evt => { this.newMessage(); });
        bind('div.icon.reply', 'click', evt => { this.replyMessage(evt.target.dataset.id); });
        bind('ul.mlist li summary', 'click', (evt) => { this.expandMessage(evt); });
        bind('ul.mlist li div.icon.trash', 'click', (evt) => { this.deleteMessage(evt.target.dataset.id); });
        bind('div.scroll .scroll__up', 'click', (evt) => { window.scrollTo(0, 0); });
        bind('div.scroll .scroll__down', 'click', (evt) => { window.scrollTo(0, document.body.scrollHeight); });
        MessageForm.init('#modal_message');
    },

    addMessage(message) {
        let str = '';
        let open = '';
        let li = document.createElement('li');
        str += '<div class="icons">';
        if(parseInt(message.from_id) && parseInt(message.from_id) != parseInt(message.user_id)) str += '<div class="icon reply" data-id="'+message.id+'" title="Répondre"></div>&nbsp;';
        str += '<div class="icon trash" data-id="'+message.id+'" title="Supprimer"></div></div>';
        str += '<h3>De: '+message.from_name+'&nbsp;<em>('+message.sent+')</em></h3>';
        str += '<h4>Sujet: '+message.subject+'</h4>';
        if(localStorage.getItem('message_details_'+message.id) === 'true') open = ' open';
        str += '<details'+open+'><summary data-id="'+message.id+'">Voir le message</summary>'+message.body+'</details>';
        li.innerHTML = str;
        li.id = 'message_'+message.id;
        if(parseInt(message.unread)) li.classList.add('unread');
        this.list.appendChild(li);
    },

    deleteMessage(id) {
        ModalBox.confirm("Êtes-vous sur de vouloir supprimer ce message?", () => {
            let message = this.getMessage(id);
            document.getElementById('message_'+message.id).remove();
            localStorage.removeItem('message_details_'+message.id);
            if(parseInt(message.unread)) this.refreshMailbox();
            post({ action: 'delete', id: id });
        });
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
        let message = this.getMessage(evt.target.dataset.id);
        if(parseInt(message.unread)) {
            document.getElementById('message_'+message.id).classList.remove('unread');
            this.refreshMailbox();
            message.unread = 0;
            post({ action: 'read', id: message.id });
        }
        if(evt.target.parentElement.open) localStorage.removeItem('message_details_'+evt.target.dataset.id);
        else localStorage.setItem('message_details_'+evt.target.dataset.id, 'true');
    },

    getUnreadMessages() {
        return query('ul.mlist li.unread', true).length;
    },

    refreshMailbox() {
        if(!this.getUnreadMessages()) {
            query('header div.mail').classList.remove('new');
        }
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
            ModalPlane.show();
        });
    },

    replyMessage(id) {
        let message = this.getMessage(id);
        if(!/^RE:/.test(message.subject)) message.subject = 'RE: '+message.subject;
        MessageForm.show({
            user_id: parseInt(message.from_id),
            from_name: message.from_name,
            parent_id: parseInt(message.id),
            subject: message.subject,
            body: '',
        }, true, data => {
            data.action = 'reply'
            post(data);
            MessageForm.hide();
            ModalPlane.show();
        });
    }


}