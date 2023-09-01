<?php
$mailclass = ['mail'];
if($this->page->path == 'messages' || $this->page->path == 'messages/envoyes') $mailclass[] = 'inside';
if(SYS::getLoggedUser()->hasUnreadMessages()) $mailclass[] = 'new';
$lightmode = !empty($_COOKIE['lightmode']) && $_COOKIE['lightmode'] == 'true';
?><!DOCTYPE html>
<html lang="fr-ca">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?php echo $this->root; ?>favicon<?php echo $lightmode ? '_light' : ''; ?>.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $this->root; ?>styles/styles<?php echo PROD ? '.min.css' : '.css' ?>">
    <script src="<?php echo $this->root; ?>jscripts/commons<?php echo PROD ? '.min.js' : '.js' ?>"></script>
    <script src="<?php echo $this->root; ?>jscripts/pell<?php echo PROD ? '.min.js' : '.js' ?>"></script>
    <script>const root='<?php echo $this->root; ?>';</script>
    <title><?php echo $this->page->title; ?></title>
</head>
<body<?php echo $lightmode ? ' class="light"' : ''; ?>>
    <script>if(localStorage.getItem('lightmode')==='true')document.body.className='light';</script>
     <header>
        <div class="logo"></div>
        <div class="title"><h1><?php echo $this->page->title; ?></h1></div>
        <div class="<?php echo join(' ', $mailclass); ?>" title="Boîte de réception"></div>
        <div class="logout"><a href="<?php echo $this->root; ?>profil"><?php echo $this->user->name; ?></a>&nbsp;/&nbsp;<a href="<?php echo $this->root; ?>deconnexion">Déconnexion</a></div>
    </header>
    <main>
        <div class="menu"><?php include('menu.php'); ?></div>
        <div class="content" id="content">
