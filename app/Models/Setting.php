<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Setting extends Model
{
    protected string $table = 'settings';
    protected string $primaryKey = 'key';
    protected array $fillable = ['key', 'value', 'description'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::findBy('key', $key);
        return $setting !== null ? $setting['value'] : $default;
    }

    public static function set(string $key, mixed $value, ?string $description = null): void
    {
        $existing = self::findBy('key', $key);
        if ($existing) {
            $data = ['value' => (string)$value];
            if ($description !== null) {
                $data['description'] = $description;
            }
            self::db()->update('settings', $data, "`key` = :k", ['k' => $key]);
        } else {
            self::db()->insert('settings', [
                'key'         => $key,
                'value'       => (string)$value,
                'description' => $description ?? ''
            ]);
        }
    }

    public static function allSettings(): array
    {
        $rows = self::db()->fetchAll("SELECT * FROM `settings` ORDER BY `key` ASC");
        $mapped = [];
        foreach ($rows as $row) {
            $mapped[$row['key']] = $row['value'];
        }
        return $mapped;
    }
}
