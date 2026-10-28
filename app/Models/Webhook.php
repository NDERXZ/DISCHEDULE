<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Webhook extends Model
{
    protected string $table = 'webhooks';
    protected array $fillable = [
        'user_id', 'name', 'url', 'channel_id', 'guild_id', 'mention_role', 'is_default'
    ];

    public static function getForUser(int $userId): array
    {
        return self::db()->fetchAll("SELECT * FROM `webhooks` WHERE `user_id` = :uid ORDER BY `is_default` DESC, `id` DESC", ['uid' => $userId]);
    }

    public static function getDefault(int $userId): ?array
    {
        return self::db()->fetch("SELECT * FROM `webhooks` WHERE `user_id` = :uid AND `is_default` = 1 LIMIT 1", ['uid' => $userId]);
    }

    public static function setDefault(int $webhookId, int $userId): void
    {
        self::db()->update('webhooks', ['is_default' => 0], "`user_id` = :uid", ['uid' => $userId]);
        self::db()->update('webhooks', ['is_default' => 1], "`id` = :id AND `user_id` = :uid", ['id' => $webhookId, 'uid' => $userId]);
    }

    public static function getForEvent(int $eventId): array
    {
        $sql = "SELECT w.* FROM `webhooks` w
                INNER JOIN `event_webhook` ew ON ew.webhook_id = w.id
                WHERE ew.event_id = :event_id";
        return self::db()->fetchAll($sql, ['event_id' => $eventId]);
    }

    public static function syncEventWebhooks(int $eventId, array $webhookIds): void
    {
        self::db()->delete('event_webhook', "`event_id` = :eid", ['eid' => $eventId]);

        foreach ($webhookIds as $webhookId) {
            $webhookId = (int)$webhookId;
            if ($webhookId > 0) {
                self::db()->insert('event_webhook', [
                    'event_id'   => $eventId,
                    'webhook_id' => $webhookId
                ]);
            }
        }
    }
}
