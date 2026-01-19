<?php

namespace App\Infrastructure\Database;

use PDO;
use PDOException;
use RuntimeException;

class Connection
{
    private static ?PDO $instance = null;
    private static array $config = [];

    private function __construct()
    {
        // Private constructor to prevent direct instantiation
    }

    public static function setConfig(array $config): void
    {
        self::$config = $config;
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
        }

        return self::$instance;
    }

    private static function createConnection(): PDO
    {
        $config = self::$config;

        if (empty($config)) {
            throw new RuntimeException('Database configuration not set');
        }

        $dbPath = $config['path'] ?? './database/app.db';

        // Ensure database directory exists
        $dbDir = dirname($dbPath);
        if (!is_dir($dbDir)) {
            if (!mkdir($dbDir, 0755, true)) {
                throw new RuntimeException("Failed to create database directory: {$dbDir}");
            }
        }

        try {
            $pdo = new PDO(
                "sqlite:{$dbPath}",
                null,
                null,
                $config['options'] ?? []
            );

            return $pdo;
        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed: " . $e->getMessage(), 0, $e);
        }
    }

    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * Test database connection
     */
    public static function testConnection(): bool
    {
        try {
            $pdo = self::getInstance();
            $pdo->query('SELECT 1');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Test write capability
     */
    public static function testWrite(): bool
    {
        try {
            $pdo = self::getInstance();
            $pdo->exec('CREATE TABLE IF NOT EXISTS _test_table (id INTEGER PRIMARY KEY)');
            $pdo->exec('DROP TABLE _test_table');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
