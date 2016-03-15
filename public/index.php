<?php

session_start();

$config = require_once('../config.php');
$config['routes'] = require_once('../src/routes.php');

require_once '../vendor/autoload.php';

$framework = new App\MasterController($config);
echo $framework->execute();
