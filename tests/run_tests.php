<?php

declare(strict_types=1);

/**
 * Dischedule Standalone Test Runner (Zero Dependencies)
 * 
 * Usage: php tests/run_tests.php
 */

$baseDir = dirname(__DIR__);

// Load Environment
$envFile = $baseDir . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines) {
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
require_once $baseDir . '/app/Helpers/functions.php';

\Core\Cache::init($baseDir . '/storage/cache');
\Core\Logger::init($baseDir . '/storage/logs');
\Core\I18n::init($baseDir . '/app/Languages');

class TestRunner
{
    private int $passed = 0;
    private int $failed = 0;
    private array $errors = [];

    public function run(): void
    {
        echo "\n======================================================\n";
        echo "   DISCHEDULE NATIVE TEST SUITE (PHP 8.1+)\n";
        echo "======================================================\n\n";

        $this->testAutoloader();
        $this->testValidator();
        $this->testCache();
        $this->testRecurrenceService();
        $this->testICSExportAndImport();
        $this->testStringAndDateHelpers();

        echo "\n------------------------------------------------------\n";
        echo "RESULTS: Passed: {$this->passed} | Failed: {$this->failed}\n";
        if ($this->failed > 0) {
            echo "\nFailures:\n";
            foreach ($this->errors as $err) {
                echo "  [FAIL] {$err}\n";
            }
            exit(1);
        } else {
            echo "\n✅ ALL UNIT TESTS COMPLETED SUCCESSFULLY!\n\n";
            exit(0);
        }
    }

    private function assert(bool $condition, string $message): void
    {
        if ($condition) {
            $this->passed++;
            echo "  [PASS] {$message}\n";
        } else {
            $this->failed++;
            $this->errors[] = $message;
            echo "  [FAIL] {$message}\n";
        }
    }

    private function testAutoloader(): void
    {
        echo "Running Autoloader Tests...\n";
        $this->assert(class_exists(\Core\Database::class), "Core\\Database class autoloaded successfully");
        $this->assert(class_exists(\App\Models\Event::class), "App\\Models\\Event class autoloaded successfully");
        $this->assert(class_exists(\App\Services\DiscordService::class), "App\\Services\\DiscordService class autoloaded successfully");
    }

    private function testValidator(): void
    {
        echo "\nRunning Validator Tests...\n";
        $v1 = \Core\Validator::make([
            'email' => 'valid.user@dischedule.local',
            'username' => 'admin_user',
            'password' => 'StrongP@ssw0rd!'
        ], [
            'email' => 'required|email',
            'username' => 'required|min:3|max:20',
            'password' => 'required|password_strength'
        ]);
        $this->assert($v1->passes(), "Valid user input passes all validation rules");

        $v2 = \Core\Validator::make([
            'email' => 'invalid-email',
            'password' => 'weak'
        ], [
            'email' => 'required|email',
            'password' => 'required|password_strength'
        ]);
        $this->assert($v2->fails(), "Invalid email and weak password correctly trigger failure");
    }

    private function testCache(): void
    {
        echo "\nRunning File Cache Tests...\n";
        $key = 'test_unit_key_' . time();
        \Core\Cache::set($key, ['status' => 'cached_payload', 'id' => 42], 10);

        $cached = \Core\Cache::get($key);
        $this->assert(is_array($cached) && ($cached['id'] ?? 0) === 42, "Cache writes and reads structured payload");

        \Core\Cache::delete($key);
        $this->assert(\Core\Cache::get($key) === null, "Cache key deletion cleans up stored cache file");
    }

    private function testRecurrenceService(): void
    {
        echo "\nRunning Recurrence Engine Tests...\n";
        $occurrences = \App\Services\RecurrenceService::generateOccurrences(
            'FREQ=DAILY;INTERVAL=1',
            '2026-08-01 09:00:00',
            '2026-08-01 10:00:00',
            '2026-08-01 00:00:00',
            '2026-08-05 23:59:59'
        );

        $this->assert(count($occurrences) === 5, "Daily recurrence generates exactly 5 occurrences over 5 days window");
    }

    private function testICSExportAndImport(): void
    {
        echo "\nRunning RFC 5545 iCalendar Import & Export Tests...\n";
        $events = [
            [
                'id' => 101,
                'title' => 'Sprint Demo & AMA',
                'description' => 'Review demo features with team.',
                'start_datetime' => '2026-08-20 14:00:00',
                'end_datetime' => '2026-08-20 15:30:00',
                'location' => 'Discord Voice #stage',
                'category_name' => 'Meetings',
                'status' => 'published',
                'recurrence_rule' => null
            ]
        ];

        $icsString = \App\Services\ICSExportService::export($events, 'Dischedule Unit Test Calendar');
        $this->assert(str_contains($icsString, 'BEGIN:VCALENDAR') && str_contains($icsString, 'SUMMARY:Sprint Demo & AMA'), "ICS Export builds RFC 5545 calendar string");

        $parsed = \App\Services\ICSImportService::parse($icsString);
        $this->assert(count($parsed) === 1 && $parsed[0]['title'] === 'Sprint Demo & AMA', "ICS Import parser reconstructs original event from string");
    }

    private function testStringAndDateHelpers(): void
    {
        echo "\nRunning Helper Tests...\n";
        $slug = \App\Helpers\StringHelper::slug("Dischedule 2.0: Best Discord Tool!");
        $this->assert($slug === 'dischedule-2-0-best-discord-tool', "StringHelper generates clean URL slugs");

        $discordColor = \App\Helpers\StringHelper::hexToDiscordInt('#5865F2');
        $this->assert($discordColor === 5793266, "StringHelper converts hex to accurate Discord decimal color");
    }
}

$runner = new TestRunner();
$runner->run();
