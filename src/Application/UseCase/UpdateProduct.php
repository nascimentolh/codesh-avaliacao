<?php

namespace App\Application\UseCase;

use App\Application\DTO\ProductDTO;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Domain\ValueObject\ProductCode;

class UpdateProduct
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function execute(int $code, array $data): ?ProductDTO
    {
        $productCode = ProductCode::fromInt($code);
        $product = $this->productRepository->findByCode($productCode);

        if ($product === null) {
            return null;
        }

        $product->update($data);
        $this->productRepository->update($product);

        return ProductDTO::fromEntity($product);
    }
}
