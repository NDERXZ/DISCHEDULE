<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;
use DateTime;

class Reminder extends Model
{
    protected string $table = 'reminders';
    protected array $fillable = [
        'event_id', 'remind_at', 'minutes_before', 'type', 'sent_at', 'status', 'error_message'
    ];

    public static function getForEvent(int $eventId): array
    {
        return self::db()->fetchAll("SELECT * FROM `reminders` WHERE `event_id` = :eid ORDER BY `minutes_before` ASC", ['eid' => $eventId]);
    }

    public static function syncEventReminders(int $eventId, array $reminders, string $startDatetime): void
    {
        self::db()->delete('reminders', "`event_id` = :eid AND `status` = 'pending'", ['eid' => $eventId]);

        $eventDt = new DateTime($startDatetime);

        foreach ($reminders as $reminder) {
            $minutesBefore = isset($reminder['minutes_before']) ? (int)$reminder['minutes_before'] : 0;
            $type = $reminder['type'] ?? 'discord';

            $remindDt = clone $eventDt;
            if ($minutesBefore > 0) {
                $remindDt->modify("-{$minutesBefore} minutes");
            }

            self::create([
                'event_id'       => $eventId,
                'remind_at'      => $remindDt->format('Y-m-d H:i:s'),
                'minutes_before' => $minutesBefore,
                'type'           => $type,
                'status'         => 'pending'
            ]);
        }
    }

    public static function getDueReminders(): array
    {
        $sql = "SELECT r.*, e.title, e.description, e.start_datetime, e.end_datetime, e.location, e.color, 
                       e.user_id, e.recurrence_rule, e.recurrence_end, e.recurrence_count,
                       u.username, u.email, u.timezone, u.discord_id
                FROM `reminders` r
                INNER JOIN `events` e ON e.id = r.event_id
                INNER JOIN `users` u ON u.id = e.user_id
                WHERE r.status = 'pending' 
                  AND r.remind_at <= UTC_TIMESTAMP()
                  AND e.deleted_at IS NULL
                ORDER BY r.remind_at ASC
                LIMIT 50";
        return self::db()->fetchAll($sql);
    }
}
