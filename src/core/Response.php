<?php

namespace app\core;

class Response
{

    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

//    public function redirect(string $url = null)
//    {
//        var_dump($url);
//        if ($url !== null) {
//            header("Location: " . $url);
//        }
//
//        return $this; // Retourne l'instance de Response pour permettre l'enchaînement des méthodes
//    }

    public function redirect(?string $short = null, ?array $params = [])
    {
        if ($short !== null) {
            $associatedRoutes = Application::$app->router->associatedRoutes;

            if (array_key_exists($short, $associatedRoutes)) {
                $url = $associatedRoutes[$short];

                foreach ($params as $key => $value) {
                    $url = str_replace(":$key", $value, $url);
                }
                header("Location: " . $url);
            }
        }

        return $this;
    }

    public function abort_if(bool $bool, $code = 404, $message = "Not found !")
    {
        if ($bool) {
            $this->setStatusCode($code);

            Application::$app->router->renderView("errors", [
                "code" => $code,
                "message" => $message,
            ]);

            exit();
        }
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