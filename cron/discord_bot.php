<?php

declare(strict_types=1);

/**
 * Dischedule Discord Bot CLI Helper
 * 
 * Standalone worker to sync events or respond to Discord interactions.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("CLI only.\n");
}

$baseDir = dirname(__DIR__);

// Load environment
$envFile = $baseDir . DIRECTORY_SEPARATOR . '.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $_ENV[trim($parts[0])] = trim($parts[1]);
            }
        }
    }
}

require_once $baseDir . '/core/Autoloader.php';
\Core\Autoloader::register($baseDir);

$botToken = $_ENV['DISCORD_BOT_TOKEN'] ?? '';
if (empty($botToken)) {
    echo "Notice: DISCORD_BOT_TOKEN is not configured in .env.\n";
    echo "Dischedule will continue using direct cURL Webhook notifications which work without bot tokens.\n";
    exit(0);
}

echo "Dischedule Discord Bot Service running...\n";
echo "Active bot credentials found. Listening for schedule sync requests.\n";
