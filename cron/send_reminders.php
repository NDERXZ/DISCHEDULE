<?php

declare(strict_types=1);

/**
 * Dischedule Reminder Cron Job
 * 
 * Run every minute:
 * * * * * * php /path/to/dischedule/cron/send_reminders.php >> /dev/null 2>&1
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Access denied. Command line interface only.\n");
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
                $k = trim($parts[0]);
                $v = trim($parts[1]);
                if ((str_starts_with($v, '"') && str_ends_with($v, '"')) ||
                    (str_starts_with($v, "'") && str_ends_with($v, "'"))) {
                    $v = substr($v, 1, -1);
                }
                $_ENV[$k] = $v;
                putenv("{$k}={$v}");
            }
        }
    }
}

// Set timezone to UTC for background processing
date_default_timezone_set('UTC');

require_once $baseDir . '/core/Autoloader.php';
\Core\Autoloader::register($baseDir);
require_once $baseDir . '/app/Helpers/functions.php';

\Core\Logger::init($baseDir . '/storage/logs');
\Core\Cache::init($baseDir . '/storage/cache');

echo "[" . date('Y-m-d H:i:s') . " UTC] Starting Dischedule Reminder Worker...\n";

$dueReminders = \App\Models\Reminder::getDueReminders();
$count = count($dueReminders);

echo "Found {$count} pending reminder(s) to process.\n";

$notifService = new \App\Services\NotificationService();

foreach ($dueReminders as $reminder) {
    echo "Processing reminder #{$reminder['id']} for event '{$reminder['title']}' ({$reminder['type']})... ";
    try {
        $notifService->onReminderFired($reminder);
        
        \App\Models\Reminder::update($reminder['id'], [
            'status'  => 'sent',
            'sent_at' => date('Y-m-d H:i:s')
        ]);
        echo "SUCCESS\n";

        // Advance reminder for recurring events (e.g. FREQ=DAILY, FREQ=WEEKLY)
        if (!empty($reminder['recurrence_rule'])) {
            $minutesBefore = (int)$reminder['minutes_before'];
            // Look for future occurrences starting from now
            $windowStart = date('Y-m-d H:i:s', time());
            $windowEnd = date('Y-m-d H:i:s', strtotime('+60 days'));
            $nextOccurrences = \App\Services\RecurrenceService::generateOccurrences(
                $reminder['recurrence_rule'],
                $reminder['start_datetime'],
                $reminder['end_datetime'],
                $windowStart,
                $windowEnd,
                $reminder['recurrence_end'] ?? null,
                !empty($reminder['recurrence_count']) ? (int)$reminder['recurrence_count'] : null
            );

            $nowUtc = new DateTime('now', new DateTimeZone('UTC'));

            foreach ($nextOccurrences as $occ) {
                $nextStart = $occ['start'];
                $nextRemindDt = new DateTime($nextStart, new DateTimeZone('UTC'));
                $nextRemindDt->modify("-{$minutesBefore} minutes");

                // CRITICAL: Only schedule if reminder is strictly in the FUTURE
                if ($nextRemindDt > $nowUtc) {
                    $remindAtStr = $nextRemindDt->format('Y-m-d H:i:s');

                    $exists = \Core\Model::db()->fetch(
                        "SELECT id FROM `reminders` WHERE `event_id` = :eid AND `remind_at` = :rat AND `status` = 'pending'",
                        ['eid' => (int)$reminder['event_id'], 'rat' => $remindAtStr]
                    );

                    if (!$exists) {
                        \App\Models\Reminder::create([
                            'event_id'       => (int)$reminder['event_id'],
                            'remind_at'      => $remindAtStr,
                            'minutes_before' => $minutesBefore,
                            'type'           => $reminder['type'],
                            'status'         => 'pending'
                        ]);
                        echo "  -> Scheduled next recurring reminder at {$remindAtStr} UTC\n";
                    }
                    break;
                }
            }
        }
    } catch (\Throwable $e) {
        \App\Models\Reminder::update($reminder['id'], [
            'status'        => 'failed',
            'error_message' => $e->getMessage()
        ]);
        \Core\Logger::error("Reminder failed for event #{$reminder['event_id']}: " . $e->getMessage());
        echo "FAILED: " . $e->getMessage() . "\n";
    }
}

echo "[" . date('Y-m-d H:i:s') . " UTC] Completed reminder processing.\n";
