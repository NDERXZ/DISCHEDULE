<?php

declare(strict_types=1);

namespace Core;

/**
 * Dischedule Application Logger
 * Daily rotating file-based logging system.
 */
class Logger
{
    public const DEBUG = 'DEBUG';
    public const INFO = 'INFO';
    public const WARNING = 'WARNING';
    public const ERROR = 'ERROR';

    private static string $logDir = '';

    public static function init(string $logDirectory): void
    {
        self::$logDir = rtrim($logDirectory, DIRECTORY_SEPARATOR);
        if (!is_dir(self::$logDir)) {
            @mkdir(self::$logDir, 0755, true);
        }
    }

    private static function getLogFile(): string
    {
        if (empty(self::$logDir)) {
            self::$logDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
        }
        if (!is_dir(self::$logDir)) {
            @mkdir(self::$logDir, 0755, true);
        }
        return self::$logDir . DIRECTORY_SEPARATOR . 'app-' . date('Y-m-d') . '.log';
    }

    public static function log(string $level, string $message, array $context = []): void
    {
        $file = self::getLogFile();
        $timestamp = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
        $logLine = "[{$timestamp}] [{$ip}] [{$level}]: {$message}{$contextString}" . PHP_EOL;

        @file_put_contents($file, $logLine, FILE_APPEND | LOCK_EX);
    }

    public static function debug(string $message, array $context = []): void
    {
        self::log(self::DEBUG, $message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::log(self::INFO, $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log(self::WARNING, $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log(self::ERROR, $message, $context);
    }
}
