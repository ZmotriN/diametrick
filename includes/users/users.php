<?php
if($this->isPost()) {
    if(!$data = $this->getPostData()) $this->outjson(['success' => false, 'errmsg' => "Argument invalide."]);
    if(empty($data->action)) $this->outjson(['success' => false, 'errmsg' => "Argument invalide."]);
    if(empty($data->id)) $this->outjson(['success' => false, 'errmsg' => "ID d'utilisateur invalide."]);

    if($data->action == 'delete') {
        try {
            User::load($data->id)->delete();
            $this->outjson(['success' => true]);
        } catch(Exception $e) {
            $this->outjson(['success' => false, 'errmsg' => $e->getMessage()]);
        }
    } else $this->outjson(['success' => false, 'errmsg' => 'Action invalide.']);
}

$page = SYS::getPageUsers(getPageParam(), 'name ASC');
?>

<button class="list_button add">Ajouter un utilisateur +</button>

<div class="spacer"></div>

<?php echo $page->paging_table; ?>

<table class="list">
    <thead>
        <tr>
            <th class="actions">&nbsp;</th>
            <th>Nom</th>
            <th>Courriel</th>
            <th>Role</th>
            <th class="actions" style="text-align: center;">Accès</th>
            <th class="actions">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($page->users as $user): ?>
        <tr>
            
            <td class="actions"><div title="Supprimer" class="<?php echo $user->active ? 'user' : 'userx'; ?>" data-id="<?php echo $user->id; ?>"></div></td>
            <td><a href="<?php echo $this->root . 'utilisateurs/modifier?id=' . $user->id; ?>"><?php echo $user->name; ?></a></td>
            <td><?php echo $user->email; ?></td>
            <td><?php echo $user->role_name; ?></td>
            <td style="text-align: center;"><?php echo $user->lastseen == '0000-00-00 00:00:00' ? '&nbsp;' : '<span title="'.dateToFrench($user->lastseen ,"l j F Y H:i").'">'.dateago(strtotime($user->lastseen)).'</span>'; ?></td>
            <td class="actions">
                <div title="Modifier" class="edit" data-id="<?php echo $user->id; ?>"></div>
                <?php if($this->user->role_slug == 'dev'): ?>
                    <div title="Supprimer" class="trash" data-id="<?php echo $user->id; ?>"></div>
                <? endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php echo $page->paging_table; ?>

<div class="spacer"></div>

<script>
    const app = {
        init: function() {
            bind('table.list div.edit', 'click', this.edit);
            bind('table.list div.trash', 'click', this.trash);
            bind('button.list_button.add', 'click', this.add);
        },
        add: function(e) {
            redirect('utilisateurs/ajouter');
        },
        edit: function(e) {
            redirect('utilisateurs/modifier?id=' + e.target.dataset.id);
        },
        trash: function(e) {
            ModalBox.confirm("Voulez-vous vraiment supprimer cet utilisateur?", () => {
                postmodal('utilisateurs', {
                    action: 'delete',
                    id: e.target.dataset.id,
                });
            });
        }
    }
    app.init();
</script>