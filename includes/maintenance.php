<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('content-type: application/json');
    header('X-Robots-Tag: noindex');
    echo json_encode(['success' => false, 'errmsg' => 'Maintenance'], JSON_PRETTY_PRINT);
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
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kavoon&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $root; ?>styles/styles.css">
    <script src="<?php echo $root; ?>jscripts/diametrick.bundle.js"></script>
    <script>const root = '<?php echo $root; ?>';</script>
    <title>Maintenance</title>
</head>
<body<?php echo !empty($_COOKIE['lightmode']) && $_COOKIE['lightmode'] == 'true' ? ' class="light"' : ''; ?>>
    <script>if(localStorage.getItem('lightmode') === 'true') document.body.classList.add('light');</script>
    <div class="splash_container">
        <div class="splash">
            <div class="splash__title">Maintenance</div>
            <div class="splash__contents">
                <svg class="maintenance" viewBox="0 0 512 512">
                    <path d="M494 428 213 153a111 111 0 0 0 12-30l1-4A111 111 0 0 0 167 0h-1l-9 2-4 3-1 5 20 70c0 2-1 5-3 7l-44 32-4 1-47-7c-3 0-5-2-5-4L48 35c-1-3-4-5-7-4l-12 3-2 1C13 54 7 76 6 98a110 110 0 0 0 128 109l13-2 11-4 278 285a41 41 0 1 0 58-58zm-15 43a20 20 0 1 1-28-28 20 20 0 0 1 28 28z"/>
                    <path d="m406 136 46-11 44-77-22-20-21-20-74 48-9 46-79 83 36 35zM178 280c-9 8-22-2-39 16L28 413a58 58 0 0 0 85 81l111-117c17-18 7-30 14-40l4-4-58-60-6 7z"/>
                </svg>
            </div>
            <?php if(!empty($msg)): ?><div class="splash__legend"><?php echo $msg; ?></div><?php endif; ?>
        </div>
    </div>
</body>
</html>
