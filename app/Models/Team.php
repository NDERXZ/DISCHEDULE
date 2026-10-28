<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Team extends Model
{
    protected string $table = 'teams';
    protected array $fillable = ['owner_id', 'name', 'description'];

    public static function getForUser(int $userId): array
    {
        $sql = "SELECT t.*, tm.role AS user_role, u.username AS owner_name,
                (SELECT COUNT(*) FROM `team_members` WHERE `team_id` = t.id) AS members_count
                FROM `teams` t
                INNER JOIN `team_members` tm ON tm.team_id = t.id
                INNER JOIN `users` u ON u.id = t.owner_id
                WHERE tm.user_id = :uid
                ORDER BY t.name ASC";
        return self::db()->fetchAll($sql, ['uid' => $userId]);
    }

    public static function getMembers(int $teamId): array
    {
        $sql = "SELECT u.id, u.username, u.email, u.avatar, u.discord_avatar, u.role AS site_role, tm.role AS team_role, tm.created_at AS joined_at
                FROM `team_members` tm
                INNER JOIN `users` u ON u.id = tm.user_id
                WHERE tm.team_id = :tid
                ORDER BY FIELD(tm.role, 'owner', 'admin', 'member'), u.username ASC";
        return self::db()->fetchAll($sql, ['tid' => $teamId]);
    }

    public static function addMember(int $teamId, int $userId, string $role = 'member'): void
    {
        self::db()->query(
            "INSERT INTO `team_members` (`team_id`, `user_id`, `role`, `created_at`) 
             VALUES (:tid, :uid, :role, NOW()) 
             ON DUPLICATE KEY UPDATE `role` = :role_up",
            ['tid' => $teamId, 'uid' => $userId, 'role' => $role, 'role_up' => $role]
        );
    }

    public static function removeMember(int $teamId, int $userId): void
    {
        self::db()->delete('team_members', "`team_id` = :tid AND `user_id` = :uid", ['tid' => $teamId, 'uid' => $userId]);
    }

    public static function isMember(int $teamId, int $userId): bool
    {
        $count = (int)self::db()->fetchColumn(
            "SELECT COUNT(*) FROM `team_members` WHERE `team_id` = :tid AND `user_id` = :uid",
            ['tid' => $teamId, 'uid' => $userId]
        );
        return $count > 0;
    }

    public static function userRole(int $teamId, int $userId): ?string
    {
        $res = self::db()->fetch(
            "SELECT `role` FROM `team_members` WHERE `team_id` = :tid AND `user_id` = :uid LIMIT 1",
            ['tid' => $teamId, 'uid' => $userId]
        );
        return $res ? $res['role'] : null;
    }
}
