<?php

require_once "./gatewaySetup.php";

require_once "../Models/Project.php";

$res->send(Project::delete($req->body->ID));