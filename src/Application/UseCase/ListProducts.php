<?php

namespace App\Application\UseCase;

use App\Application\DTO\ProductDTO;
use App\Application\DTO\PaginationDTO;
use App\Domain\Repository\ProductRepositoryInterface;

class ListProducts
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @return array{products: ProductDTO[], pagination: PaginationDTO}
     */
    public function execute(int $page = 1, int $limit = 20): array
    {
        $products = $this->productRepository->findAll($page, $limit);
        $total = $this->productRepository->count();

        $productDTOs = array_map(
            fn($product) => ProductDTO::fromEntity($product),
            $products
        );

        $pagination = new PaginationDTO($page, $limit, $total);

        return [
            'products' => $productDTOs,
            'pagination' => $pagination,
        ];
    }
}
