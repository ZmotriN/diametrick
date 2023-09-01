<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
date_default_timezone_set('America/Toronto');
include('libraries/functions.php');

spl_autoload_register(function($class) {
    static $catalog = [
        'DB'         => 'db.class.php',
        'LoremIpsum' => 'loremipsum.class.php',
        'Message'    => 'message.class.php',
        'Normalizer' => 'normalizer.class.php',
        'Page'       => 'pageapp.class.php',
        'PageApp'    => 'pageapp.class.php',
        'SYS'        => 'sys.class.php',
        'User'       => 'user.class.php',
    ];
    if (isset($catalog[$class])) require_once(__DIR__ . '/libraries/' . $catalog[$class]);
}, true, true);

if(MAINTENANCE) PageApp::maintenance();

if(!DB::connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB, MYSQL_PORT))
    PageApp::e500("Connexion à la base de données impossible.");

SYS::updateLastSeen();