<?php
include('config.inc.php');
include('loader.inc.php');

new PageApp([

    // --->>> ERREUR 404
    new Page([
        'path'   => '404',
        'file'   => 'e404.php',
        'menu'   => false,
        'bypass' => true,
        'public' => true,
    ]),

    // --->>> ERREUR 500
    new Page([
        'path'   => '500',
        'file'   => 'e500.php',
        'menu'   => false,
        'bypass' => true,
        'public' => true,
    ]),

    // --->>> MAINTENANCE
    new Page([
        'path'   => 'maintenance',
        'file'   => 'maintenance.php',
        'menu'   => false,
        'bypass' => true,
        'public' => true,
    ]),

    // --->>> TUNNEL
    new Page([
        'path'   => 'tunnel',
        'file'   => 'tunnel.php',
        'menu'   => false,
        'bypass' => true,
        'public' => true,
        'nopost' => true,
    ]),

    // --->>> SCHEMA
    new Page([
        'path'   => 'schema',
        'file'   => 'schema.php',
        'menu'   => false,
        'bypass' => true,
        'nopost' => true,
        'access' => ['dev', 'admin'],
    ]),

    // --->>> CONNEXION
    new Page([
        'path'   => 'connexion',
        'file'   => 'login.php',
        'menu'   => false,
        'bypass' => true,
        'public' => true,
    ]),

    // --->>> DÉCONNEXION
    new Page([
        'path'   => 'deconnexion',
        'name'   => '<span style="font-size: 0.8em">Déconnexion</span>',
        'file'   => 'logout.php',
        'menu'   => false,
        'public' => true,
        'bypass' => true,
    ]),

    // --->>> PRÉFÉRENCES
    new Page([
        'path'   => 'profil',
        'name'   => 'Profil',
        'title'  => 'Profil utilisateur',
        'file'   => 'profile.php',
        'menu'   => false,
        'access' => ['dev', 'admin', 'concealer', 'editor'],
    ]),

    // --->>> MESSAGES
    new Page([
        'path'   => 'messages',
        'name'   => 'Messages',
        'title'  => 'Boîte de réception',
        'file'   => 'messages/messages.php',
        'menu'   => false,
        'access' => ['dev', 'admin', 'concealer', 'editor'],
    ]),
    new Page([
        'path'   => 'messages/envoyes',
        'name'   => 'Messages',
        'title'  => 'Boîte d\'envoi',
        'file'   => 'messages/messages_outbox.php',
        'menu'   => false,
        'access' => ['dev', 'admin', 'concealer', 'editor'],
    ]),



    // --->>> ACCUEIL
    new Page([
        'path'   => '',
        'name'   => 'Accueil',
        'title'  => 'Générateur de questionnaires',
        'file'   => 'home.php',
        'menu'   => true,
        'access' => ['dev', 'admin', 'concealer', 'editor'],
    ]),

    // --->>> UTILISATEURS
    new Page([
        'path'   => 'utilisateurs',
        'name'   => 'Utilisateurs',
        'title'  => 'Utilisateurs',
        'file'   => 'users/users.php',
        'menu'   => true,
        'access' => ['dev', 'admin'],
    ]),
    new Page([
        'path'     => 'utilisateurs/ajouter',
        'name'     => 'Ajouter',
        'title'    => 'Ajouter un utilisateur',
        'file'     => 'users/users_add.php',
        'menu'     => false,
        'submenu'  => true,
        'menupath' => 'utilisateurs',
        'access'   => ['dev', 'admin'],
    ]),
    new Page([
        'path'     => 'utilisateurs/modifier',
        'name'     => 'Modifier',
        'title'    => 'Modifier un utilisateur',
        'file'     => 'users/users_edit.php',
        'menu'     => false,
        'submenu'  => true,
        'menupath' => 'utilisateurs',
        'access'   => ['dev', 'admin'],
    ]),

    // --->>> SYSTÈME
    new Page([
        'path'   => 'systeme',
        'name'   => 'Système',
        'title'  => 'Outils système',
        'file'   => 'system.php',
        'menu'   => true,
        'access' => ['dev', 'admin'],
    ]),

    // --->>> TEST
    new Page([
        'path'   => 'test',
        'name'   => 'Testeur',
        'title'  => 'Testeur',
        'file'   => 'tester.php',
        'menu'   => true,
        'access' => ['dev'],
    ]),
    // --->>> PHP INFO
    new Page([
        'path'    => 'phpinfo',
        'name'    => 'PHPinfo',
        'file'    => 'phpinfo.php',
        'menu'    => true,
        'submenu' => true,
        'bypass'  => true,
        'access'  => ['dev'],
    ]),

    // --->>> À PROPOS
    new Page([
        'path'   => 'apropos',
        'name'   => 'À propos',
        'title'  => 'À propos',
        'file'   => 'about.php',
        'menu'   => true,
        'access' => ['dev', 'admin', 'concealer', 'editor'],
    ]),

], SYS::getLoggedUser());