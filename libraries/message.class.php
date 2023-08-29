<?php

class Message implements JsonSerializable {

    private $data = [];


    public function __construct(array $data) {
        $this->data = $data;
    }


    public function __get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }


    public function __set($key, $val) {
        $this->data[$key] = $val;
    }

    public function jsonSerialize() {
        return $this->data;
    }

    public static function load($data) {
        if(is_array($data)) {
            return new self($data);
        } elseif(is_object($data)) {
            return new self((array)$data);
        } elseif(is_numeric($data)) {
            if(!$obj = DB::getObject('messages', $data)) throw new MessageException("ID de message invalide.");
            return new self((array)$obj);
        } else {
            throw new MessageException("Data invalide.");
        }
    }

    
    public function save() {
        if(isset($this->data['body'])) $this->data['body'] = trim(strip_tags($this->data['body'], '<b><i><u><sub><sup><p><div><h3><h4><em><br><a>'));        
        if($this->id) return $this->update();
        else return $this->insert();
    }


    private function insert() {
        if(!$this->user_id) throw new MessageException("ID d'utilisateur invalide.");
        if(!$this->subject) throw new MessageException("Sujet invalide.");
        if(!$this->body) throw new MessageException("Message invalide.");
        if(!$id = DB::insert('messages', $this->data)) throw new MessageException("Impossible d'envoyer le message.");
        $this->data['id'] = $id;
        return $this;
    }


    private function update() {
        if(!$this->id) throw new MessageException("ID de message invalide.");
        $data = $this->data;
        unset($data['id'], $data['updated']);
        if(!DB::update('messages', ['id' => $this->id], $data)) throw new MessageException("Impossible de mettre à jour le message.");
        else return $this;
    }


    public function delete() {
        if(!$this->id) throw new MessageException("ID de message invalide.");
        if(!DB::update('messages', ['id' => $this->id], ['hidden' => 1]))  throw new MessageException("Impossible de supprimer le messasge.");
        return $this;
    }


    public function markAsRead() {
        if(!$this->id) throw new MessageException("ID de message invalide.");
        if(!DB::update('messages', ['id' => $this->id], ['unread' => 0]))  throw new MessageException("Impossible de supprimer le messasge.");
        return $this;
    }

}

class MessageException extends Exception { }