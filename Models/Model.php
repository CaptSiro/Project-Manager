<?php 

  require_once(Path::translate("./Classes/JSONEncodeAble.php"));
  require_once(Path::translate("./Database/Database.php"));


  class Model extends JSONEncodeAble {
    public function propExists ($propName) {
      $objectProps = get_object_vars($this);

      if (!array_key_exists($propName, $objectProps)) {
        throw new Exception("Property '{$propName}' does not exist for " . get_class($this) . ":[" . join(", ", array_keys($objectProps)) . "].");
        return false;
      }

      return true;
    }

    public function __get($propName) {
      if ($this->propExists($propName)) {
        return $this->$propName;
      }
    }
    
    public function __set($propName, $value) {
      if ($this->propExists($propName)) {
        return $this->$propName = $value;
      }
    }
  }