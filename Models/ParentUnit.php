<?php

  require_once(Path::translate("./Models/StrictModel.php"));
  require_once(Path::translate("./Models/Unit.php"));

  class ParentUnit extends StrictModel {
    protected $ID;
    protected static function getNumberProps(): array {
      return ["ID"];
    }

    static function getAll ($projectsID) {
      return self::parseNumberProps(Database::get()->fetchAll(
        "SELECT
          units.ID ID,
          units.state state,
          units.description description,
          units.projectsID projectsID,
          units.title title
        FROM
        	units
            JOIN parents ON parents.ID = units.ID
            	AND units.projectsID = :projectsID",
        "Unit",
        [new DatabaseParam("projectsID", $projectsID)]
      ));
    }
  }