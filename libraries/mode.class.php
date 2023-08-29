<?php

class Mode {


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
            if(!$obj = DB::getObject('modes', $data)) throw new ModeException("ID de type invalide.");
            return new self((array)$obj);
        } else {
            throw new ModeException("Data invalide.");
        }
    }

}

class ModeException extends Exception { }