<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Notification extends Model
{
    protected string $table = 'notifications';
    protected array $fillable = [
        'user_id', 'type', 'title', 'message', 'link', 'is_read', 'read_at'
    ];
    public bool $timestamps = false;

    public static function getForUser(int $userId, int $limit = 20): array
    {
        return self::db()->fetchAll(
            "SELECT * FROM `notifications` WHERE `user_id` = :uid ORDER BY `is_read` ASC, `created_at` DESC LIMIT {$limit}",
            ['uid' => $userId]
        );
    }

    public static function unreadCount(int $userId): int
    {
        return (int)self::db()->fetchColumn(
            "SELECT COUNT(*) FROM `notifications` WHERE `user_id` = :uid AND `is_read` = 0",
            ['uid' => $userId]
        );
    }

    public static function markAsRead(int $id, int $userId): void
    {
        self::db()->update(
            'notifications',
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
            "`id` = :id AND `user_id` = :uid",
            ['id' => $id, 'uid' => $userId]
        );
    }

    public static function markAllAsRead(int $userId): void
    {
        self::db()->update(
            'notifications',
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
            "`user_id` = :uid AND `is_read` = 0",
            ['uid' => $userId]
        );
    }

    public static function clearAll(int $userId): void
    {
        self::db()->delete('notifications', "`user_id` = :uid", ['uid' => $userId]);
    }

    public static function notify(int $userId, string $title, string $message, ?string $link = null, string $type = 'info'): int
    {
        return self::create([
            'user_id'    => $userId,
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'link'       => $link,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
