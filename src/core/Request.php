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
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }

        return $body;
    }

    public function has($input)
    {
        return isset($this->getBody()[$input]) || isset($this->getBody()[$input]) == "on";
    }

    public function input($input)
    {
        return $this->getBody()[$input] ?? null;
    }
}