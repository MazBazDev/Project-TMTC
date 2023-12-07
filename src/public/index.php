<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\controllers\Admin\AdminController;
use app\controllers\Admin\EquipmentsController;
use app\controllers\Admin\HousingsController;
use app\controllers\Admin\HousingsTypesController;
use app\controllers\Admin\UsersController;
use app\controllers\AuthController;
use app\controllers\HomeController;
use app\core\Application;
use app\core\routing\Router;
use app\middlewares\IsAuth;
use app\middlewares\IsAdmin;
use app\middlewares\IsNotAuth;

$app = new Application(dirname(__DIR__));

$router = $app->router;


$router->name("home")->path("/")->get([HomeController::class, 'index']);

$router->middlewares([IsNotAuth::class])->group(function (Router $router) {
    $router->name("login")->path('/login')->get([AuthController::class, 'login']);
    $router->name("login.store")->path('/login')->post([AuthController::class, 'login_store']);

    $router->name("register")->path('/register')->get([AuthController::class, 'register']);
    $router->name("register.store")->path('/register')->post([AuthController::class, 'register_store']);
});

$router->middlewares([IsAuth::class])->group(function (Router $router) {
    $router->name("logout")->path('/logout')->post([AuthController::class, 'logout']);

    $router->name("profile")->path('/profile')->get([AuthController::class, 'profile']);
    $router->name("profile.update")->path('/profile')->patch([AuthController::class, 'profile_update']);

    $router->name("dashboard")->path('/dashboard')->middlewares([IsAdmin::class])->group(function (Router $router) {
        $router->name("index")->path('/')->get([AdminController::class, 'index']);

        $router->name("users")->path("/users")->group(function (Router $router) {
            $router->name("index")->path('/')->get([UsersController::class, 'index']);
            $router->name("create")->path('/create')->get([UsersController::class, 'create']);
            $router->name("store")->path('/')->post([UsersController::class, 'store']);
            $router->name("show")->path('/:id')->get([UsersController::class, 'show']);
            $router->name("update")->path('/:id')->patch([UsersController::class, 'update']);
            $router->name("delete")->path('/:id/delete')->delete([UsersController::class, 'delete']);
        });

        $router->name("housings")->path("/housings")->group(function (Router $router) {
            $router->name("index")->path('/')->get([HousingsController::class, 'index']);
            $router->name("create")->path('/create')->get([HousingsController::class, 'create']);
            $router->name("store")->path('/')->post([HousingsController::class, 'store']);
            $router->name("show")->path('/:id')->get([HousingsController::class, 'show']);
            $router->name("update")->path('/:id')->patch([HousingsController::class, 'update']);
            $router->name("delete")->path('/:id/delete')->delete([HousingsController::class, 'delete']);
            $router->name("image.delete")->path('/:id/image/:imageid/delete')->get([HousingsController::class, 'image_delete']);
        });

        $router->name("equipments")->path("/equipments")->group(function (Router $router) {
            $router->name("index")->path('/')->get([EquipmentsController::class, 'index']);
            $router->name("create")->path('/create')->get([EquipmentsController::class, 'create']);
            $router->name("store")->path('/')->post([EquipmentsController::class, 'store']);
            $router->name("show")->path('/:id')->get([EquipmentsController::class, 'show']);
            $router->name("update")->path('/:id')->patch([EquipmentsController::class, 'update']);
            $router->name("delete")->path('/:id/delete')->delete([EquipmentsController::class, 'delete']);
        });

        $router->name("housings.types")->path("/types")->group(function (Router $router) {
            $router->name("index")->path('/')->get([HousingsTypesController::class, 'index']);
            $router->name("create")->path('/create')->get([HousingsTypesController::class, 'create']);
            $router->name("store")->path('/')->post([HousingsTypesController::class, 'store']);
            $router->name("show")->path('/:id')->get([HousingsTypesController::class, 'show']);
            $router->name("update")->path('/:id')->patch([HousingsTypesController::class, 'update']);
            $router->name("delete")->path('/:id/delete')->delete([HousingsTypesController::class, 'delete']);
        });
    });
});

$app->run();
