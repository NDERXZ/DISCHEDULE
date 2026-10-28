<?php

declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

/**
 * Dischedule Database Layer (PDO Singleton)
 */
class Database
{
    private static ?Database $instance = null;
    private ?PDO $pdo = null;

    private function __construct()
    {
        $config = require dirname(__DIR__) . '/app/Config/database.php';
        $driver = $config['default'] ?? 'mysql';
        $dbConfig = $config['connections'][$driver] ?? [];

        $host = $dbConfig['host'] ?? '127.0.0.1';
        $port = $dbConfig['port'] ?? 3306;
        $dbname = $dbConfig['database'] ?? 'dischedule';
        $charset = $dbConfig['charset'] ?? 'utf8mb4';
        $username = $dbConfig['username'] ?? 'root';
        $password = $dbConfig['password'] ?? '';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE " . ($dbConfig['collation'] ?? 'utf8mb4_unicode_ci')
        ];

        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            Logger::error("Database Connection Failed: " . $e->getMessage());
            throw new RuntimeException("Database connection error: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get underlying PDO instance
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Run a prepared statement with parameters
     *
     * @param string $sql
     * @param array<int|string, mixed> $params
     * @return PDOStatement
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            Logger::error("SQL Query Error: {$e->getMessage()} | SQL: {$sql} | Params: " . json_encode($params));
            throw $e;
        }
    }

    /**
     * Fetch a single row
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    /**
     * Fetch all rows
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Fetch a single scalar column value
     */
    public function fetchColumn(string $sql, array $params = [], int $columnIndex = 0): mixed
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn($columnIndex);
    }

    /**
     * Insert record and return last insert ID
     */
    public function insert(string $table, array $data): int
    {
        $keys = array_keys($data);
        $fields = implode('`, `', $keys);
        $placeholders = ':' . implode(', :', $keys);

        $sql = "INSERT INTO `{$table}` (`{$fields}`) VALUES ({$placeholders})";
        $this->query($sql, $data);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update records by conditions
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $setClauses = [];
        $params = [];

        foreach ($data as $key => $value) {
            $setClauses[] = "`{$key}` = :set_{$key}";
            $params["set_{$key}"] = $value;
        }

        $sql = "UPDATE `{$table}` SET " . implode(', ', $setClauses) . " WHERE {$where}";
        $mergedParams = array_merge($params, $whereParams);
        $stmt = $this->query($sql, $mergedParams);

        return $stmt->rowCount();
    }

    /**
     * Delete records by condition
     */
    public function delete(string $table, string $where, array $whereParams = []): int
    {
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        $stmt = $this->query($sql, $whereParams);
        return $stmt->rowCount();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollBack(): bool
    {
        if ($this->pdo->inTransaction()) {
            return $this->pdo->rollBack();
        }
        return false;
    }

    /**
     * Check if in transaction
     */
    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }
}
