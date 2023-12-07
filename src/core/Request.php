<?php

namespace app\core;

class Request extends Validator
{
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');

        if ($position === false) {
            return $path;
        };

        return substr($path, 0, $position);
    }

    public function method()
    {
        if (isset($_POST["_method"])) {
            return strtolower($_POST["_method"]);
        }

        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet()
    {
        return $this->method() === "get";
    }

    public function isPost()
    {
        return $this->method() === "post";
    }

    public function isDelete()
    {
        return $this->method() === "delete";
    }

    public function isPatch()
    {
        return $this->method() === "patch";
    }

    public function getBody()
    {
        $body = [];

        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }

        if ($this->isPost() || $this->isDelete() || $this->isPatch()) {
            foreach ($_POST as $key => $value) {
                $body[$key] = $value;
            }
        }

        return $body;
    }

    public function getFiles($input)
    {
        $result = [];

        if (isset($_FILES[$input])) {
            $filesData = $_FILES[$input];

            if (is_array($filesData['name'])) {
                foreach ($filesData['name'] as $key => $name) {
                    $result[$key] = [
                        'name' => $name,
                        'full_path' => $name, // Vous pouvez ajuster cela selon vos besoins
                        'type' => $filesData['type'][$key],
                        'tmp_name' => $filesData['tmp_name'][$key],
                        'error' => $filesData['error'][$key],
                        'size' => $filesData['size'][$key],
                    ];
                }
            } else {
                // Si le champ n'est pas multiple, traitez-le comme un tableau avec un seul élément
                $result[] = [
                    'name' => $filesData['name'],
                    'full_path' => $filesData['name'], // Vous pouvez ajuster cela selon vos besoins
                    'type' => $filesData['type'],
                    'tmp_name' => $filesData['tmp_name'],
                    'error' => $filesData['error'],
                    'size' => $filesData['size'],
                ];
            }
        }

        return $result;
    }





    public function has($input)
    {
        return (isset($this->getBody()[$input]) || isset($this->getBody()[$input]) == "on") ? 1 : 0;
    }

    public function input($input)
    {
        return $this->getBody()[$input] ?? null;
    }
}