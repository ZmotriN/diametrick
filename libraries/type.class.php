<?php

class Type {


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
            if(!$obj = DB::getObject('types', $data)) throw new TypeException("ID de type invalide.");
            return new self((array)$obj);
        } else {
            throw new TypeException("Data invalide.");
        }
    }


    public function save() {
        if($this->id) return $this->update();
        else return $this->insert();
    }


    private function insert() {
        if(!$this->name) throw new TypeException("Nom de type invalide.");
        if(!$this->verifyName()) throw new TypeException("Nom de type déjà utilisé.");
        if(!$id = DB::insert('types', $this->data)) throw new TypeException("Impossible d'ajouter le type.");
        $this->data['id'] = $id;
        return true;
    }


    private function update() {
        if(!$this->id) throw new TypeException("ID de type invalide.");
        if(!$this->name) throw new TypeException("Nom de type invalide.");
        if(!$this->verifyName())  throw new TypeException("Nom de type déjà utilisé.");
        $data = $this->data;
        unset($data['id'], $data['updated']);
        if(!DB::update('types', ['id' => $this->id], $data)) throw new TypeException("Impossible de mettre à jour le type.");
        else return true;
    }

    
    public function delete() {
        if(!$this->id) throw new TypeException("ID de type invalide.");
        if(!DB::delete('types', $this->id))  throw new TypeException("Impossible de supprimer le type.");
        // verifier si questions
        return true;
    }


    private function verifyName() {
        if($this->id) {
            if(DB::query('SELECT COUNT(*) as nb FROM types WHERE name = "'.DB::escape($this->name).'" AND id <> ' . $this->id . ';', true, true)) return false;
            else return true;
        } else {
            if(DB::query('SELECT COUNT(*) as nb FROM types WHERE name = "'.DB::escape($this->name).'";', true, true)) return false;
            else return true;
        }
    }


}

class TypeException extends Exception { }