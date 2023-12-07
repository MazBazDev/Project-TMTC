<?php

use app\core\Application;
use app\core\Auth;

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

if (!function_exists("checked")) {
    function checked(bool $input = false) {

        return $input ? 'checked' : '';
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

if (!function_exists("method")) {
    function method(string $type)
    {
        return new \Twig\Markup('<input name="_method" type="hidden" value="' . $type .'" />', 'UTF-8');
    }
}

if (!function_exists("auth")) {
    function auth() {
        return new Auth();
    }
}

if (!function_exists("dd")) {
    function dd(...$vars) {
        dump($vars);
        die();
    }
}
if (!function_exists("displayHtml")) {
    function displayhtml($html)
    {
        return html_entity_decode($html);
    }
}

if (!function_exists("urlParam")) {
    function urlParam($name)
    {
        return Application::$app->request->getParams()[$name] ?? "";
    }
}

