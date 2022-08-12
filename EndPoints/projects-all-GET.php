<?php

require_once "./gatewaySetup.php";

require_once "../Models/Project.php";

$res->json(Project::getAll());