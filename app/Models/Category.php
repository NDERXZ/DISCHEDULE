<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Category extends Model
{
    protected string $table = 'categories';
    protected array $fillable = [
        'user_id', 'team_id', 'name', 'color', 'icon', 'is_global'
    ];

    public static function getForUser(int $userId): array
    {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM `events` e WHERE e.category_id = c.id AND e.deleted_at IS NULL) AS events_count
                FROM `categories` c
                WHERE c.is_global = 1 OR c.user_id = :uid 
                ORDER BY c.is_global DESC, c.name ASC";
        return self::db()->fetchAll($sql, ['uid' => $userId]);
    }

    public static function getGlobal(): array
    {
        $sql = "SELECT * FROM `categories` WHERE `is_global` = 1 ORDER BY `name` ASC";
        return self::db()->fetchAll($sql);
    }
}
