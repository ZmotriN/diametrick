<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('content-type: application/json');
    header('X-Robots-Tag: noindex');
    echo json_encode(['success' => false, 'errmsg' => 'Erreur 500'], JSON_PRETTY_PRINT);
    die();
}
$root = trim(str_replace('\\', '/', pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME)), '.');
if(empty($root)) $root = '/';
elseif($root != '/') $root .= '/';
$lightmode = !empty($_COOKIE['lightmode']) && $_COOKIE['lightmode'] == 'true';
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?php echo $root; ?>favicon<?php echo $lightmode ? '_light' : ''; ?>.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>>
    <link href="https://fonts.googleapis.com/css2?family=Kavoon&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $root; ?>styles/styles.css">
    <script src="<?php echo $root; ?>jscripts/diametrick.bundle.js"></script>
    <script>const root = '<?php echo $root; ?>';</script>
    <title>Erreur 500</title>
</head>
<body<?php echo !empty($_COOKIE['lightmode']) && $_COOKIE['lightmode'] == 'true' ? ' class="light"' : ''; ?>>
    <script>if(localStorage.getItem('lightmode') === 'true') document.body.classList.add('light');</script>
    <div class="splash_container">
        <div class="splash">
            <div class="splash__title">Erreur 500</div>
            <div class="splash__contents">
                <svg class="shrug" viewBox="0 0 285 436">
                    <path d="M96 221v6l3 9v5l1 20 2 12 10 38 1 7v16l2 14 4 17 4 14 1 2-1 4v8l2 8s-1 8 2 8v-1c-1-1-3 3-4 3h-4l-3 2 1 4-2 1 1 3c3 2 6 2 10 2h9l9-3 2-2 7-1 6-1 4-2-1-7v-1l1-1v-3l1-11-1-10-4-15-4-12 1-15v-18l-3-7-2-2h1l1-23-2-10 2-18 3 13 1 19 2 12 2 16 1 19 4 31 2 9 1 2s-4 9-4 14c1 7 8 18 8 18l2 3-1 3v5l6 4 3 5c4 2 15 1 15 1h8l3-3-1-6-3-2-1-5 5-14v-10l-1-3-1-12-2-22-2-28-2-15v-30l-1-22-2-9v-17l-1-15z"/>
                    <path d="M98 222s-4-7-3-10c0-2 3-5 3-5l2-13-3-19v-21l-5 8-2 8-6 5-7 2c-5-1-7-6-10-8-4-3-8-4-10-7-3-4 0-10-3-12-2-2-6 1-7 0-3-3 0-11 0-11h-9l-5 4H23l-8 2c-4-1-9-4-9-7-1-1 1-3 1-3h5l2-1 4-1 4 2 5-2 3-3-2-1-6 1-3-1c-1-1-3-2-2-3h1l5-2h6l10-3h4l6 3s3-3 5-3l3 3 2 4 8 3 6 9v-7l6-20 2-6 2-12c2-5 4-10 7-13 3-4 8-7 13-8h3l10-3 10-5-1-4-1-4 1-1 2-3v-5l-4-4-2-7v-5l1-5h2l1-10 4-7 6-5c3-2 6-2 10-2h10c3 0 5 0 8 2l3 2 4 5 4 8v7l-2 7-2 5v6l-2 6-2 4-2-1v8l5 3 8 3h3l5 4 8 1 5 3 6 6 5 7 4 8 4 12 5 12 2 11 2-2 2-5 5-5 5-1 4-2 7-2 6 1 8-4 3 1v2l-6 5h-4l-1 2 4 3 7 2 9 1h3v3l-2 2-2 2h-2l-3 2-5-1-4 2-6-1h-8l-1 6-2 6-4 8-11 12s-2 3-4 3l-4-4-7-3-5-5-1-4h-3c-2-1-3-6-3-6l-2-2c-1-2 1-5 1-5l-3-1-2 35 2 17 1 19v13z"/>
                </svg>
            </div>
            <?php if(!empty($msg)): ?><div class="splash__legend"><?php echo $msg; ?></div><?php endif; ?>
        </div>
    </div>
</body>
</html>
