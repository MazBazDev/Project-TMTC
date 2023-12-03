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
