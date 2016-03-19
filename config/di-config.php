<?php

use Pimple\Container;

$container = new Container();

$container['dbconfig'] = $config['database'];
$container['dsn'] = 'mysql:host=' . $container['dbconfig']['host'] . ';dbname=' . $container['dbconfig']['name'];

$container['PDO'] = function ($container) use ($config) {
    $pdo = new PDO($container['dsn'], $container['dbconfig']['user'], $container['dbconfig']['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
};

$container['App\Dbal\Mysql'] = $container->factory(function ($container) {
    return new \App\Dbal\Mysql($container['PDO']);
});

$container['App\Models\Comment'] = function ($container) {
    return new \App\Models\Comment($container['App\Dbal\Mysql']);
};

$container['App\Models\Story'] = function ($container) {
    return new \App\Models\Story($container['App\Dbal\Mysql']);
};

$container['App\Models\User'] = function ($container) {
    return new \App\Models\User($container['App\Dbal\Mysql']);
};

$container['App\Controllers\CommentController'] = function ($container) {
    return new \App\Controllers\CommentController($container['App\Models\Comment']);
};

$container['App\Controllers\IndexController'] = function ($container) {
    return new \App\Controllers\IndexController($container['App\Models\Story'], $container['App\Models\Comment']);
};

$container['App\Controllers\StoryController'] = function ($container) {
    return new \App\Controllers\StoryController($container['App\Models\Story'], $container['App\Models\Comment']);
};

$container['App\Controllers\UserController'] = function ($container) {
    return new \App\Controllers\UserController($container['App\Models\User']);
};
