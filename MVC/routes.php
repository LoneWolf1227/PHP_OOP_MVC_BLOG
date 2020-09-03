<?php
return [

    '~^articles/add$~' => [\MVC\Controllers\ArticlesController::class, 'add'],
    '~^articles/(\d+)/edit$~' => [\MVC\Controllers\ArticlesController::class, 'edit'],
    '~^articles/(\d+)$~' => [\MVC\Controllers\ArticlesController::class, 'view'],
    '~^users/register$~' => [\MVC\Controllers\UsersController::class, 'signUp'],
    '~^users/login$~' => [\MVC\Controllers\UsersController::class, 'login'],
    '~^users/logout$~' => [\MVC\Controllers\UsersController::class, 'logout'],
    '~^users/(\d+)/activate/(.+)$~' => [\MVC\Controllers\UsersController::class, 'activate'],
    '~^$~' => [\MVC\Controllers\MainController::class, 'main']
];