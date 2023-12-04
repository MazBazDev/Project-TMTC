<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\controllers\Admin\AdminController;
use app\controllers\Admin\UsersController;
use app\controllers\AuthController;
use app\controllers\HomeController;
use app\core\Application;
use app\middlewares\IsAuth;
use app\middlewares\IsAdmin;
use app\middlewares\IsNotAuth;

$app = new Application(dirname(__DIR__));

$app->router->get('/', [HomeController::class, 'index']);

$app->router->group([IsNotAuth::class], function ($router) {
    $router->name("login")->get('/login', [AuthController::class, 'login']);
    $router->name("login.store")->post('/login', [AuthController::class, 'login_store']);

    $router->name("register")->get('/register', [AuthController::class, 'register']);
    $router->name("register.store")->post('/register', [AuthController::class, 'register_store']);
});

$app->router->group([IsAuth::class], function ($router) {
    $router->name("logout")->get('/logout', [AuthController::class, 'logout']);

    $router->name("dashboard")->prefix("/dashboard")->group([IsAdmin::class], function ($router) {
        $router->name("index")->get('/', [AdminController::class, 'index']);

        $router->name("users")->prefix("/users")->group([], function ($router) {
            $router->name("index")->get('/', [UsersController::class, 'index']);
        });
    });
});

$app->run();
