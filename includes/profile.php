<?php
if($this->isPost()) {
    if(!$data = $this->getPostData()) $this->outjson(['success' => false, 'errmsg' => "Argument invalide."]);
    if(empty($data->confirm)) $this->outjson(['success' => false, 'errmsg' => "Argument invalide."]);
    if(empty($data->name)) $this->outjson(['success' => false, 'errmsg' => "Argument invalide."]);
    if(empty($data->email)) $this->outjson(['success' => false, 'errmsg' => "Argument invalide."]);
    if(!GEHGen::getLoggedUser()->verifyPassword($data->confirm)) $this->outjson(['success' => false, 'errmsg' => "Mot de passe invalide."]);
    try {
        $user = [
            'id' => GEHGen::getLoggedUser()->id,
            'name' => $data->name,
            'email' => $data->email
        ];
        if(!empty($data->password)) $user['pass'] = $data->password;
        User::load($user)->save();
        $this->outjson(['success' => true, 'data' => $data]);
    } catch(Exception $e) {
        $this->outjson(['success' => false, 'errmsg' => $e->getMessage()]);
    }
}

if(!$user = GEHGen::getLoggedUser()) return $this->e404();
?>

<div class="back"><a href="javascript:history.back()">< Retour</a></div>

<form id="profile">
    <table class="form">
        <thead>
            <tr>
                <th colspan="2">Informations du profil</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Rôle:</td>
                <td><?php echo $user->role_name; ?></td>
            </tr>
            <tr>
                <td>Nom:</td>
                <td><input name="name" type="text" minlength="8" value="<?php echo $this->escape($user->name); ?>" required></td>
            </tr>
            <tr>
                <td>Courriel:</td>
                <td><input name="email" type="email"  autocomplete="username" value="<?php echo $this->escape($user->email); ?>" required></td>
            </tr>
            <tr>
                <td>Mot de passe:</td>
                <td><input type="password" name="password" autocomplete="new-password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Doit contenir au moins 8 caractères, un chiffre, une lettre majuscule et une lettre minuscule."></td>
                
            </tr>
            <tr>
                <td>Confirmation:</td>
                <td><input name="confirm" type="password" autocomplete="new-password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Doit contenir au moins 8 caractères, un chiffre, une lettre majuscule et une lettre minuscule."></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    <input type="submit" name="save" value="Sauvegarder">
                </td>
            </tr>
        </tfoot>
    </table>
</form>

<div id="modal_pass" class="modalform">
    <div class="modalform__box pass">
        <div>Veuillez entrer votre mot de passe:</div>
        <form>
            <input name="username" type="text" autocomplete="username" value="<?php echo $this->escape($user->email); ?>" style="display: none;">
            <input type="submit" value="Confirmer" style="display: none;">    
            <div><input type="password" name="password" autocomplete="current-password" required></div>
        </form>
    </div>
</div>

<div class="profile_anim">
    <div class="profile_anim__user">
        <svg viewBox="0 0 128 128">
            <circle cx="64" cy="64" r="51.2" fill="#a0c8d7"/>
            <g id="character">
                <path fill="#e0e0e0" d="M107 120H21l6-34c1-7 7-13 14-14l23-4 23 4c7 1 13 7 14 14l6 34z"/>
                <g fill="#c33736">
                    <path d="m71 83-7 6-7-6V72h14v11z"/>
                    <path d="M71 120H57l4-34h6l4 34z"/>
                </g>
                <path fill="#fff" d="m55 70-5-5-6 7 13 15 5-14-7-3zm18 0 5-5 6 7-13 15-5-14 7-3z"/>
                <g>
                    <path fill="#f4e6c8" d="M84 46c0 14-9 29-20 29S44 60 44 46s9-21 20-21 20 7 20 21z"/>
                    <path fill="#323232" d="M64 25c-11 0-20 7-20 21v2c9 0 17-4 23-9 2 6 7 9 13 9l4-1v-1c0-14-9-21-20-21z"/>
                </g>
            </g>
        </svg>
    </div>
</div>

<script><?php $this->includejs('profile'); ?></script>