<?php

class DatabaseParam {
    public $name, $value, $type;
    public function __construct($name, $value, $type = PDO::PARAM_INT){
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }
}