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
    $router->name("hoho")->get('/login', [AuthController::class, 'login']);
    $router->post('/login', [AuthController::class, 'login_store']);

    $router->get('/register', [AuthController::class, 'register']);
    $router->post('/register', [AuthController::class, 'register_store']);
});

$app->router->group([IsAuth::class], function ($router) {
    $router->get('/logout', [AuthController::class, 'logout']);

});


$app->router->name("dashboard")->prefix("/dashboard")->group([IsAuth::class, IsAdmin::class], function ($router) {
    $router->name("index")->get('/', [AdminController::class, 'index']);
    $router->name("test")->get('/test/:id', [AdminController::class, 'index']);
});

$app->router->prefix("/dashboard/users")->group([IsAuth::class, IsAdmin::class], function ($router) {
    $router->get('/', [UsersController::class, 'index']);
});


$app->run();
