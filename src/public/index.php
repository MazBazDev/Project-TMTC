<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\controllers\Admin\AdminController;
use app\controllers\Admin\UsersController;
use app\controllers\AuthController;
use app\controllers\HomeController;
use app\core\Application;
use app\core\Router;
use app\middlewares\IsAuth;
use app\middlewares\IsAdmin;
use app\middlewares\IsNotAuth;

$app = new Application(dirname(__DIR__));

$app->router->name("home")->get('/', [HomeController::class, 'index']);

$app->router->group([IsNotAuth::class], function (Router $router) {
    $router->name("login")->get('/login', [AuthController::class, 'login']);
    $router->name("login.store")->post('/login', [AuthController::class, 'login_store']);

    $router->name("register")->get('/register', [AuthController::class, 'register']);
    $router->name("register.store")->post('/register', [AuthController::class, 'register_store']);
});

$app->router->group([IsAuth::class], function (Router $router) {
    $router->name("logout")->get('/logout', [AuthController::class, 'logout']);

    $router->name("dashboard")->prefix("/dashboard")->group([IsAdmin::class], function (Router $router) {
        $router->name("index")->get('/', [AdminController::class, 'index']);

        $router->name("users")->prefix("/users")->group([], function (Router $router) {
            $router->name("index")->get('/', [UsersController::class, 'index']);
            $router->name("create")->get('/create', [UsersController::class, 'create']);
            $router->name("store")->post('/', [UsersController::class, 'store']);
            $router->name("show")->get('/:id', [UsersController::class, 'show']);
            $router->name("update")->patch('/:id', [UsersController::class, 'update']);
            $router->name("delete")->delete('/:id/delete', [UsersController::class, 'delete']);
        });
    });
});

$app->run();
