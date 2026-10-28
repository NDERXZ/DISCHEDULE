<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Comment extends Model
{
    protected string $table = 'comments';
    protected array $fillable = ['event_id', 'user_id', 'content'];

    public static function getForEvent(int $eventId): array
    {
        $sql = "SELECT c.*, u.username, u.avatar, u.discord_avatar, u.discord_username
                FROM `comments` c
                INNER JOIN `users` u ON u.id = c.user_id
                WHERE c.event_id = :eid
                ORDER BY c.created_at ASC";
        return self::db()->fetchAll($sql, ['eid' => $eventId]);
    }
}
