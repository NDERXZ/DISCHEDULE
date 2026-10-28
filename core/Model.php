<?php

declare(strict_types=1);

namespace Core;

/**
 * Dischedule Base Model
 */
abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $attributes = [];
    protected bool $timestamps = true;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    protected static function db(): Database
    {
        return Database::getInstance();
    }

    public static function getTable(): string
    {
        $instance = new static();
        return $instance->table;
    }

    public static function find(int|string $id): ?array
    {
        $instance = new static();
        $sql = "SELECT * FROM `{$instance->table}` WHERE `{$instance->primaryKey}` = :id LIMIT 1";
        return self::db()->fetch($sql, ['id' => $id]);
    }

    public static function findBy(string $column, mixed $value): ?array
    {
        $instance = new static();
        $sql = "SELECT * FROM `{$instance->table}` WHERE `{$column}` = :val LIMIT 1";
        return self::db()->fetch($sql, ['val' => $value]);
    }

    public static function all(string $orderBy = 'id DESC', ?int $limit = null, int $offset = 0): array
    {
        $instance = new static();
        $sql = "SELECT * FROM `{$instance->table}` ORDER BY {$orderBy}";
        if ($limit !== null) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        return self::db()->fetchAll($sql);
    }

    public static function where(string $condition, array $params = [], string $orderBy = 'id DESC', ?int $limit = null, int $offset = 0): array
    {
        $instance = new static();
        $sql = "SELECT * FROM `{$instance->table}` WHERE {$condition} ORDER BY {$orderBy}";
        if ($limit !== null) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        return self::db()->fetchAll($sql, $params);
    }

    public static function count(string $where = '', array $params = []): int
    {
        $instance = new static();
        $sql = "SELECT COUNT(*) FROM `{$instance->table}`";
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        return (int)self::db()->fetchColumn($sql, $params);
    }

    public static function paginate(int $page = 1, int $perPage = 15, string $where = '', array $params = [], string $orderBy = 'id DESC'): array
    {
        $instance = new static();
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) FROM `{$instance->table}`" . (!empty($where) ? " WHERE {$where}" : '');
        $totalItems = (int)self::db()->fetchColumn($countSql, $params);
        $totalPages = (int)ceil($totalItems / $perPage);

        $dataSql = "SELECT * FROM `{$instance->table}`" . (!empty($where) ? " WHERE {$where}" : '') . " ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
        $items = self::db()->fetchAll($dataSql, $params);

        return [
            'items'        => $items,
            'total'        => $totalItems,
            'per_page'     => $perPage,
            'current_page' => $page,
            'total_pages'  => max(1, $totalPages),
            'has_more'     => $page < $totalPages
        ];
    }

    public static function create(array $data): int
    {
        $instance = new static();
        
        if ($instance->timestamps && !isset($data['created_at'])) {
            if (empty($instance->fillable) || in_array('created_at', $instance->fillable, true)) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }
        }

        // Filter by fillable if defined
        if (!empty($instance->fillable)) {
            $data = array_intersect_key($data, array_flip($instance->fillable));
        }

        return self::db()->insert($instance->table, $data);
    }

    public static function update(int|string $id, array $data): int
    {
        $instance = new static();

        if ($instance->timestamps && !isset($data['updated_at'])) {
            if (empty($instance->fillable) || in_array('updated_at', $instance->fillable, true)) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }
        }

        if (!empty($instance->fillable)) {
            $data = array_intersect_key($data, array_flip($instance->fillable));
        }

        return self::db()->update($instance->table, $data, "`{$instance->primaryKey}` = :pk_id", ['pk_id' => $id]);
    }

    public static function delete(int|string $id): int
    {
        $instance = new static();
        return self::db()->delete($instance->table, "`{$instance->primaryKey}` = :id", ['id' => $id]);
    }

    public static function softDelete(int|string $id): int
    {
        $instance = new static();
        return self::db()->update($instance->table, ['deleted_at' => date('Y-m-d H:i:s')], "`{$instance->primaryKey}` = :id", ['id' => $id]);
    }

    public static function raw(string $sql, array $params = []): array
    {
        return self::db()->fetchAll($sql, $params);
    }
}
