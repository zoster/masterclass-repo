<?php

session_start();

define('__BASE_DIR__', dirname(__DIR__) . '/');

$config = require_once('../config.php');
$config['routes'] = require_once('../src/routes.php');

require_once '../vendor/autoload.php';

$framework = new App\Controllers\MasterController($config);
echo $framework->execute();
