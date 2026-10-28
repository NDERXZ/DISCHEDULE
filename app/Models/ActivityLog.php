<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class ActivityLog extends Model
{
    protected string $table = 'activity_logs';
    protected array $fillable = [
        'user_id', 'event_id', 'team_id', 'action', 'description', 'ip_address', 'user_agent'
    ];
    public bool $timestamps = false;

    public static function log(
        ?int $userId,
        string $action,
        string $description,
        ?int $eventId = null,
        ?int $teamId = null
    ): int {
        return self::create([
            'user_id'     => $userId,
            'event_id'    => $eventId,
            'team_id'     => $teamId,
            'action'      => $action,
            'description' => $description,
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent'  => substr($_SERVER['HTTP_USER_AGENT'] ?? 'CLI', 0, 250),
            'created_at'  => date('Y-m-d H:i:s')
        ]);
    }

    public static function getRecent(int $limit = 50): array
    {
        $sql = "SELECT a.*, u.username, u.email, e.title AS event_title, t.name AS team_name
                FROM `activity_logs` a
                LEFT JOIN `users` u ON u.id = a.user_id
                LEFT JOIN `events` e ON e.id = a.event_id
                LEFT JOIN `teams` t ON t.id = a.team_id
                ORDER BY a.created_at DESC
                LIMIT {$limit}";
        return self::db()->fetchAll($sql);
    }
}
