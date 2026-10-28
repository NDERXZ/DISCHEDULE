<?php

declare(strict_types=1);

namespace Core;

/**
 * Dischedule Custom PSR-4 Autoloader
 * 
 * Maps namespaces to directory paths without any external dependencies or Composer.
 */
class Autoloader
{
    /**
     * Namespace to base directory prefixes
     *
     * @var array<string, string>
     */
    private static array $prefixes = [];

    /**
     * Register the autoloader with SPL
     */
    public static function register(string $baseDir): void
    {
        self::addNamespace('App\\', $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);
        self::addNamespace('Core\\', $baseDir . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR);

        spl_autoload_register([__CLASS__, 'loadClass'], true, true);
    }

    /**
     * Add a namespace prefix to directory mapping
     */
    public static function addNamespace(string $prefix, string $baseDir): void
    {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        self::$prefixes[$prefix] = $baseDir;
    }

    /**
     * Load the class file for a given class name
     */
    public static function loadClass(string $class): bool
    {
        foreach (self::$prefixes as $prefix => $baseDir) {
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                continue;
            }

            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

            if (file_exists($file)) {
                require_once $file;
                return true;
            }
        }

        return false;
    }
}
