<?php

namespace app\core;

class Auth
{
    private ?Models $user;

    public function __construct()
    {
        $userClass = new Application::$app->config["userClass"];

        $primaryUserValue = Application::$app->session->get("user");
        if ($primaryUserValue) {
            $primaryKey = $userClass->primary_key;

            $this->user = $userClass->where([$primaryKey, $primaryUserValue])->first();
        }
    }

    public function check()
    {
        return $this->user();
    }

    public static function user() : bool
    {
        return Application::$app->user ?? false;
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