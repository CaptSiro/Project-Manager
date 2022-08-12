<?php

  require_once(Path::translate("./Models/StrictModel.php"));
  require_once(Path::translate("./Models/Unit.php"));


  class ChildUnit extends StrictModel {
    protected $ID;
    protected static function getNumberProps(): array {
      return ["ID"];
    }
  }