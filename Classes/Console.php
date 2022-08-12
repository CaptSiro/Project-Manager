<?php

define("LOG_DIRECTORY", Path::translate("./Logs"));

class Console {
  static function log ($content, $__file__ = null, $__line__ = null, $f = "logs.txt") {
    $path = LOG_DIRECTORY . "\\" . $f;
    $log = (isset($__file__) ? "$__file__:($__line__) " : "") . $content . "\n";

    $temp = file_get_contents($path);
    file_put_contents($path, $log . $temp);
  }
}