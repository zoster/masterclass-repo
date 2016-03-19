<?php

session_start();

define('__BASE_DIR__', dirname(__DIR__) . '/');

require_once '../vendor/autoload.php';
$config = require_once('../config/config.php');
$routes = require_once('../config/routes.php');
require_once('../config/di-config.php');

$framework = new App\MasterController($routes, $container);
echo $framework->execute();
