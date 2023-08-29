<?php

class Theme {
    
    private $data = [];
    public $categories = null;


    public function __construct(array $data) {
        if(isset($data['categories'])){
            $this->categories = $data['categories'];
            unset($data['categories']);
        }
        $this->data = $data;
    }


    public function __get($key) {
        switch($key) {
            // case 'categories': return $this->getCategories();
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
            if(!$obj = DB::getObject('themes', $data)) throw new ThemeException("ID de thématique invalide.");
            $theme = new self((array)$obj);
            $theme->categories = DB::query("SELECT id, name FROM categories WHERE theme_id = " . $data . " ORDER BY name ASC");
            return $theme;
        } else {
            throw new ThemeException("Data invalide.");
        }
    }

    
    public function getCategories() {
        if(is_null($this->categories)) {
            $this->categories = DB::query("SELECT id, name FROM categories WHERE theme_id = " . $this->id . " ORDER BY name ASC");
        }
        return $this->categories;
    }


    public function fixEncoding() {
        if(!htmlentities($this->name))
            $this->name = utf8_encode($this->name);
    }


    public function save() {
        if($this->id) return $this->update();
        else return $this->insert();
    }


    private function saveCategories() {
        if(!is_array($this->categories)) return;
        foreach($this->categories as $category) {
            if($category->id == 0) {
                if(!$id = DB::insert('categories', ['theme_id' => $this->id, 'name' => $category->name])) throw new ThemeException("Impossible d'ajouter la catégorie \"".$category->name."\".");
                $category->id = $id;
            } else {
                if(!DB::update('categories', ['id' => $category->id], ['name' => $category->name])) throw new ThemeException("Impossible de mettre à jour la catégorie \"".$category->name."\".");
            }
        }
        foreach($this->categories as $category) $ids[] = $category->id;
        // Vérifier si category utilisé dans des questions
        if(!empty($ids)) DB::exec("DELETE FROM categories WHERE theme_id = ".$this->id." AND id NOT IN (".join(',', $ids).");");
        else DB::exec("DELETE FROM categories WHERE theme_id = ".$this->id.";");
        return true;
    }


    private function insert() {
        if(!$this->name) throw new ThemeException("Nom de thématique invalide.");
        if(!$this->verifyName()) throw new ThemeException("Nom de thématique déjà utilisé.");
        if(!$id = DB::insert('themes', $this->data)) throw new ThemeException("Impossible d'ajouter la thématique.");
        $this->data['id'] = $id;
        $this->saveCategories();
        return true;
    }
    

    private function update() {
        if(!$this->id) throw new ThemeException("ID de thématique invalide.");
        if(!$this->name) throw new ThemeException("Nom de thématique invalide.");
        if(!$this->verifyName())  throw new ThemeException("Nom de thématique déjà utilisé.");
        $data = $this->data;
        unset($data['id'], $data['updated']);
        if(!DB::update('themes', ['id' => $this->id], $data)) throw new ThemeException("Impossible de mettre à jour la thématique.");
        $this->saveCategories();
        return true;
    }


    public function delete() {
        if(!$this->id) throw new ThemeException("ID de thématique invalide.");
        // verifier si questions
        if(!DB::exec("DELETE FROM categories WHERE theme_id = ".$this->id.";")) throw new ThemeException("Impossible de supprimer les catégories de la thématique.");
        if(!DB::delete('themes', $this->id)) throw new ThemeException("Impossible de supprimer la thématique.");
        return true;
    }


    private function verifyName() {
        if($this->id) {
            if(DB::query('SELECT COUNT(*) as nb FROM themes WHERE name = "'.DB::escape($this->name).'" AND id <> ' . $this->id . ';', true, true)) return false;
            else return true;
        } else {
            if(DB::query('SELECT COUNT(*) as nb FROM themes WHERE name = "'.DB::escape($this->name).'";', true, true)) return false;
            else return true;
        }
    }


}

class ThemeException extends Exception { }