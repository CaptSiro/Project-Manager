<?php

  class Path {
    /**
     * Translates path that is relative to project-manager directory to current working directory 
     */
    static function translate (string $p): string {
      $cwd = getcwd();
      $cwdCount = count(explode(
        (strpos($cwd, "/") !== false)
          ? "/"
          : "\\",
        $cwd
      ));
  
      $projectDirCount = count(explode("\\", "C:\\wamp\\www\\project-manager"));
  
      return str_repeat("../", $cwdCount - $projectDirCount) . $p;
    }

    static function breakdown (string $path): array {
      $separator = (strpos($path, "/") !== false)
        ? "/"
        : "\\";

      return explode($separator, $path);
    }
  }

  require_once(Path::translate("./Classes/Console.php"));