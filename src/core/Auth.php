<?php

namespace app\core;

class Auth
{
    public ?Models $user;

    public function __construct()
    {
        $userClass = new Application::$app->config["userClass"];

        $primaryUserValue = Application::$app->session->get("user");
        if ($primaryUserValue) {
            $primaryKey = $userClass->primary_key;

            $this->user = $userClass->where([$primaryKey, $primaryUserValue])->first();
        }
    }

    public static function check()
    {
        return !empty(Application::$app->auth->user);
    }

    public static function user()
    {
        return Application::$app->auth->user ?? false;
    }

    public function login(Models $user)
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