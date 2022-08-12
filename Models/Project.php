<?php

  require_once(Path::translate("./Models/StrictModel.php"));


  class Project extends StrictModel {
    protected $ID, $name, $totalUnits, $finishedUnits, $completionRate;
    protected static function getNumberProps(): array {
      return ["ID", "totalUnits", "finishedUnits", "completionRate"];
    }

    static function getAll () {
      return self::parseNumberProps(Database::get()->fetchAll(
        "SELECT
          projects.ID,
          projects.name,
          case 
            when total.`count` is null then 0
            else total.`count` 
          end totalUnits,
          case 
            when finished.`count` is null then 0
            else finished.`count` 
          end finishedUnits,
          case 
            when CAST(finished.count AS DECIMAL) / total.count is null then 0
            else CAST(finished.count AS DECIMAL) / total.count
          end completionRate
        FROM
          projects
          LEFT JOIN (
            SELECT COUNT(*) as \"count\", units.projectsID as \"ID\"
            FROM units
            GROUP BY 2
          ) total ON total.ID = projects.ID
          LEFT JOIN (
            SELECT COUNT(*) as \"count\", units.projectsID as \"ID\"
            FROM units
            WHERE units.state = 2 OR units.state = 5
            GROUP BY 2
          ) finished ON finished.ID = projects.ID",
        self::class
      ));
    }

    static function create ($name) {
      $rows = Database::get()->statement(
        "INSERT INTO projects (`name`) VALUE (:name)",
        [new DatabaseParam("name", $name, PDO::PARAM_STR)]
      );

      
      if ($rows == 1) {
        return Database::get()->highestID("projects");
      }

      return "-1";
    }

    static function rename ($id, $name) {
      $rows = Database::get()->statement(
        "UPDATE projects
        SET `name` = :name
        WHERE ID = :id",
        [
          new DatabaseParam("id", $id), 
          new DatabaseParam("name", $name, PDO::PARAM_STR)
        ]
      );


      if ($rows !== 0) {
        return "true";
      }

      return "false";
    }

    static function delete ($id) {
      $rows = Database::get()->statement(
        "DELETE FROM projects WHERE ID = :id",
        [new DatabaseParam("id", $id)]
      );

      if ($rows !== 0) {
        return "true";
      }

      return "false";
    }
  }