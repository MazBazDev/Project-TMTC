<?php

namespace app\core;

class Session
{
    protected const FLASH_KEY = 'flash_message';
    public function __construct()
    {
        session_start();

        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage["remove"] = true;
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function setFlash(string $key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            "remove" => false,
            "value" => $message
        ];
    }

    public function getFlash(string $key)
    {
        return $_SESSION[self::FLASH_KEY][$key]["value"] ?? false;
    }

    public function __destruct()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

        foreach ($flashMessages as $key => &$flashMessage) {
            if ($flashMessage["remove"] === true) {
                unset($flashMessages[$key]);
            }
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}