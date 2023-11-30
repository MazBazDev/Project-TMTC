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

if (!function_exists("error")) {
    function error($input) {
        $errors = getFlash("inputs_errors");

        if ($errors) {
            return $errors[$input][0];
        } else {
            return $errors;
        }
    }
}