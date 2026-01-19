<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Product;
use App\Domain\ValueObject\ProductCode;

interface ProductRepositoryInterface
{
    /**
     * Save a new product
     */
    public function save(Product $product): void;

    /**
     * Update an existing product
     */
    public function update(Product $product): void;

    /**
     * Find a product by its code
     */
    public function findByCode(ProductCode $code): ?Product;

    /**
     * Find all products with pagination
     *
     * @return Product[]
     */
    public function findAll(int $page = 1, int $limit = 20): array;

    /**
     * Count total products
     */
    public function count(): int;

    /**
     * Delete a product (soft delete - change status to trash)
     */
    public function delete(ProductCode $code): void;

    /**
     * Check if a product exists
     */
    public function exists(ProductCode $code): bool;
}
