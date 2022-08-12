<?php

require_once "./gatewaySetup.php";

session_start();

$image = null;

if (isset($_SESSION["background-image"])) {
  $image = $_SESSION["background-image"];
} else {
  $imageSource = Request::GET("http://localhost/newtab/getRandomBG.php", ["no-file-transfer" => true]);
  $_SESSION["background-image"] = $imageSource;
  $image = $imageSource;
}

$res->setHeaders([
  ["Content-Description", "File Transfer"],
  ["Content-Type", mime_content_type($image)],
  ["Content-Length", filesize($image)]
]);

$res->download($image);