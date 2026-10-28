<?php

declare(strict_types=1);

namespace App\Services;

use DateTime;
use DateTimeZone;

/**
 * Dischedule iCalendar (.ics) RFC 5545 Export Service
 */
class ICSExportService
{
    public static function export(array $events, string $calendarName = 'Dischedule Calendar'): string
    {
        $lines = [];
        $lines[] = 'BEGIN:VCALENDAR';
        $lines[] = 'VERSION:2.0';
        $lines[] = 'PRODID:-//Dischedule//Event Management v2.0//EN';
        $lines[] = 'CALSCALE:GREGORIAN';
        $lines[] = 'METHOD:PUBLISH';
        $lines[] = 'X-WR-CALNAME:' . self::escape($calendarName);
        $lines[] = 'X-WR-TIMEZONE:UTC';

        foreach ($events as $event) {
            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:event-' . $event['id'] . '@dischedule.local';
            $lines[] = 'DTSTAMP:' . gmdate('Ymd\THis\Z');

            $startDt = new DateTime($event['start_datetime'], new DateTimeZone('UTC'));
            $endDt = new DateTime($event['end_datetime'], new DateTimeZone('UTC'));

            $lines[] = 'DTSTART:' . $startDt->format('Ymd\THis\Z');
            $lines[] = 'DTEND:' . $endDt->format('Ymd\THis\Z');

            $lines[] = 'SUMMARY:' . self::escape($event['title']);

            if (!empty($event['description'])) {
                $lines[] = 'DESCRIPTION:' . self::escape(strip_tags($event['description']));
            }

            if (!empty($event['location'])) {
                $lines[] = 'LOCATION:' . self::escape($event['location']);
            }

            if (!empty($event['category_name'])) {
                $lines[] = 'CATEGORIES:' . self::escape($event['category_name']);
            }

            if (!empty($event['status'])) {
                $lines[] = 'STATUS:' . strtoupper($event['status'] === 'published' ? 'CONFIRMED' : 'TENTATIVE');
            }

            if (!empty($event['recurrence_rule'])) {
                $lines[] = 'RRULE:' . $event['recurrence_rule'];
            }

            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        // Fold lines according to RFC 5545 (max 75 octets per line)
        $formattedLines = [];
        foreach ($lines as $line) {
            $formattedLines[] = self::foldLine($line);
        }

        return implode("\r\n", $formattedLines) . "\r\n";
    }

    private static function escape(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(';', '\;', $text);
        $text = str_replace(',', '\,', $text);
        $text = str_replace(["\r\n", "\n", "\r"], '\n', $text);
        return $text;
    }

    private static function foldLine(string $line): string
    {
        if (strlen($line) <= 75) {
            return $line;
        }

        $chunks = [];
        while (strlen($line) > 75) {
            $chunks[] = substr($line, 0, 75);
            $line = ' ' . substr($line, 75);
        }
        $chunks[] = $line;

        return implode("\r\n", $chunks);
    }
}
