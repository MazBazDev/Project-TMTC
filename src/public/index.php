<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\controllers\AuthController;
use app\controllers\ContactController;
use app\controllers\HomeController;
use app\core\Application;
use app\middlewares\AuthMiddleware;

// Fuck deprecated errors

error_reporting(E_ALL ^ E_DEPRECATED);

$app = new Application(dirname(__DIR__));

$app->router->group([AuthMiddleware::class], function ($router) {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/contact/:id/delete/:te', [ContactController::class, 'index']);
    $router->post('/contact/:id', [ContactController::class, 'handleContact']);
});

//$app->router->middleware([AuthMiddleware::class])->get('/contact/:id/delete/:te', [ContactController::class, 'index']);
//$app->router->post('/contact/:id', [ContactController::class, 'handleContact']);

$app->router->get('/login', [AuthController::class, 'login']);
$app->router->post('/login', [AuthController::class, 'login_store']);

$app->router->get('/register', [AuthController::class, 'register']);
$app->router->post('/register', [AuthController::class, 'register_store']);

$app->router->get('/logout', [AuthController::class, 'logout']);

$app->run();