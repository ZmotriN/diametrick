<?php
if($this->isPost()) {
    if(!$data = $this->getPostData()) $this->outjson(['success' => false, 'errmsg' => "Argument invalide."]);
    if(empty($data->action)) $this->outjson(['success' => false, 'errmsg' => "Argument invalide."]);
    try {
        if($data->action == 'read') {
            if(empty($data->id)) $this->outjson(['success' => false, 'errmsg' => "ID de message invalide."]);
            Message::load($data->id)->markAsRead();
            $this->outjson(['success' => true, 'data' => $data]);
        } elseif($data->action == 'delete') {
            if(empty($data->id)) $this->outjson(['success' => false, 'errmsg' => "ID de message invalide."]);
            Message::load($data->id)->delete();
            $this->outjson(['success' => true, 'data' => $data]);
        } elseif($data->action == 'send') {
            SYS::getLoggedUser()->sendMessage((array)$data);
            $this->outjson(['success' => true, 'data' => $data]);
        } elseif($data->action == 'reply') {
            SYS::getLoggedUser()->sendMessage((array)$data);
            $this->outjson(['success' => true, 'data' => $data]);
        } else $this->outjson(['success' => false, 'errmsg' => 'Action invalide.']);
    } catch(Exception $e) {
        $this->outjson(['success' => false, 'errmsg' => $e->getMessage()]);
    }
}
$my_id = SYS::getLoggedUser()->id;
foreach(SYS::getUsers('name ASC') as $user) if($user->id != $my_id) $users[] = $user;
foreach(SYS::getLoggedUser()->getMessages() as $message) {
    // $message->sent = dateToFrench($message->sent ,"l j F Y H:i");
    $messages[] = $message;
}
$params['messages'] = $messages ?? [];
?>

<div class="back"><a href="javascript:history.back();">< Retour</a></div>

<div class="scroll"><div class="scroll__up"></div><div class="scroll__down"></div></div>

<table class="form large">
    <thead>
        <tr>
            <th colspan="2"><div class="icon outbox" title="Messages envoyés"></div>Messages reçus<div class="icon send" title="Envoyer un message"></div></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2">
                <ul class="mlist"></ul>
            </td>
        </tr>
    </tbody>
</table>

<div id="modal_message" class="modal-form">
    <form id="message">
        <input type="hidden" name="parent_id" value="0">
        <table class="form">
            <thead>
                <tr>
                    <th colspan="2">Envoyer un message</th>
                </tr>
            </thead>
            <tbody>
                <tr id="message_dest_row">
                    <td style="width: 120px;">
                        Destinataire:&nbsp;
                    </td>
                    <td>
                        <select name="user_id" style="width: 100%;" required>
                            <option value="">--- Choisir un destinataire ---</option>
                            <?php foreach($users as $user): ?>
                                <option value="<?php echo $user->id?>"><?php echo $user->name?></option>
                            <?php endforeach;?>
                            <?php if(SYS::getLoggedUser()->isSuperAdmin()): ?>
                                <option value="0">--- Tous les utilisateurs ---</option>
                            <?php endif;?>
                        </select>
                    </td>
                </tr>
                <tr id="message_subject_row">
                    <td style="width: 120px;">
                        Sujet:&nbsp;
                    </td>
                    <td>
                        <input style="width: 100%;" type="text" name="subject" required>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div id="message_body"></div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="save" value="Envoyer">
                        <input type="button" name="cancel" value="Annuler">
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>
</div>

<script><?php $this->includejs('messages/messages'); ?>app.init(<?php echo json_encode($params); ?>);</script>