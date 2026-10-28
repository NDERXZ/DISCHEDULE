<?php

declare(strict_types=1);

namespace Core;

/**
 * Dischedule File-Based Cache
 */
class Cache
{
    private static string $cacheDir = '';

    public static function init(string $cacheDirectory): void
    {
        self::$cacheDir = rtrim($cacheDirectory, DIRECTORY_SEPARATOR);
        if (!is_dir(self::$cacheDir)) {
            @mkdir(self::$cacheDir, 0755, true);
        }
    }

    private static function getDir(): string
    {
        if (empty(self::$cacheDir)) {
            self::$cacheDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache';
        }
        if (!is_dir(self::$cacheDir)) {
            @mkdir(self::$cacheDir, 0755, true);
        }
        return self::$cacheDir;
    }

    private static function getFilePath(string $key): string
    {
        $hash = md5($key);
        return self::getDir() . DIRECTORY_SEPARATOR . 'cache_' . $hash . '.json';
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $filePath = self::getFilePath($key);
        if (!file_exists($filePath)) {
            return $default;
        }

        $raw = @file_get_contents($filePath);
        if ($raw === false) {
            return $default;
        }

        $data = json_decode($raw, true);
        if (!is_array($data) || !isset($data['expires_at'], $data['payload'])) {
            @unlink($filePath);
            return $default;
        }

        if ($data['expires_at'] !== 0 && time() > $data['expires_at']) {
            @unlink($filePath);
            return $default;
        }

        return $data['payload'];
    }

    public static function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        $filePath = self::getFilePath($key);
        $expiresAt = $ttl > 0 ? (time() + $ttl) : 0;

        $data = [
            'key'        => $key,
            'expires_at' => $expiresAt,
            'created_at' => time(),
            'payload'    => $value
        ];

        return @file_put_contents($filePath, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
    }

    public static function has(string $key): bool
    {
        return self::get($key) !== null;
    }

    public static function delete(string $key): bool
    {
        $filePath = self::getFilePath($key);
        if (file_exists($filePath)) {
            return @unlink($filePath);
        }
        return true;
    }

    public static function flush(): bool
    {
        $dir = self::getDir();
        $files = glob($dir . DIRECTORY_SEPARATOR . 'cache_*.json');
        if ($files === false) {
            return false;
        }

        foreach ($files as $file) {
            @unlink($file);
        }
        return true;
    }

    public static function remember(string $key, int $ttl, callable $callback): mixed
    {
        $cached = self::get($key);
        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        self::set($key, $value, $ttl);
        return $value;
    }
}
