#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Infrastructure\Database\Connection;
use App\Infrastructure\Repository\SQLiteProductRepository;
use App\Infrastructure\Repository\SQLiteImportHistoryRepository;
use App\Infrastructure\ExternalService\OpenFoodFactsClient;
use App\Infrastructure\Cron\ImportCronJob;

// Load autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Load configuration
$dbConfig = require __DIR__ . '/../config/database.php';
$cronConfig = require __DIR__ . '/../config/cron.php';

// Initialize database connection
Connection::setConfig($dbConfig);
$pdo = Connection::getInstance();

// Initialize repositories
$productRepository = new SQLiteProductRepository($pdo);
$historyRepository = new SQLiteImportHistoryRepository($pdo);

// Initialize Open Food Facts client
$client = new OpenFoodFactsClient(
    $cronConfig['open_food_facts_url']
);

// Initialize CRON job
$cronJob = new ImportCronJob(
    $client,
    $productRepository,
    $historyRepository,
    $cronConfig['import_limit_per_file']
);

// Execute import
echo "[" . date('Y-m-d H:i:s') . "] Starting product import...\n";

try {
    $result = $cronJob->execute();

    if ($result['success']) {
        echo "[" . date('Y-m-d H:i:s') . "] Import completed successfully!\n";
        echo "  Files processed: {$result['files_processed']}\n";
        echo "  Products imported: {$result['products_imported']}\n";

        if (!empty($result['errors'])) {
            echo "  Errors encountered: " . count($result['errors']) . "\n";
            foreach ($result['errors'] as $error) {
                echo "    - {$error['filename']}: {$error['error']}\n";
            }
        }
    } else {
        echo "[" . date('Y-m-d H:i:s') . "] Import failed!\n";
        foreach ($result['errors'] as $error) {
            echo "  Error: " . ($error['error'] ?? 'Unknown error') . "\n";
        }
        exit(1);
    }
} catch (\Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] Fatal error: " . $e->getMessage() . "\n";
    echo "  File: {$e->getFile()}\n";
    echo "  Line: {$e->getLine()}\n";
    exit(1);
}

exit(0);
