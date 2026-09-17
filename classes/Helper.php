<?php

class Helper
{
    public static function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public static function setFlash(string $key, string $message): void
    {
        $_SESSION[$key] = $message;
    }

    public static function getFlash(string $key): string
    {
        $message = $_SESSION[$key] ?? '';
        unset($_SESSION[$key]);

        return $message;
    }
}
