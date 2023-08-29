<?php


class PageApp {

    const INCLUDE_PATH  = 'includes/';
    const TEMPLATE_PATH = 'templates/';

    private $pages;
    public $root;
    public $path;
    public $page;
    public $user;


    public function __construct(array $pages, $user=null) {
        $this->pages = $pages;
        $this->user = $user;
        $this->route();
    }


    public function route() {
        $root = trim(str_replace('\\', '/', pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME)), '.');
        if(empty($root)) $root = '/';
        elseif($root != '/') $root .= '/';
        $this->root = $root;

        if(empty($_GET['path'])) $path = '';
        else $path = trim($_GET['path'], '/');
        $this->path = $path;
        
        foreach($this->pages as $page) {
            if($page->isPath($path)) {
                $this->page = $page;
                break;
            }
        }

        if(empty($this->page)) {
            if($this->user) $this->e404();
            else $this->gotoLogin();
        } elseif($this->page->public || ($this->user && in_array($this->user->role_slug, $this->page->access))) {
            $this->render();
        } elseif($this->user) {
            $this->e404();
        } else {
            $this->gotoLogin();
        }
    }


    private function gotoLogin() {
        if($this->isPost()) $this->outjson(['success' => false, 'errmsg' => "Échec de connexion"]);
        header('location: '.$this->root.'connexion');
        die();
    }


    private function render() {
        header('X-Robots-Tag: noindex');
        if($this->isPost() && defined('DELAY_POST') && DELAY_POST) usleep(DELAY_POST);
        if($this->page->bypass || $this->isPost()) $this->include(self::INCLUDE_PATH . $this->page->file);
        else {
            ob_start();
            $this->include(self::INCLUDE_PATH . $this->page->file);
            $contents = ob_get_clean();
            $this->include(self::TEMPLATE_PATH . 'header.php');
            echo $contents;
            $this->include(self::TEMPLATE_PATH . 'footer.php');
        }
    }


    private function include($file) {
        $incfile = $_SERVER['DOCUMENT_ROOT'] . $this->root . $file;
        if(is_file($incfile)) include($incfile);
    }


    private function includejs($file) {
        $incfile = $_SERVER['DOCUMENT_ROOT'] . $this->root . self::INCLUDE_PATH . $file.(PROD ? '.min.js' : '.js');
        if(is_file($incfile)) readfile($incfile);
    }


    private function includephp($file) {
        $incfile = $_SERVER['DOCUMENT_ROOT'] . $this->root . self::INCLUDE_PATH . $file.'.php';
        if(is_file($incfile)) readfile($incfile);
    }


    public function isPost() {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }


    public function getPostData() {
        return @json_decode(@file_get_contents('php://input'));
    }


    public function error($msg) {
        echo '<div class="back"><a href="javascript:history.back();">< Retour</a></div><div class="error">' . $msg . '</div>';
        return true;
    }


    public function escape($str) {
        $str = htmlentities($str);
        return $str;
    }


    public function outjson($obj) {
        header('content-type: application/json');
        header('X-Robots-Tag: noindex');
        echo json_encode($obj, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);
        die();
    }


    public function e404() {
        if($this->isPost()) $this->outjson(['success' => false, 'errmsg' => "Erreur 404"]);
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
        header('X-Robots-Tag: noindex');
        $this->include(self::INCLUDE_PATH . 'e404.php');
        die();
    }


    public static function e500($msg) {
        header($_SERVER["SERVER_PROTOCOL"] . " 500 " . $msg, true, 500);
        header('X-Robots-Tag: noindex');
        include(realpath(__DIR__ . '/../' . self::INCLUDE_PATH . '/e500.php'));
        die();
    }


    public static function maintenance() {
        if($_SERVER['REMOTE_ADDR'] != '127.0.0.1' && !preg_match('#/tunnel$#i', $_SERVER['REQUEST_URI'])) {
            header('X-Robots-Tag: noindex');
            include(realpath(__DIR__ . '/../' . self::INCLUDE_PATH . '/maintenance.php'));
            die();
        }
    }
    
}


class Page {

    const DEFAULT = [
        'path'     => '',
        'title'    => '',
        'name'     => '',
        'file'     => '',
        'public'   => false,
        'menu'     => false,
        'submenu'  => false,
        'bypass'   => false,
        'spacer'   => false,
        'menupath' => '&',
        'access'   => [],
    ];

    private $props = [];

    public function __construct(array $props) {
        $this->props = array_merge(self::DEFAULT, $props);
    }

    public function __get($key) {
       return isset($this->props[$key]) ? $this->props[$key] : null;
    }

    public function isPath($path) {
        return $path == $this->path;
    }

    
}