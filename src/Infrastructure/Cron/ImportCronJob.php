<?php

namespace App\Infrastructure\Cron;

use App\Domain\Entity\ImportHistory;
use App\Domain\Entity\Product;
use App\Domain\Repository\ImportHistoryRepositoryInterface;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Domain\ValueObject\ProductCode;
use App\Infrastructure\ExternalService\OpenFoodFactsClient;

class ImportCronJob
{
    private OpenFoodFactsClient $client;
    private ProductRepositoryInterface $productRepository;
    private ImportHistoryRepositoryInterface $historyRepository;
    private int $limitPerFile;

    public function __construct(
        OpenFoodFactsClient $client,
        ProductRepositoryInterface $productRepository,
        ImportHistoryRepositoryInterface $historyRepository,
        int $limitPerFile = 100
    ) {
        $this->client = $client;
        $this->productRepository = $productRepository;
        $this->historyRepository = $historyRepository;
        $this->limitPerFile = $limitPerFile;
    }

    /**
     * Execute the import job
     *
     * @return array Statistics about the import
     */
    public function execute(): array
    {
        $totalImported = 0;
        $totalFiles = 0;
        $errors = [];

        try {
            $fileList = $this->client->fetchFileList();

            foreach ($fileList as $filename) {
                try {
                    $imported = $this->importFile($filename);
                    $totalImported += $imported;
                    $totalFiles++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'filename' => $filename,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return [
                'success' => true,
                'files_processed' => $totalFiles,
                'products_imported' => $totalImported,
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'files_processed' => $totalFiles,
                'products_imported' => $totalImported,
                'errors' => array_merge($errors, [['error' => $e->getMessage()]]),
            ];
        }
    }

    /**
     * Import products from a single file
     */
    private function importFile(string $filename): int
    {
        $history = ImportHistory::start($filename);
        $this->historyRepository->save($history);

        try {
            $products = $this->client->fetchProducts($filename, $this->limitPerFile);
            $imported = 0;

            foreach ($products as $productData) {
                try {
                    if (!isset($productData['code'])) {
                        continue;
                    }

                    $code = ProductCode::fromInt((int)$productData['code']);

                    // Check if product already exists
                    if ($this->productRepository->exists($code)) {
                        // Update existing product
                        $product = $this->productRepository->findByCode($code);
                        if ($product) {
                            $product->update($productData);
                            $this->productRepository->update($product);
                        }
                    } else {
                        // Create new product
                        $product = Product::create($code, $productData);
                        $this->productRepository->save($product);
                    }

                    $imported++;
                } catch (\Exception $e) {
                    // Log individual product error but continue with others
                    error_log("Failed to import product from {$filename}: " . $e->getMessage());
                }
            }

            $history->complete($imported);
            $this->historyRepository->update($history);

            return $imported;
        } catch (\Exception $e) {
            $history->fail($e->getMessage());
            $this->historyRepository->update($history);
            throw $e;
        }
    }

    /**
     * Get the last import time
     */
    public function getLastImportTime(): ?string
    {
        $lastHistory = $this->historyRepository->findLast();

        if ($lastHistory === null) {
            return null;
        }

        return $lastHistory->getStartedAt()->format('Y-m-d H:i:s');
    }
}
