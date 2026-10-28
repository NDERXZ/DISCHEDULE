<?php

declare(strict_types=1);

namespace App\Services;

use DateTime;
use DateInterval;

/**
 * Dischedule Recurrence Rule (RRULE) Calculation Service
 */
class RecurrenceService
{
    /**
     * Generate instances of recurring events in a date window
     */
    public static function generateOccurrences(
        string $rruleStr,
        string $eventStart,
        string $eventEnd,
        string $windowStart,
        string $windowEnd,
        ?string $recurrenceEnd = null,
        ?int $recurrenceCount = null
    ): array {
        $occurrences = [];
        $rule = self::parseRrule($rruleStr);
        $freq = strtoupper($rule['FREQ'] ?? 'DAILY');
        $interval = max(1, (int)($rule['INTERVAL'] ?? 1));
        $byDay = !empty($rule['BYDAY']) ? explode(',', strtoupper($rule['BYDAY'])) : [];

        $origStartDt = new DateTime($eventStart);
        $origEndDt = new DateTime($eventEnd);
        $durationSeconds = $origEndDt->getTimestamp() - $origStartDt->getTimestamp();

        $winStartDt = new DateTime($windowStart);
        $winEndDt = new DateTime($windowEnd);
        $untilDt = !empty($recurrenceEnd) ? new DateTime($recurrenceEnd) : null;

        $currentStart = clone $origStartDt;
        $count = 0;
        $maxIterations = 500; // Safeguard against infinite loops

        while ($maxIterations-- > 0) {
            $count++;

            if ($recurrenceCount !== null && $count > $recurrenceCount) {
                break;
            }

            if ($untilDt !== null && $currentStart > $untilDt) {
                break;
            }

            // Stop if past the requested window (unless daily/weekly might wrap)
            if ($currentStart > $winEndDt) {
                break;
            }

            $currentEnd = clone $currentStart;
            $currentEnd->modify("+{$durationSeconds} seconds");

            // Check if falls in window
            if ($currentStart <= $winEndDt && $currentEnd >= $winStartDt) {
                $occurrences[] = [
                    'start' => $currentStart->format('Y-m-d H:i:s'),
                    'end'   => $currentEnd->format('Y-m-d H:i:s')
                ];
            }

            // Advance date based on frequency
            switch ($freq) {
                case 'DAILY':
                    $currentStart->modify("+{$interval} day");
                    break;

                case 'WEEKLY':
                    if (empty($byDay)) {
                        $currentStart->modify("+{$interval} week");
                    } else {
                        // Advance day by day until matching one of the BYDAY days
                        $dayCodeMap = [
                            'MO' => 1, 'TU' => 2, 'WE' => 3, 'TH' => 4, 'FR' => 5, 'SA' => 6, 'SU' => 7
                        ];
                        $targetDays = array_map(fn($d) => $dayCodeMap[$d] ?? 0, $byDay);

                        do {
                            $currentStart->modify('+1 day');
                            $currentDayNum = (int)$currentStart->format('N');
                        } while (!in_array($currentDayNum, $targetDays, true) && $maxIterations-- > 0);
                    }
                    break;

                case 'MONTHLY':
                    $currentStart->modify("+{$interval} month");
                    break;

                case 'YEARLY':
                    $currentStart->modify("+{$interval} year");
                    break;

                default:
                    $currentStart->modify('+1 day');
                    break;
            }
        }

        return $occurrences;
    }

    public static function parseRrule(string $rrule): array
    {
        $parts = explode(';', trim($rrule));
        $data = [];
        foreach ($parts as $part) {
            $kv = explode('=', $part, 2);
            if (count($kv) === 2) {
                $data[trim($kv[0])] = trim($kv[1]);
            }
        }
        return $data;
    }
}
