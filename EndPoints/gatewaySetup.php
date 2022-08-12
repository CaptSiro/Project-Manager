<?php

if (strpos(getcwd(), "EndPoints") !== false) {
  require_once "../Classes/Path.php";
  require_once "../Classes/Console.php";
  require_once "../Classes/Response.php";
  require_once "../Classes/Request.php";
} else {
  exit();
}

$res = new Response();
$req = new Request($res);