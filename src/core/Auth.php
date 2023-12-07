<?php

namespace app\core;

use app\models\Likes;
use app\models\User;

class Auth
{
    public ?User $user;

    public function __construct()
    {
        $this->loadUser();
    }

    private function loadUser()
    {
        $userClass = new Application::$app->config["userClass"];

        $primaryUserValue = Application::$app->session->get("user");
        if ($primaryUserValue) {
            $primaryKey = $userClass->primary_key;
            $user = $userClass->where([$primaryKey, "=", $primaryUserValue])->first();

            if (!$user) $this->logout();

            $this->user = $user;
        }
    }

    public static function check()
    {
        return !empty(Application::$app->auth->user);
    }

    public static function user() : User
    {
        return Application::$app->auth->user ?? false;
    }

    public static function isAdmin() : bool
    {
        return Application::$app->auth->user->isAdmin();
    }

    public function login(User $user)
    {
        $this->user = $user;

        $primary_value = $user->{$user->primary_key};

        Application::$app->session->set("user", $primary_value);
    }

    public function logout()
    {
        Application::$app->session->remove("user");
    }
}