<?php

declare(strict_types=1);

use Core\I18n;
use Core\Session;
use Core\View;

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $val = $_ENV[$key] ?? getenv($key);
        if ($val === false || $val === null) {
            return $default;
        }
        if (is_string($val)) {
            $lower = strtolower($val);
            if ($lower === 'true') return true;
            if ($lower === 'false') return false;
            if ($lower === 'null') return null;
            if ($lower === 'empty') return '';
        }
        return $val;
    }
}

if (!function_exists('__')) {
    function __(string $key, array $replace = [], ?string $locale = null): string
    {
        return I18n::trans($key, $replace, $locale);
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return View::escape($value);
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $path = ltrim($path, '/');
        return $path === '' ? '/' : "/{$path}";
    }
}

if (!function_exists('absolute_url')) {
    function absolute_url(string $path = ''): string
    {
        $appUrl = env('APP_URL');
        if (empty($appUrl) || $appUrl === 'http://localhost:8000') {
            if (!empty($_SERVER['HTTP_HOST'])) {
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $appUrl = "{$protocol}://{$_SERVER['HTTP_HOST']}";
            } else {
                $appUrl = 'http://localhost:8000';
            }
        }
        $base = rtrim($appUrl, '/');
        $path = ltrim($path, '/');
        return $path === '' ? $base : "{$base}/{$path}";
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Session::getCsrfToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('method_field')) {
    function method_field(string $method): string
    {
        return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
    }
}

if (!function_exists('auth')) {
    function auth(): ?array
    {
        return Session::getUser();
    }
}

if (!function_exists('auth_id')) {
    function auth_id(): ?int
    {
        return Session::getUserId();
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        $user = Session::getUser();
        return $user !== null && ($user['role'] ?? '') === 'admin';
    }
}

if (!function_exists('is_moderator')) {
    function is_moderator(): bool
    {
        $user = Session::getUser();
        return $user !== null && in_array($user['role'] ?? '', ['admin', 'moderator'], true);
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = null): mixed
    {
        $oldInput = Session::get('_old_input', []);
        return $oldInput[$key] ?? $default;
    }
}

if (!function_exists('has_error')) {
    function has_error(string $key): bool
    {
        $errors = Session::get('_validation_errors', []);
        return isset($errors[$key]);
    }
}

if (!function_exists('error_msg')) {
    function error_msg(string $key): ?string
    {
        $errors = Session::get('_validation_errors', []);
        return $errors[$key][0] ?? null;
    }
}
