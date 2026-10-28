<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\StringHelper;
use App\Services\RecurrenceService;
use Core\Database;
use Core\Model;

class Event extends Model
{
    protected string $table = 'events';
    protected array $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'description', 'start_datetime',
        'end_datetime', 'timezone', 'location', 'location_url', 'color', 'priority',
        'status', 'visibility', 'share_token', 'share_password_hash', 'share_expires_at',
        'recurrence_rule', 'recurrence_end', 'recurrence_count', 'parent_event_id',
        'deleted_at'
    ];

    public static function findWithDetails(int $id, ?int $userId = null): ?array
    {
        $sql = "SELECT e.*, c.name AS category_name, c.color AS category_color, c.icon AS category_icon,
                       u.username AS author_name, u.avatar AS author_avatar, u.discord_avatar AS author_discord_avatar
                FROM `events` e
                LEFT JOIN `categories` c ON c.id = e.category_id
                LEFT JOIN `users` u ON u.id = e.user_id
                WHERE e.id = :id AND e.deleted_at IS NULL";
        
        $params = ['id' => $id];
        if ($userId !== null) {
            $sql .= " AND (e.user_id = :uid OR e.visibility IN ('public', 'shared'))";
            $params['uid'] = $userId;
        }

        $event = self::db()->fetch($sql, $params);
        if (!$event) {
            return null;
        }

        $event['tags'] = Tag::getForEvent($id);
        $event['webhooks'] = Webhook::getForEvent($id);
        $event['reminders'] = Reminder::getForEvent($id);
        $event['attachments'] = Attachment::getForEvent($id);
        $event['comments'] = Comment::getForEvent($id);

        return $event;
    }

    public static function findByShareToken(string $token): ?array
    {
        $sql = "SELECT e.*, c.name AS category_name, c.color AS category_color, c.icon AS category_icon,
                       u.username AS author_name, u.avatar AS author_avatar
                FROM `events` e
                LEFT JOIN `categories` c ON c.id = e.category_id
                LEFT JOIN `users` u ON u.id = e.user_id
                WHERE e.share_token = :token 
                  AND e.deleted_at IS NULL 
                  AND (e.share_expires_at IS NULL OR e.share_expires_at > NOW())
                LIMIT 1";
        
        $event = self::db()->fetch($sql, ['token' => $token]);
        if ($event) {
            $event['tags'] = Tag::getForEvent((int)$event['id']);
            $event['attachments'] = Attachment::getForEvent((int)$event['id']);
            $event['comments'] = Comment::getForEvent((int)$event['id']);
        }
        return $event;
    }

    public static function getEventsForRange(int $userId, string $rangeStart, string $rangeEnd, array $filters = []): array
    {
        $sql = "SELECT e.*, c.name AS category_name, c.color AS category_color, c.icon AS category_icon
                FROM `events` e
                LEFT JOIN `categories` c ON c.id = e.category_id
                WHERE (e.user_id = :uid OR e.visibility = 'public')
                  AND e.deleted_at IS NULL";

        $params = [
            'uid' => $userId
        ];

        // Apply sidebar and search filters
        if (!empty($filters['category_id'])) {
            $sql .= " AND e.category_id = :cat_id";
            $params['cat_id'] = (int)$filters['category_id'];
        }

        if (!empty($filters['priority'])) {
            $sql .= " AND e.priority = :priority";
            $params['priority'] = $filters['priority'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (e.title LIKE :search OR e.description LIKE :search OR e.location LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['tag_id'])) {
            $sql .= " AND EXISTS (SELECT 1 FROM `event_tag` et WHERE et.event_id = e.id AND et.tag_id = :tag_id)";
            $params['tag_id'] = (int)$filters['tag_id'];
        }

        $user = User::find($userId);
        $userTz = $user['timezone'] ?? 'UTC';

        $allEvents = self::db()->fetchAll($sql, $params);
        $finalInstances = [];

        foreach ($allEvents as $event) {
            if (empty($event['recurrence_rule'])) {
                // Non-recurring event: check if in range
                if ($event['start_datetime'] <= $rangeEnd && $event['end_datetime'] >= $rangeStart) {
                    $localStart = \App\Helpers\DateHelper::formatToUserTimezone($event['start_datetime'], $userTz, 'Y-m-d H:i:s');
                    $localEnd = \App\Helpers\DateHelper::formatToUserTimezone($event['end_datetime'], $userTz, 'Y-m-d H:i:s');
                    $finalInstances[] = self::formatCalendarItem($event, $localStart, $localEnd);
                }
            } else {
                // Recurring event: expand occurrences within range
                $occurrences = RecurrenceService::generateOccurrences(
                    $event['recurrence_rule'],
                    $event['start_datetime'],
                    $event['end_datetime'],
                    $rangeStart,
                    $rangeEnd,
                    $event['recurrence_end'] ?? null,
                    $event['recurrence_count'] ? (int)$event['recurrence_count'] : null
                );

                foreach ($occurrences as $occ) {
                    $localStart = \App\Helpers\DateHelper::formatToUserTimezone($occ['start'], $userTz, 'Y-m-d H:i:s');
                    $localEnd = \App\Helpers\DateHelper::formatToUserTimezone($occ['end'], $userTz, 'Y-m-d H:i:s');
                    $finalInstances[] = self::formatCalendarItem($event, $localStart, $localEnd, true);
                }
            }
        }

        return $finalInstances;
    }

    private static function formatCalendarItem(array $event, string $start, string $end, bool $isRecurring = false): array
    {
        $color = !empty($event['color']) ? $event['color'] : (!empty($event['category_color']) ? $event['category_color'] : '#4F46E5');

        return [
            'id'             => (int)$event['id'],
            'title'          => $event['title'],
            'start'          => $start,
            'end'            => $end,
            'color'          => $color,
            'category_name'  => $event['category_name'] ?? 'General',
            'category_icon'  => $event['category_icon'] ?? 'fa-solid fa-calendar',
            'location'       => $event['location'] ?? '',
            'priority'       => $event['priority'] ?? 'medium',
            'status'         => $event['status'] ?? 'published',
            'visibility'     => $event['visibility'] ?? 'private',
            'is_recurring'   => $isRecurring || !empty($event['recurrence_rule']),
            'description'    => $event['description'] ?? '',
            'url'            => url('events/' . $event['id'])
        ];
    }

    public static function duplicate(int $id, int $userId): int
    {
        $event = self::find($id);
        if (!$event) {
            throw new \RuntimeException("Event not found to duplicate.");
        }

        $newTitle = $event['title'] . ' (Copy)';
        $newSlug = StringHelper::slug($newTitle) . '-' . time();

        $newEventId = self::create([
            'user_id'          => $userId,
            'category_id'      => $event['category_id'],
            'title'            => $newTitle,
            'slug'             => $newSlug,
            'description'      => $event['description'],
            'start_datetime'   => $event['start_datetime'],
            'end_datetime'     => $event['end_datetime'],
            'timezone'         => $event['timezone'],
            'location'         => $event['location'],
            'location_url'     => $event['location_url'],
            'color'            => $event['color'],
            'priority'         => $event['priority'],
            'status'           => 'draft',
            'visibility'       => 'private',
            'recurrence_rule'  => $event['recurrence_rule'],
            'recurrence_end'   => $event['recurrence_end'],
            'recurrence_count' => $event['recurrence_count']
        ]);

        // Copy tags
        $tags = Tag::getForEvent($id);
        foreach ($tags as $t) {
            self::db()->insert('event_tag', ['event_id' => $newEventId, 'tag_id' => $t['id']]);
        }

        // Copy webhooks
        $webhooks = Webhook::getForEvent($id);
        foreach ($webhooks as $w) {
            self::db()->insert('event_webhook', ['event_id' => $newEventId, 'webhook_id' => $w['id']]);
        }

        return $newEventId;
    }

    public static function bulkDelete(array $ids, int $userId, bool $isAdmin = false): int
    {
        if (empty($ids)) return 0;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $params = $ids;

        $sql = "UPDATE `events` SET `deleted_at` = NOW() WHERE `id` IN ({$placeholders})";
        if (!$isAdmin) {
            $sql .= " AND `user_id` = ?";
            $params[] = $userId;
        }

        $stmt = self::db()->query($sql, $params);
        return $stmt->rowCount();
    }

    public static function bulkArchive(array $ids, int $userId, bool $isAdmin = false): int
    {
        if (empty($ids)) return 0;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $params = array_merge(['archived'], $ids);

        $sql = "UPDATE `events` SET `status` = ? WHERE `id` IN ({$placeholders})";
        if (!$isAdmin) {
            $sql .= " AND `user_id` = ?";
            $params[] = $userId;
        }

        $stmt = self::db()->query($sql, $params);
        return $stmt->rowCount();
    }

    public static function bulkChangeCategory(array $ids, int $categoryId, int $userId, bool $isAdmin = false): int
    {
        if (empty($ids)) return 0;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $params = array_merge([$categoryId], $ids);

        $sql = "UPDATE `events` SET `category_id` = ? WHERE `id` IN ({$placeholders})";
        if (!$isAdmin) {
            $sql .= " AND `user_id` = ?";
            $params[] = $userId;
        }

        $stmt = self::db()->query($sql, $params);
        return $stmt->rowCount();
    }
}
