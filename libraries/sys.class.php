<?php

class SYS {

    public static function session() {
        return PROJECT . '_user_id';
    }


    public static function updateLastSeen() {
        if(!empty($_SESSION[self::session()])) DB::exec('UPDATE users SET updated = updated, lastseen = CURRENT_TIMESTAMP WHERE id = '.$_SESSION[self::session()]);
    }


    public static function getLoggedUser() {
        static $user = null;
        if($user === null) {
            if(empty($_SESSION[self::session()])) return null;
            if(!$_SESSION[self::session()]) return null;
            $query = 'SELECT users.*, roles.slug AS role_slug, roles.name AS role_name FROM users INNER JOIN roles ON roles.id = users.role_id WHERE users.id = '.$_SESSION[self::session()].' AND users.active = 1 LIMIT 1';
            if(!$user = DB::query($query, true)) {
                unset($_SESSION[self::session()]);
                return null;
            } else $user = User::load($user);
        }
        return $user;
    }


    public static function login($email, $password) {
        $query = 'SELECT users.*, roles.slug AS role_slug FROM users INNER JOIN roles ON roles.id = users.role_id WHERE users.email = "'.DB::escape($email).'" AND users.pass = "'.DB::escape(sha1($password)).'" AND users.active = 1 LIMIT 1';
        if(!$user = DB::query($query, true)) throw new SYSException("Échec de connexion");
        $_SESSION[self::session()] = $user->id;
        return true;
    }


    public static function logout($root) {
        unset($_SESSION[self::session()]);
        header('location: '.$root.'connexion');
        die();
    }


    public static function getUsers($order='updated DESC, name ASC') {
        foreach(DB::query("SELECT users.*, roles.name AS role_name, users.lastseen FROM users INNER JOIN roles ON users.role_id = roles.id ORDER BY ".$order) as $obj) {
            $users[] = User::load($obj);
        }
        return isset($users) ? $users : [];
    }


    public static function getPageUsers($page=0, $order='updated DESC, name ASC') {
        $query = "SELECT users.*, roles.name AS role_name, users.lastseen FROM users INNER JOIN roles ON users.role_id = roles.id ORDER BY ".$order;
        $page = DB::getPage($query, $page);
        $page->users = [];
        foreach($page->items as $item) $page->users[] = User::load($item);
        unset($page->items);
        return $page;
    }


    public static function getRoles($order='id ASC') {
        return DB::query("SELECT * FROM roles ORDER BY ".$order);
    }


    public static function sendMessage($data) {
        $data += [
            'user_id' => 0,
            'from_id' => 0,
            'subject' => '',
            'body' => '',
        ];
        if($data['user_id'] == -1) {
            foreach(DB::query('SELECT users.id, roles.slug AS role_slug FROM users INNER JOIN roles ON roles.id = users.role_id WHERE users.active = 1') as $user)
                if(User::load($user)->isAdmin())
                    self::sendMessage(['user_id' => $user->id] + $data);
        } elseif(!$data['user_id']) {
            foreach(DB::query('SELECT id FROM users WHERE active = 1') as $user)
                self::sendMessage(['user_id' => $user->id] + $data);
        } else Message::load($data)->save();
    }


    public static function backupDatabase() {
        $tempdir = str_replace('\\','/',sys_get_temp_dir()).'/'.PROJECT.'/';
        $sqldest = $tempdir.PROJECT.'_'.date('YmdHis').'.sql';
        if(!is_dir($tempdir))
            if(!@mkdir($tempdir))
                throw new SYSException("Impossible de créer le fichier temporaire.");
        if(!DB::dump($sqldest)) throw new SYSException("Impossible de sauvegarder la base de données.");
        if(!is_file($sqldest) || !filesize($sqldest)) throw new SYSException("Sauvegarde de base de données invalide.");
        $database = gzencode(file_get_contents($sqldest),9);
        file_put_contents(($gzdest = $sqldest.'.gz'), $database);
        unlink($sqldest);
        return $gzdest;
    }


    public static function downloadFile($file) {
        if(!$tempfile = realpath(str_replace('\\','/',sys_get_temp_dir()).'/'.PROJECT.'/'.pathinfo($file, PATHINFO_BASENAME)))
            throw new SYSException("Fichier invalide.");
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header('Content-Disposition: attachment; filename="' . basename($tempfile) . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');
        header('Content-Length: '.filesize($tempfile));
        readfile($tempfile);
        unlink($tempfile);
        die();
    }

 }

class SYSException extends Exception { }