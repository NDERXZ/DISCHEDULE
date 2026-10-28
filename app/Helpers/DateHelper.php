<?php

declare(strict_types=1);

namespace App\Helpers;

use DateTime;
use DateTimeZone;

/**
 * Dischedule Date & Timezone Helper
 */
class DateHelper
{
    public static function formatToUserTimezone(string|DateTime $datetime, string $userTimezone = 'UTC', string $format = 'Y-m-d H:i'): string
    {
        if (is_string($datetime)) {
            $dt = new DateTime($datetime, new DateTimeZone('UTC'));
        } else {
            $dt = clone $datetime;
        }

        try {
            $dt->setTimezone(new DateTimeZone($userTimezone));
        } catch (\Throwable) {
            $dt->setTimezone(new DateTimeZone('UTC'));
        }

        return $dt->format($format);
    }

    public static function convertUserToUtc(string $datetime, string $userTimezone = 'UTC', string $format = 'Y-m-d H:i:s'): string
    {
        try {
            $tz = new DateTimeZone($userTimezone);
        } catch (\Throwable) {
            $tz = new DateTimeZone('UTC');
        }

        $dt = new DateTime($datetime, $tz);
        $dt->setTimezone(new DateTimeZone('UTC'));
        return $dt->format($format);
    }

    public static function toIsoString(string|DateTime $datetime, string $userTimezone = 'UTC'): string
    {
        return self::formatToUserTimezone($datetime, $userTimezone, 'c');
    }

    public static function humanReadable(string|DateTime $datetime, string $userTimezone = 'UTC'): string
    {
        return self::formatToUserTimezone($datetime, $userTimezone, 'M j, Y g:i A');
    }

    public static function timeAgo(string|DateTime $datetime): string
    {
        if (is_string($datetime)) {
            $time = strtotime($datetime);
        } else {
            $time = $datetime->getTimestamp();
        }

        $diff = time() - $time;

        if ($diff < 0) {
            $absDiff = abs($diff);
            if ($absDiff < 60) return "in {$absDiff} seconds";
            if ($absDiff < 3600) return "in " . round($absDiff / 60) . " minutes";
            if ($absDiff < 86400) return "in " . round($absDiff / 3600) . " hours";
            if ($absDiff < 2592000) return "in " . round($absDiff / 86400) . " days";
            return "in " . round($absDiff / 2592000) . " months";
        }

        if ($diff < 60) return "just now";
        if ($diff < 3600) return round($diff / 60) . " min ago";
        if ($diff < 86400) return round($diff / 3600) . " hrs ago";
        if ($diff < 2592000) return round($diff / 86400) . " days ago";
        return date('M j, Y', $time);
    }

    public static function getTimezonesList(): array
    {
        return DateTimeZone::listIdentifiers();
    }
}
