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

    public function serveAsset($filePath)
    {
        // Vérifiez que le fichier existe
        if (file_exists($filePath) && is_file($filePath)) {
            // Obtenez le type MIME du fichier
            $mimeType = mime_content_type($filePath);

            // Définissez le type MIME dans les en-têtes de réponse
            header("Content-Type: $mimeType");

            // Lisez le contenu du fichier et renvoyez-le
            readfile($filePath);
            return true;
        } else {
            // Si le fichier n'existe pas, retournez une réponse 404
            return false;
        }
    }
}