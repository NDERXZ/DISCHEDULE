<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\StringHelper;
use Core\Model;

class Tag extends Model
{
    protected string $table = 'tags';
    protected array $fillable = ['name', 'slug'];
    public bool $timestamps = false;

    public static function findOrCreate(string $name): int
    {
        $name = trim($name);
        if (empty($name)) {
            return 0;
        }

        $slug = StringHelper::slug($name);
        $existing = self::findBy('slug', $slug);
        if ($existing) {
            return (int)$existing['id'];
        }

        return self::create([
            'name' => $name,
            'slug' => $slug
        ]);
    }

    public static function getForEvent(int $eventId): array
    {
        $sql = "SELECT t.* FROM `tags` t
                INNER JOIN `event_tag` et ON et.tag_id = t.id
                WHERE et.event_id = :event_id
                ORDER BY t.name ASC";
        return self::db()->fetchAll($sql, ['event_id' => $eventId]);
    }

    public static function syncEventTags(int $eventId, array|string $tags): void
    {
        self::db()->delete('event_tag', "`event_id` = :eid", ['eid' => $eventId]);

        if (is_string($tags)) {
            $tags = array_filter(array_map('trim', explode(',', $tags)));
        }

        foreach ($tags as $tagName) {
            $tagId = self::findOrCreate((string)$tagName);
            if ($tagId > 0) {
                self::db()->insert('event_tag', [
                    'event_id' => $eventId,
                    'tag_id'   => $tagId
                ]);
            }
        }
    }

    public static function popular(int $limit = 20): array
    {
        $sql = "SELECT t.*, COUNT(et.event_id) AS event_count
                FROM `tags` t
                INNER JOIN `event_tag` et ON et.tag_id = t.id
                GROUP BY t.id
                ORDER BY event_count DESC, t.name ASC
                LIMIT {$limit}";
        return self::db()->fetchAll($sql);
    }
}
