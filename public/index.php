<?php

session_start();

define('__BASE_DIR__', dirname(__DIR__) . '/');

$config = require_once('../config.php');
require_once '../vendor/autoload.php';

$framework = new App\Controllers\MasterController($config);
echo $framework->execute();
