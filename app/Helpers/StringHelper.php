<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Dischedule String & Utility Helper
 */
class StringHelper
{
    public static function slug(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text) ?: $text;
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);

        return empty($text) ? 'item-' . time() : $text;
    }

    public static function random(int $length = 32): string
    {
        return bin2hex(random_bytes((int)ceil($length / 2)));
    }

    public static function truncate(string $text, int $length = 100, string $append = '...'): string
    {
        $clean = strip_tags($text);
        if (mb_strlen($clean) <= $length) {
            return $clean;
        }
        return mb_substr($clean, 0, $length) . $append;
    }

    public static function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }
        $name = $parts[0];
        $domain = $parts[1];

        $maskedName = substr($name, 0, 1) . str_repeat('*', max(1, strlen($name) - 2)) . substr($name, -1);
        return $maskedName . '@' . $domain;
    }

    public static function hexToDiscordInt(string $hex): int
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        return (int)hexdec($hex);
    }
}
