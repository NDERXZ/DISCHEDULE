<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Attachment extends Model
{
    protected string $table = 'attachments';
    protected array $fillable = [
        'event_id', 'user_id', 'filename', 'original_name', 'mime_type', 'size', 'path'
    ];
    public bool $timestamps = false;

    public static function getForEvent(int $eventId): array
    {
        return self::db()->fetchAll(
            "SELECT a.*, u.username FROM `attachments` a 
             INNER JOIN `users` u ON u.id = a.user_id 
             WHERE a.event_id = :eid ORDER BY a.id ASC",
            ['eid' => $eventId]
        );
    }

    public static function deleteWithFile(int $id): bool
    {
        $att = self::find($id);
        if (!$att) {
            return false;
        }

        if (!empty($att['path']) && file_exists($att['path'])) {
            @unlink($att['path']);
        }

        self::delete($id);
        return true;
    }
}
