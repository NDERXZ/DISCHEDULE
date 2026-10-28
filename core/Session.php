<?php

declare(strict_types=1);

namespace Core;

/**
 * Dischedule Session Manager
 */
class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $lifetime = (int)($_ENV['SESSION_LIFETIME'] ?? 86400);
        $cookieName = $_ENV['SESSION_COOKIE_NAME'] ?? 'dischedule_session';
        $secure = filter_var($_ENV['SESSION_SECURE'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $httpOnly = filter_var($_ENV['SESSION_HTTPONLY'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $sameSite = $_ENV['SESSION_SAMESITE'] ?? 'Lax';

        session_name($cookieName);

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite
        ]);

        session_start();
        self::$started = true;

        if (!isset($_SESSION['_csrf_token'])) {
            self::regenerateCsrfToken();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function regenerate(bool $deleteOldSession = true): bool
    {
        self::start();
        return session_regenerate_id($deleteOldSession);
    }

    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
        self::$started = false;
    }

    // --- CSRF Token Management ---

    public static function regenerateCsrfToken(): string
    {
        self::start();
        $token = bin2hex(random_bytes(32));
        $_SESSION['_csrf_token'] = $token;
        return $token;
    }

    public static function getCsrfToken(): string
    {
        self::start();
        if (empty($_SESSION['_csrf_token'])) {
            return self::regenerateCsrfToken();
        }
        return $_SESSION['_csrf_token'];
    }

    public static function validateCsrfToken(?string $token): bool
    {
        self::start();
        if (empty($token) || empty($_SESSION['_csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['_csrf_token'], $token);
    }

    // --- Flash Messages ---

    public static function setFlash(string $type, string $message): void
    {
        self::start();
        if (!isset($_SESSION['_flashes'])) {
            $_SESSION['_flashes'] = [];
        }
        $_SESSION['_flashes'][] = [
            'type'    => $type, // success, error, warning, info
            'message' => $message
        ];
    }

    public static function getFlashes(): array
    {
        self::start();
        $flashes = $_SESSION['_flashes'] ?? [];
        unset($_SESSION['_flashes']);
        return $flashes;
    }

    public static function hasFlashes(): bool
    {
        self::start();
        return !empty($_SESSION['_flashes']);
    }

    // --- Authentication Helpers ---

    public static function setUser(array $user): void
    {
        self::start();
        self::regenerate(true);
        $_SESSION['_user'] = $user;
        $_SESSION['_logged_in_at'] = time();
    }

    public static function getUser(): ?array
    {
        self::start();
        return $_SESSION['_user'] ?? null;
    }

    public static function getUserId(): ?int
    {
        $user = self::getUser();
        return isset($user['id']) ? (int)$user['id'] : null;
    }

    public static function isLoggedIn(): bool
    {
        return self::getUser() !== null;
    }

    public static function logout(): void
    {
        self::start();
        unset($_SESSION['_user']);
        unset($_SESSION['_logged_in_at']);
        self::regenerate(true);
    }
}
