<?php
if($this->isPost()) {
    $this->outjson(['success' => false, 'errmsg' => 'Erreur 404']);
}
$lightmode = !empty($_COOKIE['lightmode']) && $_COOKIE['lightmode'] == 'true';
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?php echo $this->root; ?>favicon<?php echo $lightmode ? '_light' : ''; ?>.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kavoon&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $this->root; ?>styles/styles.css">
    <script src="<?php echo $this->root; ?>jscripts/bundle.js"></script>
    <script>const root = '<?php echo $this->root; ?>';</script>
    <title>Erreur 404</title>
    <style>
        .marmotte {
            background-image: url(data:image/webp;base64,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);
        }
        .terre {
            background-image: url(data:image/webp;base64,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);
        }
    </style>
</head>
<body<?php echo !empty($_COOKIE['lightmode']) && $_COOKIE['lightmode'] == 'true' ? ' class="light"' : ''; ?>>
    <script>if(localStorage.getItem('lightmode') === 'true') document.body.classList.add('light');</script>
    <div class="splash_container">
        <div class="splash">
            <div class="splash__title">Erreur 404</div>
            <div class="champ">
                <?php for($i = 0; $i < 12; $i++): ?>
                    <div class="trou-container"><div class="trou"><div class="terre"></div><div class="marmotte"></div></div></div>
                <?php endfor; ?>
            </div>
            <div class="splash__legend"><a href="javascript:history.back()">Retour</div>
        </div>
    </div>
</body>
</html>

<script>
<?php $this->includejs('e404'); ?>
app.init();
</script>