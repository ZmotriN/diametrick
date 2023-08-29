<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', true);
date_default_timezone_set('America/Toronto');
include('libraries/functions.php');

const RN = "\r\n";
const BR = "<br/>";

const PROD        = true;
const MAINTENANCE = false;
const LOREM_IPSUM = false;

const MYSQL_USER  = 'root';
const MYSQL_PASS  = 'root';
const MYSQL_HOST  = 'localhost';
const MYSQL_PORT  = 3306;
const MYSQL_DB    = 'diametrick';

const DELAY_POST  = 0;
const PAGE_SIZE   = 50;
const QUEST_TTL   = '3 years';

const COMPARE_THRESHOLD = 83;

spl_autoload_register(function($class) {
    static $catalog = [
        'DB'         => 'db.class.php',
        'Gabarit'    => 'gabarit.class.php',
        'GEHGen'     => 'gehgen.class.php',
        'Level'      => 'level.class.php',
        'LoremIpsum' => 'loremipsum.class.php',
        'Message'    => 'message.class.php',
        'Mode'       => 'mode.class.php',
        'Normalizer' => 'normalizer.class.php',
        'Question'   => 'question.class.php',
        'Page'       => 'pageapp.class.php',
        'PageApp'    => 'pageapp.class.php',
        'Quiz'       => 'quiz.class.php',
        'Season'     => 'season.class.php',
        'Theme'      => 'theme.class.php',
        'Type'       => 'type.class.php',
        'User'       => 'user.class.php',
    ];
    if (isset($catalog[$class])) require_once(__DIR__ . '/libraries/' . $catalog[$class]);
}, true, true);