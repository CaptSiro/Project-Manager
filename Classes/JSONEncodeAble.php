<?php

  class JSONEncodeAble implements JsonSerializable {
    function jsonSerialize() {
      $t = [];
      foreach($this as $key => $value) {
        if (isset($this->$key)) {
          $t[$key] = $value;
        }
      }
  
      return ((object)$t);
    }
  }