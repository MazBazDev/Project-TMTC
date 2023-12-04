<?php

use app\core\Application;

if (!function_exists("env")) {
    function env($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
}
if (!function_exists("getFlash")) {
    function getFlash($key) {
        return Application::$app->session->getFlash($key);
    }
}

if (!function_exists("setFlash")) {
    function setFlash($key, $value) {
        Application::$app->session->setFlash($key, $value);
    }
}


if (!function_exists("error")) {
    function error($input) {
        $errors = getFlash("inputs_errors");

        if (isset($errors[$input])) {
            return $errors[$input][0];
        }
        return false;
    }
}

if (!function_exists("old")) {
    function old($input, $default = "") {
        $old_datas = getFlash("inputs_old");

        if (isset($old_datas[$input])) {
            return $old_datas[$input];
        } else {
            return $default;
        }
    }
}

if (!function_exists("config")) {
    function config($key, $default = "") {
        $config = Application::$app->config;

        if (isset($config[$key])) {
            return $config[$key];
        } else {
            return $default;
        }
    }
}

if (!function_exists("asset")) {
    function asset($public_path) {
        return env("app_url") . "/assets/$public_path";
    }
}

if (!function_exists("route")) {
    function route($short, $params = []) {
        $associatedRoutes = Application::$app->router->associatedRoutes;
        var_dump($associatedRoutes);
        if (array_key_exists($short, $associatedRoutes)) {
            $url = env("APP_URL") . $associatedRoutes[$short];

            foreach ($params as $key => $value) {
                $url = str_replace(":$key", $value, $url);
            }
            return $url;
        }
        return null;
    }
}