<?php

require_once "./gatewaySetup.php";

require_once "../Models/Project.php";

$res->send(Project::rename($req->body->ID, $req->body->name));