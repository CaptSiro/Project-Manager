<?php

require_once(Path::translate("./Models/StrictModel.php"));


  class Unit extends StrictModel {
    protected $ID, $state, $description, $projectsID, $title;
    protected static function getNumberProps(): array {
      return ["ID", "state", "projectsID"];
    }
  }