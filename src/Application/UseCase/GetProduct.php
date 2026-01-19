<?php

namespace App\Application\UseCase;

use App\Application\DTO\ProductDTO;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Domain\ValueObject\ProductCode;

class GetProduct
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function execute(int $code): ?ProductDTO
    {
        $productCode = ProductCode::fromInt($code);
        $product = $this->productRepository->findByCode($productCode);

        if ($product === null) {
            return null;
        }

        return ProductDTO::fromEntity($product);
    }
}
