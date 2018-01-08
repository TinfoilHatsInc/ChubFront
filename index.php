<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'autoload.php';

use core\routing\RoutingController;

echo RoutingController::getInstance()->route();