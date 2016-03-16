<?php
return array(

        '' => App\Index::class . '@index',
        'story' => App\Story::class . '@index',
        'story/create' => App\Story::class . '@create',
        'comment/create' => App\Comment::class . '@create',
        'user/create' => App\User::class . '@create',
        'user/account' => App\User::class . '@account',
        'user/login' => App\User::class . '@login',
        'user/logout' => App\User::class . '@logout',

);
