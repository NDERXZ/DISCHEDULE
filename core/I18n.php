<?php

declare(strict_types=1);

namespace Core;

/**
 * Dischedule Internationalization (i18n) Engine
 */
class I18n
{
    private static string $currentLocale = 'en';
    private static array $translations = [];
    private static string $langDir = '';

    public static function init(?string $langDirectory = null): void
    {
        self::$langDir = $langDirectory ?? (dirname(__DIR__) . '/app/Languages');
        $configuredLocale = $_ENV['APP_LOCALE'] ?? 'en';
        
        // Detect from session or user
        $sessionLocale = Session::get('user_locale');
        self::$currentLocale = $sessionLocale ?? $configuredLocale;

        self::loadTranslations(self::$currentLocale);
    }

    public static function setLocale(string $locale): void
    {
        self::$currentLocale = $locale;
        Session::set('user_locale', $locale);
        self::loadTranslations($locale);
    }

    public static function getLocale(): string
    {
        return self::$currentLocale;
    }

    public static function loadTranslations(string $locale): void
    {
        if (isset(self::$translations[$locale])) {
            return;
        }

        $file = self::$langDir . DIRECTORY_SEPARATOR . $locale . '.php';
        if (file_exists($file)) {
            self::$translations[$locale] = require $file;
        } else {
            // Fallback to English
            $enFile = self::$langDir . DIRECTORY_SEPARATOR . 'en.php';
            self::$translations[$locale] = file_exists($enFile) ? require $enFile : [];
        }
    }

    public static function trans(string $key, array $replace = [], ?string $locale = null): string
    {
        $targetLocale = $locale ?? self::$currentLocale;
        if (!isset(self::$translations[$targetLocale])) {
            self::loadTranslations($targetLocale);
        }

        $text = self::$translations[$targetLocale][$key] ?? self::$translations['en'][$key] ?? $key;

        foreach ($replace as $placeholder => $value) {
            $text = str_replace(
                [':' . $placeholder, '{' . $placeholder . '}'],
                (string)$value,
                $text
            );
        }

        return $text;
    }
}
