<?php

class Gabarit {

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
            if(!$obj = DB::getObject('gabarits', $data)) throw new GabaritException("ID de gabarit invalide.");
            $obj->sections = json_decode($obj->sections);
            return new self((array)$obj);
        } else {
            throw new GabaritException("Data invalide.");
        }
    }

    
    public function save() {
        if($this->id) return $this->update();
        else return $this->insert();
    }


    private function insert() {
        if(!$this->name) throw new GabaritException("Nom de gabarit invalide.");
        if(!$this->season_id) throw new GabaritException("ID de saison invalide.");
        if(!$this->verifyName()) throw new GabaritException("Nom de gabarit déjà utilisé pour cette saison.");
        $data = $this->data;
        $data['sections'] = json_encode($data['sections']);
        if(!$id = DB::insert('gabarits', $data)) throw new GabaritException("Impossible d'ajouter le gabarit.");
        $this->data['id'] = $id;
        return true;
    }


    private function update() {
        if(!$this->id) throw new GabaritException("ID de gabarit invalide.");
        if(!$this->name) throw new GabaritException("Nom de gabarit invalide.");
        if(!$this->season_id) throw new GabaritException("ID de saison invalide.");
        if(!$this->verifyName())  throw new GabaritException("Nom de gabarit déjà utilisé pour cette saison.");
        $data = $this->data;
        $data['sections'] = json_encode($data['sections']);
        unset($data['id'], $data['updated']);
        if(!DB::update('gabarits', ['id' => $this->id], $data)) throw new GabaritException("Impossible de mettre à jour le gabarit.");
        else return true;
    }


    public function delete() {
        if(!$this->id) throw new GabaritException("ID de gabarit invalide.");
        // if($this->locked) throw new GabaritException("Impossible de supprimer un gabarit verrouillé.");
        if(DB::query('SELECT COUNT(*) FROM quizzes WHERE gabarit_id = '.$this->id, true, true)) throw new GabaritException("Impossible de supprimer un gabarit déjà utilisé.");
        if(!DB::delete('gabarits', $this->id))  throw new GabaritException("Impossible de supprimer le gabarit.");
        return true;
    }


    public function duplicate() {
        $season_id = DB::query('SELECT id FROM seasons WHERE active = 1 LIMIT 1;', true, true);
        for($i = 1; !$this->verifyName($this->name.' (copie '.$i.')', $season_id); $i++);
        $newgabarit = Gabarit::load([
            'id' => 0,
            'season_id' => $season_id,
            'name' => $this->name.' (copie '.$i.')',
            'sections' => $this->sections,
            'locked' => 0,
        ]);
        $newgabarit->save();
        return $newgabarit;
    }


    private function verifyName($name=null, $season_id=null) {
        if(!$name) $name = $this->name;
        if(!$season_id) $name = $this->season_id;
        if($this->id) {
            if(DB::query('SELECT COUNT(*) as nb FROM gabarits WHERE name = "'.DB::escape($name).'" AND season_id = '.$season_id.' AND id <> ' . $this->id . ';', true, true)) return false;
            else return true;
        } else {
            if(DB::query('SELECT COUNT(*) as nb FROM gabarits WHERE name = "'.DB::escape($name).'" AND season_id = '.$season_id.';', true, true)) return false;
            else return true;
        }
    }

}

class GabaritException extends Exception { }