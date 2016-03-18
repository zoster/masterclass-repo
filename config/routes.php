<?php
return array(

    '' => App\Controllers\IndexController::class . '@index',
    'story' => App\Controllers\StoryController::class . '@index',
    'story/create' => App\Controllers\StoryController::class . '@create',
    'comment/create' => App\Controllers\CommentController::class . '@create',
    'user/create' => App\Controllers\UserController::class . '@create',
    'user/account' => App\Controllers\UserController::class . '@account',
    'user/login' => App\Controllers\UserController::class . '@login',
    'user/logout' => App\Controllers\UserController::class . '@logout',

);
