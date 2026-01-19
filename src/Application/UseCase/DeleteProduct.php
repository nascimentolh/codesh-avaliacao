<?php

namespace App\Application\UseCase;

use App\Domain\Repository\ProductRepositoryInterface;
use App\Domain\ValueObject\ProductCode;

class DeleteProduct
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function execute(int $code): bool
    {
        $productCode = ProductCode::fromInt($code);

        if (!$this->productRepository->exists($productCode)) {
            return false;
        }

        $this->productRepository->delete($productCode);
        return true;
    }
}
