<?php

declare(strict_types=1);

/**
 * Dischedule Maintenance & Log Rotation Worker
 * 
 * Run daily:
 * 0 2 * * * php /path/to/dischedule/cron/cleanup_old_logs.php >> /dev/null 2>&1
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

$db = \Core\Database::getInstance();
echo "[" . date('Y-m-d H:i:s') . "] Starting system cleanup & rotation...\n";

// 1. Delete webhook logs older than 30 days
$deletedLogs = $db->query("DELETE FROM `webhook_logs` WHERE `created_at` < DATE_SUB(NOW(), INTERVAL 30 DAY)")->rowCount();
echo "Cleaned up {$deletedLogs} old webhook logs.\n";

// 2. Delete expired rate limits
$deletedRates = $db->query("DELETE FROM `rate_limits` WHERE `reset_at` < UNIX_TIMESTAMP()")->rowCount();
echo "Cleaned up {$deletedRates} expired rate limits.\n";

// 3. Clean up cache directory
$cacheFiles = glob($baseDir . '/storage/cache/cache_*.json');
$deletedCache = 0;
if ($cacheFiles) {
    foreach ($cacheFiles as $file) {
        $content = @file_get_contents($file);
        if ($content) {
            $data = json_decode($content, true);
            if (isset($data['expires_at']) && $data['expires_at'] !== 0 && time() > $data['expires_at']) {
                @unlink($file);
                $deletedCache++;
            }
        }
    }
}
echo "Purged {$deletedCache} expired cache files.\n";

// 4. Rotate old file logs older than 30 days
$logFiles = glob($baseDir . '/storage/logs/app-*.log');
$deletedLogsFiles = 0;
if ($logFiles) {
    $thirtyDaysAgo = time() - (86400 * 30);
    foreach ($logFiles as $lf) {
        if (filemtime($lf) < $thirtyDaysAgo) {
            @unlink($lf);
            $deletedLogsFiles++;
        }
    }
}
echo "Deleted {$deletedLogsFiles} old log files.\n";
echo "[" . date('Y-m-d H:i:s') . "] Maintenance completed.\n";
