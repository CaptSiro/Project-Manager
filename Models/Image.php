<?php

  require_once(Path::translate("./Models/StrictModel.php"));


  class Image extends StrictModel {
    protected $ID, $source, $unitsID;
    protected static function getNumberProps(): array {
      return ["ID", "unitsID"];
    }
  }