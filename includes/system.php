<?php
if($this->isPost()) {
    if(!$data = $this->getPostData()) $this->outjson(['success' => false, 'errmsg' => "Argument invalide."]);
    if(empty($data->action)) $this->outjson(['success' => false, 'errmsg' => "Argument invalide."]);
    try {
        if($data->action == 'backupdatabase') {
            sleep(1);
            $file = GEHGen::backupDatabase();
            $this->outjson(['success' => true, 'file' => pathinfo($file, PATHINFO_BASENAME), 'url' => $this->root.'systeme?file='.pathinfo($file, PATHINFO_BASENAME)]);
            
        } else $this->outjson(['success' => false, 'errmsg' => 'Action invalide.']);
    } catch(Exception $e) {
        $this->outjson(['success' => false, 'errmsg' => $e->getMessage()]);
    }
}

if(!empty($_GET['file'])) {
    try {
        GEHGen::downloadFile($_GET['file']);
    } catch(Exception $e) {
        $this->e404();
    }
}
?>

<div class="system_list">
    <div class="system_list__item" id="item_database_backup" title="Sauvegarde de la base de données">
        <svg viewBox="0 0 100 100">
            <path d="M14.3 70v6c0 6.6 14.6 14 35.6 14s35.7-7.4 35.7-14v-6c-6.9 5.8-20.1 9.7-35.7 9.7-15.4 0-28.6-3.9-35.6-9.7z"/>
            <path d="m78.6 58.9-3.3 1.3c-6.9 2.5-15.7 4-25.4 4s-18.4-1.5-25.4-4l-3.3-1.3a35.8 35.8 0 0 1-7-4.3v6c0 .8.2 1.7.7 2.6.4.7.9 1.4 1.6 2.1 4.9 5 17.2 9.4 33.4 9.4 16.1 0 28.5-4.3 33.4-9.4.7-.7 1.2-1.4 1.6-2.1a5 5 0 0 0 .7-2.6v-6c-1.2 1-2.7 2-4.3 2.9l-2.7 1.4z"/>
            <path d="M79.2 43.7 76 45.1c-7 2.7-16 4.3-26 4.3a73.6 73.6 0 0 1-29.2-5.7l-2.7-1.4c-1.4-.8-2.7-1.7-3.8-2.7v5.5c0 .8.2 1.7.7 2.6.4.7.9 1.4 1.6 2.1 1.8 1.8 4.5 3.5 8 5l3.9 1.4a80.4 80.4 0 0 0 46.9-1.4c3.5-1.5 6.3-3.2 8-5 .7-.7 1.2-1.4 1.6-2.1a5 5 0 0 0 .7-2.6v-5.5l-3.8 2.7-2.7 1.4z"/>
            <path d="M50 10c-21 0-35.6 7.4-35.6 14v6.2c0 .7.2 1.5.5 2.2.3.7.8 1.5 1.5 2.2A31.8 31.8 0 0 0 27.7 41l4.8 1.4A76.9 76.9 0 0 0 76 39.6c3.4-1.5 6-3.2 7.6-5 .7-.7 1.2-1.4 1.5-2.2.3-.7.5-1.5.5-2.2V24C85.7 17.4 71 10 50 10z"/>
        </svg>
        <div class="system_list__item__title">Sauvegarde de la base de données</div>
    </div>
</div>

<div class="system_anim">
    <div class="system_anim__database">
        <svg viewBox="0 0 100 100">
            <g stroke="#333" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3.5" class="ldl-scale">
                <path fill="#e0e0e0" d="M12 38h76v24H12z"/>
                <path fill="none" d="M78 46v9m-8-9v9m-8-9v9m-8-9v9m-8-9v9m-8-9v9m-8-9v9m-8-9v9"/>
                <path fill="#e0e0e0" d="M12 14h76v24H12z"/>
                <path fill="none" d="M78 21v9m-8-9v9m-8-9v9"/>
                <path fill="none" d="M39 23h15m-15 7h15"/>
                <path fill="#e0e0e0" d="M12 62h76v24H12z"/>
                <path fill="none" d="M78 70v9m-8-9v9m-8-9v9"/>
                <path fill="none" d="M39 71h15m-15 7h15"/>
                <circle class="system_anim__database__greenlight" cx="25" cy="25.9" r="5.8" fill="#abbd81"/>
                <circle class="system_anim__database__redlight" cx="25" cy="74.1" r="5.8" fill="#e15b64"/>
            </g>
        </svg>
    </div>
</div>

<script><?php $this->includejs('system'); ?></script>