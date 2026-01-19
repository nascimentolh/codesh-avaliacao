<?php

namespace App\Presentation\Controller;

use App\Application\UseCase\GetProduct;
use App\Application\UseCase\ListProducts;
use App\Application\UseCase\UpdateProduct;
use App\Application\UseCase\DeleteProduct;

class ProductController
{
    private GetProduct $getProduct;
    private ListProducts $listProducts;
    private UpdateProduct $updateProduct;
    private DeleteProduct $deleteProduct;

    public function __construct(
        GetProduct $getProduct,
        ListProducts $listProducts,
        UpdateProduct $updateProduct,
        DeleteProduct $deleteProduct
    ) {
        $this->getProduct = $getProduct;
        $this->listProducts = $listProducts;
        $this->updateProduct = $updateProduct;
        $this->deleteProduct = $deleteProduct;
    }

    public function index(array $params): array
    {
        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $limit = isset($params['limit']) ? min(100, max(1, (int)$params['limit'])) : 20;

        $result = $this->listProducts->execute($page, $limit);

        return [
            'data' => array_map(fn($dto) => $dto->toArray(), $result['products']),
            'pagination' => $result['pagination']->toArray(),
        ];
    }

    public function show(int $code): array
    {
        $product = $this->getProduct->execute($code);

        if ($product === null) {
            http_response_code(404);
            return [
                'error' => 'Not Found',
                'message' => "Product with code {$code} not found",
            ];
        }

        return $product->toArray();
    }

    public function update(int $code, array $data): array
    {
        try {
            $product = $this->updateProduct->execute($code, $data);

            if ($product === null) {
                http_response_code(404);
                return [
                    'error' => 'Not Found',
                    'message' => "Product with code {$code} not found",
                ];
            }

            return [
                'message' => 'Product updated successfully',
                'data' => $product->toArray(),
            ];
        } catch (\Exception $e) {
            http_response_code(400);
            return [
                'error' => 'Bad Request',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function delete(int $code): array
    {
        $deleted = $this->deleteProduct->execute($code);

        if (!$deleted) {
            http_response_code(404);
            return [
                'error' => 'Not Found',
                'message' => "Product with code {$code} not found",
            ];
        }

        return [
            'message' => 'Product moved to trash successfully',
        ];
    }
}
