<?php
if($this->isPost()) {
    if(!$data = $this->getPostData()) $this->outjson(['success' => false, 'errmsg' => "Argument invalide."]);
    try {
        User::load($data)->save();
        $this->outjson(['success' => true]);
    } catch(Exception $e) {
        $this->outjson(['success' => false, 'errmsg' => $e->getMessage()]);
    }
}
$roles = GEHGen::getRoles();
?>

<div class="back"><a href="javascript:history.back()">< Retour</a></div>

<form>
    <table class="form">
        <thead>
            <tr>
                <th colspan="2">Utilisateur</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Role:</td>
                <td>
                    <select name="role_id">
                    <?php foreach($roles as $role): ?>
                        <option value="<?php echo $this->escape($role->id); ?>"><?php echo $this->escape($role->name); ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Nom:</td>
                <td><input name="name" type="text" required></td>
            </tr>
            <tr>
                <td>Courriel:</td>
                <td><input name="email" type="email" required></td>
            </tr>
            <tr>
                <td>Mot de passe:</td>
                <td><input name="password" type="password" value="" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Doit contenir au moins 8 caractères, un chiffre, une lettre majuscule et une lettre minuscule." required></td>
            </tr>
            <tr>
                <td>Actif:</td>
                <td><input name="active" type="checkbox" checked></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    <input type="submit" name="save" value="Sauvegarder">
                    <input type="button" name="cancel" value="Annuler">
                </td>
            </tr>
        </tfoot>
    </table>
</form>

<script>
    const app = {
        init: function() {
            bind('form', 'submit', (evt) => { evt.preventDefault(); this.submit(); });
            bind('form input[name="cancel"]', 'click', (evt) => { redirect('utilisateurs'); });
            query('form input[name="name"]').focus();
        },
        submit: function(){
            postmodal('utilisateurs', {
                name: query('form input[name="name"]').value,
                email: query('form input[name="email"]').value,
                pass: query('form input[name="password"]').value,
                role_id: query('form select[name="role_id"]').value,
                active: query('form input[name="active"]').checked ? 1 : 0,
            });
        }
    }
    app.init();
</script>