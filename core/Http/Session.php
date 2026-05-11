<?php

namespace Core\Http;

class Session
{
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Auto-login (Remember Me)
        if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
            $token = $_COOKIE['remember_me'];
            $userModel = new \App\Auth\Models\User();
            $user = $userModel->findByRememberToken($token);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
            } else {
                // Token inválido, limpa o cookie
                setcookie('remember_me', '', time() - 3600, '/');
            }
        }

        if (!isset($_SESSION['_flash_next'])) {
            $_SESSION['_flash_next'] = [];
        }

        $_SESSION['_flash_current'] = $_SESSION['_flash_next'];
        $_SESSION['_flash_next'] = [];
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        session_destroy();
    }

    public static function setFlash(string $key, $message): void
    {
        $_SESSION['_flash_next'][$key] = $message;
    }

    public static function getFlash(string $key)
    {
        return $_SESSION['_flash_current'][$key] ?? null;
    }

    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash_current'][$key]);
    }
}
