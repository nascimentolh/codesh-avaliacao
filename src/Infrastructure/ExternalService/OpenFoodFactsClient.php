<?php

namespace App\Infrastructure\ExternalService;

use RuntimeException;

class OpenFoodFactsClient
{
    private string $baseUrl;
    private int $timeout;

    public function __construct(string $baseUrl, int $timeout = 30)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
    }

    /**
     * Fetch the list of available JSON files
     *
     * @return string[] Array of filenames
     */
    public function fetchFileList(): array
    {
        $url = "{$this->baseUrl}/index.txt";
        $content = $this->fetchUrl($url);

        if (empty($content)) {
            return [];
        }

        $lines = explode("\n", trim($content));
        return array_filter(array_map('trim', $lines));
    }

    /**
     * Fetch products from a specific JSON file
     *
     * @param string $filename
     * @param int $limit Maximum number of products to return
     * @return array
     */
    public function fetchProducts(string $filename, int $limit = 100): array
    {
        $url = "{$this->baseUrl}/{$filename}";
        $content = $this->fetchUrl($url);

        if (empty($content)) {
            return [];
        }

        $products = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Failed to decode JSON from {$filename}: " . json_last_error_msg());
        }

        if (!is_array($products)) {
            return [];
        }

        // Limit the number of products
        return array_slice($products, 0, $limit);
    }

    /**
     * Fetch URL content with retry logic
     */
    private function fetchUrl(string $url, int $maxRetries = 3): string
    {
        $attempt = 0;
        $lastError = null;

        while ($attempt < $maxRetries) {
            try {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => $this->timeout,
                        'user_agent' => 'Fitness Foods API/1.0',
                    ],
                ]);

                $content = @file_get_contents($url, false, $context);

                if ($content === false) {
                    $error = error_get_last();
                    throw new RuntimeException($error['message'] ?? 'Unknown error');
                }

                return $content;
            } catch (\Exception $e) {
                $lastError = $e;
                $attempt++;

                if ($attempt < $maxRetries) {
                    // Exponential backoff: 1s, 2s, 4s
                    sleep(pow(2, $attempt - 1));
                }
            }
        }

        throw new RuntimeException(
            "Failed to fetch {$url} after {$maxRetries} attempts: " . $lastError->getMessage(),
            0,
            $lastError
        );
    }

    /**
     * Test connection to Open Food Facts API
     */
    public function testConnection(): bool
    {
        try {
            $url = "{$this->baseUrl}/index.txt";
            $this->fetchUrl($url, 1);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
