#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Infrastructure\Database\Connection;
use App\Infrastructure\Database\Migration\CreateTables;

// Load autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Load configuration
$dbConfig = require __DIR__ . '/../config/database.php';

// Initialize database connection
Connection::setConfig($dbConfig);
$pdo = Connection::getInstance();

// Run migration
echo "Running database migration...\n";

try {
    $migration = new CreateTables($pdo);
    $migration->run();

    echo "✓ Migration completed successfully!\n";
    echo "  - products table created\n";
    echo "  - import_history table created\n";
    echo "  - indexes created\n";
} catch (\Exception $e) {
    echo "✗ Migration failed: {$e->getMessage()}\n";
    exit(1);
}

exit(0);
