<?php

class Level {

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


    public static function load($data) {
        if(is_array($data)) {
            return new self($data);
        } elseif(is_object($data)) {
            return new self((array)$data);
        } elseif(is_numeric($data)) {
            if(!$obj = DB::getObject('levels', $data)) throw new LevelException("ID de niveau invalide.");
            return new self((array)$obj);
        } else {
            throw new LevelException("Data invalide.");
        }
    }


    public function save() {
        if($this->id) return $this->update();
        else return $this->insert();
    }


    private function insert() {
        if(!$this->name) throw new LevelException("Nom de niveau invalide.");
        if(!$this->verifyName()) throw new LevelException("Nom de niveau déjà utilisé.");
        if(!$id = DB::insert('levels', $this->data)) throw new LevelException("Impossible d'ajouter le niveau.");
        $this->data['id'] = $id;
        return true;
    }


    private function update() {
        if(!$this->id) throw new LevelException("ID de niveau invalide.");
        if(!$this->name) throw new LevelException("Nom de niveau invalide.");
        if(!$this->verifyName())  throw new LevelException("Nom de niveau déjà utilisé.");
        $data = $this->data;
        unset($data['id'], $data['updated']);
        if(!DB::update('levels', ['id' => $this->id], $data)) throw new LevelException("Impossible de mettre à jour le niveau.");
        else return true;
    }

    
    public function delete() {
        if(!$this->id) throw new LevelException("ID de niveau invalide.");
        if(!DB::delete('levels', $this->id))  throw new LevelException("Impossible de supprimer le niveau.");
        // verifier si questions
        return true;
    }


    private function verifyName() {
        if($this->id) {
            if(DB::query('SELECT COUNT(*) as nb FROM levels WHERE name = "'.DB::escape($this->name).'" AND id <> ' . $this->id . ';', true, true)) return false;
            else return true;
        } else {
            if(DB::query('SELECT COUNT(*) as nb FROM levels WHERE name = "'.DB::escape($this->name).'";', true, true)) return false;
            else return true;
        }
    }


}

class LevelException extends Exception { }