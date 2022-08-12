<?php

require_once "./gatewaySetup.php";

require_once "../Models/Project.php";


$name = $req->body->name;
$ID = Project::create($name);

if ($ID == "-1") {
  $res->setStatusCode(Response::INTERNAL_SERVER_ERROR);
  $res->error("Error while inserting new project into database");
}

$res->json((object)["name" => $name, "ID" => $ID]);