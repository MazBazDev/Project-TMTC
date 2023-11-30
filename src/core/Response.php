<?php

namespace app\core;

class Response
{

    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    public function redirect(string $url = null)
    {
        if ($url !== null) {
            header("Location: " . $url);
        }

        return $this; // Retourne l'instance de Response pour permettre l'enchaînement des méthodes
    }

    public function back()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        return $this;
    }

    public function with($key, $value)
    {
        Application::$app->session->setFlash($key, $value);
        return $this; // Retourne l'instance de Response pour permettre l'enchaînement des méthodes
    }
}