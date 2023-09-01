<?php

class User {
    
    private $data = [];


    public function __construct(array $data) {
        $this->data = $data;

    }


    public function __get($key) {
        switch($key) {
            case 'role_slug': return $this->getRoleSlug();
            default: return isset($this->data[$key]) ? $this->data[$key] : null;
        }
    }


    public function __set($key, $val) {
        $this->data[$key] = $val;
    }


    public static function load($data) {
        if(is_array($data)) {
            return new self($data);
        } elseif(is_object($data)) {
            return new self((array)$data);
        } elseif(is_numeric($data)) {
            if(!$obj = DB::getObject('users', $data)) throw new UserException("ID d'utilisateur invalide.");
            unset($obj->pass);
            return new self((array)$obj);
        } else {
            throw new UserException("Data invalide.");
        }
    }


    public function getRoleSlug() {
        if(!isset($this->data['role_slug'])) {
            if(!isset($this->data['role_id'])) return false;
            $this->data['role_slug'] = DB::query('SELECT slug FROM roles WHERE id = '.$this->role_id, true, true);
        }
        return $this->data['role_slug'];
    }


    public function isDev() {
        return $this->role_slug == 'dev';
    }


    public function isAdmin() {
        return in_array($this->role_slug, ['dev', 'admin', 'concealer']);
    }


    public function isSuperAdmin(){
        return in_array($this->role_slug, ['dev', 'admin']);
    }


    public function hasUnreadMessages() {
        return DB::query('SELECT COUNT(*) FROM messages WHERE hidden = 0 AND unread = 1 AND user_id = '.$this->id, true, true) ? true : false;
    }


    public function getMessages() {
        foreach(DB::query('SELECT messages.*, users.name AS from_name FROM messages LEFT JOIN users ON messages.from_id = users.id WHERE messages.hidden = 0 AND messages.user_id = '.$this->id.' ORDER BY messages.sent DESC') as $message) {
            if(!$message->from_name) $message->from_name = 'MPGHP';
            $messages[] = Message::load($message);
        }
        return $messages ?? [];
    }


    public function getSentMessages() {
        foreach(DB::query('SELECT messages.*, users.name AS user_name FROM messages LEFT JOIN users ON messages.user_id = users.id WHERE messages.from_id = '.$this->id.' ORDER BY messages.sent DESC LIMIT '.PAGE_SIZE) as $message) {
            if(!$message->user_name) $message->user_name = 'MPGHP';
            $messages[] = Message::load($message);
        }
        return $messages ?? [];
    }


    public function sendMessage(array $data) {
        $data['from_id'] = $this->id;
        if($data['parent_id']) {
            $parent = Message::load($data['parent_id']);
            $data['body'] .= '<div class="reply_body">';
            $data['body'] .= '<h3>'.User::load($parent->from_id)->name.'&nbsp;<em>('.$parent->sent.')</em></h3>';
            $data['body'] .= $parent->body;
            $data['body'] .= '</div>';
        }
        SYS::sendMessage($data);
        return $this;
    }


    public function save() {
        if(empty($this->data['pass'])) unset($this->data['pass']);
        else $this->data['pass'] = sha1($this->data['pass'] );
        if($this->id) return $this->update();
        else return $this->insert();
    }


    private function insert() {
        if(!$this->name) throw new UserException("Nom d'utilisateur invalide.");
        if(!$this->verifyName()) throw new UserException("Nom d'utilisateur déjà utilisé.");
        if(!$this->email) throw new UserException("Courriel invalide.");
        if(!$this->verifyEmail()) throw new UserException("Courriel déjà utilisé.");
        if(!$id = DB::insert('users', $this->data)) throw new UserException("Impossible d'ajouter l'utilisateur.");
        $this->data['id'] = $id;
        return true;
    }


    private function update() {
        if(!$this->id) throw new UserException("ID d'utilisateur invalide.");
        if(!$this->name) throw new UserException("Nom d'utilisateur invalide.");
        if(!$this->verifyName())  throw new UserException("Nom d'utilisateur déjà utilisé.");
        if(!$this->email) throw new UserException("Courriel invalide.");
        if(!$this->verifyEmail()) throw new UserException("Courriel déjà utilisé.");
        $data = $this->data;
        unset($data['id'], $data['updated']);
        if(!DB::update('users', ['id' => $this->id], $data)) throw new UserException("Impossible de mettre à jour l'utilisateur.");
        return true;
    }


    public function delete() {
        if(!$this->id) throw new UserException("ID d'utilisateur invalide.");
        if(!DB::delete('users', $this->id)) throw new UserException("Impossible de supprimer l'utilisateur.");
        return true;
    }


    public function verifyPassword($password) {
        return boolval(DB::query('SELECT COUNT(*) FROM users WHERE id = '.$this->id.' AND pass = "'.DB::escape(sha1($password)).'"', true, true));
    }


    public function fixEncoding() {
        if(!htmlentities($this->name))
            $this->name = utf8_encode($this->name);
    }


    private function verifyName() {
        if($this->id) {
            if(DB::query('SELECT COUNT(*) as nb FROM users WHERE name = "'.DB::escape($this->name).'" AND id <> ' . $this->id . ';', true, true)) return false;
            else return true;
        } else {
            if(DB::query('SELECT COUNT(*) as nb FROM users WHERE name = "'.DB::escape($this->name).'";', true, true)) return false;
            else return true;
        }
    }


    private function verifyEmail() {
        if($this->id) {
            if(DB::query('SELECT COUNT(*) as nb FROM users WHERE email = "'.DB::escape($this->email).'" AND id <> ' . $this->id . ';', true, true)) return false;
            else return true;
        } else {
            if(DB::query('SELECT COUNT(*) as nb FROM users WHERE email = "'.DB::escape($this->email).'";', true, true)) return false;
            else return true;
        }
    }

}


class UserException extends Exception { }