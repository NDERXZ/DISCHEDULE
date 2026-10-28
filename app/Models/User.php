<?php

declare(strict_types=1);

namespace App\Models;

use Core\Database;
use Core\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = [
        'username', 'email', 'password_hash', 'avatar', 'bio', 'timezone',
        'language', 'theme', 'accent_color', 'discord_id', 'discord_username',
        'discord_avatar', 'email_verified_at', 'verification_token', 'reset_token',
        'reset_expires_at', 'remember_token', 'role', 'status', 'notify_email',
        'notify_discord', 'notify_in_app', 'deleted_at'
    ];

    public static function findByEmail(string $email): ?array
    {
        return self::findBy('email', $email);
    }

    public static function findByUsername(string $username): ?array
    {
        return self::findBy('username', $username);
    }

    public static function findByDiscordId(string $discordId): ?array
    {
        return self::findBy('discord_id', $discordId);
    }

    public static function findByVerificationToken(string $token): ?array
    {
        return self::findBy('verification_token', $token);
    }

    public static function findByResetToken(string $token): ?array
    {
        $sql = "SELECT * FROM `users` WHERE `reset_token` = :token AND `reset_expires_at` > NOW() LIMIT 1";
        return self::db()->fetch($sql, ['token' => $token]);
    }

    public static function findByRememberToken(string $token): ?array
    {
        $tokenHash = hash('sha256', $token);
        return self::findBy('remember_token', $tokenHash);
    }

    public static function sanitize(array $user): array
    {
        unset($user['password_hash'], $user['remember_token'], $user['verification_token'], $user['reset_token']);
        return $user;
    }

    public static function getEventsCount(int $userId): int
    {
        return (int)self::db()->fetchColumn("SELECT COUNT(*) FROM `events` WHERE `user_id` = :uid AND `deleted_at` IS NULL", ['uid' => $userId]);
    }

    public static function getWebhooks(int $userId): array
    {
        return self::db()->fetchAll("SELECT * FROM `webhooks` WHERE `user_id` = :uid ORDER BY `is_default` DESC, `id` DESC", ['uid' => $userId]);
    }
}
