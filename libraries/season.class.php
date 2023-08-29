<?php

class Season {

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
            if(!$obj = DB::getObject('seasons', $data)) throw new SeasonException("ID de saison invalide.");
            return new self((array)$obj);
        } else {
            throw new SeasonException("Data invalide.");
        }
    }


    public function save() {
        if($this->id) return $this->update();
        else return $this->insert();
    }


    private function insert() {
        if(!$this->name) throw new SeasonException("Nom de saison invalide.");
        if(!$this->verifyName()) throw new SeasonException("Nom de saison déjà utilisé.");
        if(!$id = DB::insert('seasons', $this->data)) throw new SeasonException("Impossible d'ajouter la saison.");
        $this->data['id'] = $id;
        if($this->active) DB::exec("UPDATE seasons SET active = 0 WHERE id <> " . $this->id);
        return true;
    }


    private function update() {
        if(!$this->id) throw new SeasonException("ID de saison invalide.");
        if(!$this->name) throw new SeasonException("Nom de saison invalide.");
        if(!$this->verifyName())  throw new SeasonException("Nom de saison déjà utilisé.");
        $data = $this->data;
        unset($data['id'], $data['updated']);
        if(!DB::update('seasons', ['id' => $this->id], $data)) throw new SeasonException("Impossible de mettre à jour la saison.");
        if($this->active) DB::exec("UPDATE seasons SET active = 0 WHERE id <> " . $this->id);
        return true;
    }


    public function delete() {
        if(!$this->id) throw new SeasonException("ID de saison invalide.");
        if($this->active) throw new SeasonException("Impossible de supprimer une saison active.");
        if(!DB::delete('seasons', $this->id))  throw new SeasonException("Impossible de supprimer la saison.");
        // supprimer les questionnaires et gabarits
        return true;
    }


    private function verifyName() {
        if($this->id) {
            if(DB::query('SELECT COUNT(*) as nb FROM seasons WHERE name = "'.DB::escape($this->name).'" AND id <> ' . $this->id . ';', true, true)) return false;
            else return true;
        } else {
            if(DB::query('SELECT COUNT(*) as nb FROM seasons WHERE name = "'.DB::escape($this->name).'";', true, true)) return false;
            else return true;
        }
    }


}

class SeasonException extends Exception { }