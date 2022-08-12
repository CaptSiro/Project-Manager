<?php

  require_once(Path::translate("./Database/access.php"));
  require_once(Path::translate("./Database/DatabaseParam.php"));


  class Database {
    private $con;
    private static $instance;
    public static function get () {
      if (!isset($instance)) {
        $instance = new Database();
      }

      return $instance;
    }

    public function __construct() {
      $connectionString = "mysql:host=" . HOST . ";port=" . PORT . ";dbname=" . DB_N . ";charset=UTF8";
      $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // errors from MySQL will appear as PHP Exceptions
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false // SQL injection
      ];
      $this->con = new PDO($connectionString, USER, PASS, $opt);
    }

    public function lastInsertId() {
      return $this->con->lastInsertId();
    }

    public function highestID ($table) {
      return $this->fetch(
        "SELECT MAX(ID) \"max\" FROM $table",
        "stdClass"
      )->max;
    }

    public function statement($sql, $params = []) {
      $stmt = $this->con->prepare($sql);
      foreach($params as $param) {
        $stmt->bindValue($param->name, $param->value, $param->type);
      }
      $stmt->execute();
      return $stmt->rowCount();
    }

    public function fetch($sql, $className, $params = []) {
      $stmt = $this->con->prepare($sql);
      foreach($params as $param) {
        $stmt->bindValue($param->name, $param->value, $param->type);
      }
      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_CLASS, $className);
      return $stmt->fetch();
    }

    public function fetchAll($sql, $className, $params = []) {
      $stmt = $this->con->prepare($sql);
      foreach($params as $param) {
        $stmt->bindValue($param->name, $param->value, $param->type);
      }
      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_CLASS, $className);
      return $stmt->fetchAll();
    }
  }