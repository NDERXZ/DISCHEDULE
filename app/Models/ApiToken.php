<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class ApiToken extends Model
{
    protected string $table = 'api_tokens';
    protected array $fillable = [
        'user_id', 'token_hash', 'name', 'permissions', 'last_used_at', 'expires_at'
    ];
    public bool $timestamps = false;

    public static function createToken(int $userId, string $name, string $permissions = 'read,write', ?int $expiresDays = null): array
    {
        $plainToken = bin2hex(random_bytes(32));
        $hash = hash('sha256', $plainToken);

        $expiresAt = $expiresDays ? date('Y-m-d H:i:s', strtotime("+{$expiresDays} days")) : null;

        $id = self::create([
            'user_id'      => $userId,
            'token_hash'   => $hash,
            'name'         => $name,
            'permissions'  => $permissions,
            'expires_at'   => $expiresAt,
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        return [
            'id'          => $id,
            'plain_token' => $plainToken,
            'name'        => $name,
            'permissions' => $permissions,
            'expires_at'  => $expiresAt
        ];
    }

    public static function findByTokenHash(string $hash): ?array
    {
        return self::findBy('token_hash', $hash);
    }

    public static function getForUser(int $userId): array
    {
        return self::db()->fetchAll(
            "SELECT id, user_id, name, permissions, last_used_at, expires_at, created_at 
             FROM `api_tokens` WHERE `user_id` = :uid ORDER BY `id` DESC",
            ['uid' => $userId]
        );
    }
}
