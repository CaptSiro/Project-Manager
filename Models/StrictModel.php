<?php 

  require_once(Path::translate("./Models/Model.php"));


  abstract class StrictModel extends Model {
    abstract protected static function getNumberProps (): array;

    public static function numberVal ($string) {
      if (strpos($string, ".") !== false) {
        return floatval($string);
      } else {
        return intval($string);
      }
    }

    public static function parseNumberProps ($objectArray) {
      return array_map(function ($obj) {
        foreach ($obj as $key => $value) {
          if (in_array($key, static::getNumberProps())) {
            $obj->$key = self::numberVal($value);
          }
        }

        return $obj;
      }, $objectArray);
    }
  }