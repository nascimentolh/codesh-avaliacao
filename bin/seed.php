#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Domain\Entity\Product;
use App\Domain\ValueObject\ProductCode;
use App\Infrastructure\Database\Connection;
use App\Infrastructure\Repository\SQLiteProductRepository;

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

// Initialize repository
$productRepository = new SQLiteProductRepository($pdo);

echo "Loading sample products from products.json...\n";

// Load products.json
$jsonContent = file_get_contents(__DIR__ . '/../products.json');
$products = json_decode($jsonContent, true);

if (!is_array($products)) {
    echo "✗ Failed to parse products.json\n";
    exit(1);
}

$imported = 0;
foreach ($products as $productData) {
    try {
        if (!isset($productData['code'])) {
            continue;
        }

        $code = ProductCode::fromInt((int)$productData['code']);

        // Check if product already exists
        if ($productRepository->exists($code)) {
            echo "  - Product {$productData['code']} already exists, skipping...\n";
            continue;
        }

        $product = Product::create($code, $productData);
        $productRepository->save($product);

        echo "  ✓ Imported product: {$productData['code']} - {$productData['product_name']}\n";
        $imported++;
    } catch (\Exception $e) {
        echo "  ✗ Failed to import product: " . $e->getMessage() . "\n";
    }
}

echo "\n✓ Seed completed! {$imported} product(s) imported.\n";
exit(0);
