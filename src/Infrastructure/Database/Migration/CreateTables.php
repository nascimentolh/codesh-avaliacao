<?php

namespace App\Infrastructure\Database\Migration;

use PDO;
use RuntimeException;

class CreateTables
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function run(): void
    {
        $this->createProductsTable();
        $this->createImportHistoryTable();
        $this->createIndexes();
    }

    private function createProductsTable(): void
    {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS products (
            code INTEGER PRIMARY KEY,
            status TEXT NOT NULL CHECK(status IN ('draft','trash','published')),
            imported_t TEXT NOT NULL,
            url TEXT,
            creator TEXT,
            created_t INTEGER,
            last_modified_t INTEGER,
            product_name TEXT,
            quantity TEXT,
            brands TEXT,
            categories TEXT,
            labels TEXT,
            cities TEXT,
            purchase_places TEXT,
            stores TEXT,
            ingredients_text TEXT,
            traces TEXT,
            serving_size TEXT,
            serving_quantity REAL,
            nutriscore_score INTEGER,
            nutriscore_grade TEXT,
            main_category TEXT,
            image_url TEXT
        )
        SQL;

        if ($this->pdo->exec($sql) === false) {
            throw new RuntimeException("Failed to create products table");
        }
    }

    private function createImportHistoryTable(): void
    {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS import_history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            filename TEXT NOT NULL,
            products_imported INTEGER NOT NULL DEFAULT 0,
            started_at TEXT NOT NULL,
            completed_at TEXT,
            status TEXT NOT NULL CHECK(status IN ('running','completed','failed')),
            error_message TEXT
        )
        SQL;

        if ($this->pdo->exec($sql) === false) {
            throw new RuntimeException("Failed to create import_history table");
        }
    }

    private function createIndexes(): void
    {
        $indexes = [
            "CREATE INDEX IF NOT EXISTS idx_product_status ON products(status)",
            "CREATE INDEX IF NOT EXISTS idx_imported_t ON products(imported_t)",
            "CREATE INDEX IF NOT EXISTS idx_import_history_status ON import_history(status)",
            "CREATE INDEX IF NOT EXISTS idx_import_history_started ON import_history(started_at DESC)",
        ];

        foreach ($indexes as $sql) {
            if ($this->pdo->exec($sql) === false) {
                throw new RuntimeException("Failed to create index: {$sql}");
            }
        }
    }

    public function drop(): void
    {
        $this->pdo->exec("DROP TABLE IF EXISTS products");
        $this->pdo->exec("DROP TABLE IF EXISTS import_history");
    }
}
