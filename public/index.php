<?php

declare(strict_types=1);

/**
 * Dischedule - Web Application Front Controller
 * Pure Native PHP 8.1+ (No Framework, No Composer)
 */

// Error handling & reporting
error_reporting(E_ALL);

$baseDir = dirname(__DIR__);

// 1. Parse .env Environment File
$envFile = $baseDir . DIRECTORY_SEPARATOR . '.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $val = trim($parts[1]);
                // Strip surrounding quotes
                if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                    (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                    $val = substr($val, 1, -1);
                }
                $_ENV[$key] = $val;
                putenv("{$key}={$val}");
            }
        }
    }
}

// 2. Set Default Timezone
$defaultTz = $_ENV['APP_TIMEZONE'] ?? 'UTC';
date_default_timezone_set($defaultTz);

// 3. Register Custom Autoloader
require_once $baseDir . '/core/Autoloader.php';
\Core\Autoloader::register($baseDir);

// 4. Load Global Helper Functions
require_once $baseDir . '/app/Helpers/functions.php';

// 5. Initialize Core Systems
\Core\Logger::init($baseDir . '/storage/logs');
\Core\Cache::init($baseDir . '/storage/cache');
\Core\I18n::init($baseDir . '/app/Languages');
\Core\View::init($baseDir . '/app/Views');
\Core\Session::start();

// 6. Exception and Error Handler
set_exception_handler(function (\Throwable $e) use ($baseDir) {
    \Core\Logger::error("Uncaught Exception: " . $e->getMessage(), [
        'file'  => $e->getFile(),
        'line'  => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);

    $isDebug = filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN);

    if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => $isDebug ? $e->getMessage() : 'An internal server error occurred.',
            'error'   => $isDebug ? ['file' => $e->getFile(), 'line' => $e->getLine()] : null
        ]);
        exit;
    }

    http_response_code(500);
    try {
        echo \Core\View::render('errors.500', [
            'title'     => '500 Server Error',
            'exception' => $isDebug ? $e : null
        ], 'main');
    } catch (\Throwable) {
        echo "<h1>500 Internal Server Error</h1><p>" . ($isDebug ? htmlspecialchars($e->getMessage()) : "Please try again later.") . "</p>";
    }
    exit;
});

set_error_handler(function (int $severity, string $message, string $file, int $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

// 7. Process Request and Dispatch Router
$request = new \Core\Request();
$response = new \Core\Response();
$router = new \Core\Router();

// Load Route Definitions
require_once $baseDir . '/app/Config/routes.php';

// Dispatch
$router->dispatch($request, $response);
$response->send();
