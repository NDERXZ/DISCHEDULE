<?php

declare(strict_types=1);

namespace App\Services;

use DateTime;
use DateTimeZone;

/**
 * Dischedule iCalendar (.ics) RFC 5545 Import Parser
 */
class ICSImportService
{
    public static function parse(string $icsContent): array
    {
        $events = [];
        $unfoldedContent = self::unfold($icsContent);
        $lines = preg_split("/\r\n|\n|\r/", $unfoldedContent);

        $inVevent = false;
        $currentEvent = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (strtoupper($line) === 'BEGIN:VEVENT') {
                $inVevent = true;
                $currentEvent = [
                    'title'           => 'Untitled Event',
                    'description'     => '',
                    'start_datetime'  => date('Y-m-d H:i:s'),
                    'end_datetime'    => date('Y-m-d H:i:s', strtotime('+1 hour')),
                    'location'        => '',
                    'recurrence_rule' => null,
                    'category_name'   => null
                ];
                continue;
            }

            if (strtoupper($line) === 'END:VEVENT') {
                if ($inVevent && !empty($currentEvent)) {
                    $events[] = $currentEvent;
                }
                $inVevent = false;
                $currentEvent = [];
                continue;
            }

            if (!$inVevent) continue;

            $parts = explode(':', $line, 2);
            if (count($parts) !== 2) continue;

            $propWithParams = strtoupper($parts[0]);
            $propParts = explode(';', $propWithParams);
            $propName = $propParts[0];
            $value = $parts[1];

            switch ($propName) {
                case 'SUMMARY':
                    $currentEvent['title'] = self::unescape($value);
                    break;
                case 'DESCRIPTION':
                    $currentEvent['description'] = self::unescape($value);
                    break;
                case 'LOCATION':
                    $currentEvent['location'] = self::unescape($value);
                    break;
                case 'DTSTART':
                    $parsed = self::parseIcsDate($value);
                    if ($parsed) $currentEvent['start_datetime'] = $parsed;
                    break;
                case 'DTEND':
                    $parsed = self::parseIcsDate($value);
                    if ($parsed) $currentEvent['end_datetime'] = $parsed;
                    break;
                case 'RRULE':
                    $currentEvent['recurrence_rule'] = trim($value);
                    break;
                case 'CATEGORIES':
                    $currentEvent['category_name'] = self::unescape($value);
                    break;
            }
        }

        return $events;
    }

    private static function unfold(string $content): string
    {
        return preg_replace("/\r\n[ \t]|\n[ \t]|\r[ \t]/", '', $content);
    }

    private static function unescape(string $text): string
    {
        $text = str_replace(['\n', '\N'], "\n", $text);
        $text = str_replace('\,', ',', $text);
        $text = str_replace('\;', ';', $text);
        $text = str_replace('\\\\', '\\', $text);
        return $text;
    }

    private static function parseIcsDate(string $val): ?string
    {
        $val = trim($val);
        // Formats: 20260815T183000Z, 20260815T183000, 20260815
        if (preg_match('/^(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})Z?$/', $val, $m)) {
            return "{$m[1]}-{$m[2]}-{$m[3]} {$m[4]}:{$m[5]}:{$m[6]}";
        }
        if (preg_match('/^(\d{4})(\d{2})(\d{2})$/', $val, $m)) {
            return "{$m[1]}-{$m[2]}-{$m[3]} 00:00:00";
        }
        return null;
    }
}
