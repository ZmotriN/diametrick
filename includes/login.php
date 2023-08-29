<?php
if($this->isPost()) {
    if(!$data = $this->getPostData()) $this->outjson(['success' => false, 'errmsg' => "Argument invalide."]);
    if(empty($data->email)) $this->outjson(['success' => false, 'errmsg' => "Courriel invalide."]);
    if(empty($data->password)) $this->outjson(['success' => false, 'errmsg' => "Mot de passe invalide."]);
    try {
        GEHGen::login($data->email, $data->password);
        $this->outjson(['success' => true]);
    } catch(Exception $e) {
        $this->outjson(['success' => false, 'errmsg' => $e->getMessage()]);
    }
}
$lightmode = !empty($_COOKIE['lightmode']) && $_COOKIE['lightmode'] == 'true';
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?php echo $this->root; ?>favicon<?php echo $lightmode ? '_light' : ''; ?>.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $this->root; ?>styles/styles.css">
    <script src="<?php echo $this->root; ?>jscripts/commons.js"></script>
    <script>const root = '<?php echo $this->root; ?>';</script>
    <title>Connexion</title>
</head>
<body>
    <script>if(localStorage.getItem('lightmode') === 'true') document.body.classList.add('light');</script>
    <div class="login_container">
        <div class="login_splash">
            <form id="login">
                <table class="form">
                    <thead>
                        <tr>
                            <th colspan="2">Connexion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Courriel:</td>
                            <td><input type="email" name="email" required></td>
                        </tr>
                        <tr>
                            <td>Mot de passe:</td>
                            <td><input type="password" name="password" required></td>
                        </tr>
                        <tr class="login_msg">
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2"><input type="submit" name="submit" value="Soumettre"></td>
                        </tr>
                    </tfoot>
                </table>
            </form>
        </div>
    </div>
    <div id="modal" class="modal">
        <div class="modal__loading"></div>
    </div>
    <script><?php $this->includejs('login'); ?>app.init();</script>
</body>
</html>